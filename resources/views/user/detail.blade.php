@extends('layouts.app')

@section('title', $profile->full_name . ' - Profile Details')

@section('content')
@php
    $me = Auth::user();
    $activePlan = $me->memberships()
        ->where('status', 'active')
        ->where('end_date', '>=', now()->toDateString())
        ->where('can_view_contacts', true)
        ->exists();

    $backUrl = session('last_search_url') ?? route('profiles');

    $age = 'N/A';
    if (!empty($profile->birth_date)) {
        $age = $profile->birth_date->age . ' Years';
    }

    // Check if current user has liked this profile
    $isLiked = \App\Models\UserLike::where('user_id', $me->id)
        ->where('liked_user_id', $profile->id)
        ->exists();
@endphp

<section class="py-12 bg-light min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">

        {{-- Breadcrumb & Action Buttons --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between" data-aos="fade-up">
            <nav class="text-xs text-gray-500 mb-4 md:mb-0 flex items-center gap-1.5 font-medium">
                <a href="{{ route('home') }}" class="hover:text-primary transition">Home</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ $backUrl }}" class="hover:text-primary transition font-bold">Search Directory</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-dark">{{ $profile->full_name }} [MID: {{ $profile->profile_id }}]</span>
            </nav>

            <div class="flex gap-2.5">
                {{-- Like / Unlike Button --}}
                <button id="like-btn"
                    data-id="{{ $profile->id }}"
                    class="border font-extrabold text-xs px-4 py-2 rounded-lg shadow-sm flex items-center gap-1.5 transition
                           {{ $isLiked ? 'bg-primary text-white border-primary' : 'border-primary text-primary hover:bg-primary hover:text-white' }}"
                    title="{{ $isLiked ? 'Unlike' : 'Like' }}">
                    <i class="{{ $isLiked ? 'fas' : 'far' }} fa-heart"></i>
                    {{ $isLiked ? 'Liked' : 'Like Profile' }}
                </button>

                <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-4 py-2 rounded-lg shadow-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-file-pdf"></i> Download PDF Biodata
                </button>
            </div>
        </div>

        {{-- Main Profile Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8" data-aos="fade-up">
            <div class="flex flex-col md:flex-row">
                {{-- Profile Image --}}
                <div class="w-full md:w-1/3 relative h-96 md:h-auto bg-slate-50 flex items-center justify-center overflow-hidden border-r">
                    @if($profile->profile_photo)
                        <img src="{{ route('image.serve', ['file' => $profile->profile_photo]) }}" alt="Photo of {{ $profile->full_name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-100 font-bold text-6xl text-slate-300">
                            {{ substr($profile->full_name, 0, 1) }}
                        </div>
                    @endif
                </div>

                {{-- Core Details Summary --}}
                <div class="w-full md:w-2/3 p-6 md:p-8 flex flex-col justify-start">
                    <h2 class="text-2xl md:text-3xl font-black text-gray-900 leading-tight">
                        {{ $profile->full_name }}
                        <span class="text-xs md:text-sm font-bold text-primary block mt-1 font-mono uppercase tracking-wider">ID: {{ $profile->profile_id }}</span>
                    </h2>
                    <p class="text-xs text-gray-500 font-semibold mt-1"><i class="fas fa-map-marker-alt text-primary mr-1"></i> Native: {{ $profile->native_place ?? 'N/A' }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold text-gray-600 mt-6 bg-slate-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2"><i class="far fa-calendar-alt text-primary w-5 text-center"></i> {{ $age }}, {{ $profile->height ?? 'N/A' }}</div>
                        <div class="flex items-center gap-2"><i class="fas fa-graduation-cap text-primary w-5 text-center"></i> {{ $profile->higher_education ?? 'N/A' }}</div>
                        <div class="flex items-center gap-2"><i class="fas fa-briefcase text-primary w-5 text-center"></i> {{ $profile->occupation ?? 'N/A' }}</div>
                        <div class="flex items-center gap-2"><i class="fas fa-ring text-primary w-5 text-center"></i> {{ $profile->marital_status ?? 'N/A' }}</div>
                        <div class="flex items-center gap-2"><i class="fas fa-language text-primary w-5 text-center"></i> {{ $profile->languages ?? 'N/A' }}</div>
                        <div class="flex items-center gap-2"><i class="fas fa-om text-primary w-5 text-center"></i> Subcaste: {{ $profile->subcast ?? 'N/A' }}</div>
                    </div>

                    <div class="mt-8 space-y-3">
                        <h4 class="font-extrabold text-xs text-dark uppercase tracking-wider border-l-2 border-primary pl-2">Hobbies & Interests</h4>
                        <p class="text-gray-600 text-xs leading-relaxed">{{ $profile->hobbies ?? 'Not specified' }}</p>
                        <p class="text-gray-600 text-xs leading-relaxed"><strong class="text-gray-700">Physical Challenges:</strong> {{ $profile->handicapped ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Sections Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm" data-aos="fade-up">

            {{-- Personal Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Personal Information</h3>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <div class="text-gray-400 font-semibold">Date of Birth</div>
                    <div class="font-bold text-gray-800">{{ $profile->birth_date ? $profile->birth_date->format('d M Y') : 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Time of Birth</div>
                    <div class="font-bold text-gray-800">{{ format_birth_time($profile->birth_time) }}</div>
                    <div class="text-gray-400 font-semibold">Birth Place</div>
                    <div class="font-bold text-gray-800">{{ $profile->birth_place ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Gender</div>
                    <div class="font-bold text-gray-800">{{ $profile->gender ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Weight</div>
                    <div class="font-bold text-gray-800">{{ format_weight($profile->weight ?? ($profile->weight_kg ? $profile->weight_kg . ' kg' : null)) }}</div>
                    <div class="text-gray-400 font-semibold">Marital Status</div>
                    <div class="font-bold text-gray-800">{{ $profile->marital_status ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Disability</div>
                    <div class="font-bold text-gray-800">{{ $profile->handicapped ?? 'None' }}</div>
                    <div class="text-gray-400 font-semibold">Native Place</div>
                    <div class="font-bold text-gray-800">{{ $profile->native_place ?? 'N/A' }}</div>
                </div>
            </div>

            {{-- Religious & Astro --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Religious & Astro Background</h3>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <div class="text-gray-400 font-semibold">Subcaste</div>
                    <div class="font-bold text-gray-800">{{ $profile->subcast ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Self Gotra</div>
                    <div class="font-bold text-gray-800">{{ $profile->gotra ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Maternal Gotra (Mama)</div>
                    <div class="font-bold text-gray-800">{{ $profile->mama_gotra ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Manglik Status</div>
                    <div class="font-bold {{ strtolower($profile->manglik ?? '') === 'no' ? 'text-green-600' : 'text-red-600' }}">
                        {{ ucfirst($profile->manglik ?? 'N/A') }}
                    </div>
                    <div class="text-gray-400 font-semibold">Languages Known</div>
                    <div class="font-bold text-gray-800">{{ $profile->languages ?? 'N/A' }}</div>
                </div>
            </div>

            {{-- Education & Career --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Education & Career</h3>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <div class="text-gray-400 font-semibold">Highest Education</div>
                    <div class="font-bold text-gray-800">{{ $profile->higher_education ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Occupation</div>
                    <div class="font-bold text-gray-800">{{ $profile->occupation ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Company / Firm</div>
                    <div class="font-bold text-gray-800">{{ $profile->company_name ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Designation</div>
                    <div class="font-bold text-gray-800">{{ $profile->designation ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Monthly Income</div>
                    <div class="font-bold text-gray-800">{{ $profile->monthly_income ? '₹ ' . number_format($profile->monthly_income) : 'N/A' }}</div>
                </div>
            </div>

            {{-- Family Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Family Details</h3>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <div class="text-gray-400 font-semibold">Father's Name</div>
                    <div class="font-bold text-gray-800">{{ $profile->father_name ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Father's Occupation</div>
                    <div class="font-bold text-gray-800">{{ $profile->father_occupation ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Mother's Name</div>
                    <div class="font-bold text-gray-800">{{ $profile->mother_name ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Mother's Occupation</div>
                    <div class="font-bold text-gray-800">{{ $profile->mother_occupation ?? 'N/A' }}</div>
                    <div class="text-gray-400 font-semibold">Brothers</div>
                    <div class="font-bold text-gray-800">
                        {{ $profile->brothers ?? 0 }}
                        (Married: {{ $profile->brothers_married ?? 0 }}, Unmarried: {{ $profile->brothers_unmarried ?? 0 }})
                    </div>
                    <div class="text-gray-400 font-semibold">Sisters</div>
                    <div class="font-bold text-gray-800">
                        {{ $profile->sisters ?? 0 }}
                        (Married: {{ $profile->sisters_married ?? 0 }}, Unmarried: {{ $profile->sisters_unmarried ?? 0 }})
                    </div>
                </div>
            </div>

            {{-- References & Community --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 md:col-span-2">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">References & Community</h3>

                <div class="mb-3">
                    <span class="text-gray-400 font-semibold text-xs">Mandir / Community</span>
                    <p class="font-bold text-gray-800 text-xs mt-1">{{ $profile->mandir_name ?? ($profile->mandir ?? 'N/A') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Reference 1 --}}
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <h4 class="font-bold text-gray-700 text-xs mb-3 border-b border-gray-200 pb-2">Reference 1</h4>
                        <div class="grid grid-cols-2 gap-y-2 text-xs">
                            <div class="text-gray-400 font-semibold">Name</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref1_name ?? 'N/A' }}</div>
                            <div class="text-gray-400 font-semibold">Mobile</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref1_mobile ?? 'N/A' }}</div>
                            <div class="text-gray-400 font-semibold">Relation</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref1_relation ?? 'N/A' }}</div>
                        </div>
                    </div>

                    {{-- Reference 2 --}}
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <h4 class="font-bold text-gray-700 text-xs mb-3 border-b border-gray-200 pb-2">Reference 2</h4>
                        <div class="grid grid-cols-2 gap-y-2 text-xs">
                            <div class="text-gray-400 font-semibold">Name</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref2_name ?? 'N/A' }}</div>
                            <div class="text-gray-400 font-semibold">Mobile</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref2_mobile ?? 'N/A' }}</div>
                            <div class="text-gray-400 font-semibold">Relation</div>
                            <div class="font-bold text-gray-800">{{ $profile->ref2_relation ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Custom Fields (EAV) --}}
            @if($customData->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 md:col-span-2">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Additional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @foreach($customData as $data)
                        <div class="grid grid-cols-2">
                            <div class="text-gray-400 font-semibold">{{ $data->field->field_label }}</div>
                            <div class="font-bold text-gray-800">{{ $data->field_value ?? 'N/A' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Contact Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 md:col-span-2">
                <h3 class="font-extrabold text-dark text-xs uppercase tracking-wider border-l-2 border-primary pl-2">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-emerald-50/50 p-4 rounded-xl border border-emerald-200 text-xs">
                        <div><span class="text-emerald-800 font-bold"><i class="fa-solid fa-phone mr-1"></i> Mobile:</span> <span class="font-black text-slate-900 ml-1">{{ $profile->mobile }}</span></div>
                        <div><span class="text-emerald-800 font-bold"><i class="fa-solid fa-envelope mr-1"></i> Email:</span> <span class="font-black text-slate-900 ml-1">{{ $profile->email }}</span></div>
                        <div class="md:col-span-2 mt-1"><span class="text-emerald-800 font-bold"><i class="fa-solid fa-map-location mr-1"></i> Current Address:</span> <span class="font-black text-slate-900 ml-1">{{ $profile->current_address ?? 'N/A' }}</span></div>
                        <div class="md:col-span-2"><span class="text-emerald-800 font-bold"><i class="fa-solid fa-home mr-1"></i> Permanent Address:</span> <span class="font-black text-slate-900 ml-1">{{ $profile->permanent_address ?? 'N/A' }} {{ $profile->pin_code ?? '' }}</span></div>
                    </div>
            </div>

            {{-- Partner Preferences --}}
            <div class="bg-slate-50 rounded-xl border p-6 space-y-3 md:col-span-2">
                <h3 class="font-extrabold text-primary text-xs uppercase tracking-wider"><i class="fas fa-heart mr-1"></i> Partner Preferences</h3>
                <p class="text-gray-600 text-xs leading-relaxed italic">
                    "{{ $profile->partner_preference ?? 'Not specified by the candidate.' }}"
                </p>
            </div>

        </div>
    </div>
</section>

{{-- HIDDEN PDF CARD TEMPLATE --}}
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    const element = document.getElementById('pdf-content');
    if (!element) return;

    // Create temporary clone container at (0,0) behind page layers to avoid capturing blank offset
    const cloneContainer = document.createElement('div');
    cloneContainer.style.position = 'fixed';
    cloneContainer.style.top = '0';
    cloneContainer.style.left = '0';
    cloneContainer.style.zIndex = '-99999';
    cloneContainer.style.width = '720px';
    cloneContainer.style.background = '#ffffff';
    cloneContainer.style.opacity = '1';

    const clone = element.cloneNode(true);
    clone.style.display = 'block';
    cloneContainer.appendChild(clone);
    document.body.appendChild(cloneContainer);

    const pnum = "{{ $pnum }}";
    const filename = 'Profile_MID_' + pnum + '.pdf';
    const pdfUrl = "{{ route('profiles.pdf', $profile->id) }}";

    const opt = {
      margin:       [5, 5, 5, 5],
      filename:     filename,
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2, useCORS: true, allowTaint: true, logging: false, scrollX: 0, scrollY: 0 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    setTimeout(function() {
        if (typeof html2pdf !== 'undefined') {
            html2pdf().set(opt).from(clone).save().then(function() {
                if (cloneContainer.parentNode) {
                    cloneContainer.parentNode.removeChild(cloneContainer);
                }
            }).catch(function(err) {
                console.error('PDF download error:', err);
                if (cloneContainer.parentNode) {
                    cloneContainer.parentNode.removeChild(cloneContainer);
                }
                window.open(pdfUrl, '_blank');
            });
        } else {
            if (cloneContainer.parentNode) {
                cloneContainer.parentNode.removeChild(cloneContainer);
            }
            window.open(pdfUrl, '_blank');
        }
    }, 200);
}

// Like / Unlike Button
const likeBtn = document.getElementById('like-btn');
if (likeBtn) {
    likeBtn.addEventListener('click', function() {
        const profileId = this.getAttribute('data-id');
        const isLiked = this.classList.contains('bg-primary');

        fetch('{{ route("profiles.like", ":id") }}'.replace(':id', profileId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ action: isLiked ? 'unlike' : 'like' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.liked) {
                    this.classList.add('bg-primary', 'text-white', 'border-primary');
                    this.classList.remove('text-primary');
                    this.setAttribute('title', 'Unlike');
                    this.innerHTML = '<i class="fas fa-heart"></i> Liked';
                } else {
                    this.classList.remove('bg-primary', 'text-white');
                    this.classList.add('text-primary');
                    this.setAttribute('title', 'Like');
                    this.innerHTML = '<i class="far fa-heart"></i> Like Profile';
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', data.message || 'Something went wrong', 'error');
                }
            }
        })
        .catch(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'Network error. Please try again.', 'error');
            }
        });
    });
}
</script>
@endpush
@endsection
