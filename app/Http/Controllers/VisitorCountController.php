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

            $isAdmin = Auth::guard('admin')->check();

            if (!$isAdmin && !$isBotOrPrefetch) {
                // Perform atomic database increment
                $affected = DB::table('site_settings')
                    ->where('setting_key', 'visitor_count')
                    ->update([
                        'setting_value' => DB::raw('CAST(setting_value AS SIGNED) + 1'),
                        'updated_at' => now()
                    ]);

                if ($affected === 0) {
                    // Fallback to updateOrInsert if row did not exist
                    DB::table('site_settings')->updateOrInsert(
                        ['setting_key' => 'visitor_count'],
                        ['setting_value' => '1', 'updated_at' => now()]
                    );
                }

                // Bust cache so the dashboard/site shows the fresh count
                Cache::forget('site_settings');

                // Get fresh updated count to return
                $newCount = DB::table('site_settings')
                    ->where('setting_key', 'visitor_count')
                    ->value('setting_value') ?: 0;

                return response()->json([
                    'status' => 'counted',
                    'visitor_count' => (int)$newCount
                ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            }

            // If it's admin or bot, fetch current count without incrementing
            $currentCount = DB::table('site_settings')
                ->where('setting_key', 'visitor_count')
                ->value('setting_value') ?: 0;

            return response()->json([
                'status' => 'ignored',
                'visitor_count' => (int)$currentCount
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
