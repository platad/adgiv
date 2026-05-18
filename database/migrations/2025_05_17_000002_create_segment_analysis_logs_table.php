<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('segment_analysis_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('chat_session_id');
            $table->foreign('chat_session_id')
                ->references('id')
                ->on('chat_sessions')
                ->cascadeOnDelete();
            $table->uuid('segment_id')->nullable();
            $table->foreign('segment_id')
                ->references('id')
                ->on('transcript_segments')
                ->nullOnDelete();
            $table->string('step_name', 100);
            $table->string('sub_step', 100);
            $table->enum('status', ['pending', 'running', 'done', 'failed'])->default('pending');
            $table->text('process_detail')->nullable()->comment('deskripsi yg ditampilkan di UI');
            $table->text('input_summary')->nullable();
            $table->text('result_summary')->nullable();
            $table->json('result_data')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();

            $table->index(['chat_session_id', 'step_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('segment_analysis_logs');
    }
};
