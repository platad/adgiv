<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('slug', 20)->nullable()->unique()->after('id');
            $table->string('locale', 10)->default('id')->after('title');
            $table->unsignedSmallInteger('total_chunks')->default(0)->after('duration_seconds');
            $table->unsignedSmallInteger('processed_chunks')->default(0)->after('total_chunks');
            $table->unsignedInteger('audio_duration_seconds')->nullable()->after('processed_chunks');
            $table->string('model_used', 100)->nullable()->after('audio_duration_seconds');
            $table->string('synthesis_model', 100)->nullable()->after('model_used');
            $table->string('status', 30)->default('pending')->change();
        });
        Schema::table('analyses', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn([
                'slug', 'locale', 'total_chunks', 'processed_chunks',
                'audio_duration_seconds', 'model_used', 'synthesis_model',
            ]);
        });
    }
};
