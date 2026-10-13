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
        Schema::create('document_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('locale', 10)->default('id');
            $table->string('pdf_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->unsignedInteger('total_pages')->nullable();
            $table->unsignedInteger('word_count')->nullable();
            $table->string('status')->default('pending'); // pending, extracting, analyzing, synthesizing_audio, completed, failed
            $table->text('error_message')->nullable();
            $table->longText('executive_summary')->nullable();
            $table->json('key_conclusions')->nullable();
            $table->json('recommendations')->nullable();
            $table->text('audio_script')->nullable();
            $table->string('briefing_audio_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_analyses');
    }
};
