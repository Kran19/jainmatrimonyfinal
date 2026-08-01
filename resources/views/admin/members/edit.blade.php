@extends('layouts.admin')

@section('title', 'Edit Profile - ' . $member->full_name)
@section('header_title', 'Edit Candidate Profile: ' . $member->full_name)

@section('content')
<form action="{{ route('admin.members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Top Action Bar -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <a href="{{ route('admin.members.show', $member->id) }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition text-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Audit Profile
        </a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition duration-150 shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 text-sm">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl mb-6 text-sm">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2-Column Main Form -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Account & Basic Details -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user text-indigo-500"></i> Basic & Personal Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Full Name *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Mobile Number (10 Digits Only) *</label>
                        <input type="text" name="mobile" value="{{ old('mobile', $member->mobile) }}" required
                               maxlength="10" minlength="10" pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-400 mt-1">Must be exactly 10 digits without spaces or country code.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Gender</label>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $member->gender) === 'Male' ? 'selected' : '' }}>Male (पुरुष)</option>
                            <option value="Female" {{ old('gender', $member->gender) === 'Female' ? 'selected' : '' }}>Female (महिला)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Date of Birth</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date ? $member->birth_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Time of Birth</label>
                        <input type="text" name="birth_time" value="{{ old('birth_time', format_birth_time($member->birth_time)) }}" placeholder="e.g. 02:30 PM"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Birth Place</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $member->birth_place) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Native Place (मूल स्थान)</label>
                        <input type="text" name="native_place" value="{{ old('native_place', $member->native_place) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Marital Status</label>
                        <select name="marital_status" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Never Married" {{ old('marital_status', $member->marital_status) === 'Never Married' ? 'selected' : '' }}>Never Married (अविवाहित)</option>
                            <option value="Divorced" {{ old('marital_status', $member->marital_status) === 'Divorced' ? 'selected' : '' }}>Divorced (तलाकशुदा)</option>
                            <option value="Widowed" {{ old('marital_status', $member->marital_status) === 'Widowed' ? 'selected' : '' }}>Widowed (विधवा/विधुर)</option>
                            <option value="Awaiting Divorce" {{ old('marital_status', $member->marital_status) === 'Awaiting Divorce' ? 'selected' : '' }}>Awaiting Divorce</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Gotra (गोत्र)</label>
                        <input type="text" name="gotra" value="{{ old('gotra', $member->gotra) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Mama Gotra (ननिहाल गोत्र)</label>
                        <input type="text" name="mama_gotra" value="{{ old('mama_gotra', $member->mama_gotra) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Manglik Status</label>
                        <select name="manglik" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="No" {{ old('manglik', $member->manglik) === 'No' ? 'selected' : '' }}>No (मांगलिक नहीं)</option>
                            <option value="Yes" {{ old('manglik', $member->manglik) === 'Yes' ? 'selected' : '' }}>Yes (मांगलिक है)</option>
                            <option value="Partial" {{ old('manglik', $member->manglik) === 'Partial' ? 'selected' : '' }}>Partial (आंशिक मांगलिक)</option>
                            <option value="Don't Know" {{ old('manglik', $member->manglik) === "Don't Know" ? 'selected' : '' }}>Don't Know</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Height</label>
                        <input type="text" name="height" value="{{ old('height', $member->height) }}" placeholder="e.g. 5 ft 8 in / 172 cm"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Weight</label>
                        <input type="text" name="weight" value="{{ old('weight', $member->weight) }}" placeholder="e.g. 65 kg"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Handicapped</label>
                        <select name="handicapped" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="No" {{ old('handicapped', $member->handicapped) === 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('handicapped', $member->handicapped) === 'Yes' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Education & Profession Details -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-500"></i> Education & Professional Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Higher Education</label>
                        <input type="text" name="higher_education" value="{{ old('higher_education', $member->higher_education) }}" placeholder="e.g. B.Tech / MBA / MBBS"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $member->occupation) }}" placeholder="e.g. Software Engineer / Business"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $member->company_name) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation', $member->designation) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Monthly Income (₹)</label>
                        <input type="text" name="monthly_income" value="{{ old('monthly_income', $member->monthly_income) }}" placeholder="e.g. 50000"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- 3. Family Details -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-500"></i> Family Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Father's Name</label>
                        <input type="text" name="father_name" value="{{ old('father_name', $member->father_name) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Father's Occupation</label>
                        <input type="text" name="father_occupation" value="{{ old('father_occupation', $member->father_occupation) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Father's Income (₹)</label>
                        <input type="text" name="father_income" value="{{ old('father_income', $member->father_income) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Mother's Name</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name', $member->mother_name) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Mother's Occupation</label>
                        <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $member->mother_occupation) }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Unmarried Brothers</label>
                        <input type="number" name="unmarried_brothers" value="{{ old('unmarried_brothers', $member->unmarried_brothers ?? 0) }}" min="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Married Brothers</label>
                        <input type="number" name="married_brothers" value="{{ old('married_brothers', $member->married_brothers ?? 0) }}" min="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Unmarried Sisters</label>
                        <input type="number" name="unmarried_sisters" value="{{ old('unmarried_sisters', $member->unmarried_sisters ?? 0) }}" min="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Married Sisters</label>
                        <input type="number" name="married_sisters" value="{{ old('married_sisters', $member->married_sisters ?? 0) }}" min="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- 4. Address, About & Expectations -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-indigo-500"></i> Address, About Me & Partner Expectations
                </h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Current Residential Address</label>
                        <textarea name="current_address" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('current_address', $member->current_address) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">About Myself (Bio)</label>
                        <textarea name="about_me" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('about_me', $member->about_me) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Partner Expectations</label>
                        <textarea name="partner_expectations" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('partner_expectations', $member->partner_expectations) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 5. References -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-indigo-500"></i> Relative References
                </h3>
                <div class="space-y-6 text-sm">
                    <!-- Ref 1 -->
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs uppercase mb-2">Reference 1</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" name="ref1_name" value="{{ old('ref1_name', $member->ref1_name) }}" placeholder="Name" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref1_relation" value="{{ old('ref1_relation', $member->ref1_relation) }}" placeholder="Relation" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref1_mobile" value="{{ old('ref1_mobile', $member->ref1_mobile) }}" placeholder="Mobile Number" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref1_city" value="{{ old('ref1_city', $member->ref1_city) }}" placeholder="City" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        </div>
                    </div>

                    <!-- Ref 2 -->
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs uppercase mb-2">Reference 2</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input type="text" name="ref2_name" value="{{ old('ref2_name', $member->ref2_name) }}" placeholder="Name" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref2_relation" value="{{ old('ref2_relation', $member->ref2_relation) }}" placeholder="Relation" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref2_mobile" value="{{ old('ref2_mobile', $member->ref2_mobile) }}" placeholder="Mobile Number" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                            <input type="text" name="ref2_city" value="{{ old('ref2_city', $member->ref2_city) }}" placeholder="City" class="px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right 1-Column Sidebar (Status & Photos) -->
        <div class="space-y-6">

            <!-- Profile Status Controls -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-500"></i> Account Status & Control
                </h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Account Status *</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="approved" {{ old('status', $member->status) === 'approved' ? 'selected' : '' }}>Approved (Active Public)</option>
                            <option value="pending" {{ old('status', $member->status) === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="account_approved" {{ old('status', $member->status) === 'account_approved' ? 'selected' : '' }}>Account Approved (Wizard Incomplete)</option>
                            <option value="account_pending" {{ old('status', $member->status) === 'account_pending' ? 'selected' : '' }}>Account Pending</option>
                            <option value="rejected" {{ old('status', $member->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="blocked" {{ old('status', $member->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="deleted" {{ old('status', $member->status) === 'deleted' ? 'selected' : '' }}>Account Deleted</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 text-xs mb-1">Profile ID (JDMXXXXXX)</label>
                        <input type="text" name="profile_id" value="{{ old('profile_id', $member->profile_id) }}" placeholder="Auto-generated if approved"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-2 pt-2 text-center">
                        <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                            <span class="block text-[10px] font-bold text-blue-600 uppercase">Reg. Count</span>
                            <span class="text-lg font-extrabold text-blue-900">{{ $member->registration_count ?? 1 }}</span>
                        </div>
                        <div class="bg-rose-50 p-2.5 rounded-xl border border-rose-100">
                            <span class="block text-[10px] font-bold text-rose-600 uppercase">Del. Count</span>
                            <span class="text-lg font-extrabold text-rose-900">{{ $member->deletion_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Photo Upload -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-indigo-500"></i> Profile Photo
                </h3>
                <div class="text-center space-y-3">
                    <div class="w-32 h-32 mx-auto rounded-2xl overflow-hidden border border-gray-200 bg-slate-100 flex items-center justify-center shadow-sm">
                        @if($member->profile_photo)
                            <img src="/image?file={{ urlencode($member->profile_photo) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <span class="text-slate-400 font-bold text-3xl">{{ substr($member->full_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="profile_photo_file" accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Horoscope Photo Upload -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-scroll text-indigo-500"></i> Horoscope (कुंडली) Photo
                </h3>
                <div class="space-y-3">
                    @if($member->horoscope_photo)
                        <div class="w-full h-32 rounded-xl overflow-hidden border border-gray-200 bg-slate-100">
                            <img src="/image?file={{ urlencode($member->horoscope_photo) }}" alt="Horoscope" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div>
                        <input type="file" name="horoscope_photo_file" accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- ID Proof Upload -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-indigo-500"></i> ID Proof Document
                </h3>
                <div class="space-y-3">
                    @if($member->id_proof_photo)
                        <div class="w-full h-32 rounded-xl overflow-hidden border border-gray-200 bg-slate-100">
                            <img src="/image?file={{ urlencode($member->id_proof_photo) }}" alt="ID Proof" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div>
                        <input type="file" name="id_proof_photo_file" accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Save Action Bar -->
    <div class="mt-8 flex justify-end pb-8">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl text-sm font-bold transition duration-150 shadow-md flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
        </button>
    </div>

</form>
@endsection
