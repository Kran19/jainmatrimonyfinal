@extends('layouts.app')

@section('title', 'Committee Members')

@section('content')
<!-- Page Banner -->
<section class="relative h-48 md:h-64 bg-cover bg-center flex items-center justify-center text-center px-4"
    style="background-image: url('{{ asset('assets/images/about-us-img.jpeg') }}');">
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
</section>

<!-- Language Toggle -->
<div class="container mx-auto px-4 max-w-6xl mt-8 flex justify-end">
    <button id="langToggleBtn" onclick="toggleLanguage()"
        class="bg-primary text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-opacity-90 transition flex items-center gap-2">
        <i class="fas fa-language"></i> <span id="langToggleText">Translate to English</span>
    </button>
</div>

<!-- Committee Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Quote Section -->
        <div class="text-center mb-16">
            <div id="committeeQuoteHi">
                <h1 class="text-3xl md:text-4xl font-extrabold text-dark mb-4" id="pageTitleHi">कार्यकारिणी समिति</h1>
                <p class="text-primary font-bold text-lg mb-3">- दिगम्बर जैन परिचय सम्मेलन समिति अहमदाबाद</p>
                <p class="text-gray-600 max-w-4xl mx-auto italic text-lg leading-relaxed font-medium">
                    "स्थापना काल से ही समिति के पाँचों सदस्य इस संस्था को आगे ले जाने में जुटे हुए हैं। सभी सदस्यों के सामूहिक प्रयासों और आपसी तालमेल का ही परिणाम है कि संस्था आज इस गौरवशाली मुकाम पर खड़ी है। यह पारस्परिक सामंजस्य ही हमारी संस्था का मुख्य आधार स्तंभ है।"
                </p>
            </div>
            
            <div id="committeeQuoteEn" class="hidden">
                <h1 class="text-3xl md:text-4xl font-extrabold text-dark mb-4" id="pageTitleEn">Executive Committee</h1>
                <p class="text-primary font-bold text-lg mb-3">- Digambar Jain Parichay Sammelan Samiti Ahmedabad</p>
                <p class="text-gray-600 max-w-4xl mx-auto italic text-lg leading-relaxed font-medium">
                    "Since its inception, all five members of the committee have been dedicated to taking this organization forward. It is the result of their collective efforts and mutual coordination that the organization stands at this glorious stage today. This mutual harmony is the main pillar of our organization."
                </p>
            </div>
            
            <div class="w-24 h-1 bg-primary mx-auto mt-6 rounded-full"></div>
        </div>

        <!-- Members Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
            @forelse($committeeMembers as $member)
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 p-6 flex flex-col h-full items-center text-center">
                
                <!-- Member Image -->
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-primary/20 shadow-md mb-6 flex-shrink-0 transition-transform duration-300 hover:scale-105">
                    @if($member->photo)
                        @php
                            $imgSrc = (str_starts_with($member->photo, 'data:') || str_starts_with($member->photo, 'http')) ? $member->photo : asset($member->photo);
                        @endphp
                        <img src="{{ $imgSrc }}" alt="{{ $member->name_en ?? $member->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-user text-4xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Member Name -->
                <h3 class="font-bold text-xl text-dark lang-hi-el mb-1">
                    {{ $member->name }}
                </h3>
                <h3 class="font-bold text-xl text-dark lang-en-el mb-1 hidden">
                    {{ $member->name_en ?? $member->name }}
                </h3>

                <!-- Designation -->
                <p class="text-primary font-bold text-sm mb-4 lang-hi-el">
                    {{ $member->designation ?? 'समिति सदस्य' }}
                </p>
                <p class="text-primary font-bold text-sm mb-4 lang-en-el hidden">
                    {{ $member->designation_en ?? $member->designation ?? 'Committee Member' }}
                </p>

                <!-- Description -->
                @if($member->description)
                <p class="text-gray-600 text-sm leading-relaxed text-justify grow lang-hi-el">
                    {{ $member->description }}
                </p>
                @endif
                
                @if($member->description_en || $member->description)
                <p class="text-gray-600 text-sm leading-relaxed text-justify grow lang-en-el hidden">
                    {{ $member->description_en ?? $member->description }}
                </p>
                @endif

            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fa-solid fa-users-slash text-5xl text-slate-300 mb-4 block"></i>
                No committee members found.
            </div>
            @endforelse
        </div>
        
        <!-- Setting/CMS initiative content placeholder if any exists -->
        @if(!empty($community_content))
        <div class="mt-16 pt-12 border-t border-gray-100">
            {!! $community_content !!}
        </div>
        @endif

    </div>
</section>

<script>
    let currentLang = 'hi';
    function toggleLanguage() {
        const toggleBtnText = document.getElementById('langToggleText');

        const hiElements = [
            document.getElementById('pageTitleHi'),
            document.getElementById('committeeQuoteHi'),
            ...document.querySelectorAll('.lang-hi-el')
        ].filter(Boolean);

        const enElements = [
            document.getElementById('pageTitleEn'),
            document.getElementById('committeeQuoteEn'),
            ...document.querySelectorAll('.lang-en-el')
        ].filter(Boolean);

        if (currentLang === 'hi') {
            hiElements.forEach(el => el.classList.add('hidden'));
            enElements.forEach(el => el.classList.remove('hidden'));
            toggleBtnText.innerText = 'हिंदी में अनुवाद करें';
            currentLang = 'en';
        } else {
            enElements.forEach(el => el.classList.add('hidden'));
            hiElements.forEach(el => el.classList.remove('hidden'));
            toggleBtnText.innerText = 'Translate to English';
            currentLang = 'hi';
        }
    }
</script>
@endsection
