@extends('layouts.admin')

@section('title', 'Audit Profile - ' . $member->full_name)
@section('header_title', 'Audit Profile: ' . $member->full_name)

@section('content')

{{-- Back + Edit header bar --}}
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.members.index') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition text-sm flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Back to Members
    </a>
    <a href="{{ route('admin.members.edit', $member->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition duration-150 shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-pen-to-square"></i> Edit Candidate Profile
    </a>
</div>

<div class="flex flex-col lg:flex-row gap-8">

    {{-- ===== LEFT SIDEBAR (1/3) ===== --}}
    <div class="w-full lg:w-1/3 space-y-8">

        {{-- Profile Card (photo + quick stats) --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Coloured top banner --}}
            <div class="bg-indigo-600 h-32 relative"></div>
            <div class="px-6 pb-6 relative">
                {{-- Avatar --}}
                <div class="w-28 h-28 rounded-full border-4 border-white bg-gray-100 mx-auto -mt-14 flex items-center justify-center shadow-md overflow-hidden relative z-10">
                    @if($member->profile_photo)
                        <img src="/image?file={{ urlencode($member->profile_photo) }}"
                             alt="Profile Photo"
                             class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->full_name) }}&background=random"
                             alt="Profile Photo"
                             class="w-full h-full object-cover">
                    @endif
                </div>

                {{-- Name / Occupation / Location --}}
                <div class="text-center mt-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $member->full_name ?? 'N/A' }}</h2>
                    <p class="text-gray-500 font-medium">{{ $member->occupation ?? 'N/A' }}</p>
                    <div class="flex items-center justify-center gap-2 mt-2 text-gray-600 text-sm">
                        <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        <span>{{ $member->native_place ?? 'N/A' }}</span>
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                {{-- Quick stats --}}
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-birthday-cake w-4 text-center text-gray-400"></i> Age / Height
                        </span>
                        @php
                            $age = $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->age : 'N/A';
                        @endphp
                        <span class="font-medium text-gray-900">{{ $age }} Yrs / {{ $member->height ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-venus-mars w-4 text-center text-gray-400"></i> Gender
                        </span>
                        <span class="font-medium text-gray-900">{{ $member->gender ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-users w-4 text-center text-gray-400"></i> Gotra
                        </span>
                        <span class="font-medium text-gray-900">{{ $member->gotra ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-id-card w-4 text-center text-gray-400"></i> Profile ID
                        </span>
                        <span class="font-medium text-gray-900">{{ $member->profile_id ?? 'Not Assigned' }}</span>
                    </div>
                    @if($member->approval_date && $member->expiry_date)
                    <div class="flex justify-between items-center text-sm border-t pt-2 mt-2">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="far fa-calendar-check w-4 text-center text-gray-400"></i> Approved Date
                        </span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->approval_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="far fa-calendar-times w-4 text-center text-gray-400"></i> End Date
                        </span>
                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->expiry_date)->format('d M Y') }}</span>
                    </div>
                    <div class="mt-2 text-xs text-indigo-600 font-bold text-center bg-indigo-50 p-2 rounded-lg border border-indigo-100">
                        Profile approval is valid for 12 months.
                    </div>
                    @endif
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-ring w-4 text-center text-gray-400"></i> Marital Status
                        </span>
                        <span class="font-medium text-gray-900">{{ $member->marital_status ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Details Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-address-book text-indigo-600"></i> Contact Details
            </h3>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Mobile Number</p>
                        <p class="font-medium text-gray-900">{{ $member->mobile ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Email Address</p>
                        <p class="font-medium text-gray-900">{{ $member->email ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Native Place</p>
                        <p class="font-medium text-gray-900 text-sm leading-snug">{!! nl2br(e($member->native_place ?? 'N/A')) !!}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Current Address</p>
                        <p class="font-medium text-gray-900 text-sm leading-snug">{!! nl2br(e($member->current_address ?? 'N/A')) !!}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Permanent Address</p>
                        <p class="font-medium text-gray-900 text-sm leading-snug">
                            {!! nl2br(e($member->permanent_address ?? 'N/A')) !!}
                            @if(!empty($member->pin_code)) - {{ $member->pin_code }} @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ADMIN-ONLY: Profile Status Management ===== --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-shield-alt text-indigo-600"></i> Profile Status
            </h3>

            {{-- Current State badge --}}
            <div class="mb-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Current State</p>
                <span class="px-3 py-1 rounded-full text-sm font-bold inline-block
                    @if($member->status === 'approved') bg-green-100 text-green-800
                    @elseif($member->status === 'pending') bg-orange-100 text-orange-800
                    @elseif($member->status === 'blocked') bg-red-100 text-red-800
                    @elseif($member->status === 'deleted') bg-rose-100 text-rose-800 border border-rose-200
                    @else bg-slate-100 text-slate-800 @endif">
                    @if($member->status === 'deleted') Account Deleted @else {{ ucfirst($member->status) }} @endif
                </span>
            </div>

            {{-- Lifecycle Metrics --}}
            <div class="grid grid-cols-2 gap-3 mb-6 pt-3 border-t border-gray-100">
                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 text-center">
                    <span class="block text-[11px] font-bold text-blue-600 uppercase">Registrations</span>
                    <span class="text-xl font-extrabold text-blue-900">{{ $member->registration_count ?? 1 }}</span>
                </div>
                <div class="bg-rose-50 p-3 rounded-xl border border-rose-100 text-center">
                    <span class="block text-[11px] font-bold text-rose-600 uppercase">Deletions</span>
                    <span class="text-xl font-extrabold text-rose-900">{{ $member->deletion_count ?? 0 }}</span>
                </div>
            </div>

            {{-- Status Change Form --}}
            <form action="{{ route('admin.members.status', $member->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="status" class="block text-xs font-bold text-gray-500 uppercase mb-1">Modify Profile Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="account_pending"  {{ $member->status === 'account_pending'  ? 'selected' : '' }}>Account Pending</option>
                        <option value="account_approved" {{ $member->status === 'account_approved' ? 'selected' : '' }}>Account Approved</option>
                        <option value="pending"          {{ $member->status === 'pending'          ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved"         {{ $member->status === 'approved'         ? 'selected' : '' }}>Approved (Active)</option>
                        <option value="rejected"         {{ $member->status === 'rejected'         ? 'selected' : '' }}>Rejected</option>
                        <option value="blocked"          {{ $member->status === 'blocked'          ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition duration-150">
                    Apply Status Change
                </button>
            </form>
        </div>

        {{-- ===== ADMIN-ONLY: Payment Verification ===== --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-indigo-600"></i> Payment Verification
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Payment Status</p>
                        <p class="font-bold uppercase
                            @if($member->payment_status === 'approved') text-green-600
                            @elseif($member->payment_status === 'pending') text-orange-500
                            @else text-red-500 @endif">
                            {{ $member->payment_status ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                @if($member->payment_transaction_id)
                <div class="flex items-start gap-3">
                    <div class="bg-blue-50 p-2.5 rounded-lg text-indigo-600 mt-0.5">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Transaction ID</p>
                        <p class="font-bold text-gray-800 font-mono text-xs">{{ $member->payment_transaction_id }}</p>
                    </div>
                </div>
                @endif

                @if($member->payment_screenshot)
                <div class="pt-2">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Screenshot Receipt</p>
                    <a href="/image?file={{ urlencode($member->payment_screenshot) }}" target="_blank">
                        <img src="/image?file={{ urlencode($member->payment_screenshot) }}"
                             alt="Receipt"
                             class="w-full h-36 object-cover rounded-xl border hover:opacity-90 transition shadow-sm">
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end left sidebar --}}

    {{-- ===== RIGHT CONTENT (2/3) ===== --}}
    <div class="w-full lg:w-2/3 space-y-8">

        {{-- Personal & Physical Details --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Personal Details
                </h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date of Birth</p>
                    <p class="font-medium text-gray-900">{{ $member->birth_date ? $member->birth_date->format('d M Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Time of Birth</p>
                    <p class="font-medium text-gray-900">{{ format_birth_time($member->birth_time) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Place of Birth</p>
                    <p class="font-medium text-gray-900">{{ $member->birth_place ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Manglik</p>
                    <p class="font-medium text-gray-900">{{ $member->manglik ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Weight</p>
                    <p class="font-medium text-gray-900">{{ format_weight($member->weight) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Marital Status</p>
                    <p class="font-medium text-gray-900">{{ $member->marital_status ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Self Gotra</p>
                    <p class="font-medium text-gray-900">{{ $member->gotra ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Mama Gotra</p>
                    <p class="font-medium text-gray-900">{{ $member->mama_gotra ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Handicapped</p>
                    <p class="font-medium text-gray-900">{{ $member->handicapped ?? 'No' }}</p>
                </div>
            </div>
        </div>

        {{-- Education & Career --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-graduation-cap"></i> Education &amp; Career
                </h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                <div class="sm:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Highest Education</p>
                    <p class="font-medium text-gray-900 whitespace-pre-wrap">{{ $member->higher_education ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Occupation (व्यवसाय)</p>
                    <p class="font-medium text-gray-900">
                        {{ !empty($member->occupation) ? $member->occupation : 'N/A' }}
                        @if(!empty($member->designation) || !empty($member->company_name))
                            <span class="text-xs text-gray-500 block font-normal mt-0.5">
                                {{ implode(' at ', array_filter([$member->designation, $member->company_name])) }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ ($member->income_type ?? 'Yearly') === 'Monthly' ? 'Monthly Income' : 'Yearly Income' }}</p>
                    <p class="font-medium text-green-700">
                        {{ format_indian_currency($member->monthly_income) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Family Details --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-home"></i> Family Details
                </h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Father's Name</p>
                    <p class="font-medium text-gray-900">{{ $member->father_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Father's Occupation</p>
                    <p class="font-medium text-gray-900">{{ $member->father_occupation ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Mother's Name</p>
                    <p class="font-medium text-gray-900">{{ $member->mother_name ?? 'N/A' }}</p>
                </div>

                {{-- Siblings counter --}}
                <div class="sm:col-span-2 mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Siblings Info</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-5 rounded-xl border border-gray-100">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $member->brothers_married ?? '0' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Brothers<br>Married</p>
                        </div>
                        <div class="text-center border-l border-gray-200">
                            <p class="text-2xl font-bold text-indigo-600">{{ $member->brothers_unmarried ?? '0' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Brothers<br>Unmarried</p>
                        </div>
                        <div class="text-center md:border-l border-gray-200 pt-4 md:pt-0 border-t md:border-t-0 mt-2 md:mt-0">
                            <p class="text-2xl font-bold text-indigo-600">{{ $member->sisters_married ?? '0' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Sisters<br>Married</p>
                        </div>
                        <div class="text-center border-l border-gray-200 pt-4 md:pt-0 border-t md:border-t-0 mt-2 md:mt-0">
                            <p class="text-2xl font-bold text-indigo-600">{{ $member->sisters_unmarried ?? '0' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Sisters<br>Unmarried</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mandir Verification & References --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-gopuram"></i> Mandir Verification Details
                </h3>
                <span class="bg-{{ $member->status === 'approved' ? 'green' : 'yellow' }}-100 text-{{ $member->status === 'approved' ? 'green' : 'yellow' }}-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                    <i class="fas fa-{{ $member->status === 'approved' ? 'check-circle' : 'clock' }}"></i>
                    {{ ucfirst($member->status) }}
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Subcast (उपजाति)</p>
                    <p class="font-medium text-gray-900">{{ $member->subcast ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Registered Mandir (मंदिर)</p>
                    <p class="font-medium text-gray-900">
                        {{ $member->mandir_name ?? ($member->mandir ?? 'N/A') }}
                        @if(!empty($member->custom_mandir)) - {{ $member->custom_mandir }} @endif
                    </p>
                </div>
            </div>

            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Reference Persons</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-indigo-600 uppercase mb-2">Reference Person 1</p>
                    <div class="space-y-1 text-sm text-gray-700">
                        <p><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900">{{ $member->ref1_name ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-500">Mobile:</span> <span class="font-medium text-gray-900">{{ $member->ref1_mobile ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-500">Relation:</span> <span class="font-medium text-gray-900">{{ $member->ref1_relation ?? 'N/A' }}</span></p>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-bold text-indigo-600 uppercase mb-2">Reference Person 2</p>
                    <div class="space-y-1 text-sm text-gray-700">
                        <p><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900">{{ $member->ref2_name ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-500">Mobile:</span> <span class="font-medium text-gray-900">{{ $member->ref2_mobile ?? 'N/A' }}</span></p>
                        <p><span class="text-gray-500">Relation:</span> <span class="font-medium text-gray-900">{{ $member->ref2_relation ?? 'N/A' }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Partner Preferences --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-heart"></i> Partner Preferences
                </h3>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Partner Preference Description</p>
                <p class="font-medium text-gray-900">{!! nl2br(e($member->partner_preference ?? 'N/A')) !!}</p>
            </div>
        </div>

        {{-- Additional Custom Details (EAV) --}}
        @if($customData->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-sliders"></i> Additional Custom Details
                </h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-6">
                @foreach($customData as $data)
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ $data->field->field_label }}</p>
                    <p class="font-medium text-gray-900">{{ $data->field_value ?? 'N/A' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Verification Files & Media --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-bold text-indigo-600 flex items-center gap-2">
                    <i class="fas fa-file-shield"></i> Verification Files &amp; Media
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Profile Photo --}}
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Profile Photo</p>
                    @if($member->profile_photo)
                        <a href="/image?file={{ urlencode($member->profile_photo) }}" target="_blank">
                            <img src="/image?file={{ urlencode($member->profile_photo) }}"
                                 alt="Profile"
                                 class="w-full h-48 object-cover rounded-xl border shadow-sm hover:opacity-90 transition">
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-xl flex items-center justify-center text-slate-400 text-sm">
                            No photo uploaded
                        </div>
                    @endif
                </div>

                {{-- Family Photo --}}
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Family Photo</p>
                    @if($member->family_photo)
                        <a href="/image?file={{ urlencode($member->family_photo) }}" target="_blank">
                            <img src="/image?file={{ urlencode($member->family_photo) }}"
                                 alt="Family"
                                 class="w-full h-48 object-cover rounded-xl border shadow-sm hover:opacity-90 transition">
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-xl flex items-center justify-center text-slate-400 text-sm">
                            No photo uploaded
                        </div>
                    @endif
                </div>

                {{-- ID Proof --}}
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">ID Proof Document</p>
                    @if($member->id_proof_path)
                        <a href="/image?file={{ urlencode($member->id_proof_path) }}"
                           target="_blank"
                           class="w-full h-48 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col items-center justify-center text-indigo-600 hover:bg-indigo-100 transition shadow-sm">
                            <i class="fa-solid fa-id-card text-4xl mb-2"></i>
                            <span class="text-xs font-bold uppercase">{{ $member->id_proof_type ?? 'View ID Proof' }}</span>
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-xl flex items-center justify-center text-slate-400 text-sm">
                            No ID proof uploaded
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>{{-- end right content --}}

</div>{{-- end flex row --}}

@endsection
