<?php

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: text/plain');

try {
    echo "Running migrations on production...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output() . "\n";
    
    // Seed committee members if empty
    $count = \Illuminate\Support\Facades\DB::table('committee_members')->count();
    if ($count === 0) {
        echo "Seeding initial committee members...\n";
        \Illuminate\Support\Facades\DB::table('committee_members')->insert([
            [
                'name' => 'नरेन्द्र जैन',
                'name_en' => 'Narendra Jain',
                'designation' => 'Committee Member',
                'designation_en' => 'Committee Member',
                'description' => 'केमीकल के सफल व्यवसायी, धार्मिक और बहुत सारी संस्थाओं से सम्बंधित श्री नरेंद्र जी जैन इस संस्था के बहुत ही मजबूत स्तम्भ मे से एक है इस संस्था के प्रारम्भ से ही वह अपना योगदान दे रहे हैं|',
                'description_en' => 'A successful chemical businessman, religious and associated with many organizations, Mr. Narendra Jain is one of the very strong pillars of this organization. He has been contributing since the beginning.',
                'photo' => 'assets/images/narendra jain.png',
                'sort_order' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'मनोज जैन (M. COM, LLB, ACS)',
                'name_en' => 'Manoj Jain (M. COM, LLB, ACS)',
                'designation' => 'Committee Member',
                'designation_en' => 'Committee Member',
                'description' => 'श्री मनोज जैन जी 30 वर्षों का अनुभव रखने वाले वरिष्ठ कंपनी सेक्रेटरी हैं, जो अहमदाबाद की एक रियल एस्टेट कंपनी में CFO और CS के रूप में कार्यरत हैं। सामाजिक कार्यों के प्रति समर्पित, श्री जैन इस संस्था से इसके शुरुआती दिनों से ही जुड़े हुए हैं। संस्था द्वारा दी गई हर जिम्मेदारी को उन्होंने हमेशा समय पर और सफलतापूर्वक पूरा किया है।',
                'description_en' => 'Mr. Manoj Jain is a senior Company Secretary with 30 years of experience, currently working as CFO and CS in a real estate company in Ahmedabad. Dedicated to social work, Mr. Jain has been associated with this organization since its early days and successfully fulfills all responsibilities.',
                'photo' => 'assets/images/manoj jain.jpeg',
                'sort_order' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'श्री दर्शन जैन वखारिया',
                'name_en' => 'Darshan Jain Vakharia',
                'designation' => 'Committee Member',
                'designation_en' => 'Committee Member',
                'description' => 'श्री दर्शन जी इमीग्रेशन वीसा कंसल्टेंट है और साथ मे बहुत ही सामजिक और धार्मिक व्यक्ति है वह बहुत सारी संस्थाओं से जुड़ें हुये है दिगम्बर जैन समाज के परिचय सम्मेलन का सपना उनका ही था जिसको यह संस्था उनके साथ प्रारम्भ से कर रही है',
                'description_en' => 'An immigration visa consultant and a very social and religious person, he is associated with many organizations. The dream of the Parichay Sammelan was his, which this organization has been fulfilling with him since the beginning.',
                'photo' => 'assets/images/darshan jain.jpeg',
                'sort_order' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'मिलेश दोशी',
                'name_en' => 'Milesh Doshi',
                'designation' => 'Committee Member',
                'designation_en' => 'Committee Member',
                'description' => 'श्री मिलेशभाई कम्पुटर सोफ्ट्वेयर और हार्डवेयर व्यवसायी है, सभी धार्मिक कार्यो और मुनि भक्ति मे सबसे अग्रणी रहते है इस संस्था के प्रारम्भ से ही वह अपना योगदान दे रहे हैं|',
                'description_en' => 'Mr. Mileshbhai is a computer software and hardware businessman. He is at the forefront of all religious activities and devotion to monks. He has been contributing to this organization since its inception.',
                'photo' => 'assets/images/milesh.png',
                'sort_order' => 4,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'जितेंद्र शाह',
                'name_en' => 'Jitendra Shah',
                'designation' => 'Committee Member',
                'designation_en' => 'Committee Member',
                'description' => 'श्री जितेंद्र जी का प्रिंटिंग का बहुत ही बड़ा कार्य है, सभी सामजिक और धार्मिक कार्यो मे हमेशा अपना योगदान देते है इस संस्था के प्रारम्भ से ही वह अपना योगदान दे रहे हैं|',
                'description_en' => 'Mr. Jitendra has a large printing business. He always contributes to social and religious activities and has been contributing to this organization since the beginning.',
                'photo' => 'assets/images/Jitendra Shah.png',
                'sort_order' => 5,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        echo "Seeding completed successfully.\n";
    } else {
        echo "Table already has $count rows. Skipping seed.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

unlink(__FILE__); // self delete after running
