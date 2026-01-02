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
        Schema::create('villa_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('villa_id');
            $table->string('image_path');
            $table->string('alt_text_en')->nullable();
            $table->string('alt_text_fr')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade');
            $table->index('villa_id');
            $table->index('is_featured');
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_media');
    }
};
