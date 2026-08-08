<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_line_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('analyses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('line_index')->comment('Urutan kalimat dalam transcript (0-based)');
            $table->string('speaker')->nullable()->comment('Nama pembicara: Dosen/Mahasiswa');
            $table->text('text')->comment('Isi kalimat yang diberi feedback');
            $table->enum('feedback', ['like', 'dislike'])->nullable();
            $table->timestamps();

            $table->unique(['analysis_id', 'user_id', 'line_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_line_feedbacks');
    }
};
