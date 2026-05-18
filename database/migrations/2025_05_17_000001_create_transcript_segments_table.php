<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_session_id');
            $table->foreign('chat_session_id')
                ->references('id')
                ->on('chat_sessions')
                ->cascadeOnDelete();
            $table->unsignedInteger('segment_index')->default(0);
            $table->string('speaker', 50)->default('unknown');
            $table->decimal('start_time', 8, 2)->default(0)->comment('dalam detik');
            $table->decimal('end_time', 8, 2)->default(0)->comment('dalam detik');
            $table->longText('text');
            $table->string('topic', 255)->nullable();
            $table->string('dialogue_act', 100)->nullable();
            $table->string('power_marker', 10)->nullable()->comment('↑, ↓, ↔');
            $table->string('advice_category', 100)->nullable();
            $table->string('intonation', 100)->nullable();
            $table->json('discourse_markers')->nullable()->comment('{"bold":[], "question":[], "exclamation":[]}');
            $table->string('sentiment', 20)->nullable();
            $table->json('raw_json')->nullable()->comment('LLM response mentah');
            $table->timestamps();

            $table->index(['chat_session_id', 'segment_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_segments');
    }
};
