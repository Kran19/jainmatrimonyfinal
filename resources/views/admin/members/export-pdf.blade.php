<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Directory Report - दिगम्बर जैन परिचय सम्मेलन समिति</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', 'Noto Sans Devanagari', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            padding: 24px;
            font-size: 11px;
            line-height: 1.4;
        }

        .no-print-bar {
            max-width: 1200px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #1e3a5f;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #112239;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #b45309;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #7a161b;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 12px;
            color: #8b1e22;
            font-weight: 600;
        }

        .meta-strip {
            display: flex;
            justify-content: space-between;
            background: #fdf8f6;
            border: 1px solid #fed7aa;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 11px;
            color: #431407;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th {
            background: #1e3a5f;
            color: #ffffff;
            text-align: left;
            padding: 9px 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9.5px;
            border: 1px solid #0f172a;
        }

        td {
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-account { background: #e0e7ff; color: #3730a3; }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 9.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .report-container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="font-size: 13px; font-weight: 700; color: #1e3a5f;">
            <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i> Members Directory Export ({{ count($members) }} Records)
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Print / Save as PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i> Close
            </button>
        </div>
    </div>

    <div class="report-container">
        <div class="header">
            <h1>दिगम्बर जैन परिचय सम्मेलन समिति</h1>
            <p>दिगम्बर जैन समाज के विवाह योग्य युवक-युवतियों के जीवनसाथी चयन में सहायक एकमात्र वेबसाईट</p>
        </div>

        <div class="meta-strip">
            <div><strong>Generated On:</strong> {{ date('d M Y, h:i A') }}</div>
            <div><strong>Total Members:</strong> {{ count($members) }}</div>
            <div><strong>Filter:</strong> {{ request('status') ? 'Status: ' . ucfirst(str_replace('_', ' ', request('status'))) : 'All Statuses' }} | {{ request('gender') ? 'Gender: ' . request('gender') : 'All Genders' }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">#</th>
                    <th>Match ID</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Age / DOB</th>
                    <th>Gotra / Native</th>
                    <th>Education / Occupation</th>
                    <th>Annual Income</th>
                    <th>Mobile</th>
                    <th>Father Name & Mobile</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $m)
                    @php
                        $age = 'N/A';
                        if (!empty($m->birth_date)) {
                            try {
                                $dob = new \DateTime($m->birth_date);
                                $today = new \DateTime('today');
                                $age = $dob->diff($today)->y . ' yrs';
                            } catch (\Exception $e) {}
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="font-weight: bold; color: #1e3a5f;">{{ $m->profile_id ?? ('ID-' . $m->id) }}</td>
                        <td style="font-weight: 700; color: #0f172a;">{{ $m->full_name }}</td>
                        <td>{{ $m->gender ?? 'N/A' }}</td>
                        <td>
                            <div>{{ $age }}</div>
                            <div style="color: #64748b; font-size: 8.5px;">{{ $m->birth_date ?? '' }}</div>
                        </td>
                        <td>
                            <div><strong>Gotra:</strong> {{ $m->gotra ?? 'N/A' }}</div>
                            <div style="color: #64748b; font-size: 8.5px;">{{ $m->native_place ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $m->higher_education ?? 'N/A' }}</div>
                            <div style="color: #64748b; font-size: 8.5px;">{{ $m->occupation ?? 'N/A' }}</div>
                        </td>
                        <td style="font-weight: 600; color: #166534;">
                            {{ $m->monthly_income ? '₹' . number_format($m->monthly_income) : 'N/A' }}
                        </td>
                        <td style="font-family: monospace; font-weight: 600;">
                            {{ $m->mobile ?? 'N/A' }}
                        </td>
                        <td>
                            <div>{{ $m->father_name ?? 'N/A' }}</div>
                            <div style="color: #64748b; font-size: 8.5px; font-family: monospace;">{{ $m->father_mobile ?? '' }}</div>
                        </td>
                        <td>
                            @php
                                $statusClass = 'badge-pending';
                                if ($m->status === 'approved') $statusClass = 'badge-approved';
                                elseif (in_array($m->status, ['rejected', 'blocked', 'deleted'])) $statusClass = 'badge-rejected';
                                elseif (str_starts_with($m->status, 'account_')) $statusClass = 'badge-account';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $m->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 20px; color: #64748b;">
                            No members found matching current filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Generated from दिगम्बर जैन परिचय सम्मेलन समिति Administration Portal &bull; Confidential
        </div>
    </div>

</body>
</html>
