<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Biodata - {{ $profile->full_name }}</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 15px;
            color: #111;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .action-bar {
            max-width: 720px;
            margin: 0 auto 15px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
            align-items: center;
        }
        .action-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            background-color: #0f1754;
            color: #fff;
            padding: 9px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.15s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn-secondary {
            background-color: #64748b;
        }
        .btn-success {
            background-color: #059669;
        }
        .pdf-wrapper {
            max-width: 720px;
            margin: 0 auto;
            overflow-x: auto;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        #pdf-card {
            width: 720px;
            min-width: 720px;
            background: #ffffff;
            padding: 0;
            margin: 0 auto;
            color: #111;
        }
        @media print {
            .action-bar {
                display: none !important;
            }
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .pdf-wrapper {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                overflow: visible;
            }
            #pdf-card {
                width: 100% !important;
                min-width: 100% !important;
                border-width: 2px !important;
            }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="{{ url()->previous() ?: route('profiles.search') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Profile
    </a>
    <div class="action-group">
        <button type="button" onclick="downloadPdfCard()" class="btn">
            <i class="fa-solid fa-download"></i> Download PDF
        </button>
        <button type="button" onclick="window.print()" class="btn btn-success">
            <i class="fa-solid fa-print"></i> Print / Save as PDF
        </button>
    </div>
</div>

@php
    $gc = (strtolower($profile->gender ?? '') === 'female') ? 'F' : 'M';
    $rawNum = preg_replace('/[^0-9]/', '', $profile->profile_id ?: $profile->id);
    $pnum = !empty($rawNum) ? $rawNum : $profile->id;
    $badgeCode = $gc . '-' . $pnum;

    $pdfPhoto = '';
    $photoPath = !empty($profile->profile_photo) ? resolve_media_path($profile->profile_photo) : null;
    if (!empty($photoPath) && file_exists($photoPath)) {
        $mime = mime_content_type($photoPath);
        $b64 = base64_encode(file_get_contents($photoPath));
        $pdfPhoto = 'data:' . $mime . ';base64,' . $b64;
    } else {
        $palette = ['1E3A5F', '8B2323', '0D9488', '7C3AED', 'D97706', '2563EB', 'DB2777', '059669', '4F46E5', 'DC2626'];
        $bgColor = $palette[abs(crc32((string)($profile->id . ($profile->profile_id ?? $profile->id)))) % count($palette)];
        $pdfPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($profile->full_name) . '&size=300&background=' . $bgColor . '&color=fff&bold=true';
    }

    $parentMobiles = [];
    if (!empty($profile->father_mobile)) {
        $parentMobiles[] = preg_replace('/^\+?91/', '', $profile->father_mobile);
    }
    if (!empty($profile->mother_mobile)) {
        $parentMobiles[] = preg_replace('/^\+?91/', '', $profile->mother_mobile);
    }
    $parentMobileStr = count($parentMobiles) > 0 ? implode(' / ', $parentMobiles) : ($profile->mobile ?? 'N/A');
@endphp

<div class="pdf-wrapper">
<div id="pdf-card">
  <div style="border:3px solid #0f1754; background:#ffffff;">
    
    <!-- Top Pill Badge Row -->
    <div style="text-align:center; padding:8px 0; background:#ffffff; border-bottom:2px solid #0f1754;">
      <span style="display:inline-block; border:2px solid #0f1754; border-radius:4px; padding:3px 24px; font-size:16px; font-weight:bold; color:#0f1754; font-family:'Courier New', Courier, monospace, Arial, sans-serif; letter-spacing:1px;">
        {{ $badgeCode }}
      </span>
    </div>

    <!-- Candidate Name Dark Blue Header Bar -->
    <div style="background:#0f1754; padding:12px 16px; border-bottom:2px solid #0f1754;">
      <h2 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0; text-transform:uppercase; font-family:Arial, sans-serif; letter-spacing:0.5px;">
        {{ $profile->full_name }}
      </h2>
    </div>

    <!-- Main Two-Column Table -->
    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
      <tr>
        <!-- Left Column (General Details & Family) -->
        <td style="vertical-align:top; padding:12px 16px; font-size:13px; border-right:2px solid #0f1754; width:62%; line-height:1.8; color:#111;">
          
          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Education</strong> : &nbsp;{{ $profile->higher_education ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Occu. / Firm</strong> : &nbsp;{{ $profile->occupation ?? 'N/A' }}@if(!empty($profile->company_name)) ({{ $profile->company_name }})@endif
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Designation</strong> : &nbsp;{{ $profile->designation ?? '' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">{{ ($profile->income_type ?? 'Yearly') === 'Monthly' ? 'Monthly Salary / Income' : 'Annual Salary / Income' }}</strong> : &nbsp;{{ format_indian_currency($profile->monthly_income) }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Mobile</strong> : &nbsp;{{ $profile->mobile }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Hobbies</strong> : &nbsp;{{ $profile->hobbies ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Father</strong> : &nbsp;{{ $profile->father_name ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Father's Occupation</strong> : &nbsp;{{ $profile->father_occupation ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Parent's M. No.</strong> : &nbsp;{{ $parentMobileStr }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Mother</strong> : &nbsp;{{ $profile->mother_name ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Brothers</strong> : &nbsp;Married {{ $profile->brothers_married ?? 0 }} &nbsp;Unmarried {{ $profile->brothers_unmarried ?? 0 }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:130px; display:inline-block;">Sisters</strong> : &nbsp;Married {{ $profile->sisters_married ?? 0 }} &nbsp;Unmarried {{ $profile->sisters_unmarried ?? 0 }}
          </div>

          <div style="margin-bottom:3px; word-break:break-word;">
            <strong style="color:#000; width:130px; display:inline-block;">Address</strong> : &nbsp;{{ $profile->permanent_address ?? $profile->current_address ?? 'N/A' }}
          </div>

        </td>

        <!-- Right Column (Photo & Personal Attributes) -->
        <td style="vertical-align:top; padding:12px 14px; font-size:13px; width:38%; line-height:1.8; color:#111;">
          
          <!-- High-Res Profile Image Container -->
          <div style="margin:0 auto 12px auto; width:170px; height:210px; border:2px solid #0f1754; background:#f8fafc; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <img src="{{ $pdfPhoto }}" style="width:100%; height:100%; object-fit:cover; object-position:center top; display:block; image-rendering: -webkit-optimize-contrast;">
          </div>

          <!-- Key Details Under Photo -->
          <div style="padding-left:6px;">
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">DOB</strong> : &nbsp;{{ $profile->birth_date ? (is_string($profile->birth_date) ? date('d-m-Y', strtotime($profile->birth_date)) : $profile->birth_date->format('d-m-Y')) : 'N/A' }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">B. Time</strong> : &nbsp;{{ format_birth_time($profile->birth_time) }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">B. Place</strong> : &nbsp;{{ $profile->birth_place ?? 'N/A' }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">Height</strong> : &nbsp;{{ $profile->height ?? 'N/A' }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">Weight</strong> : &nbsp;{{ format_weight($profile->weight ?? ($profile->weight_kg ? $profile->weight_kg . ' kg' : null)) }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">Native</strong> : &nbsp;{{ $profile->native_place ?? 'N/A' }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">Gotra</strong> : &nbsp;{{ $profile->gotra ?? 'N/A' }}
            </div>
            <div style="margin-bottom:3px;">
              <strong style="color:#000; width:80px; display:inline-block;">Manglik</strong> : &nbsp;{{ $profile->manglik ? ucfirst($profile->manglik) : 'No' }}
            </div>
          </div>

        </td>
      </tr>

      <!-- Bottom Full-Width Section -->
      <tr>
        <td colspan="2" style="border-top:2px solid #0f1754; padding:12px 16px; font-size:13px; line-height:1.8; color:#111; background:#ffffff;">
          
          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:165px; display:inline-block;">Current Address</strong> : &nbsp;{{ $profile->current_address ?? 'as above' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:165px; display:inline-block;">Specific Partner Choice</strong> : &nbsp;{{ $profile->partner_preference ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:165px; display:inline-block;">Language Known</strong> : &nbsp;{{ $profile->languages ?? 'N/A' }}
          </div>

          <div style="margin-bottom:3px;">
            <strong style="color:#000; width:165px; display:inline-block;">Other Info.</strong> : &nbsp;{{ $profile->handicapped ? ($profile->handicapped === 'Yes' ? 'Handicapped' : 'No') : 'No' }} / {{ $profile->marital_status ?? 'N/A' }}
          </div>

        </td>
      </tr>
    </table>

  </div>
</div>
</div>

<script>
function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) || 
           (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

function downloadPdfCard() {
    const element = document.getElementById('pdf-card');
    if (!element) return;
    const filename = 'Profile_MID_{{ $pnum }}.pdf';
    const opt = {
        margin:       [5, 5, 5, 5],
        filename:     filename,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, allowTaint: true, logging: false, scrollX: 0, scrollY: 0 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    if (isIOS()) {
        // iOS Safari does not support automated <a download> file saving for javascript blob URLs
        // We open the bloburl or trigger native iOS print
        html2pdf().set(opt).from(element).outputPdf('bloburl').then(function(pdfBlobUrl) {
            window.open(pdfBlobUrl, '_blank');
        }).catch(function(err) {
            console.warn('iOS PDF generation fallback to print:', err);
            window.print();
        });
    } else {
        html2pdf().set(opt).from(element).save().catch(function(err) {
            console.error('PDF download error:', err);
            window.print();
        });
    }
}
</script>

</body>
</html>
