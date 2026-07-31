@extends('layouts.app')

@section('title', 'Registration Form - Jain Digambar Matrimony')

@section('content')
@php
if (!function_exists('renderCustomFieldHTML')) {
    function renderCustomFieldHTML($field, $customValues) {
        $val = $customValues[$field->id] ?? '';
        $req = $field->is_required ? '*' : '';
        $reqAttr = $field->is_required ? 'required' : '';
        $label = e($field->field_label);
        $key = e($field->field_key);
        $type = e($field->field_type);
        
        $html = '<div class="mb-4"><label class="block text-gray-700 font-semibold mb-2">' . $label . ' ' . $req . '</label>';
        if ($type === 'textarea') {
            $html .= '<textarea name="' . $key . '" ' . $reqAttr . ' rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-dark text-sm focus:border-primary">' . e($val) . '</textarea>';
        } elseif ($type === 'dropdown') {
            $html .= '<select name="' . $key . '" ' . $reqAttr . ' class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-dark text-sm focus:border-primary"><option value="">Select ' . $label . '</option>';
            foreach ($field->options_array as $opt) {
                $sel = ($val === $opt) ? 'selected' : '';
                $html .= '<option value="' . e($opt) . '" ' . $sel . '>' . e($opt) . '</option>';
            }
            $html .= '</select>';
        } elseif ($type === 'file') {
            if (!empty($val)) {
                $html .= '<div class="mb-1 text-xs text-gray-500 font-medium flex items-center gap-1.5"><i class="fas fa-file-image text-emerald-600"></i> Current File: <a href="' . route('image.serve', ['file' => $val]) . '" target="_blank" class="text-primary underline font-bold">View File</a></div>';
            }
            $html .= '<input type="file" name="' . $key . '" ' . ($field->is_required && empty($val) ? 'required' : '') . ' class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary">';
        } else {
            $html .= '<input type="' . $type . '" name="' . $key . '" value="' . e($val) . '" ' . $reqAttr . ' class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-dark text-sm focus:border-primary">';
        }
        $html .= '</div>';
        return $html;
    }
}
@endphp

<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-center text-dark mb-4" data-aos="fade-up">
                {{ $user->status === 'approved' ? 'Edit Profile' : 'Registration Form' }}
            </h1>
            <p class="text-center text-gray-600 mb-8" data-aos="fade-up" data-aos-delay="100">
                {{ $user->status === 'approved' ? 'Update your profile information' : 'Join the most trusted Digambar Jain Matrimony platform' }}
            </p>
            
            <div class="mb-8" id="progressBarContainer">
                <div class="flex justify-between items-center mb-2 relative">
                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 z-0"></div>
                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-primary z-0 transition-all duration-300" id="progressLine" style="width: 0%;"></div>
                    
                    <div class="step-indicator relative z-10 flex flex-col items-center cursor-default group" data-step="1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold border-4 border-white transition-colors duration-300 step-circle">1</div>
                        <span class="text-xs font-semibold text-gray-500 mt-2 absolute top-10 whitespace-nowrap step-text">Basic Info</span>
                    </div>
                    <div class="step-indicator relative z-10 flex flex-col items-center cursor-default group" data-step="2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold border-4 border-white transition-colors duration-300 step-circle">2</div>
                        <span class="text-xs font-semibold text-gray-500 mt-2 absolute top-10 whitespace-nowrap step-text">Personal Details</span>
                    </div>
                    <div class="step-indicator relative z-10 flex flex-col items-center cursor-default group" data-step="3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold border-4 border-white transition-colors duration-300 step-circle">3</div>
                        <span class="text-xs font-semibold text-gray-500 mt-2 absolute top-10 whitespace-nowrap step-text">Family Details</span>
                    </div>
                    <div class="step-indicator relative z-10 flex flex-col items-center cursor-default group" data-step="4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-500 font-bold border-4 border-white transition-colors duration-300 step-circle">4</div>
                        <span class="text-xs font-semibold text-gray-500 mt-2 absolute top-10 whitespace-nowrap step-text">Temple & Docs</span>
                    </div>
                </div>
            </div>

            <form id="registrationForm" method="POST" action="{{ route('registration.save.final') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-6 md:p-8 mt-12" data-aos="fade-up" data-aos-delay="200">
                @csrf
                <input type="hidden" name="registration_step" id="registration_step" value="{{ $user->registration_step ?? 1 }}">
                
                <!-- Section 1: Basic Information -->
                <div class="form-section mb-8 pb-4 border-b border-gray-200" data-step="1">
                    <h2 class="text-xl font-bold text-primary mb-4">Section 1: Basic Information</h2>
                    
                    <!-- Are You Digambar Jain -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Are You Digambar Jain? *</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="are_you_digambar_jain" value="yes" required class="mr-2" {{ (strtolower($user->are_you_digambar_jain) === 'yes') ? 'checked' : '' }}> Yes
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="are_you_digambar_jain" value="no" required class="mr-2" {{ (strtolower($user->are_you_digambar_jain) === 'no') ? 'checked' : '' }}> No
                            </label>
                        </div>
                    </div>
                    
                    <!-- Who is Filling This Form -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Who is filling this form? (यह फॉर्म कौन भर रहा है?) *</label>
                        <select name="filled_by" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                            <option value="">Select Option</option>
                            <option value="Candidate" {{ ($user->filled_by == 'Candidate') ? 'selected' : '' }}>Candidate (स्वयं प्रत्याशी)</option>
                            <option value="Father" {{ ($user->filled_by == 'Father') ? 'selected' : '' }}>Father (पिता)</option>
                            <option value="Mother" {{ ($user->filled_by == 'Mother') ? 'selected' : '' }}>Mother (माता)</option>
                            <option value="Brother" {{ ($user->filled_by == 'Brother') ? 'selected' : '' }}>Brother (भाई)</option>
                            <option value="Sister" {{ ($user->filled_by == 'Sister') ? 'selected' : '' }}>Sister (बहन)</option>
                            <option value="Guardian" {{ ($user->filled_by == 'Guardian') ? 'selected' : '' }}>Guardian (अभिभावक)</option>
                            <option value="Other" {{ ($user->filled_by == 'Other') ? 'selected' : '' }}>Other (अन्य)</option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Gender (लिंग) *</label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="male" required class="mr-2" {{ (strtolower($user->gender) === 'male') ? 'checked' : '' }}> Male (पुरुष)
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="gender" value="female" class="mr-2" {{ (strtolower($user->gender) === 'female') ? 'checked' : '' }}> Female (महिला)
                            </label>
                        </div>
                    </div>

                    <!-- Candidate Full Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Candidate Full Name (प्रत्याशी का नाम) *</label>
                        <input type="text" name="full_name" value="{{ $user->full_name }}" required placeholder="Enter candidate's full name" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                    </div>
                    
                    <!-- Mobile & Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mobile Number *</label>
                            <input type="tel" name="mobile" value="{{ preg_replace('/^\+?91/', '', $user->mobile) }}" required pattern="[0-9]{10}" maxlength="10" minlength="10" title="Please enter exactly 10 digits" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">10 digits only number</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email *</label>
                            <input type="email" name="email" value="{{ $user->email }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                    </div>

                    <!-- Section 1 Dynamic Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @if (!empty($customFieldsByGroup['Section 1: Basic Information']))
                            @foreach ($customFieldsByGroup['Section 1: Basic Information'] as $f)
                                {!! renderCustomFieldHTML($f, $customValues) !!}
                            @endforeach
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-end mt-6">
                        <button type="button" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg transition next-btn">Save & Continue <i class="fas fa-arrow-right ml-2"></i></button>
                    </div>
                </div>
                
                <!-- Section 2: Personal Details -->
                <div class="form-section mb-8 pb-4 border-b border-gray-200" data-step="2">
                    <h2 class="text-xl font-bold text-primary mb-4">Section 2: Personal Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Birth Date -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Birth Date *</label>
                            <input type="date" name="birth_date" value="{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : '' }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        
                        <!-- Birth Time -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Birth Time *</label>
                            @php
                                $db_time = $user->birth_time ?? '';
                                $bt_hh = ''; $bt_mm = ''; $bt_ampm = '';
                                if ($db_time) {
                                    if (preg_match('/([0-9]{1,2}):([0-9]{1,2})\s*(AM|PM)?/i', $db_time, $matches)) {
                                        $h = (int)$matches[1];
                                        $bt_mm = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                                        if (isset($matches[3]) && $matches[3]) {
                                            $bt_ampm = strtoupper($matches[3]);
                                            $bt_hh = str_pad($h, 2, '0', STR_PAD_LEFT);
                                        } else {
                                            if ($h >= 12) {
                                                $bt_ampm = 'PM';
                                                if ($h > 12) $h -= 12;
                                            } else {
                                                $bt_ampm = 'AM';
                                                if ($h == 0) $h = 12;
                                            }
                                            $bt_hh = str_pad($h, 2, '0', STR_PAD_LEFT);
                                        }
                                    }
                                }
                            @endphp
                            <div class="flex gap-2">
                                <select name="birth_time_hh" required class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                    <option value="">HH</option>
                                    @for($i=1; $i<=12; $i++)
                                        @php $val = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $val }}" {{ $bt_hh == $val ? 'selected' : '' }}>{{ $val }}</option>
                                    @endfor
                                </select>
                                <select name="birth_time_mm" required class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                    <option value="">MM</option>
                                    @for($i=0; $i<=59; $i++)
                                        @php $val = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $val }}" {{ $bt_mm == $val ? 'selected' : '' }}>{{ $val }}</option>
                                    @endfor
                                </select>
                                <select name="birth_time_ampm" required class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                    <option value="">AM/PM</option>
                                    <option value="AM" {{ $bt_ampm == 'AM' ? 'selected' : '' }}>AM</option>
                                    <option value="PM" {{ $bt_ampm == 'PM' ? 'selected' : '' }}>PM</option>
                                </select>
                            </div>
                        </div>

                        <!-- Birth Place & Native -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Birth Place *</label>
                            <input type="text" name="birth_place" value="{{ $user->birth_place }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Native (परिवार का मूल स्थान) *</label>
                            <input type="text" name="native" value="{{ $user->native_place }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>

                        <!-- Cast & Sub-Cast -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Cast (जाति) *</label>
                            @php
                                $castField = \App\Models\RegistrationField::where('field_key', 'cast')->first();
                                $db_casts = $castField && $castField->field_options 
                                    ? array_map('trim', explode(',', $castField->field_options))
                                    : ['Digambar Jain'];
                                $predefined_casts = array_filter($db_casts, function($val) {
                                    return strtolower($val) !== 'other';
                                });
                                $is_other_cast = !empty($user->cast) && !in_array($user->cast, $predefined_casts);
                            @endphp
                            <select name="cast" id="cast" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="">Select Cast</option>
                                @foreach ($predefined_casts as $c)
                                    <option value="{{ $c }}" {{ ($user->cast == $c) ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                                <option value="Other" {{ $is_other_cast ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="custom_cast" id="custom_cast" value="{{ $is_other_cast ? $user->cast : '' }}" placeholder="Please specify cast" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary mt-2 {{ $is_other_cast ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Sub-Cast (उपजाति)</label>
                            <select name="subcast" id="subcast" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
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
                                    
                                    $is_other_subcast = !empty($user->subcast) && !in_array($user->subcast, $predefined_subcasts);
                                @endphp
                                @foreach ($predefined_subcasts as $sc)
                                    <option value="{{ $sc }}" {{ $user->subcast == $sc ? 'selected' : '' }}>{{ $sc }}</option>
                                @endforeach
                                <option value="Other" {{ $is_other_subcast ? 'selected' : '' }}>Other (अन्य)</option>
                            </select>
                            <input type="text" name="custom_subcast" id="custom_subcast" value="{{ $is_other_subcast ? $user->subcast : '' }}" placeholder="Please specify sub-cast" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary mt-2 {{ $is_other_subcast ? '' : 'hidden' }}">
                        </div>

                        <!-- Gotra & Mama Gotra -->
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
                            $userGotra = $user->gotra ?? '';
                            $isCustomGotra = !empty($userGotra) && !in_array($userGotra, $gotraList);

                            $userMamaGotra = $user->mama_gotra ?? '';
                            $isCustomMamaGotra = !empty($userMamaGotra) && !in_array($userMamaGotra, $gotraList);
                        @endphp
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Gotra (गोत्र) *</label>
                            <select name="gotra" id="gotra" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="">Select Gotra</option>
                                @foreach ($gotraList as $g)
                                    <option value="{{ $g }}" {{ $userGotra === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                <option value="Others" {{ $isCustomGotra ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" name="custom_gotra" id="custom_gotra" value="{{ $isCustomGotra ? $userGotra : '' }}" placeholder="Specify Gotra" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary mt-2 {{ $isCustomGotra ? '' : 'hidden' }}">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mama Gotra (मामा का गोत्र) *</label>
                            <select name="mama_gotra" id="mama_gotra" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="">Select Mama Gotra</option>
                                @foreach ($gotraList as $g)
                                    <option value="{{ $g }}" {{ $userMamaGotra === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                <option value="Others" {{ $isCustomMamaGotra ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" name="custom_mama_gotra" id="custom_mama_gotra" value="{{ $isCustomMamaGotra ? $userMamaGotra : '' }}" placeholder="Specify Mama Gotra" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary mt-2 {{ $isCustomMamaGotra ? '' : 'hidden' }}">
                        </div>
                        
                        <!-- Manglik -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Manglik (मांगलिक) *</label>
                            <div class="flex gap-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="manglik" value="yes" required {{ (strtolower($user->manglik) === 'yes') ? 'checked' : '' }} class="mr-2"> Yes / हाँ
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="manglik" value="no" {{ (strtolower($user->manglik) === 'no') ? 'checked' : '' }} class="mr-2"> No / ना
                                </label>
                            </div>
                        </div>
                        
                        <!-- Height -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Height (ऊंचाई) *</label>
                            <select name="height" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="">Select Height</option>
                                @php
                                    $heights = [
                                        '4 ft 8 inch','4 ft 9 inch','4 ft 10 inch','4 ft 11 inch',
                                        '5 ft','5 ft 1 inch','5 ft 2 inch','5 ft 3 inch',
                                        '5 ft 4 inch','5 ft 5 inch','5 ft 6 inch','5 ft 7 inch',
                                        '5 ft 8 inch','5 ft 9 inch','5 ft 10 inch','5 ft 11 inch',
                                        '6 ft','6 ft 1 inch','6 ft 2 inch','6 ft 3 inch',
                                        '6 ft 4 inch','6 ft 5 inch'
                                    ];
                                @endphp
                                @foreach($heights as $h)
                                    <option value="{{ $h }}" {{ $user->height == $h ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Weight -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Weight *</label>
                            <select name="weight" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="">Select Weight (kg)</option>
                                @for($i=35; $i<=120; $i++)
                                    @php
                                        $w = $i . ' kg';
                                        $sel = ($user->weight == $i || $user->weight == $w) ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $w }}" {{ $sel }}>{{ $w }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <!-- Permanent Address & Pin Code -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Permanent Full Address (स्थायी पता) *</label>
                            <textarea name="permanent_address" id="permanent_address" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">{{ $user->permanent_address }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Pin Code of Permanent Address *</label>
                            <input type="text" name="pin_code" value="{{ $user->pin_code }}" pattern="[0-9]{4,6}" maxlength="6" minlength="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        
                        <!-- Same as Permanent Checkbox -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="same_as_permanent" name="same_as_permanent" value="1" class="mr-2 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-gray-700 font-medium">Current Address is same as Permanent Address (वर्तमान पता स्थायी पता जैसा ही है)</span>
                            </label>
                        </div>

                        <!-- Current Address -->
                        <div id="current_address_container" class="col-span-1 md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Candidate Current Address (वर्तमान पता) *</label>
                            <textarea name="current_address" id="current_address" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">{{ $user->current_address }}</textarea>
                        </div>

                        <!-- Education & Preference details -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Higher Education *</label>
                            <input type="text" name="education" value="{{ $user->higher_education }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Hobbies *</label>
                            <textarea name="hobbies" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">{{ $user->hobbies }}</textarea>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Your Specific Preference for the Partner *</label>
                            <textarea name="partner_preference" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">{{ $user->partner_preference }}</textarea>
                        </div>
                        
                        <!-- Marital Status & handicapped -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Marital Status *</label>
                            <select name="marital_status" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="Never Married" {{ ($user->marital_status == 'Never Married' || $user->marital_status == '') ? 'selected' : '' }}>Never Married</option>
                                <option value="Widow" {{ ($user->marital_status == 'Widow') ? 'selected' : '' }}>Widow</option>
                                <option value="Divorce" {{ ($user->marital_status == 'Divorce') ? 'selected' : '' }}>Divorce</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Handicapped / Physical Deficiency *</label>
                            <div class="flex gap-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="handicapped" value="yes" required {{ (strtolower($user->handicapped) === 'yes') ? 'checked' : '' }} class="mr-2"> Yes
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="handicapped" value="no" {{ (strtolower($user->handicapped) === 'no') ? 'checked' : '' }} class="mr-2"> No
                                </label>
                            </div>
                        </div>
                        
                        <!-- Languages Known -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Language Known *</label>
                            @php 
                                $curr_langs = !empty($user->languages) ? explode(',', $user->languages) : [];
                                $other_langs = array_diff($curr_langs, ['Gujarati', 'Hindi', 'English', 'Other']);
                                $has_other_lang = !empty($other_langs);
                            @endphp
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <label class="inline-flex items-center"><input type="checkbox" name="languages[]" value="Gujarati" {{ in_array('Gujarati', $curr_langs) ? 'checked' : '' }} class="mr-2"> Gujarati</label>
                                <label class="inline-flex items-center"><input type="checkbox" name="languages[]" value="Hindi" {{ in_array('Hindi', $curr_langs) ? 'checked' : '' }} class="mr-2"> Hindi</label>
                                <label class="inline-flex items-center"><input type="checkbox" name="languages[]" value="English" {{ in_array('English', $curr_langs) ? 'checked' : '' }} class="mr-2"> English</label>
                                <label class="inline-flex items-center"><input type="checkbox" name="languages[]" id="language_other_checkbox" value="Other" {{ $has_other_lang ? 'checked' : '' }} class="mr-2"> Other</label>
                            </div>
                            <input type="text" name="other_language" id="other_language_input" value="{{ $has_other_lang ? implode(',', $other_langs) : '' }}" placeholder="Specify other language" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 bg-white text-sm focus:border-primary {{ $has_other_lang ? '' : 'hidden' }}">
                        </div>

                        <!-- Occupation details -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Candidate Occupation (व्यवसाय) *</label>
                            @php $curr_occ = $user->occupation ?? ''; @endphp
                            <div class="flex gap-4 mt-2">
                                <label class="inline-flex items-center"><input type="radio" name="occupation" value="Job" required {{ $curr_occ == 'Job' ? 'checked' : '' }} class="mr-2"> Job</label>
                                <label class="inline-flex items-center"><input type="radio" name="occupation" value="Business" {{ $curr_occ == 'Business' ? 'checked' : '' }} class="mr-2"> Business</label>
                                <label class="inline-flex items-center"><input type="radio" name="occupation" value="Other" {{ ($curr_occ && $curr_occ != 'Job' && $curr_occ != 'Business') ? 'checked' : '' }} class="mr-2"> Other</label>
                            </div>
                            <input type="text" name="occupation_details" id="occupation_details" value="{{ ($curr_occ && $curr_occ != 'Job' && $curr_occ != 'Business') ? $curr_occ : '' }}" placeholder="Please specify occupation" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 bg-white text-sm focus:border-primary {{ ($curr_occ && $curr_occ != 'Job' && $curr_occ != 'Business') ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Candidate Annual Income (वार्षिक आय) *</label>
                            <input type="number" name="annual_income" value="{{ $user->monthly_income }}" min="0" step="1" required placeholder="Yearly income amount (e.g., 500000)" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Company/Firm Name (Optional)</label>
                            <input type="text" name="company_name" value="{{ $user->company_name }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Designation (Optional)</label>
                            <input type="text" name="designation" value="{{ $user->designation }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>

                        <!-- Section 2 Dynamic Fields -->
                        @if (!empty($customFieldsByGroup['Section 2: Personal Details']))
                            @foreach ($customFieldsByGroup['Section 2: Personal Details'] as $f)
                                <div class="col-span-1 md:col-span-2">
                                    {!! renderCustomFieldHTML($f, $customValues) !!}
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition prev-btn"><i class="fas fa-arrow-left mr-2"></i> Previous</button>
                        <button type="button" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg transition next-btn">Save & Continue <i class="fas fa-arrow-right ml-2"></i></button>
                    </div>
                </div>
                
                <!-- Section 3: Family Details -->
                <div class="form-section mb-8 pb-4 border-b border-gray-200" data-step="3">
                    <h2 class="text-xl font-bold text-primary mb-4">Section 3: Family Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Father Name *</label>
                            <input type="text" name="father_name" value="{{ $user->father_name }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Father Mobile Number *</label>
                            <input type="tel" name="father_mobile" value="{{ preg_replace('/^\+?91/', '', $user->father_mobile) }}" pattern="[0-9]{10}" maxlength="10" minlength="10" title="Please enter exactly 10 digits" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">10 digits only number</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Father Income (Optional)</label>
                            <input type="number" name="father_income" value="{{ $user->father_income }}" min="0" step="1" placeholder="Optional" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Father Occupation *</label>
                            @php $f_occ = $user->father_occupation ?? ''; @endphp
                            <select name="father_occupation" id="father_occupation" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="Job" {{ $f_occ == 'Job' ? 'selected' : '' }}>Job</option>
                                <option value="Business" {{ $f_occ == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Retired" {{ $f_occ == 'Retired' ? 'selected' : '' }}>Retired</option>
                                <option value="Other" {{ ($f_occ && $f_occ != 'Job' && $f_occ != 'Business' && $f_occ != 'Retired') ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="father_occupation_details" id="father_occupation_details" value="{{ ($f_occ && $f_occ != 'Job' && $f_occ != 'Business' && $f_occ != 'Retired') ? $f_occ : '' }}" placeholder="Please specify details" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 bg-white text-sm focus:border-primary {{ ($f_occ && $f_occ != 'Job' && $f_occ != 'Business' && $f_occ != 'Retired') ? '' : 'hidden' }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mother Name *</label>
                            <input type="text" name="mother_name" value="{{ $user->mother_name }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mother Mobile Number (Optional)</label>
                            <input type="tel" name="mother_mobile" value="{{ preg_replace('/^\+?91/', '', $user->mother_mobile) }}" pattern="[0-9]{10}" maxlength="10" minlength="10" title="Please enter exactly 10 digits" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">10 digits only number</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mother Occupation (Optional)</label>
                            @php $m_occ = $user->mother_occupation ?? ''; @endphp
                            <select name="mother_occupation" id="mother_occupation" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="House Wife" {{ $m_occ == 'House Wife' ? 'selected' : '' }}>House Wife</option>
                                <option value="Job" {{ $m_occ == 'Job' ? 'selected' : '' }}>Job</option>
                                <option value="Business" {{ $m_occ == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Other" {{ ($m_occ && $m_occ != 'House Wife' && $m_occ != 'Job' && $m_occ != 'Business') ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" name="mother_occupation_details" id="mother_occupation_details" value="{{ ($m_occ && $m_occ != 'House Wife' && $m_occ != 'Job' && $m_occ != 'Business') ? $m_occ : '' }}" placeholder="Please specify details" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 bg-white text-sm focus:border-primary {{ ($m_occ && $m_occ != 'House Wife' && $m_occ != '') ? '' : 'hidden' }}">
                        </div>

                        <!-- Brother / Sister Counts -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Brothers Married Count (Optional)</label>
                            <select name="brothers_married" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="0" {{ $user->brothers_married == 0 ? 'selected' : '' }}>0</option>
                                @for($i=1; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->brothers_married == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Brothers Unmarried Count (Optional)</label>
                            <select name="brothers_unmarried" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="0" {{ $user->brothers_unmarried == 0 ? 'selected' : '' }}>0</option>
                                @for($i=1; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->brothers_unmarried == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Total Brothers *</label>
                            <select name="brothers" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                @for($i=0; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->brothers == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Sisters Married Count (Optional)</label>
                            <select name="sisters_married" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="0" {{ $user->sisters_married == 0 ? 'selected' : '' }}>0</option>
                                @for($i=1; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->sisters_married == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Sisters Unmarried Count (Optional)</label>
                            <select name="sisters_unmarried" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                <option value="0" {{ $user->sisters_unmarried == 0 ? 'selected' : '' }}>0</option>
                                @for($i=1; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->sisters_unmarried == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Total Sisters *</label>
                            <select name="sisters" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                @for($i=0; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $user->sisters == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Section 3 Dynamic Fields -->
                        @if (!empty($customFieldsByGroup['Section 3: Family Details']))
                            @foreach ($customFieldsByGroup['Section 3: Family Details'] as $f)
                                <div class="col-span-1 md:col-span-2">
                                    {!! renderCustomFieldHTML($f, $customValues) !!}
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition prev-btn"><i class="fas fa-arrow-left mr-2"></i> Previous</button>
                        <button type="button" class="bg-primary hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg transition next-btn">Save & Continue <i class="fas fa-arrow-right ml-2"></i></button>
                    </div>
                </div>
                
                <!-- Section 4: Temple & Docs -->
                <div class="form-section mb-8 pb-4 border-b border-gray-200" data-step="4">
                    <h2 class="text-xl font-bold text-primary mb-1">Mandir / Community Verification</h2>
                    <p class="text-sm text-gray-600 mb-4">आपका परिवार किस दिगंबर जैन मंदिर से जुड़ा है.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Mandir details -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Temple Name (मंदिर का नाम) *</label>
                            <input type="text" name="mandir_name" value="{{ $user->mandir_name }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary" placeholder="Shri Digambar Jain Mandir">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Temple Pincode (मंदिर का पिनकोड) *</label>
                            <input type="text" name="mandir_pincode" value="{{ $user->mandir_pincode }}" pattern="[0-9]{4,6}" maxlength="6" minlength="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Temple Address (मंदिर का पता) *</label>
                            <textarea name="mandir_address" required rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">{{ $user->mandir_address }}</textarea>
                        </div>
                    </div>

                    <!-- References Container -->
                    <div id="referencePersonsContainer" class="mt-6 border-t border-dashed border-gray-200 pt-6">
                        <div class="mb-4 bg-blue-50/50 p-4 rounded-lg border border-primary/10">
                            <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                <i class="fas fa-users text-primary"></i> 2 Reference Persons from Same Mandir/Community
                            </h3>
                            <p class="text-sm text-gray-600">Please provide details of two people from your community or same mandir who can vouch for the candidate.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Reference Person 1 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <h4 class="font-bold text-primary mb-3 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-primary text-white text-xs font-semibold rounded-full flex items-center justify-center">1</span>
                                    Reference Person 1
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Full Name *</label>
                                        <input type="text" name="ref1_name" id="ref1_name" value="{{ $user->ref1_name }}" required class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Mobile Number *</label>
                                        <input type="tel" name="ref1_mobile" id="ref1_mobile" value="{{ preg_replace('/^\+?91/', '', $user->ref1_mobile) }}" required pattern="[0-9]{10}" maxlength="10" minlength="10" title="Exactly 10 digit mobile number" class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Relation *</label>
                                        <input type="text" name="ref1_relation" id="ref1_relation" value="{{ $user->ref1_relation }}" required class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            <!-- Reference Person 2 -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <h4 class="font-bold text-primary mb-3 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-primary text-white text-xs font-semibold rounded-full flex items-center justify-center">2</span>
                                    Reference Person 2
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Full Name *</label>
                                        <input type="text" name="ref2_name" id="ref2_name" value="{{ $user->ref2_name }}" required class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Mobile Number *</label>
                                        <input type="tel" name="ref2_mobile" id="ref2_mobile" value="{{ preg_replace('/^\+?91/', '', $user->ref2_mobile) }}" required pattern="[0-9]{10}" maxlength="10" minlength="10" title="Exactly 10 digit mobile number" class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 font-semibold mb-1">Relation *</label>
                                        <input type="text" name="ref2_relation" id="ref2_relation" value="{{ $user->ref2_relation }}" required class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:border-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mandir Details Custom Fields -->
                    @if (!empty($customFieldsByGroup['Section 4: Mandir Verification Details']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            @foreach ($customFieldsByGroup['Section 4: Mandir Verification Details'] as $f)
                                {!! renderCustomFieldHTML($f, $customValues) !!}
                            @endforeach
                        </div>
                    @endif

                    <!-- Photos Section -->
                    <div class="mt-6 border-t border-dashed border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-primary mb-4">Photos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Profile Photo -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Candidate Photo {{ !empty($user->profile_photo) ? '' : '*' }} (Passport size photo, max 10MB)</label>
                                @if(!empty($user->profile_photo))
                                    <div class="mb-2">
                                        <img src="{{ route('image.serve', ['file' => $user->profile_photo]) }}" class="w-24 h-24 object-cover border rounded" alt="Profile Photo">
                                    </div>
                                @endif
                                <input type="file" name="photo" accept="image/*" {{ !empty($user->profile_photo) ? '' : 'required' }} class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary">
                            </div>
                            
                            <!-- Family Photo -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Family Photo (Optional) (Max 10MB)</label>
                                @if(!empty($user->family_photo))
                                    <div class="mb-2">
                                        <img src="{{ route('image.serve', ['file' => $user->family_photo]) }}" class="w-32 h-24 object-cover border rounded" alt="Family Photo">
                                    </div>
                                @endif
                                <input type="file" name="family_photo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary">
                            </div>

                            <!-- Profile Photo Drive URL -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Profile Photo Drive URL (Optional)</label>
                                <input type="url" name="profile_photo_drive_url" value="{{ $user->profile_photo_drive_url }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                            </div>

                            <!-- ID Proof Type & File -->
                            <div class="col-span-1 md:col-span-2 border-t mt-4 pt-4">
                                <h3 class="text-lg font-bold text-primary mb-2">ID Proof Verification</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-2">Select ID Proof Type *</label>
                                        <select name="id_proof_type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                            <option value="">Select Option</option>
                                            <option value="Aadhaar Card" {{ $user->id_proof_type == 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card</option>
                                            <option value="PAN Card" {{ $user->id_proof_type == 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                            <option value="Voter ID" {{ $user->id_proof_type == 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
                                            <option value="Driving Licence" {{ $user->id_proof_type == 'Driving Licence' ? 'selected' : '' }}>Driving Licence</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-2">Upload ID Proof {{ !empty($user->id_proof_path) ? '' : '*' }} (Max 5MB)</label>
                                        @if(!empty($user->id_proof_path))
                                            <div class="mb-2">
                                                <a href="{{ route('image.serve', ['file' => $user->id_proof_path]) }}" target="_blank" class="text-blue-500 underline text-sm">View Current ID Proof</a>
                                            </div>
                                        @endif
                                        <input type="file" name="id_proof_path" accept="image/*,.pdf" {{ !empty($user->id_proof_path) ? '' : 'required' }} class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Photos Group Fields -->
                            @if (!empty($customFieldsByGroup['Photos']))
                                <div class="col-span-1 md:col-span-2">
                                    @foreach ($customFieldsByGroup['Photos'] as $f)
                                        {!! renderCustomFieldHTML($f, $customValues) !!}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Documents & Payment -->
                    <div class="mt-8 mb-8 pb-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-primary mb-2">Documents & Payment (Presently not compulsory)</h2>
                        <p class="text-gray-500 text-sm mb-4">You can optionally make a payment and upload the screenshot. This is not mandatory at the moment.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            
                            <!-- QR Code Display -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col items-center">
                                <h3 class="font-bold text-gray-700 mb-2">Payment QR Code</h3>
                                @if (strpos($payment_qr_code, 'data:image/') === 0)
                                    <img src="{{ $payment_qr_code }}" alt="Payment QR Code" class="w-48 h-48 border border-yellow-300 rounded shadow-sm object-cover bg-white animate-fade-in">
                                @else
                                    <img src="{{ route('image.serve', ['file' => $payment_qr_code]) }}" alt="Payment QR Code" class="w-48 h-48 border border-yellow-300 rounded shadow-sm object-cover bg-white animate-fade-in">
                                @endif
                                <p class="text-xs text-gray-500 mt-2 text-center">Scan to pay securely.</p>
                            </div>

                            <div class="space-y-4">
                                <!-- Select plan -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Select Subscription Plan</label>
                                    <select name="membership_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                        <option value="">Select Plan</option>
                                        @foreach($memberships as $plan)
                                            <option value="{{ $plan->id }}" {{ $user->payments()->where('status', 'pending')->value('membership_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->plan_name }} (₹{{ $plan->price }}, {{ $plan->duration_days }} Days)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Transaction ID -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Payment Transaction ID / Reference</label>
                                    <input type="text" name="payment_transaction_id" value="{{ $user->payment_transaction_id }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary" placeholder="Reference Number">
                                </div>

                                <!-- Screenshot upload -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Payment Screenshot (Transaction ID) (Optional)</label>
                                    @if (!empty($user->payment_screenshot))
                                        <div class="mb-2">
                                            <a href="{{ route('image.serve', ['file' => $user->payment_screenshot]) }}" target="_blank" class="text-blue-500 underline text-sm"><i class="fas fa-external-link-alt"></i> View Current Payment Screenshot</a>
                                        </div>
                                    @endif
                                    <input type="file" name="payment_screenshot" id="payment_screenshot" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white text-sm focus:border-primary">
                                </div>

                                <!-- Drive URL -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Payment Proof Drive URL (Optional)</label>
                                    <input type="url" name="payment_proof_drive_url" value="{{ $user->payment_proof_drive_url }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 focus:bg-white text-sm focus:border-primary">
                                </div>

                                <!-- Dynamic Documents & Payment custom fields -->
                                @if (!empty($customFieldsByGroup['Documents & Payment']))
                                    @foreach ($customFieldsByGroup['Documents & Payment'] as $f)
                                        {!! renderCustomFieldHTML($f, $customValues) !!}
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information dynamic fields -->
                    @if (!empty($customFieldsByGroup['Additional Information']))
                        <div class="mb-8 border-t border-dashed border-gray-200 pt-6">
                            <h2 class="text-xl font-bold text-primary mb-4">Additional Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($customFieldsByGroup['Additional Information'] as $f)
                                    {!! renderCustomFieldHTML($f, $customValues) !!}
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6">
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition prev-btn"><i class="fas fa-arrow-left mr-2"></i> Previous</button>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button id="submitBtn" type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-opacity-90 transition disabled:opacity-75 disabled:cursor-not-allowed">
                    {{ $user->status === 'approved' ? 'Update Profile' : 'Register Now' }}
                </button>
            </form>
        </div>
    </div>
</section>

<script>
const currentUserData = @json($user);

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const sections = Array.from(document.querySelectorAll('.form-section'));
    const stepIndicators = Array.from(document.querySelectorAll('.step-indicator'));
    const progressLine = document.getElementById('progressLine');
    const submitBtn = document.getElementById('submitBtn');
    
    let currentStep = parseInt(document.getElementById('registration_step').value) || 1;
    let maxUnlockedStep = parseInt('{{ $user->registration_step ?? 1 }}') || 1;
    if (currentStep > sections.length) currentStep = sections.length;
    if (currentStep < 1) currentStep = 1;
    if (maxUnlockedStep > sections.length) maxUnlockedStep = sections.length;
    if (currentStep > maxUnlockedStep) maxUnlockedStep = currentStep;

    function showStep(step) {
        sections.forEach((sec, idx) => {
            sec.style.display = (idx + 1 === step) ? 'block' : 'none';
        });

        stepIndicators.forEach((ind, idx) => {
            const circle = ind.querySelector('.step-circle');
            const text = ind.querySelector('.step-text');
            if (idx + 1 < step) {
                circle.classList.remove('bg-gray-200', 'text-gray-500', 'bg-primary', 'text-white');
                circle.classList.add('bg-green-500', 'text-white');
                circle.innerHTML = '<i class="fas fa-check"></i>';
                text.classList.remove('text-gray-500', 'text-primary');
                text.classList.add('text-green-500');
            } else if (idx + 1 === step) {
                circle.classList.remove('bg-gray-200', 'text-gray-500', 'bg-green-500');
                circle.classList.add('bg-primary', 'text-white');
                circle.innerHTML = (idx + 1);
                text.classList.remove('text-gray-500', 'text-green-500');
                text.classList.add('text-primary');
            } else {
                circle.classList.remove('bg-primary', 'bg-green-500', 'text-white');
                circle.classList.add('bg-gray-200', 'text-gray-500');
                circle.innerHTML = (idx + 1);
                text.classList.remove('text-primary', 'text-green-500');
                text.classList.add('text-gray-500');
            }
        });

        const progressPercent = ((step - 1) / (sections.length - 1)) * 100;
        progressLine.style.width = progressPercent + '%';

        submitBtn.style.display = (step === sections.length) ? 'block' : 'none';
    }

    async function autoSave(stepIndex) {
        const formData = new FormData(form);
        formData.append('registration_step', stepIndex);

        let saveUrl = "";
        if (stepIndex === 1) saveUrl = "{{ route('registration.save.basic') }}";
        else if (stepIndex === 2) saveUrl = "{{ route('registration.save.personal') }}";
        else if (stepIndex === 3) saveUrl = "{{ route('registration.save.family') }}";
        else if (stepIndex === 4) saveUrl = "{{ route('registration.save.final') }}";

        try {
            const res = await fetch(saveUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (res.status === 419) {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired for security reasons. Please refresh the page to continue.',
                    icon: 'warning',
                    confirmButtonText: 'Refresh Page'
                }).then(() => {
                    window.location.reload();
                });
                return false;
            }

            const data = await res.json();
            if (!data.success) {
                Swal.fire({
                    title: 'Error Saving Progress',
                    text: data.message || 'There was a problem saving your data. Please check your inputs.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                return false;
            }
            return true;
        } catch (err) {
            console.error('Network error during auto save:', err);
            Swal.fire({
                title: 'Network Error',
                text: 'Could not connect to the server to save your progress.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
            return false;
        }
    }

    // Attach click events to step indicators
    stepIndicators.forEach((ind) => {
        ind.classList.remove('cursor-default');
        ind.classList.add('cursor-pointer', 'hover:opacity-80', 'transition-opacity');
        ind.addEventListener('click', async () => {
            const targetStep = parseInt(ind.getAttribute('data-step'));
            
            if (targetStep <= maxUnlockedStep) {
                if (targetStep === currentStep) return;
                
                const currentSec = sections[currentStep - 1];
                let isValid = true;
                
                if (targetStep > currentStep) {
                    const inputs = currentSec.querySelectorAll('input, select, textarea');
                    for (let input of inputs) {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            isValid = false;
                            break;
                        }
                    }
                }
                
                if (isValid) {
                    const originalHtml = ind.innerHTML;
                    ind.style.opacity = '0.5';
                    
                    const isSaved = await autoSave(currentStep);
                    ind.style.opacity = '1';
                    
                    if (isSaved || targetStep < currentStep) {
                        currentStep = targetStep;
                        document.getElementById('registration_step').value = currentStep;
                        showStep(currentStep);
                        window.scrollTo({ top: document.getElementById('progressBarContainer').offsetTop - 20, behavior: 'smooth' });
                    }
                }
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Step Locked',
                    text: 'Please complete the previous steps first to unlock this section.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    // Attach next/prev buttons click event handlers
    sections.forEach((sec, idx) => {
        const nextBtn = sec.querySelector('.next-btn');
        if (nextBtn) {
            nextBtn.addEventListener('click', async () => {
                const inputs = sec.querySelectorAll('input, select, textarea');
                let isValid = true;
                for (let input of inputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        isValid = false;
                        break;
                    }
                }
                
                if (isValid) {
                    const originalHtml = nextBtn.innerHTML;
                    nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    nextBtn.disabled = true;

                    const targetStep = idx + 2;
                    const isSaved = await autoSave(idx + 1); // saves current step
                    
                    nextBtn.innerHTML = originalHtml;
                    nextBtn.disabled = false;
                    
                    if (isSaved) {
                        currentStep = targetStep;
                        if (currentStep > maxUnlockedStep) maxUnlockedStep = currentStep;
                        document.getElementById('registration_step').value = currentStep;
                        showStep(currentStep);
                        window.scrollTo({ top: document.getElementById('progressBarContainer').offsetTop - 20, behavior: 'smooth' });
                    }
                }
            });
        }

        const prevBtn = sec.querySelector('.prev-btn');
        if (prevBtn) {
            prevBtn.addEventListener('click', async () => {
                const originalHtml = prevBtn.innerHTML;
                prevBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
                prevBtn.disabled = true;

                const targetStep = idx;
                await autoSave(idx + 1); // saves current step

                prevBtn.innerHTML = originalHtml;
                prevBtn.disabled = false;

                currentStep = targetStep;
                document.getElementById('registration_step').value = currentStep;
                showStep(currentStep);
                window.scrollTo({ top: document.getElementById('progressBarContainer').offsetTop - 20, behavior: 'smooth' });
            });
        }
    });

    // Initialize step view
    showStep(currentStep);

    // Prevent form submission on Enter
    form.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            if (currentStep < sections.length) {
                e.preventDefault();
                const nextBtn = sections[currentStep - 1].querySelector('.next-btn');
                if (nextBtn) nextBtn.click();
            }
        }
    });

    // --- sessionStorage State Persistence ---
    const savedData = sessionStorage.getItem("registrationFormData");
    const currentName = currentUserData ? currentUserData.full_name : "";
    const currentMobile = currentUserData ? currentUserData.mobile.replace(/^\+?91/, '') : "";

    if (savedData) {
        try {
            const parsed = JSON.parse(savedData);
            if (parsed['full_name'] !== currentName || parsed['mobile'] !== currentMobile) {
                sessionStorage.removeItem("registrationFormData");
            }
        } catch(e) {
            sessionStorage.removeItem("registrationFormData");
        }
    }

    const freshData = sessionStorage.getItem("registrationFormData");
    if (freshData) {
        try {
            const data = JSON.parse(freshData);
            Object.keys(data).forEach(key => {
                const input = form.elements[key];
                if (input) {
                    if (input.readOnly || input.hasAttribute('readonly')) return;
                    if (input instanceof RadioNodeList || (input.length && input[0].type === 'radio')) {
                        Array.from(input).forEach(radio => {
                            if (radio.value === data[key]) radio.checked = true;
                        });
                    } else if (input.type === 'checkbox') {
                        if (Array.isArray(data[key])) {
                            input.checked = data[key].includes(input.value);
                        } else {
                            input.checked = (data[key] === input.value || data[key] === true);
                        }
                    } else if (input.type !== 'file') {
                        let val = data[key];
                        if (['mobile', 'father_mobile', 'mother_mobile', 'ref1_mobile', 'ref2_mobile'].includes(key) && typeof val === 'string') {
                            val = val.replace(/^\+?91/, '');
                        }
                        input.value = val;
                    }
                }
            });
            document.querySelectorAll('select').forEach(el => el.dispatchEvent(new Event('change')));
            document.querySelectorAll('input[type="radio"]:checked').forEach(el => el.dispatchEvent(new Event('change')));
        } catch(e) {
            console.error(e);
        }
    }

    form.addEventListener("input", function(e) {
        if (e.target.type === 'file') return;
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                if (!Array.isArray(data[key])) {
                    data[key] = [data[key]];
                }
                data[key].push(value);
            } else {
                data[key] = value;
            }
        }
        sessionStorage.setItem("registrationFormData", JSON.stringify(data));
    });

    // --- Input Restrictions ---
    const nameFields = ['full_name', 'father_name', 'mother_name', 'ref1_name', 'ref2_name'];
    const phoneFields = ['mobile', 'father_mobile', 'mother_mobile', 'ref1_mobile', 'ref2_mobile'];

    nameFields.forEach(name => {
        const field = document.querySelector(`input[name="${name}"]`);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Z\s\.]/g, '');
            });
        }
    });

    phoneFields.forEach(name => {
        const field = document.querySelector(`input[name="${name}"]`);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });

    const pinCodeFields = ['pin_code', 'mandir_pincode'];
    pinCodeFields.forEach(name => {
        const field = document.querySelector(`input[name="${name}"]`);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });
        }
    });

    // --- Sibling total counts ---
    function updateSiblingTotal(type) {
        const marriedSelect = document.querySelector(`select[name="${type}_married"]`);
        const unmarriedSelect = document.querySelector(`select[name="${type}_unmarried"]`);
        const totalSelect = document.querySelector(`select[name="${type}"]`);
        
        if (marriedSelect && unmarriedSelect && totalSelect) {
            const married = parseInt(marriedSelect.value) || 0;
            const unmarried = parseInt(unmarriedSelect.value) || 0;
            let total = married + unmarried;
            if (total > 5) total = 5;
            totalSelect.value = total;
            totalSelect.setAttribute('readonly', true);
            totalSelect.classList.add('bg-gray-100');
        }
    }
    
    ['brothers', 'sisters'].forEach(type => {
        const married = document.querySelector(`select[name="${type}_married"]`);
        const unmarried = document.querySelector(`select[name="${type}_unmarried"]`);
        if (married) married.addEventListener('change', () => updateSiblingTotal(type));
        if (unmarried) unmarried.addEventListener('change', () => updateSiblingTotal(type));
        
        const total = document.querySelector(`select[name="${type}"]`);
        if(total) {
            total.addEventListener('mousedown', function(e) {
                e.preventDefault();
            });
        }
    });

    // --- Address copy checkbox ---
    document.getElementById('same_as_permanent')?.addEventListener('change', function() {
        const currentAddress = document.getElementById('current_address');
        const permanentAddress = document.getElementById('permanent_address');
        if (this.checked) {
            currentAddress.value = permanentAddress.value;
        } else {
            currentAddress.value = '';
        }
    });

    document.getElementById('permanent_address')?.addEventListener('input', function() {
        const sameCheckbox = document.getElementById('same_as_permanent');
        if (sameCheckbox && sameCheckbox.checked) {
            document.getElementById('current_address').value = this.value;
        }
    });

    // --- Digambar warning check ---
    document.querySelectorAll('input[name="are_you_digambar_jain"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const formElements = document.querySelectorAll('#registrationForm input:not([name="are_you_digambar_jain"]), #registrationForm select, #registrationForm textarea, #registrationForm button');
            if (this.value === 'no') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: 'Sorry, this registration is strictly for Digambar Jains only.'
                });
                formElements.forEach(el => el.disabled = true);
                document.getElementById('registrationForm').classList.add('opacity-50');
            } else {
                formElements.forEach(el => el.disabled = false);
                document.getElementById('registrationForm').classList.remove('opacity-50');
            }
        });
    });

    // --- Other value inputs SweetAlert dialogs ---
    function handleOtherWithSwal(element, hiddenInputId, otherValue) {
        const hiddenInput = document.getElementById(hiddenInputId);
        let isOtherSelected = (element.type === 'radio' || element.type === 'checkbox') ? (element.checked && element.value === otherValue) : (element.value === otherValue);

        if (isOtherSelected) {
            if (!hiddenInput.value) {
                Swal.fire({
                    title: 'Please Specify Details',
                    input: 'text',
                    inputPlaceholder: 'Enter details here...',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write something!'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        hiddenInput.value = result.value;
                        hiddenInput.classList.remove('hidden');
                        hiddenInput.required = true;
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    } else {
                        if (element.type === 'radio' || element.type === 'checkbox') {
                            element.checked = false;
                        } else {
                            element.selectedIndex = 0;
                        }
                        hiddenInput.classList.add('hidden');
                        hiddenInput.required = false;
                        hiddenInput.value = '';
                    }
                });
            } else {
                hiddenInput.classList.remove('hidden');
                hiddenInput.required = true;
            }
        } else {
            if ((element.type === 'radio' && element.checked) || element.type === 'select-one' || (element.type === 'checkbox' && !element.checked)) {
                hiddenInput.classList.add('hidden');
                hiddenInput.required = false;
                hiddenInput.value = '';
            }
        }
    }

    document.getElementById('language_other_checkbox')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'other_language_input', 'Other');
    });

    document.getElementById('cast')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'custom_cast', 'Other');
    });

    document.getElementById('subcast')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'custom_subcast', 'Other');
    });

    document.getElementById('gotra')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'custom_gotra', 'Others');
    });

    document.getElementById('mama_gotra')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'custom_mama_gotra', 'Others');
    });

    document.querySelectorAll('input[name="occupation"]').forEach(radio => {
        radio.addEventListener('change', function() {
            handleOtherWithSwal(this, 'occupation_details', 'Other');
        });
    });

    document.getElementById('father_occupation')?.addEventListener('change', function() {
        handleOtherWithSwal(this, 'father_occupation_details', 'Other');
    });

    document.getElementById('mother_occupation')?.addEventListener('change', function() {
        const detailsInput = document.getElementById('mother_occupation_details');
        if (this.value !== 'House Wife' && this.value !== '') {
            detailsInput.classList.remove('hidden');
            detailsInput.required = true;
        } else {
            detailsInput.classList.add('hidden');
            detailsInput.required = false;
            detailsInput.value = '';
        }
    });

    // --- Final Form Submission & Mobile duplication checks ---
    form.addEventListener('submit', function(e) {
        if (currentStep !== sections.length) {
            e.preventDefault();
            return false;
        }

        const isDigambar = document.querySelector('input[name="are_you_digambar_jain"]:checked')?.value;
        if (isDigambar === 'no') {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Attention', text: 'Sorry, this registration is strictly for Digambar Jains only.'});
            return false;
        }

        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Checking...';

        const mobileInput = document.querySelector('input[name="mobile"]');
        
        fetch("{{ route('registration.check-mobile') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mobile: mobileInput.value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'duplicate') {
                submitBtn.innerHTML = 'Register Now';
                submitBtn.disabled = false;
                Swal.fire({
                    title: 'Already Registered',
                    text: 'This mobile number is already registered.\nPlease enter a different mobile number.',
                    icon: 'warning',
                    confirmButtonColor: '#eab308'
                });
            } else {
                // If everything is OK, do standard form submission
                submitBtn.innerHTML = 'Processing... <i class="fas fa-spinner fa-spin ml-2"></i>';
                sessionStorage.removeItem("registrationFormData");
                form.submit();
            }
        })
        .catch(err => {
            sessionStorage.removeItem("registrationFormData");
            form.submit();
        });
    });
});
</script>
@endsection
