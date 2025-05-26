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
        Schema::create('film_genre', function (Blueprint $table) {
            $table->uuid('film_id');
            $table->uuid('genre_id');
            $table->unique(['film_id', 'genre_id']);
            $table->foreign('film_id')->references('id')->on('films')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('film_genre', function (Blueprint $table) {
            $table->dropForeign(['film_id']);
            $table->dropForeign(['genre_id']);
        });

        Schema::dropIfExists('film_genre');
    }
};
