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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('text', 400);
            $table->uuid('comment_id')->nullable(true);
            $table->uuid('film_id')->nullable(false);
            $table->foreign('film_id')->references('id')->on('films');
            $table->uuid('user_id')->nullable(true);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
