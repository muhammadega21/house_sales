<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BaseService;
use App\Helpers\ResponseHelper;
use App\Traits\HasFileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CrudFoundationTest extends TestCase
{
    use RefreshDatabase;

    private BaseService $baseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->baseService = new BaseService();
    }

    public function test_base_service_can_find_by_id(): void
    {
        $user = User::first();
        $found = $this->baseService->findById(User::class, $user->id);
        $this->assertEquals($user->id, $found->id);
        $this->assertEquals($user->username, $found->username);
    }

    public function test_base_service_can_create_update_delete(): void
    {
        // Create
        $userData = [
            'nama_lengkap' => 'Test User',
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'email' => 'test@example.com',
            'role' => 'marketing',
            'status' => 'aktif',
        ];
        $newUser = $this->baseService->create($userData, User::class);
        $this->assertInstanceOf(User::class, $newUser);
        $this->assertDatabaseHas('users', ['username' => 'testuser']);

        // Update
        $this->baseService->update(['nama_lengkap' => 'Updated User Name'], User::class, $newUser->id);
        $this->assertDatabaseHas('users', ['id' => $newUser->id, 'nama_lengkap' => 'Updated User Name']);

        // Delete
        $this->baseService->delete(User::class, $newUser->id);
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_base_service_can_filter_and_search(): void
    {
        // Search by username
        $results = $this->baseService->getAllWithFilters(['search' => 'admin'], User::class, ['username']);
        $this->assertGreaterThanOrEqual(1, $results->count());
        $this->assertEquals('admin', $results->first()->username);

        // Filter by role
        $results = $this->baseService->getAllWithFilters(['role' => 'marketing'], User::class);
        $this->assertGreaterThanOrEqual(1, $results->count());
        foreach ($results as $user) {
            $this->assertEquals('marketing', $user->role->value);
        }
    }

    public function test_response_helper_json_responses(): void
    {
        $response = ResponseHelper::successJson(['foo' => 'bar'], 'Great job');
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Great job', $data['message']);
        $this->assertEquals('bar', $data['data']['foo']);

        $responseError = ResponseHelper::errorJson('Something went wrong', 422);
        $this->assertEquals(422, $responseError->getStatusCode());
        $dataError = json_decode($responseError->getContent(), true);
        $this->assertFalse($dataError['success']);
        $this->assertEquals('Something went wrong', $dataError['message']);
    }

    public function test_has_file_upload_trait(): void
    {
        Storage::fake('public');

        $uploader = new class {
            use HasFileUpload;
        };

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $path = $uploader->uploadFile($file, 'avatars');

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        // Delete file
        $uploader->deleteFile($path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_has_file_upload_validation_fails_for_too_large_image(): void
    {
        Storage::fake('public');

        $uploader = new class {
            use HasFileUpload;
        };

        // Create an image larger than 5MB (e.g. 6000 KB)
        $file = UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg');

        $this->expectException(ValidationException::class);
        $uploader->uploadFile($file, 'avatars');
    }
}
