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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('profile_id', 20)->nullable()->unique();
                $table->string('full_name', 255);
                $table->string('email', 255)->nullable()->unique();
                $table->string('mobile', 20);
                $table->string('country_code', 10)->nullable();
                $table->string('password_hash', 255)->nullable();
                $table->enum('are_you_digambar_jain', ['Yes', 'No'])->default('Yes');
                
                // Cast / Religion
                $table->string('cast', 100)->nullable();
                $table->string('subcast', 100)->nullable();
                $table->string('custom_subcast', 100)->nullable();
                
                // Address
                $table->text('permanent_address')->nullable();
                $table->string('pin_code', 10)->nullable();
                $table->text('current_address')->nullable();
                
                // Family
                $table->string('father_name', 255)->nullable();
                $table->string('father_mobile', 20)->nullable();
                $table->decimal('father_income', 12, 2)->nullable();
                $table->string('father_occupation', 100)->nullable();
                $table->string('mother_name', 255)->nullable();
                $table->string('mother_mobile', 20)->nullable();
                $table->string('mother_occupation', 100)->nullable();
                $table->string('mother_occupation_details', 255)->nullable();
                $table->integer('brothers')->default(0);
                $table->integer('brothers_married')->default(0);
                $table->integer('brothers_unmarried')->default(0);
                $table->integer('sisters')->default(0);
                $table->integer('sisters_married')->default(0);
                $table->integer('sisters_unmarried')->default(0);

                // Mandir / Community Verification
                $table->string('mandir', 255)->nullable();
                $table->string('custom_mandir', 255)->nullable();
                $table->string('mandir_name', 255)->nullable();
                $table->text('mandir_address')->nullable();
                $table->string('mandir_pincode', 10)->nullable();

                // References
                $table->string('ref1_name', 255)->nullable();
                $table->string('ref1_mobile', 20)->nullable();
                $table->string('ref1_relation', 100)->nullable();
                $table->string('ref2_name', 255)->nullable();
                $table->string('ref2_mobile', 20)->nullable();
                $table->string('ref2_relation', 100)->nullable();

                $table->string('filled_by', 50)->nullable();
                $table->string('id_proof_type', 100)->nullable();
                $table->string('id_proof_path', 500)->nullable();
                
                // Profile details
                $table->enum('gender', ['Male', 'Female'])->nullable();
                $table->date('birth_date')->nullable();
                $table->string('birth_time', 20)->nullable();
                $table->string('birth_place', 255)->nullable();
                $table->string('native_place', 255)->nullable();
                $table->string('gotra', 255)->nullable();
                $table->string('mama_gotra', 255)->nullable();
                $table->enum('manglik', ['Yes', 'No'])->nullable();
                $table->string('height', 20)->nullable();
                $table->decimal('weight', 5, 2)->nullable();
                $table->enum('marital_status', ['Never Married', 'Widow', 'Widower', 'Divorce'])->default('Never Married');
                $table->enum('handicapped', ['Yes', 'No'])->default('No');
                
                $table->text('higher_education')->nullable();
                $table->string('occupation', 255)->nullable();
                $table->string('company_name', 255)->nullable();
                $table->string('designation', 255)->nullable();
                $table->decimal('monthly_income', 12, 2)->nullable();
                
                $table->text('languages')->nullable();
                $table->text('hobbies')->nullable();
                $table->text('partner_preference')->nullable();
                
                $table->string('profile_photo', 255)->nullable();
                $table->string('family_photo', 255)->nullable();
                $table->text('profile_photo_drive_url')->nullable();
                $table->string('payment_screenshot', 255)->nullable();
                $table->text('payment_proof_drive_url')->nullable();
                $table->string('payment_transaction_id', 100)->nullable();
                $table->enum('payment_status', ['pending', 'approved', 'rejected'])->default('pending');
                
                $table->enum('status', ['account_pending', 'account_approved', 'pending', 'approved', 'rejected', 'blocked'])->default('account_pending');
                $table->boolean('verified')->default(false);
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->date('featured_until')->nullable();
                $table->boolean('has_set_password')->default(false);
                $table->enum('registration_source', ['website', 'google_form', 'admin'])->default('website');
                $table->boolean('is_public')->default(true);
                
                $table->softDeletes();
                $table->timestamps();
                
                $table->index('status');
                $table->index('gender');
                $table->index('gotra');
                $table->index('native_place');
                $table->index('marital_status');
                $table->index('mobile');
                $table->index('email');
                $table->index('featured_until');
                $table->index('birth_date');
                $table->index('verified');
                $table->index('is_public');
                $table->index('approved_by');
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('local', 'testing')) {
            Schema::dropIfExists('users');
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('sessions');
        }
    }
};
