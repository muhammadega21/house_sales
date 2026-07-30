<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatusUnit;
use App\Models\Konsumen;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingBookingCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $marketing;
    private User $admin;
    private Perumahan $perumahan;
    private UnitRumah $unit;
    private Konsumen $konsumen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->marketing = User::where('role', Role::Marketing->value)->first();
        $this->admin = User::where('role', Role::Admin->value)->first();

        $this->perumahan = Perumahan::create([
            'nama_perumahan' => 'Perumahan Test',
            'alamat' => 'Jl. Test No. 1',
            'kota' => 'Jakarta',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '12345',
            'total_unit' => 0,
            'status' => 'aktif',
        ]);

        $this->unit = UnitRumah::create([
            'id_perumahan' => $this->perumahan->id,
            'kode_unit' => 'A-001',
            'tipe_rumah' => '36/60',
            'kategori' => 'subsidi',
            'jenis_ketersediaan' => 'ready_stock',
            'luas_tanah' => 36,
            'luas_bangunan' => 60,
            'harga_jual' => 150000000,
            'status_unit' => StatusUnit::Tersedia,
        ]);

        $this->perumahan->increment('total_unit');

        $this->konsumen = Konsumen::create([
            'id_marketing' => $this->marketing->id,
            'nama_lengkap' => 'Test Konsumen',
            'nik' => '3201010101010001',
            'no_hp' => '081234567890',
            'alamat_lengkap' => 'Jl. Konsumen No. 1',
        ]);
    }

    public function test_marketing_can_view_booking_index(): void
    {
        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('marketing.booking.index');
    }

    public function test_marketing_can_view_booking_create_form(): void
    {
        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.create'));

        $response->assertStatus(200);
        $response->assertViewIs('marketing.booking.create');
        $response->assertViewHas('konsumenOptions');
        $response->assertViewHas('unitOptions');
    }

    public function test_marketing_can_create_booking_with_valid_data(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->marketing)
            ->post(route('marketing.booking.store'), [
                'id_konsumen' => $this->konsumen->id,
                'id_unit' => $this->unit->id,
                'tanggal_booking' => now()->toDateString(),
                'booking_fee' => 1000000,
                'metode_bayar_fee' => 'transfer',
                'bukti_bayar_fee' => UploadedFile::fake()->image('bukti.jpg', 500, 500),
                'catatan' => 'Booking test',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking = \App\Models\Booking::where('id_konsumen', $this->konsumen->id)
            ->where('id_unit', $this->unit->id)
            ->first();

        $this->assertNotNull($booking);

        $this->assertDatabaseHas('booking', [
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'booking_fee' => 1000000,
            'metode_bayar_fee' => 'transfer',
            'status_pembayaran_fee' => 'sudah_bayar',
            'catatan' => 'Booking test',
        ]);

        $this->assertDatabaseHas('status_history', [
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => 'booking',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $this->assertDatabaseHas('unit_rumah', [
            'id' => $this->unit->id,
            'status_unit' => StatusUnit::Dibooking,
        ]);
    }

    public function test_marketing_cannot_create_booking_for_unavailable_unit(): void
    {
        $this->unit->update(['status_unit' => StatusUnit::Dibooking]);

        $response = $this->actingAs($this->marketing)
            ->post(route('marketing.booking.store'), [
                'id_konsumen' => $this->konsumen->id,
                'id_unit' => $this->unit->id,
                'tanggal_booking' => now()->toDateString(),
                'booking_fee' => 1000000,
            ]);

        $response->assertSessionHasErrors('id_unit');
        $this->assertDatabaseMissing('booking', [
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
        ]);
    }

    public function test_marketing_cannot_create_booking_with_below_minimum_fee(): void
    {
        $response = $this->actingAs($this->marketing)
            ->post(route('marketing.booking.store'), [
                'id_konsumen' => $this->konsumen->id,
                'id_unit' => $this->unit->id,
                'tanggal_booking' => now()->toDateString(),
                'booking_fee' => 500000,
            ]);

        $response->assertSessionHasErrors('booking_fee');
    }

    public function test_marketing_can_view_booking_detail(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-001',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.show', $booking->id));

        $response->assertStatus(200);
        $response->assertViewIs('marketing.booking.show');
        $response->assertViewHas('booking');
    }

    public function test_marketing_can_view_booking_edit_form(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-002',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => 'booking',
            'catatan' => 'Booking baru',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.edit', $booking->id));

        $response->assertStatus(200);
        $response->assertViewIs('marketing.booking.edit');
        $response->assertViewHas('booking');
        $response->assertViewHas('konsumenOptions');
        $response->assertViewHas('unitOptions');
    }

    public function test_marketing_can_update_booking(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-003',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
            'catatan' => 'Catatan lama',
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => 'booking',
            'catatan' => 'Booking baru',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->put(route('marketing.booking.update', $booking->id), [
                'id_konsumen' => $this->konsumen->id,
                'id_unit' => $this->unit->id,
                'tanggal_booking' => now()->toDateString(),
                'booking_fee' => 1500000,
                'catatan' => 'Catatan diperbarui',
            ]);

        $response->assertRedirect(route('marketing.booking.show', $booking->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('booking', [
            'id' => $booking->id,
            'booking_fee' => 1500000,
            'catatan' => 'Catatan diperbarui',
        ]);
    }

    public function test_marketing_cannot_update_booking_when_status_not_booking(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-004',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => 'booking',
            'status_sesudah' => 'pengajuan_kpr',
            'catatan' => 'Pindah ke pengajuan',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->put(route('marketing.booking.update', $booking->id), [
                'id_konsumen' => $this->konsumen->id,
                'id_unit' => $this->unit->id,
                'tanggal_booking' => now()->toDateString(),
                'booking_fee' => 1500000,
            ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_marketing_can_view_cancel_form(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-005',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => 'booking',
            'catatan' => 'Booking baru',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.cancel', $booking->id));

        $response->assertStatus(200);
        $response->assertViewIs('marketing.booking.cancel');
    }

    public function test_marketing_can_cancel_booking(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-006',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => 'belum_bayar',
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => 'booking',
            'catatan' => 'Booking baru',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->post(route('marketing.booking.process-cancel', $booking->id), [
                'alasan' => 'Konsumen batal',
            ]);

        $response->assertRedirect(route('marketing.booking.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('status_history', [
            'id_booking' => $booking->id,
            'status_sebelum' => 'booking',
            'status_sesudah' => 'batal',
        ]);

        $this->assertDatabaseHas('unit_rumah', [
            'id' => $this->unit->id,
            'status_unit' => StatusUnit::Tersedia,
        ]);
    }

    public function test_marketing_konsumen_show_booking_tab_renders_without_error(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-007',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => \App\Enums\StatusPembayaranFee::SudahBayar,
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.konsumen.show', $this->konsumen->id) . '?tab=booking');

        $response->assertStatus(200);
        $response->assertViewIs('marketing.konsumen.show');
        $response->assertSee('Sudah Bayar');
    }

    public function test_cancelled_booking_hides_action_buttons_on_index(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-008',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => \App\Enums\StatusPembayaranFee::BelumBayar,
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => \App\Enums\StatusPenjualan::Booking->value,
            'catatan' => 'Booking baru',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $this->assertDatabaseHas('status_history', [
            'id_booking' => $booking->id,
            'status_sesudah' => 'booking',
        ]);

        $this->actingAs($this->marketing)
            ->post(route('marketing.booking.process-cancel', $booking->id), [
                'alasan' => 'Test cancel',
            ]);

        $this->assertDatabaseHas('status_history', [
            'id_booking' => $booking->id,
            'status_sesudah' => 'batal',
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.index'));

        $response->assertStatus(200);
        $response->assertDontSee(route('marketing.booking.cancel', $booking->id));
        $response->assertDontSee(route('marketing.booking.edit', $booking->id));
        $response->assertSee('Batal');
    }

    public function test_status_history_shows_changed_by_user(): void
    {
        $booking = \App\Models\Booking::create([
            'kode_booking' => 'BK-20250730-009',
            'id_konsumen' => $this->konsumen->id,
            'id_unit' => $this->unit->id,
            'id_marketing' => $this->marketing->id,
            'tanggal_booking' => now()->toDateString(),
            'booking_fee' => 1000000,
            'status_pembayaran_fee' => \App\Enums\StatusPembayaranFee::BelumBayar,
        ]);

        \App\Models\StatusHistory::create([
            'id_booking' => $booking->id,
            'status_sebelum' => null,
            'status_sesudah' => \App\Enums\StatusPenjualan::Booking->value,
            'catatan' => 'Test history',
            'diubah_oleh' => $this->marketing->id,
        ]);

        $response = $this->actingAs($this->marketing)
            ->get(route('marketing.booking.show', $booking->id));

        $response->assertStatus(200);
        $response->assertSee('Budi Marketing');
    }
}
