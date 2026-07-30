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
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        // Default dates if empty
        if (!$startDate) {
            $startDate = now()->subYear()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = now()->format('Y-m-d');
        }

        // Auto-correct if start_date is later than end_date
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $results = [];
        $summary = [];

        if ($reportType === 'members') {
            $query = User::query();

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

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
            $summary['blocked'] = $results->where('status', 'blocked')->count();
        } else {
            // Revenue report
            $query = Payment::with('user');

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

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
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if (!$startDate) {
            $startDate = now()->subYear()->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = now()->format('Y-m-d');
        }

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $reportType . "_report_" . date('Ymd') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        if ($reportType === 'members') {
            $query = User::query();
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
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
                        $row->profile_id ?: $row->id,
                        $row->full_name,
                        $row->gender,
                        $row->email,
                        $row->mobile,
                        $row->status,
                        $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                fclose($file);
            };
        } else {
            $query = Payment::with('user');
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
            $results = $query->orderBy('created_at', 'desc')->get();

            $callback = function() use($results) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Payment Date', 'Transaction ID', 'Member Name', 'Method', 'Amount']);
                foreach ($results as $row) {
                    fputcsv($file, [
                        $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : 'N/A',
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
