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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->string('store_name', 255);
            $table->text('description')->nullable();
            $table->string('domain', 255)->nullable()->unique();
            $table->char('currency', 3)->default('EGP');
            $table->string('default_language', 10)->default('ar');
            $table->text('header_message')->nullable();
            $table->text('footer_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
