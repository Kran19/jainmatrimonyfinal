@extends('layouts.app')

@section('title', 'Edit Profile - Jain Digambar Matrimony')

@section('content')
<section class="py-12 bg-light min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8" data-aos="fade-up">
            <nav class="text-xs text-gray-500 mb-2 flex items-center gap-1.5 font-medium">
                <a href="{{ route('home') }}" class="hover:text-primary transition">Home</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('profile.my') }}" class="hover:text-primary transition">My Profile</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-dark">Edit Profile</span>
            </nav>
            <h1 class="text-3xl font-black text-dark">Edit My Profile</h1>
            <p class="text-gray-500 text-xs mt-1">Keep your details updated to find the most compatible matches.</p>
        </div>

        @if(session('success'))
            <div id="flash-message" class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl flex items-center justify-between shadow-sm">
                <p class="text-xs font-bold text-emerald-800 flex items-center gap-2">
                    <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                    {{ session('success') }}
                </p>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 p-1"><i class="fas fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl flex items-center justify-between">
                <p class="text-xs font-semibold text-red-700">{{ session('error') }}</p>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-xmark"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl space-y-1">
                <p class="text-xs font-bold text-red-800 mb-1"><i class="fas fa-triangle-exclamation mr-1 text-red-600"></i> Please fix the following errors before saving:</p>
                @foreach($errors->all() as $error)
                    <p class="text-[11px] font-semibold text-red-700"><i class="fas fa-circle-exclamation mr-1"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col lg:flex-row gap-8">
            @csrf
            
            <!-- Left Side Tabs Navigation -->
            <div class="w-full lg:w-1/4 flex-shrink-0">
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 sticky top-24 space-y-1 text-sm font-semibold text-gray-600">
                    <button type="button" onclick="switchTab('personal')" id="tab-btn-personal" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5 text-primary bg-slate-50">
                        <i class="fas fa-user text-xs w-4"></i> Personal Details
                    </button>
                    <button type="button" onclick="switchTab('education')" id="tab-btn-education" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-graduation-cap text-xs w-4"></i> Education & Job
                    </button>
                    <button type="button" onclick="switchTab('family')" id="tab-btn-family" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-users text-xs w-4"></i> Family Details
                    </button>
                    <button type="button" onclick="switchTab('astro')" id="tab-btn-astro" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-om text-xs w-4"></i> Gotra & Mandir
                    </button>
                    <button type="button" onclick="switchTab('references')" id="tab-btn-references" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-address-book text-xs w-4"></i> References
                    </button>
                    <button type="button" onclick="switchTab('preferences')" id="tab-btn-preferences" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-heart text-xs w-4"></i> Preferences & Address
                    </button>
                    <button type="button" onclick="switchTab('photos')" id="tab-btn-photos" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5">
                        <i class="fas fa-images text-xs w-4"></i> Photos & Proofs
                    </button>
                    
                    @if(count($customFieldsByGroup) > 0)
                    <button type="button" onclick="switchTab('additional')" id="tab-btn-additional" class="w-full text-left px-4 py-2.5 rounded-lg hover:bg-slate-50 transition flex items-center gap-2.5 border-t mt-2 pt-3">
                        <i class="fas fa-info-circle text-xs w-4"></i> Additional Fields
                    </button>
                    @endif
                </div>
            </div>

            <!-- Right Side Tab Panels -->
            <div class="flex-grow bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                
                <!-- Tab: Personal -->
                <div id="tab-panel-personal" class="tab-panel space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-user text-primary"></i> Personal Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" required value="{{ old('full_name', $user->full_name) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="text" name="mobile" required value="{{ old('mobile', $user->mobile) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" required class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Are you Digambar Jain?</label>
                            <select name="are_you_digambar_jain" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="Yes" {{ old('are_you_digambar_jain', $user->are_you_digambar_jain) === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('are_you_digambar_jain', $user->are_you_digambar_jain) === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Profile Created By</label>
                            <select name="filled_by" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="Self" {{ old('filled_by', $user->filled_by) === 'Self' ? 'selected' : '' }}>Self</option>
                                <option value="Parent" {{ old('filled_by', $user->filled_by) === 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Brother" {{ old('filled_by', $user->filled_by) === 'Brother' ? 'selected' : '' }}>Brother</option>
                                <option value="Sister" {{ old('filled_by', $user->filled_by) === 'Sister' ? 'selected' : '' }}>Sister</option>
                                <option value="Relative" {{ old('filled_by', $user->filled_by) === 'Relative' ? 'selected' : '' }}>Relative</option>
                                <option value="Guardian" {{ old('filled_by', $user->filled_by) === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="birth_date" required value="{{ old('birth_date', $user->birth_date ? (is_string($user->birth_date) ? substr($user->birth_date, 0, 10) : $user->birth_date->format('Y-m-d')) : '') }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Time of Birth <span class="text-red-500">*</span></label>
                            <input type="text" name="birth_time" placeholder="e.g. 10:15 AM" required value="{{ old('birth_time', $user->birth_time) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Place of Birth <span class="text-red-500">*</span></label>
                            <input type="text" name="birth_place" required value="{{ old('birth_place', $user->birth_place) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Native Place / Town <span class="text-red-500">*</span></label>
                            <input type="text" name="native_place" required value="{{ old('native_place', $user->native_place) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Cast (जाति) <span class="text-red-500">*</span></label>
                            <select name="cast" id="cast" required onchange="toggleCustomCast(this.value)" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="">Select Cast</option>
                                <option value="Digambar Jain" {{ old('cast', $user->cast) === 'Digambar Jain' ? 'selected' : '' }}>Digambar Jain</option>
                                <option value="Other" {{ (old('cast', $user->cast) && old('cast', $user->cast) !== 'Digambar Jain') ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="custom_cast" id="custom_cast" value="{{ (old('cast', $user->cast) && old('cast', $user->cast) !== 'Digambar Jain') ? old('cast', $user->cast) : '' }}" placeholder="Specify custom cast" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium mt-2 {{ (old('cast', $user->cast) && old('cast', $user->cast) !== 'Digambar Jain') ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Sub-Cast (उपजाति)</label>
                            <select name="subcast" id="subcast" onchange="toggleCustomSubcast(this.value)" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="">Select Sub-Cast</option>
                                @php
                                    $subcastField = \App\Models\RegistrationField::where('field_key', 'subcast')->first();
                                    $db_subcasts = $subcastField && $subcastField->field_options 
                                        ? array_map('trim', explode(',', $subcastField->field_options))
                                        : ['Khandelwal', 'Agrawal', 'Oswal', 'Porwal', 'Golalare', 'Humad', 'Bagherwal', 'Chaturth', 'Pancham'];
                                        
                                    // Remove 'Other' variants to handle them separately
                                    $predefined_subcasts = array_filter($db_subcasts, function($val) {
                                        return strtolower($val) !== 'other' && strtolower($val) !== 'other (अन्य)';
                                    });
                                    
                                    $is_other_subcast = !empty(old('subcast', $user->subcast)) && !in_array(old('subcast', $user->subcast), $predefined_subcasts);
                                @endphp
                                @foreach($predefined_subcasts as $sc)
                                    <option value="{{ $sc }}" {{ old('subcast', $user->subcast) === $sc ? 'selected' : '' }}>{{ $sc }}</option>
                                @endforeach
                                <option value="Other" {{ $is_other_subcast ? 'selected' : '' }}>Other (अन्य)</option>
                            </select>
                            <input type="text" name="custom_subcast" id="custom_subcast" value="{{ $is_other_subcast ? old('subcast', $user->subcast) : '' }}" placeholder="Specify custom sub-cast" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium mt-2 {{ $is_other_subcast ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Marital Status <span class="text-red-500">*</span></label>
                            <select name="marital_status" required class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="Never Married" {{ old('marital_status', $user->marital_status) === 'Never Married' ? 'selected' : '' }}>Never Married</option>
                                <option value="Widow" {{ old('marital_status', $user->marital_status) === 'Widow' ? 'selected' : '' }}>Widow</option>
                                <option value="Widower" {{ old('marital_status', $user->marital_status) === 'Widower' ? 'selected' : '' }}>Widower</option>
                                <option value="Divorce" {{ old('marital_status', $user->marital_status) === 'Divorce' ? 'selected' : '' }}>Divorce</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Height <span class="text-red-500">*</span></label>
                            <input type="text" name="height" placeholder="e.g. 5ft 6in" required value="{{ old('height', $user->height) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Weight (kg)</label>
                            <input type="text" name="weight" placeholder="e.g. 65 kg or 65" value="{{ old('weight', $user->weight ?? ($user->weight_kg ? $user->weight_kg . ' kg' : '')) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Physical Disability <span class="text-red-500">*</span></label>
                            <select name="handicapped" required class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="No" {{ old('handicapped', $user->handicapped) === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('handicapped', $user->handicapped) === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab: Education & Job -->
                <div id="tab-panel-education" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-graduation-cap text-primary"></i> Education & Job</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Higher Education <span class="text-red-500">*</span></label>
                            <input type="text" name="higher_education" required value="{{ old('higher_education', $user->higher_education) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium" placeholder="e.g. B.Tech / M.Com / MBA / CA">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Occupation (व्यवसाय)</label>
                            @php
                                $stdOccs = ['Job', 'Business', 'Service', 'Professional', 'Private Sector', 'Government Sector', 'Self Employed', 'Housewife', 'Retired'];
                                $currOcc = old('occupation', $user->occupation);
                                $isOtherOcc = !empty($currOcc) && !in_array($currOcc, $stdOccs);
                            @endphp
                            <select name="occupation" id="occupation" onchange="toggleCustomOccupation(this.value)" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="">Select Occupation</option>
                                @foreach($stdOccs as $occ)
                                    <option value="{{ $occ }}" {{ $currOcc === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                                @endforeach
                                <option value="Other" {{ $isOtherOcc ? 'selected' : '' }}>Other (अन्य)</option>
                            </select>
                            <input type="text" name="custom_occupation" id="custom_occupation" value="{{ $isOtherOcc ? $currOcc : '' }}" placeholder="Specify occupation details" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium mt-2 {{ $isOtherOcc ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Company / Firm Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="e.g. TCS / Self Business / Firm" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Designation / Position</label>
                            <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Software Engineer / Manager / Owner" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Annual / Monthly Income (INR)</label>
                            <input type="number" name="monthly_income" value="{{ old('monthly_income', $user->monthly_income) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium" placeholder="e.g. 500000">
                        </div>
                    </div>
                </div>

                <!-- Tab: Family -->
                <div id="tab-panel-family" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-users text-primary"></i> Family Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Father's Name <span class="text-red-500">*</span></label>
                            <input type="text" name="father_name" required value="{{ old('father_name', $user->father_name) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Father's Occupation</label>
                            <input type="text" name="father_occupation" value="{{ old('father_occupation', $user->father_occupation) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Father's Mobile</label>
                            <input type="text" name="father_mobile" value="{{ old('father_mobile', $user->father_mobile) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Father's Annual Income (INR)</label>
                            <input type="number" name="father_income" value="{{ old('father_income', $user->father_income) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium" placeholder="e.g. 500000">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Mother's Name <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name" required value="{{ old('mother_name', $user->mother_name) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Mother's Occupation</label>
                            <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $user->mother_occupation) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Mother's Mobile</label>
                            <input type="text" name="mother_mobile" value="{{ old('mother_mobile', $user->mother_mobile) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        
                        <div class="border-t pt-4 md:col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Brothers</label>
                                <input type="number" name="brothers" value="{{ old('brothers', $user->brothers ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Married</label>
                                <input type="number" name="brothers_married" value="{{ old('brothers_married', $user->brothers_married ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Unmarried</label>
                                <input type="number" name="brothers_unmarried" value="{{ old('brothers_unmarried', $user->brothers_unmarried ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                        </div>

                        <div class="border-t pt-4 md:col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Sisters</label>
                                <input type="number" name="sisters" value="{{ old('sisters', $user->sisters ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Married</label>
                                <input type="number" name="sisters_married" value="{{ old('sisters_married', $user->sisters_married ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Unmarried</label>
                                <input type="number" name="sisters_unmarried" value="{{ old('sisters_unmarried', $user->sisters_unmarried ?? 0) }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50 text-center text-dark font-medium">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Astro & Gotra -->
                <div id="tab-panel-astro" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-om text-primary"></i> Gotra & Mandir Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        @php
                            $gotraList = [
                                'Agrawal', 'Bagherval', 'Bagar/ Bogar', 'Chaturtha', 'Chittauda',
                                'Dasa Humad', 'Dasa Nagda', 'Dasa Narsinhpura', 'Golalare', 'Golapurab',
                                'GolSingare', 'Humad', 'Jaiswal', 'Kathnera', 'Keshval',
                                'Khandelwal', 'Kharva', 'Lamechu', 'Levechval', 'Mevada',
                                'Narsinhpura', 'Padmavati Porval', 'Pallival', 'Parvar', 'Raikwad',
                                'Saitwal', 'Saraogi', 'Shrimal', 'Vagad Chhappan', 'Varahiya',
                                'Visa Humad', 'Visa Mevada', 'Visa Nagda', 'Visa Narsinhpura'
                            ];
                            $editGotra = old('gotra', $user->gotra);
                            $isCustomEditGotra = !empty($editGotra) && !in_array($editGotra, $gotraList);

                            $editMamaGotra = old('mama_gotra', $user->mama_gotra);
                            $isCustomEditMamaGotra = !empty($editMamaGotra) && !in_array($editMamaGotra, $gotraList);
                        @endphp
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Self Gotra <span class="text-red-500">*</span></label>
                            <select name="gotra" id="edit_gotra" required onchange="toggleCustomGotra(this.value)" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="">Select Self Gotra</option>
                                @foreach($gotraList as $g)
                                    <option value="{{ $g }}" {{ $editGotra === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                <option value="Others" {{ $isCustomEditGotra ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" name="custom_gotra" id="custom_gotra" value="{{ $isCustomEditGotra ? $editGotra : '' }}" placeholder="Specify custom Gotra" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium mt-2 {{ $isCustomEditGotra ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Maternal Gotra (Mama Gotra) <span class="text-red-500">*</span></label>
                            <select name="mama_gotra" id="edit_mama_gotra" required onchange="toggleCustomMamaGotra(this.value)" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="">Select Mama Gotra</option>
                                @foreach($gotraList as $g)
                                    <option value="{{ $g }}" {{ $editMamaGotra === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                <option value="Others" {{ $isCustomEditMamaGotra ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" name="custom_mama_gotra" id="custom_mama_gotra" value="{{ $isCustomEditMamaGotra ? $editMamaGotra : '' }}" placeholder="Specify custom Mama Gotra" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium mt-2 {{ $isCustomEditMamaGotra ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Manglik Status <span class="text-red-500">*</span></label>
                            <select name="manglik" required class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                <option value="No" {{ old('manglik', $user->manglik) === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('manglik', $user->manglik) === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        
                        <div class="border-t pt-4 md:col-span-2">
                            <h4 class="font-bold text-dark text-sm mb-1 flex items-center gap-2"><i class="fas fa-gopuram text-primary"></i> Mandir / Community Verification</h4>
                            <p class="text-xs text-gray-600 mb-3">आपका परिवार किस दिगंबर जैन मंदिर से जुड़ा है.</p>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Jain Mandir / Temple Name</label>
                            <input type="text" name="mandir_name" value="{{ old('mandir_name', $user->mandir_name ?? $user->mandir) }}" placeholder="e.g. Digambar Jain Temple" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Temple Pincode</label>
                            <input type="text" name="mandir_pincode" value="{{ old('mandir_pincode', $user->mandir_pincode) }}" placeholder="e.g. 452001" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Temple Address</label>
                            <input type="text" name="mandir_address" value="{{ old('mandir_address', $user->mandir_address) }}" placeholder="e.g. Main Street, City" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                    </div>
                </div>

                <!-- Tab: References -->
                <div id="tab-panel-references" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-address-book text-primary"></i> Reference Contacts</h3>
                    <div class="space-y-6 text-sm">
                        <!-- Reference 1 -->
                        <div class="bg-gray-50 p-4 rounded-xl border space-y-4">
                            <h4 class="font-extrabold text-gray-800 text-xs uppercase tracking-wider">Reference 1</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Full Name</label>
                                    <input type="text" name="ref1_name" value="{{ old('ref1_name', $user->ref1_name) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Mobile Number</label>
                                    <input type="text" name="ref1_mobile" value="{{ old('ref1_mobile', $user->ref1_mobile) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Relation</label>
                                    <input type="text" name="ref1_relation" value="{{ old('ref1_relation', $user->ref1_relation) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                            </div>
                        </div>

                        <!-- Reference 2 -->
                        <div class="bg-gray-50 p-4 rounded-xl border space-y-4">
                            <h4 class="font-extrabold text-gray-800 text-xs uppercase tracking-wider">Reference 2</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Full Name</label>
                                    <input type="text" name="ref2_name" value="{{ old('ref2_name', $user->ref2_name) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Mobile Number</label>
                                    <input type="text" name="ref2_mobile" value="{{ old('ref2_mobile', $user->ref2_mobile) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Relation</label>
                                    <input type="text" name="ref2_relation" value="{{ old('ref2_relation', $user->ref2_relation) }}" class="w-full border rounded-lg px-3 py-2 bg-white text-dark font-medium">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Preferences & Address -->
                <div id="tab-panel-preferences" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-heart text-primary"></i> Preferences & Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Languages Known</label>
                            <input type="text" name="languages" placeholder="e.g. Hindi, English, Gujarati" value="{{ old('languages', $user->languages) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Hobbies & Interests</label>
                            <input type="text" name="hobbies" placeholder="e.g. Reading, Music, Cooking" value="{{ old('hobbies', $user->hobbies) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Partner Preferences</label>
                            <textarea name="partner_preference" rows="3" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium" placeholder="Describe the kind of life partner you are looking for...">{{ old('partner_preference', $user->partner_preference) }}</textarea>
                        </div>
                        
                        <div class="md:col-span-2 border-t pt-4">
                            <h4 class="font-bold text-gray-800 text-sm mb-3">Address Information</h4>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Current Residence Address</label>
                            <input type="text" name="current_address" value="{{ old('current_address', $user->current_address) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-gray-700 mb-1.5">Permanent Address</label>
                            <input type="text" name="permanent_address" value="{{ old('permanent_address', $user->permanent_address) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1.5">Pin Code</label>
                            <input type="text" name="pin_code" value="{{ old('pin_code', $user->pin_code) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                        </div>
                    </div>
                </div>

                <!-- Tab: Photos & Proofs -->
                <div id="tab-panel-photos" class="tab-panel hidden space-y-6">
                    <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-images text-primary"></i> Photos & ID Proof</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                        <!-- Profile Photo -->
                        <div class="border p-4 rounded-xl space-y-3">
                            <label class="block font-semibold text-gray-700">Candidate Profile Photo</label>
                            @if($user->profile_photo)
                                <div class="w-36 h-44 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-sm relative group">
                                    <img src="{{ route('image.serve', ['file' => $user->profile_photo]) }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 right-1 bg-emerald-600 text-white text-[10px] px-1.5 py-0.5 rounded font-bold">Uploaded</span>
                                </div>
                            @endif
                            <input type="file" name="profile_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                        </div>

                        <!-- Family Photo -->
                        <div class="border p-4 rounded-xl space-y-3">
                            <label class="block font-semibold text-gray-700">Family Photo</label>
                            @if($user->family_photo)
                                <div class="w-48 h-36 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-sm relative group">
                                    <img src="{{ route('image.serve', ['file' => $user->family_photo]) }}" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 right-1 bg-emerald-600 text-white text-[10px] px-1.5 py-0.5 rounded font-bold">Uploaded</span>
                                </div>
                            @else
                                <div class="w-48 h-36 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center text-gray-400 text-xs">
                                    <i class="fas fa-users-viewfinder text-2xl mb-1 text-gray-300"></i>
                                    <span>No Family Photo</span>
                                </div>
                            @endif
                            <input type="file" name="family_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                        </div>

                        <!-- ID Proof Type & File -->
                        <div class="border p-4 rounded-xl space-y-4 md:col-span-2">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">ID Proof Type</label>
                                <select name="id_proof_type" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                    <option value="">Select ID Proof Type</option>
                                    <option value="Aadhaar Card" {{ old('id_proof_type', $user->id_proof_type) === 'Aadhaar Card' || old('id_proof_type', $user->id_proof_type) === 'Aadhar Card' ? 'selected' : '' }}>Aadhaar Card</option>
                                    <option value="PAN Card" {{ old('id_proof_type', $user->id_proof_type) === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                    <option value="Voter ID" {{ old('id_proof_type', $user->id_proof_type) === 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
                                    <option value="Driving Licence" {{ old('id_proof_type', $user->id_proof_type) === 'Driving Licence' || old('id_proof_type', $user->id_proof_type) === 'Driving License' ? 'selected' : '' }}>Driving Licence</option>
                                    <option value="Passport" {{ old('id_proof_type', $user->id_proof_type) === 'Passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="Other" {{ old('id_proof_type', $user->id_proof_type) === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1.5">Upload / Update ID Proof Document</label>
                                @if($user->id_proof_path)
                                    <div class="mb-3 flex items-center gap-3">
                                        <div class="w-40 h-28 rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                            <img src="{{ route('image.serve', ['file' => $user->id_proof_path]) }}" class="w-full h-full object-contain bg-slate-100">
                                        </div>
                                        <a href="{{ route('image.serve', ['file' => $user->id_proof_path]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline bg-blue-50 px-3 py-2 rounded-lg">
                                            <i class="fas fa-file-image"></i> View Full Document
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="id_proof" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Additional dynamic custom fields -->
                @if(count($customFieldsByGroup) > 0)
                <div id="tab-panel-additional" class="tab-panel hidden space-y-8">
                    @foreach($customFieldsByGroup as $groupTitle => $fields)
                        <div class="space-y-6">
                            <h3 class="text-xl font-bold text-dark border-b pb-2 mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-primary"></i> {{ $groupTitle }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                                @foreach($fields as $field)
                                    @php
                                        $val = $customValues[$field->id] ?? '';
                                    @endphp
                                    <div>
                                        <label class="block font-semibold text-gray-700 mb-1.5">
                                            {{ $field->field_label }}
                                            @if($field->is_required) <span class="text-red-500">*</span> @endif
                                        </label>
                                        
                                        @if($field->field_type === 'textarea')
                                            <textarea name="{{ $field->field_key }}" rows="2" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">{{ old($field->field_key, $val) }}</textarea>
                                        @elseif($field->field_type === 'dropdown')
                                            <select name="{{ $field->field_key }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                                <option value="">Select {{ $field->field_label }}</option>
                                                @foreach($field->options_array as $opt)
                                                    <option value="{{ $opt }}" {{ old($field->field_key, $val) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($field->field_type === 'file')
                                            @if(!empty($val))
                                                <div class="mb-1 text-xs text-gray-500 font-medium flex items-center gap-1.5">
                                                    <i class="fas fa-file-image text-emerald-600"></i> Current File: 
                                                    <a href="{{ route('image.serve', ['file' => $val]) }}" target="_blank" class="text-primary underline font-bold">View File</a>
                                                </div>
                                            @endif
                                            <input type="file" name="{{ $field->field_key }}" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                                        @else
                                            <input type="{{ $field->field_type ?? 'text' }}" name="{{ $field->field_key }}" value="{{ old($field->field_key, $val) }}" class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 focus:bg-white text-dark font-medium">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-between border-t pt-6 mt-8">
                    <a href="{{ route('profile.my') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition flex items-center gap-1">
                        <i class="fas fa-chevron-left text-xs"></i> Back to Profile
                    </a>
                    <button type="submit" class="bg-primary hover:bg-opacity-90 text-white font-extrabold px-8 py-3 rounded-xl transition text-sm shadow-md flex items-center gap-2">
                        <i class="fas fa-floppy-disk text-base"></i> Save Profile Changes
                    </button>
                </div>

            </div>
        </form>
    </div>
</section>

<script>
function toggleCustomCast(val) {
    const input = document.getElementById('custom_cast');
    if (val === 'Other') {
        input.classList.remove('hidden');
        input.required = true;
    } else {
        input.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

function toggleCustomSubcast(val) {
    const input = document.getElementById('custom_subcast');
    if (val === 'Other') {
        input.classList.remove('hidden');
        input.required = true;
    } else {
        input.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

function toggleCustomOccupation(val) {
    const input = document.getElementById('custom_occupation');
    if (val === 'Other') {
        input.classList.remove('hidden');
    } else {
        input.classList.add('hidden');
        input.value = '';
    }
}

function toggleCustomGotra(val) {
    const input = document.getElementById('custom_gotra');
    if (input) {
        if (val === 'Others' || val === 'Other') {
            input.classList.remove('hidden');
            input.required = true;
        } else {
            input.classList.add('hidden');
            input.required = false;
            input.value = '';
        }
    }
}

function toggleCustomMamaGotra(val) {
    const input = document.getElementById('custom_mama_gotra');
    if (input) {
        if (val === 'Others' || val === 'Other') {
            input.classList.remove('hidden');
            input.required = true;
        } else {
            input.classList.add('hidden');
            input.required = false;
            input.value = '';
        }
    }
}

function switchTab(tabId) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });
    // Remove active styles from all buttons
    document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
        btn.classList.remove('text-primary', 'bg-slate-50');
    });

    // Show active panel
    const activePanel = document.getElementById('tab-panel-' + tabId);
    if (activePanel) {
        activePanel.classList.remove('hidden');
    }
    // Set active style on button
    const activeBtn = document.getElementById('tab-btn-' + tabId);
    if (activeBtn) {
        activeBtn.classList.add('text-primary', 'bg-slate-50');
    }
}

// Auto-switch to tab containing error field if validation fails
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        const errorFields = {!! json_encode(array_keys($errors->toArray())) !!};
        if (errorFields.length > 0) {
            const firstErrField = errorFields[0];
            const fieldEl = document.querySelector(`[name="${firstErrField}"]`);
            if (fieldEl) {
                const panel = fieldEl.closest('.tab-panel');
                if (panel) {
                    const tabId = panel.id.replace('tab-panel-', '');
                    switchTab(tabId);
                    fieldEl.focus();
                }
            }
        }
    @endif
});
</script>
@endsection
