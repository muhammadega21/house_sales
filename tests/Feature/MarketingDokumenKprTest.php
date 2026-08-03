<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Konsumen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingDokumenKprTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_upload_document_for_own_customer(): void
    {
        Storage::fake('public');

        $marketing = User::create([
            'nama_lengkap' => 'Marketing Satu',
            'username' => 'marketing_dokumen',
            'password' => 'password123',
            'no_hp' => '08123456789',
            'role' => Role::Marketing,
            'status' => 'aktif',
        ]);

        $konsumen = Konsumen::create([
            'id_marketing' => $marketing->id,
            'nama_lengkap' => 'Budi Santoso',
            'nik' => '3201010101010001',
            'no_hp' => '08111111111',
            'alamat_lengkap' => 'Jl. Contoh No. 1',
        ]);

        $file = UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf');

        $response = $this->actingAs($marketing)->post(route('marketing.dokumen.store'), [
            'id_konsumen' => $konsumen->id,
            'jenis_dokumen' => 'ktp',
            'file_dokumen' => $file,
        ]);

        $response->assertRedirect(route('marketing.dokumen.index', $konsumen->id));
        $this->assertDatabaseHas('dokumen_kpr', [
            'id_konsumen' => $konsumen->id,
            'jenis_dokumen' => 'ktp',
            'diupload_oleh' => $marketing->id,
            'status_verifikasi' => 'belum_diverifikasi',
        ]);
    }
}
