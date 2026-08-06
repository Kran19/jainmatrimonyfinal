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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('users', 'rejected_at')) {
                $table->dateTime('rejected_at')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('users', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('users', 'submitted_for_review_at')) {
                $table->dateTime('submitted_for_review_at')->nullable()->after('rejected_by');
            }
        });

        if (!Schema::hasTable('profile_status_logs')) {
            Schema::create('profile_status_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('status', 50);
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->string('performed_by_type', 50)->default('admin');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_status_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejected_at', 'rejected_by', 'submitted_for_review_at']);
        });
    }
};
