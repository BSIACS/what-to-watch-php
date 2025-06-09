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
        Schema::create('film_user', function (Blueprint $table) {
            $table->timestamps();
            $table->uuid('film_id');
            $table->uuid('user_id');
            $table->unique(['film_id', 'user_id']);
            $table->foreign('film_id')->references('id')->on('films')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('film_user', function (Blueprint $table) {
            $table->dropForeign(['film_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('film_user');
    }
};
