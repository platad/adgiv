<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('analyses')->onDelete('cascade');
            $table->string('event', 60);
            $table->enum('status', ['info', 'success', 'warning', 'error'])->default('info');
            $table->string('message', 500)->nullable();
            $table->json('detail')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['analysis_id', 'event']);
            $table->index(['analysis_id', 'created_at']);
        });

        Schema::create('analysis_logs_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analysis_id');
            $table->string('event', 60);
            $table->enum('status', ['info', 'success', 'warning', 'error'])->default('info');
            $table->string('message', 500)->nullable();
            $table->json('detail')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();
            $table->index(['analysis_id']);
        });

        Schema::create('pending_file_deletions', function (Blueprint $table) {
            $table->id();
            $table->string('file_path', 1000);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->index('is_processed');
        });

        Schema::create('daily_analysis_stats', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique();
            $table->unsignedInteger('total_analyses')->default(0);
            $table->unsignedInteger('completed_analyses')->default(0);
            $table->unsignedInteger('failed_analyses')->default(0);
            $table->unsignedBigInteger('total_tokens_used')->default(0);
            $table->unsignedBigInteger('avg_duration_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->index('snapshot_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_analysis_stats');
        Schema::dropIfExists('pending_file_deletions');
        Schema::dropIfExists('analysis_logs_archive');
        Schema::dropIfExists('analysis_logs');
    }
};
