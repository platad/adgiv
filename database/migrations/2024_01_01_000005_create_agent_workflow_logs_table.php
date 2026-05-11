<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
            $table->string('agent_name');
            $table->enum('status', ['thinking', 'done', 'failed'])->default('thinking');
            $table->text('process_note')->nullable();
            $table->json('result_data')->nullable(); // structured verdict from the agent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_workflow_logs');
    }
};
