<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reporting controls and results.
     */
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'members');
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $results = [];
        $summary = [];

        if ($reportType === 'members') {
            $query = User::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $results = $query->orderBy('created_at', 'desc')->get();

            // Calculate summaries
            $summary['total'] = $results->count();
            $summary['approved'] = $results->where('status', 'approved')->count();
            $summary['pending'] = $results->where('status', 'pending')->count();
        } else {
            // Revenue report
            $query = Payment::with('user')
                ->where('status', 'verified')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            $results = $query->orderBy('created_at', 'desc')->get();

            // Calculate summaries
            $summary['total_transactions'] = $results->count();
            $summary['total_revenue'] = $results->sum('amount');
        }

        return view('admin.reports.index', compact('results', 'summary', 'reportType', 'startDate', 'endDate'));
    }

    /**
     * Export reports as CSV.
     */
    public function export(Request $request)
    {
        $reportType = $request->input('report_type', 'members');
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $reportType . "_report_" . date('Ymd') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        if ($reportType === 'members') {
            $query = User::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            $results = $query->orderBy('created_at', 'desc')->get();

            $callback = function() use($results) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Profile ID', 'Full Name', 'Gender', 'Email', 'Mobile', 'Status', 'Registered At']);
                foreach ($results as $row) {
                    fputcsv($file, [
                        $row->profile_id,
                        $row->full_name,
                        $row->gender,
                        $row->email,
                        $row->mobile,
                        $row->status,
                        $row->created_at
                    ]);
                }
                fclose($file);
            };
        } else {
            $query = Payment::with('user')
                ->where('status', 'verified')
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
            $results = $query->orderBy('created_at', 'desc')->get();

            $callback = function() use($results) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Payment Date', 'Transaction ID', 'Member Name', 'Method', 'Amount']);
                foreach ($results as $row) {
                    fputcsv($file, [
                        $row->created_at,
                        $row->transaction_id,
                        $row->user->full_name ?? 'N/A',
                        $row->payment_method,
                        $row->amount
                    ]);
                }
                fclose($file);
            };
        }

        return response()->stream($callback, 200, $headers);
    }
}
