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
                <img src="{{ route('image.serve', ['file' => $profile->profile_photo]) }}" alt="Photo" class="w-full h-full object-cover">
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
                <div><span class="text-gray-400 font-semibold">Monthly Income:</span> <span class="font-bold text-gray-800">₹{{ $profile->monthly_income ? number_format($profile->monthly_income, 2) : 'N/A' }}</span></div>
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
    $gc = (strtolower($profile->gender ?? '') === 'female') ? 'G' : 'B';
    $pnum = preg_replace('/[^0-9]/', '', $profile->profile_id ?: $profile->id);
    
    // Server-side base64 encode the profile photo to support CORS-free HTML2PDF generation
    $pdfPhoto = '';
    if (!empty($profile->profile_photo) && file_exists(public_path($profile->profile_photo))) {
        $mime = mime_content_type(public_path($profile->profile_photo));
        $b64 = base64_encode(file_get_contents(public_path($profile->profile_photo)));
        $pdfPhoto = 'data:' . $mime . ';base64,' . $b64;
    } else {
        $pdfPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($profile->full_name) . '&size=200&background=1a1a6e&color=fff';
    }
@endphp
<div id="pdf-content" style="display:none; font-family:Arial,sans-serif; width:680px; background:#fff; padding:0; margin:0; color:#333;">
  <table style="width:100%; border-collapse:collapse; border:3px solid #1a1a6e;">
    <tr>
      <td colspan="2" style="text-align:center; padding:8px 0; background:#fff; border-bottom:2px solid #1a1a6e;">
        <span style="display:inline-block; border:2px solid #1a1a6e; border-radius:3px; padding:2px 18px; font-size:15px; font-weight:bold; color:#1a1a6e; letter-spacing:1px;">{{ $gc }}-{{ $pnum }}</span>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="background:#1a1a6e; padding:10px 14px; border-bottom:2px solid #1a1a6e;">
        <span style="color:#fff; font-size:18px; font-weight:bold; text-transform:uppercase;">{{ $profile->full_name }}</span>
      </td>
    </tr>
    <tr>
      <!-- Left Info Details -->
      <td style="vertical-align:top; padding:10px 14px; font-size:12px; border-right:1px solid #ccc; width:62%; line-height:1.8;">
        <div><strong>Education</strong> &nbsp;: &nbsp;{{ $profile->higher_education ?? 'N/A' }}</div>
        <div><strong>Occu. / Firm</strong> &nbsp;: &nbsp;{{ $profile->occupation ?? 'N/A' }} @if(!empty($profile->company_name)) ({{ $profile->company_name }}) @endif</div>
        <div><strong>Designation</strong> &nbsp;: &nbsp;{{ $profile->designation ?? 'N/A' }}</div>
        <div><strong>Monthly Income</strong> &nbsp;: &nbsp;{{ $profile->monthly_income ? '₹' . number_format($profile->monthly_income) : 'N/A' }}</div>
        
        <div><strong>Mobile</strong> &nbsp;: &nbsp;{{ $profile->mobile }}</div>
        
        <div><strong>Hobbies</strong> &nbsp;: &nbsp;{{ $profile->hobbies ?? 'N/A' }}</div>
        <div><strong>Father's Name</strong> &nbsp;: &nbsp;{{ $profile->father_name ?? 'N/A' }} ({{ $profile->father_occupation ?? 'N/A' }})</div>
        <div><strong>Mother's Name</strong> &nbsp;: &nbsp;{{ $profile->mother_name ?? 'N/A' }} ({{ $profile->mother_occupation ?? 'N/A' }})</div>
        <div><strong>Brothers</strong> &nbsp;: &nbsp;{{ $profile->brothers ?? 0 }} (Married: {{ $profile->brothers_married ?? 0 }}, Unmarried: {{ $profile->brothers_unmarried ?? 0 }})</div>
        <div><strong>Sisters</strong> &nbsp;: &nbsp;{{ $profile->sisters ?? 0 }} (Married: {{ $profile->sisters_married ?? 0 }}, Unmarried: {{ $profile->sisters_unmarried ?? 0 }})</div>
        
        <div><strong>Current Address</strong> &nbsp;: &nbsp;{{ $profile->current_address ?? 'N/A' }}</div>
      </td>

      <!-- Right Photo Details -->
      <td style="vertical-align:top; padding:10px; font-size:12px; width:38%; line-height:1.8; text-align:center;">
        <div style="margin-bottom:8px;">
          <img src="{{ $pdfPhoto }}" style="width:150px; height:180px; object-fit:cover; border:2px solid #1a1a6e;">
        </div>
        <div style="text-align:left; padding-left:15px;">
            <div><strong>DOB</strong> &nbsp;: &nbsp;{{ $profile->birth_date ? $profile->birth_date->format('d-m-Y') : 'N/A' }}</div>
            <div><strong>B. Time</strong> &nbsp;: &nbsp;{{ $profile->birth_time ?? 'N/A' }}</div>
            <div><strong>B. Place</strong> &nbsp;: &nbsp;{{ $profile->birth_place ?? 'N/A' }}</div>
            <div><strong>Height</strong> &nbsp;: &nbsp;{{ $profile->height ?? 'N/A' }}</div>
            <div><strong>Weight</strong> &nbsp;: &nbsp;{{ $profile->weight ? $profile->weight . ' kg' : 'N/A' }}</div>
            <div><strong>Gotra</strong> &nbsp;: &nbsp;{{ $profile->gotra ?? 'N/A' }} (Mama: {{ $profile->mama_gotra ?? 'N/A' }})</div>
            <div><strong>Manglik</strong> &nbsp;: &nbsp;{{ $profile->manglik ?? 'No' }}</div>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border-top:2px solid #1a1a6e; padding:10px 14px; font-size:12px; line-height:1.7;">
        <div><strong>Partner Preferences</strong> &nbsp;: &nbsp;{{ $profile->partner_preference ?? 'Not specified' }}</div>
        @if($customData->count() > 0)
            @foreach($customData as $data)
                <div><strong>{{ $data->field->field_label }}</strong> &nbsp;: &nbsp;{{ $data->field_value ?? 'N/A' }}</div>
            @endforeach
        @endif
      </td>
    </tr>
  </table>
</div>
