<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->decimal('audio_duration', 8, 2)->default(0)->after('suggestions');
            $table->unsignedInteger('segment_count')->default(0)->after('audio_duration');
            $table->string('analysis_status', 50)->default('idle')->after('segment_count');
            $table->json('workflow_state')->nullable()->after('analysis_status');
            $table->json('raw_whisper_response')->nullable()->after('workflow_state');
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'audio_duration',
                'segment_count',
                'analysis_status',
                'workflow_state',
                'raw_whisper_response',
            ]);
        });
    }
};
