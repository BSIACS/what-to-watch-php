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
        Schema::table('films', function (Blueprint $table) {
            Schema::table('films', function (Blueprint $table) {
                $table->string('name', 255)->nullable()->change();
                $table->string('poster_image', 255)->nullable()->change();
                $table->string('preview_image', 255)->nullable()->change();
                $table->string('background_image', 255)->nullable()->change();
                $table->string('background_color', 255)->nullable()->change();
                $table->unsignedSmallInteger('released')->nullable()->change();
                $table->string('description', 1000)->nullable()->change();
                $table->string('director', 255)->nullable()->change();
                $table->string('starring', 1000)->nullable()->change();
                $table->unsignedSmallInteger('run_time')->nullable()->change();
                $table->string('video_link', 255)->nullable()->change();
                $table->string('preview_video_link', 255)->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('poster_image', 255)->change();
            $table->string('preview_image', 255)->change();
            $table->string('background_image', 255)->change();
            $table->string('background_color', 255)->change();
            $table->unsignedSmallInteger('released')->change();
            $table->string('description', 1000)->change();
            $table->string('director', 255)->change();
            $table->string('starring', 1000)->change();
            $table->unsignedSmallInteger('run_time')->change();
            $table->string('video_link', 255)->change();
            $table->string('preview_video_link', 255)->change();
        });
    }
};
