@extends('layouts.app')

@section('title', 'My Profile - Jain Digambar Matrimony')

@section('content')
<section class="py-12 md:py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Waiting for Approval Banner -->
            @if($user->status !== 'approved')
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded-r-lg shadow-sm" data-aos="fade-down">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-base font-bold text-yellow-800">
                            Waiting for Approval
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>
                                Your account is currently under review by our administration team. You will be able to access the full platform once your account has been approved. Approvals usually take between 24-48 hours. Thank you for your patience!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8" data-aos="fade-up">
                <h1 class="text-3xl md:text-4xl font-bold text-dark">My Profile</h1>
                <div class="mt-4 md:mt-0 flex gap-4 flex-wrap">
                    @if($user->status === 'approved')
                    <a href="{{ route('profile.edit') }}" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-opacity-90 shadow-md transition font-medium flex items-center gap-2">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    @endif

                    <form action="{{ route('profile.delete') }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="delete_reason" value="User deleted directly from profile">
                        <button type="submit" onclick="return confirm('Are you sure you want to deactivate your profile? This action cannot be undone.')" class="bg-red-600 text-white px-6 py-2.5 rounded-lg hover:bg-red-700 shadow-md transition font-medium flex items-center gap-2">
                            <i class="fas fa-trash-alt"></i> Delete Profile
                        </button>
                    </form>
                    <a href="{{ route('password.change') }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-50 shadow-sm transition font-medium flex items-center gap-2">
                        <i class="fas fa-key"></i> Change Password
                    </a>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="bg-red-50 border border-red-200 text-red-600 px-6 py-2.5 rounded-lg hover:bg-red-100 transition font-medium flex items-center gap-2 shadow-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column (Profile Summary) -->
                <div class="w-full lg:w-1/3 space-y-8" data-aos="fade-up" data-aos-delay="100">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="bg-primary h-32 relative">
                            <!-- Optional cover pattern/image could go here -->
                        </div>
                        <div class="px-6 pb-6 relative">
                            <div class="w-28 h-28 rounded-full border-4 border-white bg-gray-100 mx-auto -mt-14 flex items-center justify-center shadow-md overflow-hidden relative z-10">
                                @if(!empty($user->profile_photo) && (str_starts_with($user->profile_photo, 'data:image/') || resolve_media_path($user->profile_photo) !== null))
                                    <img src="{{ route('image.serve', ['file' => $user->profile_photo]) }}" alt="Profile Photo" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=random" alt="Profile Photo" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="text-center mt-4">
                                <h2 class="text-2xl font-bold text-dark">{{ $user->full_name ?? 'N/A' }}</h2>
                                <p class="text-gray-500 font-medium">{{ $user->occupation ?? 'N/A' }}</p>
                                <div class="flex items-center justify-center gap-2 mt-2 text-gray-600 text-sm">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    <span>{{ $user->native_place ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <hr class="my-6 border-gray-100">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-birthday-cake w-4 text-center text-gray-400"></i> Age / Height</span>
                                    <span class="font-medium text-dark">{{ $age }} Yrs / {{ $user->height ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-om w-4 text-center text-gray-400"></i> Religion</span>
                                    <span class="font-medium text-dark">Digambar Jain</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-users w-4 text-center text-gray-400"></i> Gotra</span>
                                    <span class="font-medium text-dark">{{ $user->mama_gotra ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-id-card w-4 text-center text-gray-400"></i> Profile ID</span>
                                    <span class="font-medium text-dark">{{ $user->profile_id ?? 'Not Assigned' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Card -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-dark mb-4 flex items-center gap-2">
                            <i class="fas fa-address-book text-primary"></i> Contact Details
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-50 p-2.5 rounded-lg text-primary mt-0.5">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Mobile Number</p>
                                    <p class="font-medium text-dark">{{ $user->mobile ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-50 p-2.5 rounded-lg text-primary mt-0.5">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Email Address</p>
                                    <p class="font-medium text-dark">{{ $user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-50 p-2.5 rounded-lg text-primary mt-0.5">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Native Place</p>
                                    <p class="font-medium text-dark text-sm leading-snug">{!! nl2br(e($user->native_place ?? 'N/A')) !!}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-50 p-2.5 rounded-lg text-primary mt-0.5">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Current Address</p>
                                    <p class="font-medium text-dark text-sm leading-snug">{!! nl2br(e($user->current_address ?? 'N/A')) !!}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-50 p-2.5 rounded-lg text-primary mt-0.5">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Permanent Address</p>
                                    <p class="font-medium text-dark text-sm leading-snug">
                                        {!! nl2br(e($user->permanent_address ?? 'N/A')) !!}
                                        @if(!empty($user->pin_code)) - {{ $user->pin_code }} @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Screenshot Upload Card -->
                    <div id="payment-upload" class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 mt-8">
                        <h3 class="text-lg font-bold text-dark mb-4 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-primary"></i> Payment Screenshot
                        </h3>
                        @if(!empty($user->payment_screenshot))
                            <div class="mb-4">
                                <p class="text-sm text-green-600 font-medium mb-2"><i class="fas fa-check-circle"></i> Screenshot Uploaded</p>
                                @php
                                    $ext = strtolower(pathinfo($user->payment_screenshot, PATHINFO_EXTENSION));
                                @endphp
                                @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                    <img src="{{ route('image.serve', ['file' => $user->payment_screenshot]) }}" alt="Payment Receipt" class="w-full h-auto rounded border border-gray-200">
                                @else
                                    <a href="{{ route('image.serve', ['file' => $user->payment_screenshot]) }}" target="_blank" class="text-blue-600 underline text-sm"><i class="fas fa-external-link-alt"></i> View Uploaded File</a>
                                @endif
                            </div>
                            @if(!empty($user->payment_transaction_id))
                                <div class="mb-4 bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-sm text-gray-500 font-medium">Transaction ID:</p>
                                    <p class="text-md text-gray-800 font-bold">{{ $user->payment_transaction_id }}</p>
                                </div>
                            @endif
                        @endif
                        
                        @if(empty($user->payment_screenshot) || $paymentStatus === 'rejected')
                            @if($paymentStatus === 'rejected')
                                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> Your previous screenshot was rejected. Please upload a new one.
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4 text-xs text-red-700 font-semibold">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-4 text-xs text-green-700 font-semibold">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('profile.payment.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID <span class="text-red-500">*</span></label>
                                    <input type="text" name="payment_transaction_id" placeholder="e.g. GPay Transaction ID" class="w-full text-sm border-gray-300 rounded-md shadow-sm p-2 mb-3 focus:ring-primary focus:border-primary border focus:outline-none" required>
                                    
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Screenshot <span class="text-red-500">*</span></label>
                                    <input type="file" name="payment_screenshot" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-primary hover:file:bg-blue-100" required>
                                    <p class="text-xs text-gray-500 mt-1">Allowed formats: JPG, PNG, PDF</p>
                                </div>
                                <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg font-medium hover:bg-opacity-90 transition">
                                    Save Profile
                                </button>
                            </form>
                        @elseif($paymentStatus === 'verified')
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm font-bold text-center shadow-sm">
                                <i class="fas fa-check-circle mr-2 text-green-600 text-lg"></i> Done! Your payment has been approved by the admin.
                            </div>
                        @else
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm shadow-sm">
                                <i class="fas fa-info-circle mr-2"></i> Your payment screenshot has been uploaded and is currently under review by the admin.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column (Detailed Info) -->
                <div class="w-full lg:w-2/3 space-y-8" data-aos="fade-up" data-aos-delay="200">
                    
                    <!-- Personal & Physical Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Personal Details
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-6">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date of Birth</p>
                                <p class="font-medium text-dark">{{ !empty($user->birth_date) ? $user->birth_date->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Time of Birth</p>
                                <p class="font-medium text-dark">{{ !empty($user->birth_time) ? $user->birth_time : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Place of Birth</p>
                                <p class="font-medium text-dark">{{ $user->birth_place ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Manglik</p>
                                <p class="font-medium text-dark">{{ $user->manglik ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Weight</p>
                                <p class="font-medium text-dark">{{ format_weight($user->weight ?? ($user->weight_kg ? $user->weight_kg . ' kg' : null)) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Marital Status</p>
                                <p class="font-medium text-dark">{{ $user->marital_status ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Mama Gotra</p>
                                <p class="font-medium text-dark">{{ $user->mama_gotra ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Handicapped</p>
                                <p class="font-medium text-dark">{{ $user->handicapped ?? 'No' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Career -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-graduation-cap"></i> Education & Career
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Highest Education</p>
                                <p class="font-medium text-dark">{{ $user->higher_education ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Occupation</p>
                                <p class="font-medium text-dark">{{ $user->occupation ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Monthly Income</p>
                                <p class="font-medium text-dark">{{ $user->monthly_income ?? 'N/A' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Partner Preferences</p>
                                <p class="font-medium text-dark">{!! nl2br(e($user->partner_preference ?? 'N/A')) !!}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Family Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-home"></i> Family Details
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Father's Name</p>
                                <p class="font-medium text-dark">{{ $user->father_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Father's Occupation</p>
                                <p class="font-medium text-dark">{{ $user->father_occupation ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Mother's Name</p>
                                <p class="font-medium text-dark">{{ $user->mother_name ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="sm:col-span-2 mt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Siblings Info</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-5 rounded-xl border border-gray-100">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-primary">{{ $user->brothers_married ?? '0' }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Brothers<br>Married</p>
                                    </div>
                                    <div class="text-center border-l border-gray-200">
                                        <p class="text-2xl font-bold text-primary">{{ $user->brothers_unmarried ?? '0' }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Brothers<br>Unmarried</p>
                                    </div>
                                    <div class="text-center md:border-l border-gray-200 pt-4 md:pt-0 border-t md:border-t-0 mt-2 md:mt-0">
                                        <p class="text-2xl font-bold text-primary">{{ $user->sisters_married ?? '0' }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Sisters<br>Married</p>
                                    </div>
                                    <div class="text-center border-l border-gray-200 pt-4 md:pt-0 border-t md:border-t-0 mt-2 md:mt-0">
                                        <p class="text-2xl font-bold text-primary">{{ $user->sisters_unmarried ?? '0' }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase font-bold mt-1 tracking-wider">Sisters<br>Unmarried</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mandir Verification & References -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-xl font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-gopuram text-primary"></i> Mandir Verification Details
                            </h3>
                            <span class="bg-{{ $user->status === 'approved' ? 'green' : 'yellow' }}-100 text-{{ $user->status === 'approved' ? 'green' : 'yellow' }}-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                <i class="fas fa-{{ $user->status === 'approved' ? 'check-circle' : 'clock' }}"></i> {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-6">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Subcast (उपजाति)</p>
                                <p class="font-medium text-dark">{{ $user->subcast ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Registered Mandir (मंदिर)</p>
                                <p class="font-medium text-dark">{{ $user->mandir_name ?? ($user->mandir ?? 'N/A') }} @if(!empty($user->custom_mandir)) - {{ $user->custom_mandir }} @endif</p>
                            </div>
                        </div>

                        <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Reference Persons</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-bold text-primary uppercase mb-2">Reference Person 1</p>
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><span class="text-gray-500">Name:</span> <span class="font-medium text-dark">{{ $user->ref1_name ?? 'N/A' }}</span></p>
                                    <p><span class="text-gray-500">Mobile:</span> <span class="font-medium text-dark">{{ $user->ref1_mobile ?? 'N/A' }}</span></p>
                                    <p><span class="text-gray-500">Relation:</span> <span class="font-medium text-dark">{{ $user->ref1_relation ?? 'N/A' }}</span></p>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-bold text-primary uppercase mb-2">Reference Person 2</p>
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><span class="text-gray-500">Name:</span> <span class="font-medium text-dark">{{ $user->ref2_name ?? 'N/A' }}</span></p>
                                    <p><span class="text-gray-500">Mobile:</span> <span class="font-medium text-dark">{{ $user->ref2_mobile ?? 'N/A' }}</span></p>
                                    <p><span class="text-gray-500">Relation:</span> <span class="font-medium text-dark">{{ $user->ref2_relation ?? 'N/A' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
