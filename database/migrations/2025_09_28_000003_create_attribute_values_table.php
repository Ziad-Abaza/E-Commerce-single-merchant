<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_detail_id')->constrained('product_details')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->text('value')->nullable();
            $table->string('value_type')->default('string');
            $table->timestamps();
            
            $table->unique(['product_detail_id', 'attribute_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attribute_values');
    }
};
