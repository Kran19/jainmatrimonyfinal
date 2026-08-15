<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VisitorCountController extends Controller
{
    /**
     * Track a unique visitor session asynchronously.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request)
    {
        try {
            // Exclude admins, bots, and browser prefetch requests
            $isBotOrPrefetch = $request->hasHeader('X-Purpose') 
                || $request->hasHeader('Sec-Purpose')
                || $request->header('Purpose') === 'prefetch';

            $currentValue = DB::table('site_settings')
                ->where('setting_key', 'visitor_count')
                ->value('setting_value') ?: 0;

            if (!Auth::guard('admin')->check() && !$isBotOrPrefetch) {
                // If this session has not been counted yet
                if (!$request->session()->has('visitor_counted')) {
                    $request->session()->put('visitor_counted', true);

                    $newCount = (int)$currentValue + 1;

                    DB::table('site_settings')->updateOrInsert(
                        ['setting_key' => 'visitor_count'],
                        ['setting_value' => (string)$newCount, 'updated_at' => now()]
                    );

                    // Bust cache so the dashboard/site shows the fresh count
                    Cache::forget('site_settings');

                    return response()->json([
                        'status' => 'counted',
                        'visitor_count' => $newCount
                    ]);
                }
            }

            return response()->json([
                'status' => 'already_counted_or_ignored',
                'visitor_count' => (int)$currentValue
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
