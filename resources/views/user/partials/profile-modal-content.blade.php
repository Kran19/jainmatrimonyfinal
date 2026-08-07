@php
    // Verify if logged-in user can view contacts
    $me = Auth::user();
    $activePlan = $me->memberships()
        ->where('status', 'active')
        ->where('end_date', '>=', now()->toDateString())
        ->where('can_view_contacts', true)
        ->exists();
@endphp

<div class="space-y-6">
    <div class="flex flex-col md:flex-row gap-6 items-center border-b pb-6">
        <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-primary flex-shrink-0 bg-gray-50">
            @if($profile->profile_photo)
                @php
                    $isPublicPhoto = str_starts_with($profile->profile_photo, 'imports/') || str_starts_with($profile->profile_photo, 'uploads/');
                    $photoSrc = $isPublicPhoto ? asset($profile->profile_photo) : route('image.serve', ['file' => $profile->profile_photo]);
                @endphp
                <img src="{{ $photoSrc }}" alt="Photo" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-slate-100 flex items-center justify-center font-bold text-3xl text-slate-400">
                    {{ substr($profile->full_name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="text-center md:text-left flex-grow">
            <h3 class="text-2xl font-black text-gray-900">{{ $profile->full_name }}</h3>
            <p class="text-sm font-semibold text-primary mt-1 font-mono">ID: {{ $profile->profile_id }}</p>
            <p class="text-xs text-slate-400 mt-0.5 font-medium">Subcast: {{ $profile->subcast ?? 'N/A' }} | Gotra: {{ $profile->gotra ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        
        <!-- Personal -->
        <div class="space-y-3">
            <h4 class="font-bold text-gray-800 uppercase text-xs tracking-wider border-l-2 border-primary pl-2">Personal info</h4>
            <div class="space-y-1 bg-slate-50 p-3 rounded-xl border border-gray-100">
                <div><span class="text-gray-400 font-semibold">Age:</span> <span class="font-bold text-gray-800">{{ $profile->birth_date ? $profile->birth_date->age . ' Years' : 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Height:</span> <span class="font-bold text-gray-800">{{ $profile->height ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Marital Status:</span> <span class="font-bold text-gray-800">{{ $profile->marital_status ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Native Place:</span> <span class="font-bold text-gray-800">{{ $profile->native_place ?? 'N/A' }}</span></div>
            </div>
        </div>

        <!-- Profession -->
        <div class="space-y-3">
            <h4 class="font-bold text-gray-800 uppercase text-xs tracking-wider border-l-2 border-primary pl-2">Professional details</h4>
            <div class="space-y-1 bg-slate-50 p-3 rounded-xl border border-gray-100">
                <div><span class="text-gray-400 font-semibold">Education:</span> <span class="font-bold text-gray-800">{{ $profile->higher_education ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Occupation:</span> <span class="font-bold text-gray-800">{{ $profile->occupation ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">{{ ($profile->income_type ?? 'Yearly') === 'Monthly' ? 'Monthly Income:' : 'Yearly Income:' }}</span> <span class="font-bold text-gray-800">{{ format_indian_currency($profile->monthly_income) }}</span></div>
            </div>
        </div>

        <!-- Family details -->
        <div class="space-y-3 md:col-span-2">
            <h4 class="font-bold text-gray-800 uppercase text-xs tracking-wider border-l-2 border-primary pl-2">Family background</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-gray-100">
                <div><span class="text-gray-400 font-semibold">Father's Name:</span> <span class="font-bold text-gray-800">{{ $profile->father_name ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Mother's Name:</span> <span class="font-bold text-gray-800">{{ $profile->mother_name ?? 'N/A' }}</span></div>
                <div><span class="text-gray-400 font-semibold">Brothers:</span> <span class="font-bold text-gray-800">{{ $profile->brothers ?? 0 }} ({{ $profile->brothers_married ?? 0 }} Married)</span></div>
                <div><span class="text-gray-400 font-semibold">Sisters:</span> <span class="font-bold text-gray-800">{{ $profile->sisters ?? 0 }} ({{ $profile->sisters_married ?? 0 }} Married)</span></div>
            </div>
        </div>

        <!-- Custom Fields EAV -->
        @if($customData->count() > 0)
        <div class="space-y-3 md:col-span-2">
            <h4 class="font-bold text-gray-800 uppercase text-xs tracking-wider border-l-2 border-primary pl-2">Other Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-gray-100">
                @foreach($customData as $data)
                    <div>
                        <span class="text-gray-400 font-semibold">{{ $data->field->field_label }}:</span>
                        <span class="font-bold text-gray-800 block mt-0.5">{{ $data->field_value ?? 'N/A' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="space-y-3 md:col-span-2">
            <h4 class="font-bold text-gray-800 uppercase text-xs tracking-wider border-l-2 border-primary pl-2">Contact details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-emerald-50/50 p-4 rounded-xl border border-emerald-200">
                <div><span class="text-emerald-800 font-bold"><i class="fa-solid fa-phone mr-1"></i> Mobile:</span> <span class="font-black text-slate-900">{{ $profile->mobile }}</span></div>
                <div><span class="text-emerald-800 font-bold"><i class="fa-solid fa-envelope mr-1"></i> Email:</span> <span class="font-black text-slate-900">{{ $profile->email }}</span></div>
            </div>
        </div>

    </div>
</div>

<!-- HIDDEN PDF CARD TEMPLATE FOR MODAL DOWNLOAD -->
@php
    $gc = (strtolower($profile->gender ?? '') === 'female') ? 'F' : 'M';
    $rawNum = preg_replace('/[^0-9]/', '', $profile->profile_id ?: $profile->id);
    $pnum = !empty($rawNum) ? $rawNum : $profile->id;
    $badgeCode = $gc . '-' . $pnum;
    
    // Server-side base64 encode the profile photo to support CORS-free HTML2PDF generation
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
<div id="pdf-content" style="display:none; font-family:Arial, sans-serif; width:720px; background:#ffffff; padding:0; margin:0 auto; color:#111; box-sizing:border-box;">
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
            <strong style="color:#000; width:130px; display:inline-block;">{{ ($profile->income_type ?? 'Yearly') === 'Monthly' ? 'Monthly Income' : 'Yearly Income' }}</strong> : &nbsp;{{ format_indian_currency($profile->monthly_income) }}
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
