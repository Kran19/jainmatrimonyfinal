<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. admins
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('email', 150)->unique();
                $table->string('password_hash', 255);
                $table->enum('role', ['super_admin', 'admin', 'moderator'])->default('admin');
                $table->boolean('status')->default(true);
                $table->dateTime('last_login')->nullable();
                $table->string('last_login_ip', 45)->nullable();
                $table->dateTime('password_updated_at')->nullable();
                $table->boolean('two_factor_enabled')->default(false);
                $table->timestamps();

                $table->index('status');
                $table->index('role');
                $table->index('last_login');
            });
        }

        // 2. user_addresses (Unused in functional code, kept for compatibility)
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->text('permanent_address')->nullable();
                $table->string('permanent_pin_code', 20)->nullable();
                $table->text('current_address')->nullable();
                $table->string('current_pin_code', 20)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('country', 100)->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 3. family_details (Unused in functional code, kept for compatibility)
        if (!Schema::hasTable('family_details')) {
            Schema::create('family_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('father_name', 255)->nullable();
                $table->string('father_mobile', 20)->nullable();
                $table->decimal('father_income', 12, 2)->nullable();
                $table->string('father_occupation', 255)->nullable();
                $table->string('mother_name', 255)->nullable();
                $table->string('mother_mobile', 20)->nullable();
                $table->string('mother_occupation', 255)->nullable();
                $table->integer('brothers')->default(0);
                $table->integer('brothers_married')->default(0);
                $table->integer('brothers_unmarried')->default(0);
                $table->integer('sisters')->default(0);
                $table->integer('sisters_married')->default(0);
                $table->integer('sisters_unmarried')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 4. memberships
        if (!Schema::hasTable('memberships')) {
            Schema::create('memberships', function (Blueprint $table) {
                $table->id();
                $table->string('plan_name', 100);
                $table->decimal('price', 10, 2);
                $table->integer('duration_days');
                $table->integer('contact_limit')->default(0);
                $table->boolean('featured_profile')->default(false);
                $table->boolean('priority_support')->default(false);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 5. user_memberships
        if (!Schema::hasTable('user_memberships')) {
            Schema::create('user_memberships', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('membership_id');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
                $table->boolean('can_view_contacts')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('membership_id')->references('id')->on('memberships');
                $table->unique(['user_id', 'membership_id', 'status'], 'idx_user_membership_active');
            });
        }

        // 6. payments
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('membership_id')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->string('transaction_id', 255)->nullable();
                $table->string('payment_method', 100)->nullable();
                $table->text('payment_remarks')->nullable();
                $table->string('payment_screenshot', 255)->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->unsignedBigInteger('verified_by')->nullable();
                
                // Form backup columns
                $table->string('full_name', 255)->nullable();
                $table->string('phone_number', 20)->nullable();
                $table->string('email', 255)->nullable();
                $table->text('address')->nullable();
                $table->date('dob')->nullable();
                
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('verified_by')->references('id')->on('admins')->onDelete('set null');
                $table->index('status', 'idx_payments_status');
                $table->index('transaction_id', 'idx_payments_transaction');
            });
        }

        // 7. contact_messages
        if (!Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->string('mobile', 20)->nullable();
                $table->string('subject', 255)->nullable();
                $table->text('message')->nullable();
                $table->enum('status', ['unread', 'read', 'replied'])->default('unread');
                $table->text('reply_text')->nullable();
                $table->timestamps();
            });
        }

        // 8. success_stories
        if (!Schema::hasTable('success_stories')) {
            Schema::create('success_stories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('couple_name', 255)->nullable();
                $table->date('engagement_date')->nullable();
                $table->date('marriage_date')->nullable();
                $table->longText('story')->nullable();
                $table->text('photo')->nullable(); // LONGTEXT in some places, varchar in database.sql
                $table->enum('status', ['pending', 'approved'])->default('pending');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 9. community_events
        if (!Schema::hasTable('community_events')) {
            Schema::create('community_events', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->longText('description')->nullable();
                $table->date('event_date')->nullable();
                $table->string('location', 255)->nullable();
                $table->string('banner', 255)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 10. advertisements
        if (!Schema::hasTable('advertisements')) {
            Schema::create('advertisements', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->longText('image')->nullable(); // LONGTEXT for Base64 compatibility
                $table->string('link', 255)->nullable();
                $table->enum('position', ['home_top', 'home_bottom', 'sidebar', 'left_sidebar', 'right_sidebar', 'bottom_banner'])->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 11. site_settings
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key', 100)->unique();
                $table->longText('setting_value')->nullable();
                $table->timestamps();
            });
        }

        // 12. activity_logs
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->enum('user_type', ['admin', 'user'])->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 255)->nullable();
                $table->text('details')->nullable();
                $table->string('ip_address', 100)->nullable();
                $table->timestamps();
            });
        }

        // 13. import_history
        if (!Schema::hasTable('import_history')) {
            Schema::create('import_history', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 50)->nullable();
                $table->integer('imported_records')->nullable();
                $table->unsignedBigInteger('imported_by')->nullable();
                $table->timestamp('import_date')->useCurrent();
                $table->timestamps();

                $table->foreign('imported_by')->references('id')->on('admins')->onDelete('set null');
            });
        }

        // 14. members (Import Layer Table)
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->string('full_name', 255);
                $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
                $table->date('birth_date')->nullable();
                $table->time('birth_time')->nullable();
                $table->string('birth_place', 255)->nullable();
                $table->string('native', 100)->nullable();
                $table->string('gotra', 100)->nullable();
                $table->string('mama_gotra', 100)->nullable();
                $table->enum('manglik', ['yes', 'no'])->nullable();
                $table->unsignedSmallInteger('height_cm')->nullable();
                $table->decimal('weight_kg', 5, 2)->nullable();
                
                $table->string('country_code', 5)->nullable();
                $table->string('mobile_number', 20)->nullable();
                $table->string('email', 255)->nullable()->unique();
                $table->text('permanent_address')->nullable();
                $table->char('permanent_pin_code', 6)->nullable();
                $table->text('current_address')->nullable();
                
                $table->text('higher_education')->nullable();
                $table->string('occupation', 100)->nullable();
                $table->string('company_name', 255)->nullable();
                $table->string('designation', 100)->nullable();
                $table->decimal('monthly_income', 12, 2)->nullable();
                
                $table->string('father_name', 255)->nullable();
                $table->string('father_mobile', 20)->nullable();
                $table->string('father_occupation', 100)->nullable();
                $table->decimal('father_monthly_income', 12, 2)->nullable();
                $table->string('mother_name', 255)->nullable();
                $table->string('mother_mobile', 20)->nullable();
                $table->string('mother_occupation', 100)->nullable();
                $table->unsignedTinyInteger('brothers_total')->nullable();
                $table->unsignedTinyInteger('brothers_married')->nullable();
                $table->unsignedTinyInteger('brothers_unmarried')->nullable();
                $table->unsignedTinyInteger('sisters_total')->nullable();
                $table->unsignedTinyInteger('sisters_married')->nullable();
                $table->unsignedTinyInteger('sisters_unmarried')->nullable();
                
                $table->text('partner_preferences')->nullable();
                $table->text('languages_known')->nullable();
                $table->text('hobbies')->nullable();
                $table->enum('widow_divorce', ['widow', 'divorcee', 'none'])->nullable();
                $table->text('handicapped_physical_deficiency')->nullable();
                
                $table->string('profile_photo_path', 500)->nullable();
                $table->timestamps();
            });
        }

        // 15. import_images
        if (!Schema::hasTable('import_images')) {
            Schema::create('import_images', function (Blueprint $table) {
                $table->id();
                $table->string('image_type', 50);
                $table->string('member_name_key', 255)->nullable();
                $table->string('file_name', 255);
                $table->string('file_path', 500);
                $table->bigInteger('file_size_bytes')->nullable();
                $table->timestamps();
            });
        }

        // 16. otp_verifications
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255);
                $table->string('otp_code', 6);
                $table->dateTime('expires_at');
                $table->boolean('verified')->default(false);
                $table->timestamps();

                $table->index('email', 'idx_otp_email');
            });
        }

        // 17. password_resets (Fallback table used by Core PHP)
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255);
                $table->string('token', 255);
                $table->timestamp('created_at')->useCurrent();

                $table->index('email');
            });
        }

        // 18. account_requests
        if (!Schema::hasTable('account_requests')) {
            Schema::create('account_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->enum('request_type', ['deactivation', 'deletion'])->default('deletion');
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 19. registration_fields
        if (!Schema::hasTable('registration_fields')) {
            Schema::create('registration_fields', function (Blueprint $table) {
                $table->id();
                $table->string('field_group', 100)->default('Basic Details');
                $table->string('field_key', 100)->unique();
                $table->string('field_label', 255);
                $table->string('field_type', 50)->default('text');
                $table->text('field_options')->nullable();
                $table->boolean('is_custom')->default(false);
                $table->boolean('is_visible')->default(true);
                $table->boolean('is_required')->default(false);
                $table->boolean('is_core')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 20. user_custom_data
        if (!Schema::hasTable('user_custom_data')) {
            Schema::create('user_custom_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('field_id');
                $table->text('field_value')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('field_id')->references('id')->on('registration_fields')->onDelete('cascade');
                $table->unique(['user_id', 'field_id'], 'unique_user_field');
            });
        }

        // 21. user_likes
        if (!Schema::hasTable('user_likes')) {
            Schema::create('user_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('liked_user_id');
                $table->timestamps();

                $table->unique(['user_id', 'liked_user_id'], 'unique_like');
            });
        }

        // 22. committee_members
        if (!Schema::hasTable('committee_members')) {
            Schema::create('committee_members', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('designation', 150)->nullable();
                $table->text('description')->nullable();
                $table->string('photo', 255)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 23. user_relatives
        if (!Schema::hasTable('user_relatives')) {
            Schema::create('user_relatives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('relation', 100)->nullable();
                $table->string('name', 255)->nullable();
                $table->string('mobile', 20)->nullable();
                $table->string('occupation', 150)->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 24. scrolling_news
        if (!Schema::hasTable('scrolling_news')) {
            Schema::create('scrolling_news', function (Blueprint $table) {
                $table->id();
                $table->string('content', 500);
                $table->string('link', 255)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 25. video_gallery
        if (!Schema::hasTable('video_gallery')) {
            Schema::create('video_gallery', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->enum('video_type', ['youtube', 'mp4'])->default('youtube');
                $table->string('video_url', 255)->nullable();
                $table->string('video_file', 255)->nullable();
                $table->string('thumbnail', 255)->nullable();
                $table->text('description')->nullable();
                $table->integer('display_order')->default(0);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // 26. news
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('content')->nullable();
                $table->longText('image')->nullable(); // LONGTEXT for Base64 compatibility
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 27. gallery
        if (!Schema::hasTable('gallery')) {
            Schema::create('gallery', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('category', 100)->default('All Photos');
                $table->longText('image_path')->nullable(); // LONGTEXT for Base64 compatibility
                $table->enum('media_type', ['image', 'pdf', 'video', 'youtube'])->default('image');
                $table->string('media_url', 500)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 28. marquee_ads
        if (!Schema::hasTable('marquee_ads')) {
            Schema::create('marquee_ads', function (Blueprint $table) {
                $table->id();
                $table->text('notice_text')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('local', 'testing')) {
            Schema::dropIfExists('marquee_ads');
            Schema::dropIfExists('gallery');
            Schema::dropIfExists('news');
            Schema::dropIfExists('video_gallery');
            Schema::dropIfExists('scrolling_news');
            Schema::dropIfExists('user_relatives');
            Schema::dropIfExists('committee_members');
            Schema::dropIfExists('user_likes');
            Schema::dropIfExists('user_custom_data');
            Schema::dropIfExists('registration_fields');
            Schema::dropIfExists('account_requests');
            Schema::dropIfExists('password_resets');
            Schema::dropIfExists('otp_verifications');
            Schema::dropIfExists('import_images');
            Schema::dropIfExists('members');
            Schema::dropIfExists('import_history');
            Schema::dropIfExists('activity_logs');
            Schema::dropIfExists('site_settings');
            Schema::dropIfExists('advertisements');
            Schema::dropIfExists('community_events');
            Schema::dropIfExists('success_stories');
            Schema::dropIfExists('contact_messages');
            Schema::dropIfExists('payments');
            Schema::dropIfExists('user_memberships');
            Schema::dropIfExists('memberships');
            Schema::dropIfExists('family_details');
            Schema::dropIfExists('user_addresses');
            Schema::dropIfExists('admins');
        }
    }
};
