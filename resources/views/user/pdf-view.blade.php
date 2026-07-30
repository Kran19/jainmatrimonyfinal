<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata - {{ $profile->full_name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #111;
        }
        .action-bar {
            max-width: 720px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background-color: #0f1754;
            color: #fff;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn:hover {
            background-color: #1e293b;
        }
        @media print {
            .action-bar {
                display: none !important;
            }
            body {
                background: #fff;
                padding: 0;
            }
            #pdf-card {
                border-width: 2px !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="{{ url()->previous() }}" class="btn" style="background:#64748b;">&larr; Back to Profile</a>
    <button onclick="downloadPdfCard()" class="btn">Download / Save PDF</button>
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
        $pdfPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($profile->full_name) . '&size=300&background=0f1754&color=fff';
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

<div id="pdf-card" style="width:720px; background:#ffffff; padding:0; margin:0 auto; color:#111; box-sizing:border-box;">
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
            <strong style="color:#000; width:130px; display:inline-block;">Monthly Income</strong> : &nbsp;{{ $profile->monthly_income ? number_format($profile->monthly_income) : 'N/A' }}
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
              <strong style="color:#000; width:80px; display:inline-block;">B. Time</strong> : &nbsp;{{ $profile->birth_time ?? 'N/A' }}
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

<script>
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
    html2pdf().set(opt).from(element).save().catch(function() {
        window.print();
    });
}
</script>

</body>
</html>
