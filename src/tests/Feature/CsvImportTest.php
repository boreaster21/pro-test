<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage; 

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $generalUser;
    protected string $successCsvPath;
    protected string $errorCsvPath;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->generalUser = User::factory()->create(['role' => 'general']);

        $this->successCsvPath = base_path('tests/Feature/TestFiles/import_success.csv');
        $this->errorCsvPath = base_path('tests/Feature/TestFiles/import_validation_error.csv');

        if (!file_exists($this->successCsvPath)) {
            dump("Success CSV not found at: " . $this->successCsvPath);
        }
        if (!file_exists($this->errorCsvPath)) {
            dump("Error CSV not found at: " . $this->errorCsvPath);
        }
    }

    private function createTestCsv(string $path, string $filename): UploadedFile
    {
        if (!file_exists($path)) {
            throw new \Exception("Test CSV file not found at: {$path}");
        }
        return new UploadedFile(
            $path,
            $filename,
            'text/csv',
            null,
            true
        );
    }

    public function test_admin_user_can_import_valid_csv(): void
    {
        $csvFile = $this->createTestCsv($this->successCsvPath, 'import_success.csv');

        $response = $this->actingAs($this->adminUser)
                         ->post(route('admin.import.csv'), [
                             'csv_file' => $csvFile,
                         ]);

        $response->assertRedirect(route('admin.import.csv.form'));
        $response->assertSessionHas('success', '3件の店舗情報をインポートしました。');

        $this->assertDatabaseHas('stores', ['name' => '成功テスト寿司', 'region' => '東京都', 'genre' => '寿司']);
        $this->assertDatabaseHas('stores', ['name' => '成功テスト焼肉', 'region' => '大阪府', 'genre' => '焼肉']);
        $this->assertDatabaseHas('stores', ['name' => '成功テストラーメン', 'region' => '福岡県', 'genre' => 'ラーメン']);
        $this->assertDatabaseCount('stores', 3);
    }

    public function test_general_user_cannot_import_csv(): void
    {
        $csvFile = $this->createTestCsv($this->successCsvPath, 'import_success.csv');

        $response = $this->actingAs($this->generalUser)
                         ->post(route('admin.import.csv'), [
                             'csv_file' => $csvFile,
                         ]);

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseCount('stores', 0);
    }

    public function test_unauthenticated_user_cannot_import_csv(): void
    {
        $csvFile = $this->createTestCsv($this->successCsvPath, 'import_success.csv');

        $response = $this->post(route('admin.import.csv'), [
                             'csv_file' => $csvFile,
                         ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('stores', 0);
    }

    public function test_import_fails_without_csv_file(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->post(route('admin.import.csv'), []);

        $response->assertSessionHasErrors('csv_file');
        $this->assertDatabaseCount('stores', 0);
    }

    public function test_import_fails_with_non_csv_file(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->adminUser)
                         ->post(route('admin.import.csv'), [
                             'csv_file' => $file,
                         ]);

        $response->assertSessionHasErrors('csv_file');
        $this->assertDatabaseCount('stores', 0);
    }

    public function test_import_fails_with_file_size_exceeding_limit(): void
    {
        $file = UploadedFile::fake()->create('large_import.csv', 6000, 'text/csv');

        $response = $this->actingAs($this->adminUser)
                         ->post(route('admin.import.csv'), [
                             'csv_file' => $file,
                         ]);

        $response->assertSessionHasErrors('csv_file');
        $this->assertDatabaseCount('stores', 0);
    }

    public function test_import_fails_with_validation_errors_in_csv(): void
    {
        $csvFile = $this->createTestCsv($this->errorCsvPath, 'import_validation_error.csv');

        $response = $this->actingAs($this->adminUser)
                         ->post(route('admin.import.csv'), [
                             'csv_file' => $csvFile,
                         ]);

        $response->assertRedirect(route('admin.import.csv.form'));
        $response->assertSessionHasErrors();

        $errors = session('errors')->getBag('default');
        $this->assertTrue($errors->has(0));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '店舗名 は 50 文字以内で入力してください。')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '地域 が無効な値です。')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, 'ジャンル が無効な値です。')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '店舗概要 は 400 文字以内で入力してください。')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '画像URL は有効なURL形式である必要があります。')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '画像URL の形式が無効です')));
        $this->assertTrue(collect($errors->all())->contains(fn($msg) => str_contains($msg, '地域 は必須です。')));
        $this->assertDatabaseCount('stores', 0);
    }
} 