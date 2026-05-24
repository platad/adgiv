<?php

use App\Models\User;
use App\Services\AI\BimaAnalysisConfiguration;
use Illuminate\Support\Facades\File;

$hasDatabase = extension_loaded('pdo_sqlite');

test('analysis initialize stores selected locale in result_data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('analysis.initialize'), [
        'title' => 'Test English Session',
        'locale' => 'en',
    ]);

    $response->assertOk();
    $analysis = \App\Models\Analysis::first();
    expect($analysis->result_data['language'])->toBe('en');
})->skip(! $hasDatabase, 'SQLite driver not available');

test('analysis initialize stores chinese locale in result_data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('analysis.initialize'), [
        'title' => 'Test Chinese Session',
        'locale' => 'zh',
    ]);

    $response->assertOk();
    $analysis = \App\Models\Analysis::first();
    expect($analysis->result_data['language'])->toBe('zh');
})->skip(! $hasDatabase, 'SQLite driver not available');

test('analysis initialize defaults to id locale when not provided', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('analysis.initialize'), [
        'title' => 'Test Default Session',
    ]);

    $response->assertOk();
    $analysis = \App\Models\Analysis::first();
    expect($analysis->result_data['language'])->toBe('id');
})->skip(! $hasDatabase, 'SQLite driver not available');

test('analysis initialize rejects invalid locale', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('analysis.initialize'), [
        'title' => 'Test Invalid',
        'locale' => 'xyz',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('locale');
})->skip(! $hasDatabase, 'SQLite driver not available');

test('bima configuration loads english prompt file', function () {
    $config = new BimaAnalysisConfiguration();
    $prompt = $config->getSystemPrompt('en');

    expect($prompt)->toContain('Expert');
    expect(File::exists(resource_path('prompts/en/advice_giving.md')))->toBeTrue();
});

test('bima configuration loads chinese prompt file', function () {
    $config = new BimaAnalysisConfiguration();
    $prompt = $config->getSystemPrompt('zh');

    expect($prompt)->toContain('学术');
    expect(File::exists(resource_path('prompts/zh/advice_giving.md')))->toBeTrue();
});

test('bima configuration falls back to indonesian when locale file is missing', function () {
    $config = new BimaAnalysisConfiguration();
    $prompt = $config->getSystemPrompt('nonexistent');

    expect($prompt)->toContain('Pakar Analisis');
});

test('bima configuration returns correct user prompt per locale', function () {
    $config = new BimaAnalysisConfiguration();

    $idPrompt = $config->getUserPrompt('id');
    $enPrompt = $config->getUserPrompt('en');
    $zhPrompt = $config->getUserPrompt('zh');

    expect($idPrompt)->toContain('Tolong analisa');
    expect($enPrompt)->toContain('Please analyze');
    expect($zhPrompt)->toContain('请根据');
});

test('bima configuration loads english synthesis prompt', function () {
    $config = new BimaAnalysisConfiguration();
    $prompt = $config->getSynthesisSystemPrompt('en');

    expect($prompt)->toContain('comprehensive');
    expect(File::exists(resource_path('prompts/en/synthesis.md')))->toBeTrue();
});

test('bima configuration falls back synthesis prompt to indonesian for unknown locale', function () {
    $config = new BimaAnalysisConfiguration();
    $prompt = $config->getSynthesisSystemPrompt('nonexistent');

    expect($prompt)->toContain('potongan');
});
