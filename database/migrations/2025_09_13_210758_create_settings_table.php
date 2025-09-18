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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json, file, image
            $table->string('group')->default('general'); // general, appearance, email, payment, etc.
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // For select/radio options
            $table->boolean('is_public')->default(false); // Can be accessed without auth
            $table->integer('sort_order')->default(0);
            $table->boolean('is_protected')->default(false);
            $table->timestamps();

            $table->index(['group', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
