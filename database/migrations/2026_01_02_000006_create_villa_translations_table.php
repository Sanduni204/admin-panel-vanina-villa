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
        Schema::create('villa_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('villa_id');
            $table->string('locale', 2);
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('amenities')->nullable();
            $table->longText('rules')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_guests')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->decimal('bathrooms', 3, 1)->nullable();
            $table->timestamps();

            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade');
            $table->unique(['villa_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_translations');
    }
};
