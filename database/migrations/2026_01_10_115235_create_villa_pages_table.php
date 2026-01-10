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
        Schema::create('villa_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name')->nullable();
            $table->text('hero_image_path')->nullable();
            $table->longText('hero_content')->nullable();
            $table->string('brand_topic')->nullable();
            $table->longText('brand_express_sentence')->nullable();
            $table->longText('brand_description')->nullable();
            $table->longText('features_description')->nullable();
            $table->string('rates_topic')->nullable();
            $table->string('rates_sentence')->nullable();
            $table->text('room_image_path')->nullable();
            $table->json('gallery_images')->nullable();
            $table->timestamps();
            $table->unique(['villa_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_pages');
    }
};
