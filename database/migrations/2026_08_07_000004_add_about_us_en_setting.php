<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $englishAboutUs = '<p>Marriage plays an important role in maintaining the prestige of religion, society, culture, and the nation. Marriage is not just a union of two individuals but also plays a vital role in building future generations. If the right girl and boy are selected at the right time and the marriage takes place, it will certainly create a healthy environment for society and the country.</p>
<p>For the past 5 years, our organization has been very successfully organizing the Parichay Sammelan for the young men and women of the entire Digambar Jain community in Ahmedabad. This is the most successful event in the state of Gujarat. Today, selecting a suitable girl and boy in society has become very complex and difficult. Keeping this in mind, this program is organized by the committee. In the same series, taking a step forward, our organization has created this website so that a suitable life partner is always available for the young men and women of the society. We have full faith that through this website we will be successful in fulfilling your aspirations.</p>
<p>I convey my best wishes for the bright future of all the parents and loving young men and women registering on this website and hope that everyone\'s search for a life partner will definitely be fulfilled through this website.</p>
<p>Our main objective is that a Jain\'s marriage should happen within Jainism and Jain religious values should be maintained in our children.</p>';

        Setting::updateOrCreate(
            ['setting_key' => 'about_us_en'],
            ['setting_value' => $englishAboutUs]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('setting_key', 'about_us_en')->delete();
    }
};
