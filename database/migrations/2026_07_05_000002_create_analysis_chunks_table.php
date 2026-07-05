<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('analyses')->onDelete('cascade');
            $table->unsignedSmallInteger('chunk_index');        
            $table->unsignedSmallInteger('total_chunks');      
            $table->string('chunk_path')->nullable();           
            $table->unsignedInteger('chunk_duration_seconds')->nullable();
            $table->unsignedBigInteger('chunk_size_bytes')->nullable();
            $table->enum('status', ['pending', 'running', 'done', 'failed', 'skipped'])->default('pending');
            $table->string('model_used', 100)->nullable();
            $table->longText('prompt_used')->nullable();         
            $table->longText('raw_response')->nullable();        
            $table->json('result_data')->nullable();            
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['analysis_id', 'chunk_index']);
            $table->index(['analysis_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_chunks');
    }
};
