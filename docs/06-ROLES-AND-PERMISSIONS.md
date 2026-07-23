# ROLES AND PERMISSIONS

## Definisi Role

### Admin

- Hak akses tertinggi
- Mengelola seluruh data master
- Verifikasi pembayaran dan dokumen
- Mengubah semua status penjualan
- Mengelola user dan pengaturan sistem
- Mengakses semua laporan

### Marketing

- Input prospek dan konsumen
- Melakukan booking rumah
- Upload dokumen konsumen
- Input pembayaran (bukti transfer)
- Mengajukan KPR
- Update status terbatas (prospek → booking → pengajuan)
- Melihat kinerja pribadi
- Hanya bisa akses data miliknya sendiri

### Manajemen

- Read-only (tidak ada transaksi)
- Melihat dashboard perusahaan
- Melihat semua laporan
- Melihat kinerja semua marketing
- Export laporan

## Matriks CRUD

| Modul            | Admin             | Marketing     | Manajemen |
| ---------------- | ----------------- | ------------- | --------- |
| Users            | CRUD              | -             | -         |
| Perumahan        | CRUD              | R             | R         |
| Unit Rumah       | CRUD              | R             | R         |
| Prospek          | CRUD              | CRU (sendiri) | R         |
| Konsumen         | CRUD              | CRU (sendiri) | R         |
| Booking          | CRUD              | CRU (sendiri) | R         |
| Pembayaran       | CRUD (verifikasi) | CR (input)    | R         |
| Dokumen          | CRUD (verifikasi) | CRU (upload)  | R         |
| Pengajuan KPR    | CRUD              | CRU (sendiri) | R         |
| Status Penjualan | RU (semua)        | RU (terbatas) | R         |
| Marketing/Target | CRUD              | R (sendiri)   | R         |
| Simulasi         | CRUD              | CR            | R         |
| Laporan          | R                 | R (pribadi)   | R (semua) |
| Pengaturan       | CRUD              | -             | -         |

## Implementasi Middleware

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'role:marketing'])->prefix('marketing')->group(function () {
    // Marketing routes
});

Route::middleware(['auth', 'role:manajemen'])->prefix('manajemen')->group(function () {
    // Manajemen routes
});
```

## Implementasi Policy (Contoh)

```php
// Marketing hanya bisa akses data miliknya
public function update(User $user, Prospek $prospek): bool
{
    return $user->id === $prospek->id_marketing;
}
```

## Redirect Setelah Login

| Role      | Redirect To          |
| --------- | -------------------- |
| admin     | /admin/dashboard     |
| marketing | /marketing/dashboard |
| manajemen | /manajemen/dashboard |
