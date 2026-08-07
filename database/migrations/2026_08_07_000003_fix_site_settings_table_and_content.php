<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            $legacyColumns = ['about_us', 'privacy_policy', 'terms_conditions'];
            $hasLegacyColumns = false;

            // Check if any legacy columns exist
            foreach ($legacyColumns as $col) {
                if (Schema::hasColumn('site_settings', $col)) {
                    $hasLegacyColumns = true;
                }
            }

            $legacyData = [];
            if ($hasLegacyColumns) {
                // Read the first row's legacy column values
                $firstRow = DB::table('site_settings')->first();
                if ($firstRow) {
                    foreach ($legacyColumns as $col) {
                        if (property_exists($firstRow, $col)) {
                            $legacyData[$col] = $firstRow->$col;
                        }
                    }
                }
            }

            // Create/update setting rows for key-value settings
            // 1. About Us
            $aboutUsVal = $legacyData['about_us'] ?? null;
            
            // Look for existing setting in key-value row
            $existingAboutUs = DB::table('site_settings')->where('setting_key', 'about_us')->value('setting_value');
            if ($existingAboutUs) {
                $aboutUsVal = $existingAboutUs;
            }

            $hindiAboutUs = '<p class="font-bold text-xl text-primary">सादर जय जिनेंद्र,</p>
<p>धर्म, समाज, संस्कृति और राष्ट्र की प्रतिष्ठा को अक्षुण्ण बनाए रखने में विवाह की अत्यंत महत्वपूर्ण भूमिका होती है। विवाह न केवल दो व्यक्तियों का मिलन है, बल्कि यह आगामी पीढ़ियों के निर्माण और सुदृढ़ीकरण का मुख्य आधार है। यदि उचित समय पर सही वर-वधू का चयन कर विवाह संपन्न हो, तो निश्चित ही समाज और राष्ट्र को एक स्वस्थ और सुसंस्कृत स्वरूप प्राप्त होगा।</p>
<p>हमारी संस्था द्वारा विगत 5 वर्षों से समग्र दिगंबर जैन समाज के योग्य युवक-युवतियों के लिए \'परिचय सम्मेलन\' का आयोजन अहमदाबाद में अत्यंत सफलतापूर्वक किया जा रहा है। यह गुजरात राज्य का सबसे सफल and प्रतिष्ठित आयोजन माना जाता है। आज के आधुनिक परिवेश में योग्य वर-वधू का चयन बेहद जटिल और कठिन कार्य हो गया है, इसी बात को ध्यान में रखकर समिति निरंतर इस दिशा में कार्यरत है।</p>
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
<p>हमारी संस्था ने सकल दिगम्बर जैन समाज के लिये ही यह बीड़ा उठाया है। हमारी संस्था का उद्देश्य केवल यही है कि समाज के बच्चों का विवाह समाज मे ही समय पर हो जाये, हमारी संस्था विगत 5 वर्षो से अहमदाबाद मे सकल दिगम्बर जैन समाज के विवाह योग्य युवक युवतियो का परिचय सम्मेलन बहुत ही सफलता पूर्वक आयोजित कर रही है। संस्था का उद्देश्य कभी भी पैसा कमाना नही रहा। समिति के सभी सद्स्य अपना अमूल्य समय देकर इस कार्य को समाज हित मे कर रहे हैं।</p>';

            // Overwrite if it is empty, placeholder or invalid
            if (empty(trim(strip_tags($aboutUsVal))) || str_contains($aboutUsVal, 'Our Committee Initiatives') || str_contains($aboutUsVal, 'development')) {
                $aboutUsVal = $hindiAboutUs;
            }

            DB::table('site_settings')->updateOrInsert(
                ['setting_key' => 'about_us'],
                ['setting_value' => $aboutUsVal]
            );

            // 2. Terms Conditions
            $termsVal = $legacyData['terms_conditions'] ?? null;
            $existingTerms = DB::table('site_settings')->where('setting_key', 'terms_conditions')->value('setting_value');
            if ($existingTerms) {
                $termsVal = $existingTerms;
            }

            if (empty(trim(strip_tags($termsVal)))) {
                $defaultTermsPath = base_path('../digambar-samaj/includes/default_terms.php');
                if (file_exists($defaultTermsPath)) {
                    require $defaultTermsPath;
                    if (isset($default_terms)) {
                        $termsVal = $default_terms;
                    }
                }
            }
            if ($termsVal) {
                DB::table('site_settings')->updateOrInsert(
                    ['setting_key' => 'terms_conditions'],
                    ['setting_value' => $termsVal]
                );
            }

            // 3. Privacy Policy
            $privacyVal = $legacyData['privacy_policy'] ?? null;
            $existingPrivacy = DB::table('site_settings')->where('setting_key', 'privacy_policy')->value('setting_value');
            if ($existingPrivacy) {
                $privacyVal = $existingPrivacy;
            }

            if (empty(trim(strip_tags($privacyVal)))) {
                $defaultPrivacyPath = base_path('../digambar-samaj/includes/default_privacy.php');
                if (file_exists($defaultPrivacyPath)) {
                    require $defaultPrivacyPath;
                    if (isset($default_privacy)) {
                        $privacyVal = $default_privacy;
                    }
                }
            }
            if ($privacyVal) {
                DB::table('site_settings')->updateOrInsert(
                    ['setting_key' => 'privacy_policy'],
                    ['setting_value' => $privacyVal]
                );
            }

            // Drop columns if they exist
            Schema::table('site_settings', function (Blueprint $table) use ($legacyColumns) {
                foreach ($legacyColumns as $col) {
                    if (Schema::hasColumn('site_settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('site_settings', 'about_us')) {
                    $table->longText('about_us')->nullable();
                }
                if (!Schema::hasColumn('site_settings', 'privacy_policy')) {
                    $table->longText('privacy_policy')->nullable();
                }
                if (!Schema::hasColumn('site_settings', 'terms_conditions')) {
                    $table->longText('terms_conditions')->nullable();
                }
            });
        }
    }
};
