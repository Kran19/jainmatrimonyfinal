@extends('layouts.admin')

@section('title', 'Audit Profile - ' . $member->full_name)
@section('header_title', 'Audit Profile: ' . $member->full_name)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('admin.members.index') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition text-sm flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Back to Members
    </a>
    <a href="{{ route('admin.members.edit', $member->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-sm transition duration-150 shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-pen-to-square"></i> Edit Candidate Profile
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Audit Details -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Section 1: Basic & Personal Info -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                <i class="fa-solid fa-circle-info mr-2 text-indigo-500"></i>Basic & Personal Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="font-semibold text-gray-400">Full Name:</span> <span class="font-bold text-gray-900">{{ $member->full_name }}</span></div>
                <div><span class="font-semibold text-gray-400">Gender:</span> <span class="font-bold text-gray-900">{{ $member->gender ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Date of Birth:</span> <span class="font-bold text-gray-900">{{ $member->birth_date ? $member->birth_date->format('M d, Y') : 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Time of Birth:</span> <span class="font-bold text-gray-900">{{ format_birth_time($member->birth_time) }}</span></div>
                <div><span class="font-semibold text-gray-400">Birth Place:</span> <span class="font-bold text-gray-900">{{ $member->birth_place ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Marital Status:</span> <span class="font-bold text-gray-900">{{ $member->marital_status ?? 'Never Married' }}</span></div>
                <div><span class="font-semibold text-gray-400">Gotra:</span> <span class="font-bold text-gray-900">{{ $member->gotra ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Mama Gotra:</span> <span class="font-bold text-gray-900">{{ $member->mama_gotra ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Manglik Status:</span> <span class="font-bold text-gray-900">{{ $member->manglik ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Height:</span> <span class="font-bold text-gray-900">{{ $member->height ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Weight:</span> <span class="font-bold text-gray-900">{{ format_weight($member->weight) }}</span></div>
                <div><span class="font-semibold text-gray-400">Handicapped:</span> <span class="font-bold text-gray-900">{{ $member->handicapped ?? 'No' }}</span></div>
            </div>
        </div>

        <!-- Section 2: Education & Profession -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                <i class="fa-solid fa-graduation-cap mr-2 text-indigo-500"></i>Education & Professional Info
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="md:col-span-2"><span class="font-semibold text-gray-400">Higher Education:</span> <p class="font-semibold text-gray-950 mt-1 whitespace-pre-wrap">{{ $member->higher_education ?? 'N/A' }}</p></div>
                <div><span class="font-semibold text-gray-400">Occupation:</span> <span class="font-bold text-gray-900">{{ $member->occupation ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Company Name:</span> <span class="font-bold text-gray-900">{{ $member->company_name ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Designation:</span> <span class="font-bold text-gray-900">{{ $member->designation ?? 'N/A' }}</span></div>
                <div><span class="font-semibold text-gray-400">Monthly Income:</span> <span class="font-bold text-green-700">₹{{ $member->monthly_income ? number_format($member->monthly_income, 2) : 'N/A' }}</span></div>
            </div>
        </div>

        <!-- Section 3: Dynamic EAV Custom fields -->
        @if($customData->count() > 0)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                <i class="fa-solid fa-sliders mr-2 text-indigo-500"></i>Additional Custom Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach($customData as $data)
                    <div>
                        <span class="font-semibold text-gray-400">{{ $data->field->field_label }}:</span>
                        <span class="font-bold text-gray-900 block mt-0.5">{{ $data->field_value ?? 'N/A' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Section 4: Document Verification -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                <i class="fa-solid fa-file-shield mr-2 text-indigo-500"></i>Verification Files & Media
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile photo -->
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Profile Photo</span>
                    @if($member->profile_photo)
                        <a href="/image?file={{ urlencode($member->profile_photo) }}" target="_blank">
                            <img src="/image?file={{ urlencode($member->profile_photo) }}" alt="Profile" class="w-full h-48 object-cover rounded-lg border shadow-sm hover:opacity-90 transition">
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-lg flex items-center justify-center text-slate-400 text-sm">No photo uploaded</div>
                    @endif
                </div>

                <!-- Family photo -->
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Family Photo</span>
                    @if($member->family_photo)
                        <a href="/image?file={{ urlencode($member->family_photo) }}" target="_blank">
                            <img src="/image?file={{ urlencode($member->family_photo) }}" alt="Family" class="w-full h-48 object-cover rounded-lg border shadow-sm hover:opacity-90 transition">
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-lg flex items-center justify-center text-slate-400 text-sm">No photo uploaded</div>
                    @endif
                </div>

                <!-- ID proof -->
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-2">ID Proof Document</span>
                    @if($member->id_proof_path)
                        <a href="/image?file={{ urlencode($member->id_proof_path) }}" target="_blank" class="w-full h-48 bg-indigo-50 border border-indigo-200 rounded-lg flex flex-col items-center justify-center text-indigo-600 hover:bg-indigo-100 transition shadow-sm">
                            <i class="fa-solid fa-id-card text-4xl mb-2"></i>
                            <span class="text-xs font-bold uppercase">{{ $member->id_proof_type ?? 'View ID Proof' }}</span>
                        </a>
                    @else
                        <div class="w-full h-48 bg-slate-100 border border-dashed rounded-lg flex items-center justify-center text-slate-400 text-sm">No ID proof uploaded</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Status Audit Sidebar -->
    <div class="space-y-6">
        <!-- Status Panel -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Profile Status
            </h3>
            
            <div class="mb-4">
                <span class="block text-xs font-bold text-gray-400 uppercase">Current State</span>
                <div class="mt-1">
                    <span class="px-3 py-1 rounded-full text-sm font-bold inline-block
                        @if($member->status === 'approved') bg-green-100 text-green-800
                        @elseif($member->status === 'pending') bg-orange-100 text-orange-800
                        @elseif($member->status === 'blocked') bg-red-100 text-red-800
                        @elseif($member->status === 'deleted') bg-rose-100 text-rose-800 border border-rose-200
                        @else bg-slate-100 text-slate-800 @endif">
                        @if($member->status === 'deleted') Account Deleted @else {{ ucfirst($member->status) }} @endif
                    </span>
                </div>
            </div>

            <!-- Account Lifecycle Metrics -->
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

            <form action="{{ route('admin.members.status', $member->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="status" class="block text-xs font-bold text-gray-400 uppercase mb-1">Modify Profile Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="account_pending" {{ $member->status === 'account_pending' ? 'selected' : '' }}>Account Pending</option>
                        <option value="account_approved" {{ $member->status === 'account_approved' ? 'selected' : '' }}>Account Approved</option>
                        <option value="pending" {{ $member->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ $member->status === 'approved' ? 'selected' : '' }}>Approved (Active)</option>
                        <option value="rejected" {{ $member->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="blocked" {{ $member->status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition duration-150">
                    Apply Status Change
                </button>
            </form>
        </div>

        <!-- Payment Verification Reference -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Payment Verification
            </h3>
            <div class="space-y-2 text-sm">
                <div><span class="font-semibold text-gray-400">Payment Status:</span> 
                    <span class="font-bold uppercase 
                        @if($member->payment_status === 'approved') text-green-600
                        @elseif($member->payment_status === 'pending') text-orange-500
                        @else text-red-500 @endif">
                        {{ $member->payment_status }}
                    </span>
                </div>
                @if($member->payment_transaction_id)
                    <div><span class="font-semibold text-gray-400">Transaction ID:</span> <span class="font-bold text-gray-800 font-mono text-xs">{{ $member->payment_transaction_id }}</span></div>
                @endif
                
                @if($member->payment_screenshot)
                    <div class="pt-2">
                        <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Screenshot Receipt</span>
                        <a href="/image?file={{ urlencode($member->payment_screenshot) }}" target="_blank">
                            <img src="/image?file={{ urlencode($member->payment_screenshot) }}" alt="Receipt" class="w-full h-32 object-cover rounded-lg border hover:opacity-90 transition">
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
