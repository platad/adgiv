<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('analyses', 'visibility')) {
                $table->string('visibility', 20)->default('private')->after('status');
                $table->index('visibility');
            }
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            if (Schema::hasColumn('analyses', 'visibility')) {
                $table->dropIndex(['visibility']);
                $table->dropColumn('visibility');
            }
        });
    }
};
