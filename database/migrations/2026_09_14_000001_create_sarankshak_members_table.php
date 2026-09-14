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
        if (!Schema::hasTable('sarankshak_members')) {
            Schema::create('sarankshak_members', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('name_en', 255)->nullable();
                $table->string('designation', 150)->nullable()->default('Our Sarankshak Member');
                $table->string('designation_en', 150)->nullable()->default('Our Sarankshak Member');
                $table->text('description')->nullable();
                $table->text('description_en')->nullable();
                $table->string('photo', 500)->nullable();
                $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('sarankshak_members');
    }
};
