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
        Schema::create('films', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('name', 255);
            $table->string('poster_image', 255);
            $table->string('preview_image', 255);
            $table->string('background_image', 255);
            $table->string('background_color', 255);
            $table->unsignedSmallInteger('released');
            $table->string('description', 1000);
            $table->string('director', 255);
            $table->string('starring', 1000);
            $table->unsignedSmallInteger('run_time');
            $table->string('video_link', 255);
            $table->string('preview_video_link', 255);
            $table->string('imdb_id', 255);                      //На рабочем проекте установить столбцу imdb_id ->unique();
            $table->unsignedInteger('rating');
            $table->unsignedInteger('score_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
