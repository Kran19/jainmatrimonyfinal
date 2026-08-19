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
                // Determine driver to use appropriate SQL cast expression (cross-database compatibility)
                $driver = DB::getDriverName();
                $castExpression = match ($driver) {
                    'pgsql' => 'CAST(setting_value AS INTEGER) + 1',
                    'sqlsrv' => 'CAST(setting_value AS INT) + 1',
                    default => 'CAST(setting_value AS SIGNED) + 1',
                };

                // Check if site_settings table has updated_at column to avoid SQL failures on legacy databases
                $hasUpdatedAt = Cache::remember('site_settings_has_updated_at', 86400, function () {
                    try {
                        return \Illuminate\Support\Facades\Schema::hasColumn('site_settings', 'updated_at');
                    } catch (\Exception $e) {
                        return false;
                    }
                });

                $updateData = [
                    'setting_value' => DB::raw($castExpression)
                ];
                if ($hasUpdatedAt) {
                    $updateData['updated_at'] = now();
                }

                // Perform atomic database increment
                $affected = DB::table('site_settings')
                    ->where('setting_key', 'visitor_count')
                    ->update($updateData);

                if ($affected === 0) {
                    // Fallback to updateOrInsert if row did not exist
                    $insertData = ['setting_value' => '1'];
                    if ($hasUpdatedAt) {
                        $insertData['updated_at'] = now();
                        try {
                            if (\Illuminate\Support\Facades\Schema::hasColumn('site_settings', 'created_at')) {
                                $insertData['created_at'] = now();
                            }
                        } catch (\Exception $e) {}
                    }
                    DB::table('site_settings')->updateOrInsert(
                        ['setting_key' => 'visitor_count'],
                        $insertData
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
