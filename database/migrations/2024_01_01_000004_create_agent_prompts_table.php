<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name')->unique(); // e.g. 'kosakata', 'otoritas', 'gaya_bahasa', 'judge'
            $table->string('display_name');
            $table->text('system_prompt');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_prompts');
    }
};
