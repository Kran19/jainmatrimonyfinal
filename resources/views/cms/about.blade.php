@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<!-- Page Banner -->
<section class="relative h-72 md:h-[500px] bg-cover bg-center"
    style="background-image: url('{{ asset('assets/images/about-us-img.jpeg') }}');">
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="relative h-full flex items-center justify-center text-center px-4">
    </div>
</section>

<!-- Language Toggle -->
<div class="container mx-auto px-4 max-w-6xl mt-8 flex justify-end">
    <button id="langToggleBtn" onclick="toggleLanguage()"
        class="bg-primary text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-opacity-90 transition flex items-center gap-2">
        <i class="fas fa-language"></i> <span id="langToggleText">Translate to English</span>
    </button>
</div>

<!-- About Content -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4" id="aboutTitleHi">हमारे बारे में</h2>
            <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4 hidden" id="aboutTitleEn">About Us</h2>
            <div class="w-16 h-1 bg-primary mb-6"></div>

            <!-- Content -->
            <!-- Hindi Content -->
            <div id="aboutContentHi" class="text-gray-600 leading-relaxed text-lg space-y-4 text-justify">
                @if (!empty($about_us))
                    {!! $about_us !!}
                @else
                    <p class="font-bold text-xl text-primary">सादर जय जिनेंद्र,</p>
                    <p>धर्म, समाज, संस्कृति और राष्ट्र की प्रतिष्ठा को अक्षुण्ण बनाए रखने में विवाह की अत्यंत महत्वपूर्ण भूमिका होती है। विवाह न केवल दो व्यक्तियों का मिलन है, बल्कि यह आगामी पीढ़ियों के निर्माण और सुदृढ़ीकरण का मुख्य आधार है। यदि उचित समय पर सही वर-वधू का चयन कर विवाह संपन्न हो, तो निश्चित ही समाज और राष्ट्र को एक स्वस्थ और सुसंस्कृत स्वरूप प्राप्त होगा।</p>
                    <p>हमारी संस्था द्वारा विगत 5 वर्षों से समग्र दिगंबर जैन समाज के योग्य युवक-युवतियों के लिए 'परिचय सम्मेलन' का आयोजन अहमदाबाद में अत्यंत सफलतापूर्वक किया जा रहा है। यह गुजरात राज्य का सबसे सफल and प्रतिष्ठित आयोजन माना जाता है। आज के आधुनिक परिवेश में योग्य वर-वधू का चयन बेहद जटिल और कठिन कार्य हो गया है, इसी बात को ध्यान में रखकर समिति निरंतर इस दिशा में कार्यरत है।</p>
                    <p>इसी श्रृंखला में एक कदम और आगे बढ़ाते हुए हमारी संस्था ने इस आधुनिक वेबसाइट का निर्माण किया है, ताकि समाज के युवक-युवतियों के लिए सुयोग्य जीवनसाथी की खोज हर समय सुलभ और आसान हो सके। हमें पूर्ण विश्वास है कि इस डिजिटल माध्यम से हम आपकी आकांक्षाओं को पूर्ण करने में पूरी तरह सफल होंगे।</p>
                    <p>मैं इस वेबसाइट पर पंजीकृत होने वाले सभी अभिभावकों और स्नेही युवक-युवतियों के उज्ज्वल भविष्य के लिए अपनी मंगलकामनाएं प्रेषित करता हूँ। आशा है कि सुयोग्य जीवनसाथी की आपकी तलाश इस मंच के माध्यम से अवश्य पूर्ण होगी।</p>
                    <p class="font-semibold text-primary">हमारा मुख्य उद्देश्य और संकल्प यही है कि— "जैन की शादी जैन में ही हो" ताकि हमारी आने वाली पीढ़ी में जैन धर्म के मूल संस्कार और संस्कृति जीवंत बनी रहे।</p>
                    <p class="font-bold mt-4">शुभकामनाओं सहित,</p>
                    <p class="font-bold text-primary">दिगम्बर जैन परिचय सम्मेलन समिति अहमदाबाद</p>
                    
                    <hr class="my-8 border-gray-200">
                    
                    <p>हमारी वेबसाइट “दिगम्बर जैन परिचय” केवल एक वैवाहिक परिचय का मंच नहीं है, बल्कि दिगम्बर जैन समाज की समृद्ध सांस्कृतिक विरासत एवं आध्यात्मिक मूल्यों पर आधारित एक विश्वसनीय परिवार है। हमारा मानना है कि विवाह केवल जीवनसाथी की खोज नहीं, बल्कि ऐसे दो व्यक्तित्वों का पवित्र मिलन है जिनके जीवन के लक्ष्य, संस्कार, मूल्य और विचार समान हों।</p>
                    <p>सार्थक एवं दीर्घकालिक वैवाहिक संबंध स्थापित करने की हमारी प्रतिबद्धता हमारी प्रत्येक सेवा में दिखाई देती है। सत्यापित एवं विस्तृत प्रोफ़ाइल डेटाबेस तथा पारदर्शी प्रक्रिया के माध्यम से हम आपको आपके लिए उपयुक्त जीवनसाथी खोजने के लिये एक माध्यम प्रदान कर रहे हैं। यह सिर्फ दिगम्बर जैन समाज के विवाह योग्य बच्चों के सम्बंध खोजने के लिये एकमात्र वेबसाइट है।</p>
                    <p>जीवनसाथी का चयन जीवन के सबसे महत्वपूर्ण निर्णयों में से एक है। “दिगम्बर जैन परिचय” वेबसाइट इस महत्वपूर्ण यात्रा के प्रत्येक चरण में आपके साथ खड़ी है। हम आपको 100% सत्यापित एवं विस्तृत प्रोफ़ाइल और डेटाबेस अपने समाज के विवाह योग्य बच्चो के इस माध्यम से उपलब्ध कराते हैं, जिनकी सहायता से आप ऐसा जीवनसाथी चुन सकें जो आपके मूल्यों, जीवन-दृष्टि और भविष्य के सपनों के अनुरूप हो। हमारा मुख्य उद्देश्य यही है कि जैन की शादी जैन मे हो और हमारे बच्चो मे दिगम्बर जैन परम्परा के संस्कार बने रहें और अक्षुण बनी रहे।</p>
                    <p>वर्तमान समय की परिस्थितियों एवं बदलते परिवेश में मनुष्य के पास समय का अभाव है। वह दिन-रात अपने परिवार के खुशहाल जीवन यापन एवं उनके भविष्य को उज्जवल बनाने के लिए प्रयासरत रहता है। जीवन की इसी भागदौड़ में वह यह भी भूल जाता है कि उसके बच्चे बड़े एवं विवाह योग्य हो गए हैं। जब उसे इस बात का ध्यान आता है बिंदु तो वह अच्छे संबंध की तलाश करना शुरु करता है और यही से उसकी परेशानी शुरू होती है। आज का समय पहले जैसा नही रहा कि अपने परिचित / रिश्तेदार ही सम्बंध बता देते थे अब किसी का भी सहयोग इस कार्य मे नही के बराबर हो गया है।</p>
                    <p>हमारी संस्था ने सकल दिगम्बर जैन समाज के लिये ही यह बीड़ा उठाया है। हमारी संस्था का उद्देश्य केवल यही है कि समाज के बच्चों का विवाह समाज मे ही समय पर हो जाये, हमारी संस्था विगत 5 वर्षो से अहमदाबाद मे सकल दिगम्बर जैन समाज के विवाह योग्य युवक युवतियो का परिचय सम्मेलन बहुत ही सफलता पूर्वक आयोजित कर रही है। संस्था का उद्देश्य कभी भी पैसा कमाना नही रहा। समिति के सभी सद्स्य अपना अमूल्य समय देकर इस कार्य को समाज हित मे कर रहे हैं।</p>
                @endif
            </div>

            <!-- English Content -->
            <div id="aboutContentEn" class="text-gray-600 leading-relaxed text-lg space-y-4 hidden">
                @if (!empty($about_us_en))
                    {!! $about_us_en !!}
                @else
                    <p>Marriage plays an important role in maintaining the prestige of religion, society, culture, and the nation. Marriage is not just a union of two individuals but also plays a vital role in building future generations. If the right girl and boy are selected at the right time and the marriage takes place, it will certainly create a healthy environment for society and the country.</p>
                    <p>For the past 5 years, our organization has been very successfully organizing the Parichay Sammelan for the young men and women of the entire Digambar Jain community in Ahmedabad. This is the most successful event in the state of Gujarat. Today, selecting a suitable girl and boy in society has become very complex and difficult. Keeping this in mind, this program is organized by the committee. In the same series, taking a step forward, our organization has created this website so that a suitable life partner is always available for the young men and women of the society. We have full faith that through this website we will be successful in fulfilling your aspirations.</p>
                    <p>I convey my best wishes for the bright future of all the parents and loving young men and women registering on this website and hope that everyone's search for a life partner will definitely be fulfilled through this website.</p>
                    <p>Our main objective is that a Jain's marriage should happen within Jainism and Jain religious values should be maintained in our children.</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Disclosure Section -->
<section class="py-12 bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4 max-w-4xl text-center" data-aos="fade-up">
        <h2 class="text-2xl font-bold text-dark mb-4" id="disclosureTitleHi">महत्वपूर्ण सूचना (Disclosure)</h2>
        <h2 class="text-2xl font-bold text-dark mb-4 hidden" id="disclosureTitleEn">Disclosure</h2>
        <div class="w-16 h-1 bg-primary mx-auto mb-6"></div>

        <!-- Hindi Disclosure -->
        <div id="disclosureContentHi"
            class="text-gray-600 leading-relaxed space-y-4 bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100 text-left">
            <p>दिगम्बर जैन परिचय वेबसाईट पर प्रत्याशियों से सम्बंधित सभी जानकारियों की सत्यता आप स्वयं अपने व्यक्तिगत सूत्रो से अवश्य प्राप्त करें| इस वेबसाईट मे दी गयी सभी जानकारी प्रत्याशियों द्वारा डिजिटली स्वयं भरी गयी है वेबसाईट मे दिये गये विवरण के लिये प्रत्याशी और अभिभावक स्वयं जिम्मेवार है, अतः प्रत्याशी के किसी भी विवरण के लिये समिति उत्तरदायी नही होगी|</p>
            <p>आयोजन समिति द्वारा यह कार्य समाज के हित मे पूर्ण निस्वार्थ भाव से किया जा रहा है। हमारा पूर्ण प्रयास रहा है कि किसी भी समाज जन को कोई भी परेशानी नही हो और हमारी सिर्फ यही भावना है की सभी को उचित वर – वधु शीघ्र मिलें</p>
            <p class="font-bold text-primary mt-4">दिगम्बर जैन परिचय सम्मेलन समिति अहमदाबाद</p>
        </div>

        <!-- English Disclosure -->
        <div id="disclosureContentEn"
            class="text-gray-600 leading-relaxed space-y-4 bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100 text-left hidden">
            <p>You must verify the authenticity of all the information related to the candidates on the Digambar Jain Parichay website through your own personal sources. All the information provided on this website has been filled digitally by the candidates themselves. The candidates and parents are themselves responsible for the details provided in the website, hence the committee will not be responsible for any details of the candidate.</p>
            <p>This work is being done by the organizing committee in the interest of the society completely selflessly. It has been our constant endeavor that no society member faces any trouble and our only sentiment is that everyone finds a suitable girl/boy soon.</p>
            <p class="font-bold text-primary mt-4">Digambar Jain Parichay Sammelan Samiti Ahmedabad</p>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-dark mb-3">Our Core Philosophy</h2>
            <p class="text-gray-600">Built on the foundation of trust, purity, and lifelong commitment.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition"
                data-aos="fade-up">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                    <i class="fas fa-bullseye text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark">Our Mission</h3>
                <p class="text-gray-600 text-sm leading-relaxed">To provide a highly secure and culturally tailored platform for Digambar Jains to find compatible life partners while strictly preserving our religious heritage.</p>
            </div>

            <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition"
                data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                    <i class="fas fa-eye text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark">Our Vision</h3>
                <p class="text-gray-600 text-sm leading-relaxed">To be the most trusted, respected, and globally recognized matrimony platform exclusively serving the entire Digambar Jain diaspora across the world.</p>
            </div>

            <div class="text-center p-8 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition"
                data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                    <i class="fas fa-praying-hands text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-dark">Our Values</h3>
                <p class="text-gray-600 text-sm leading-relaxed">We operate on the principles of Ahimsa (Non-violence), Satya (Truth), and transparency, ensuring every profile is authenticated for a safe experience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="bg-primary rounded-2xl p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-10 rounded-full"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white opacity-10 rounded-full"></div>

            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-4">Why Choose Us?</h2>
                    <p class="mb-6 text-white/90">We go beyond simple matchmaking. We build communities and foster relationships that last lifetimes.</p>
                    <ul class="space-y-4">
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-secondary"></i> 100% Verified Mobile Numbers</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-secondary"></i> Exclusive to Digambar Jain Community</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-secondary"></i> Advanced Gotra & Astrological Filtering</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-secondary"></i> Complete Privacy & Photo Protection</li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="inline-block mt-8 bg-white text-primary font-bold px-8 py-3 rounded hover:bg-gray-100 transition shadow-lg">Register Free Now</a>
                </div>
                <div>
                    <img src="{{ asset('assets/images/aboutus-img2.jpg') }}" alt="Why Choose Us"
                        class="rounded-xl shadow-2xl border-4 border-white/20 transform rotate-3">
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let currentLang = 'hi';
    function toggleLanguage() {
        const toggleBtnText = document.getElementById('langToggleText');

        const hiElements = [
            document.getElementById('aboutTitleHi'),
            document.getElementById('aboutContentHi'),
            document.getElementById('disclosureTitleHi'),
            document.getElementById('disclosureContentHi')
        ].filter(Boolean);

        const enElements = [
            document.getElementById('aboutTitleEn'),
            document.getElementById('aboutContentEn'),
            document.getElementById('disclosureTitleEn'),
            document.getElementById('disclosureContentEn')
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

