# ROUTES / ENDPOINTS

Catatan: semua route berikut menggunakan prefix dan middleware sesuai role yang telah ditetapkan.

## Prefix: /admin (Role: admin)

### Dashboard

| Method | URI              | Controller                      | Description     |
| ------ | ---------------- | ------------------------------- | --------------- |
| GET    | /admin/dashboard | Admin\DashboardController@index | Dashboard admin |

### Data Pengguna

| Method | URI                    | Controller                   | Description |
| ------ | ---------------------- | ---------------------------- | ----------- |
| GET    | /admin/users           | Admin\UserController@index   | List users  |
| GET    | /admin/users/create    | Admin\UserController@create  | Form create |
| POST   | /admin/users           | Admin\UserController@store   | Simpan user |
| GET    | /admin/users/{id}/edit | Admin\UserController@edit    | Form edit   |
| PUT    | /admin/users/{id}      | Admin\UserController@update  | Update user |
| DELETE | /admin/users/{id}      | Admin\UserController@destroy | Hapus user  |

### Data Perumahan

| Method | URI                        | Controller                        | Description |
| ------ | -------------------------- | --------------------------------- | ----------- |
| GET    | /admin/perumahan           | Admin\PerumahanController@index   | List        |
| GET    | /admin/perumahan/create    | Admin\PerumahanController@create  | Form create |
| POST   | /admin/perumahan           | Admin\PerumahanController@store   | Simpan      |
| GET    | /admin/perumahan/{id}/edit | Admin\PerumahanController@edit    | Form edit   |
| PUT    | /admin/perumahan/{id}      | Admin\PerumahanController@update  | Update      |
| DELETE | /admin/perumahan/{id}      | Admin\PerumahanController@destroy | Hapus       |

### Data Unit Rumah

| Method | URI                         | Controller                        | Description |
| ------ | --------------------------- | --------------------------------- | ----------- |
| GET    | /admin/unit-rumah           | Admin\UnitRumahController@index   | List        |
| GET    | /admin/unit-rumah/create    | Admin\UnitRumahController@create  | Form create |
| POST   | /admin/unit-rumah           | Admin\UnitRumahController@store   | Simpan      |
| GET    | /admin/unit-rumah/{id}/edit | Admin\UnitRumahController@edit    | Form edit   |
| PUT    | /admin/unit-rumah/{id}      | Admin\UnitRumahController@update  | Update      |
| DELETE | /admin/unit-rumah/{id}      | Admin\UnitRumahController@destroy | Hapus       |

### Data Konsumen

| Method | URI                       | Controller                       | Description |
| ------ | ------------------------- | -------------------------------- | ----------- |
| GET    | /admin/konsumen           | Admin\KonsumenController@index   | List        |
| GET    | /admin/konsumen/create    | Admin\KonsumenController@create  | Form create |
| POST   | /admin/konsumen           | Admin\KonsumenController@store   | Simpan      |
| GET    | /admin/konsumen/{id}      | Admin\KonsumenController@show    | Detail      |
| GET    | /admin/konsumen/{id}/edit | Admin\KonsumenController@edit    | Form edit   |
| PUT    | /admin/konsumen/{id}      | Admin\KonsumenController@update  | Update      |
| DELETE | /admin/konsumen/{id}      | Admin\KonsumenController@destroy | Hapus       |

### Booking

| Method | URI                      | Controller                     | Description |
| ------ | ------------------------ | ------------------------------ | ----------- |
| GET    | /admin/booking           | Admin\BookingController@index  | List        |
| GET    | /admin/booking/create    | Admin\BookingController@create | Form create |
| POST   | /admin/booking           | Admin\BookingController@store  | Simpan      |
| GET    | /admin/booking/{id}      | Admin\BookingController@show   | Detail      |
| GET    | /admin/booking/{id}/edit | Admin\BookingController@edit   | Form edit   |
| PUT    | /admin/booking/{id}      | Admin\BookingController@update | Update      |

### Pembayaran

| Method | URI                               | Controller                                  | Description     |
| ------ | --------------------------------- | ------------------------------------------- | --------------- |
| GET    | /admin/pembayaran                 | Admin\PembayaranController@index            | List            |
| GET    | /admin/pembayaran/{id}/verifikasi | Admin\PembayaranController@verifikasi       | Form verifikasi |
| PUT    | /admin/pembayaran/{id}/verifikasi | Admin\PembayaranController@prosesVerifikasi | Proses          |

### Dokumen KPR

| Method | URI                            | Controller                               | Description     |
| ------ | ------------------------------ | ---------------------------------------- | --------------- |
| GET    | /admin/dokumen                 | Admin\DokumenController@index            | List            |
| GET    | /admin/dokumen/{id}/verifikasi | Admin\DokumenController@verifikasi       | Form verifikasi |
| PUT    | /admin/dokumen/{id}/verifikasi | Admin\DokumenController@prosesVerifikasi | Proses          |

### Pengajuan KPR

| Method | URI                              | Controller                                | Description   |
| ------ | -------------------------------- | ----------------------------------------- | ------------- |
| GET    | /admin/pengajuan-kpr             | Admin\PengajuanKprController@index        | List          |
| GET    | /admin/pengajuan-kpr/{id}        | Admin\PengajuanKprController@show         | Detail        |
| PUT    | /admin/pengajuan-kpr/{id}/status | Admin\PengajuanKprController@updateStatus | Update status |

### Status Penjualan

| Method | URI                          | Controller                             | Description   |
| ------ | ---------------------------- | -------------------------------------- | ------------- |
| GET    | /admin/status-penjualan      | Admin\StatusPenjualanController@index  | List          |
| PUT    | /admin/status-penjualan/{id} | Admin\StatusPenjualanController@update | Update status |

### Marketing

| Method | URI                          | Controller                          | Description    |
| ------ | ---------------------------- | ----------------------------------- | -------------- |
| GET    | /admin/marketing             | Admin\MarketingController@index     | List           |
| GET    | /admin/marketing/{id}        | Admin\MarketingController@show      | Detail kinerja |
| POST   | /admin/marketing/{id}/target | Admin\MarketingController@setTarget | Set target     |

### Simulasi Pembayaran

| Method | URI                    | Controller                      | Description      |
| ------ | ---------------------- | ------------------------------- | ---------------- |
| GET    | /admin/simulasi        | Admin\SimulasiController@index  | Halaman simulasi |
| POST   | /admin/simulasi/hitung | Admin\SimulasiController@hitung | Proses hitung    |

### Laporan

| Method | URI                         | Controller                          | Description       |
| ------ | --------------------------- | ----------------------------------- | ----------------- |
| GET    | /admin/laporan/penjualan    | Admin\LaporanController@penjualan   | Laporan penjualan |
| GET    | /admin/laporan/marketing    | Admin\LaporanController@marketing   | Laporan marketing |
| GET    | /admin/laporan/unit         | Admin\LaporanController@unit        | Laporan unit      |
| GET    | /admin/laporan/export-pdf   | Admin\LaporanController@exportPdf   | Export PDF        |
| GET    | /admin/laporan/export-excel | Admin\LaporanController@exportExcel | Export Excel      |

### Pengaturan

| Method | URI               | Controller                        | Description     |
| ------ | ----------------- | --------------------------------- | --------------- |
| GET    | /admin/pengaturan | Admin\PengaturanController@index  | Form pengaturan |
| PUT    | /admin/pengaturan | Admin\PengaturanController@update | Simpan          |

---

## Prefix: /marketing (Role: marketing)

| Method | URI                              | Controller                                 | Description       |
| ------ | -------------------------------- | ------------------------------------------ | ----------------- |
| GET    | /marketing/dashboard             | Marketing\DashboardController@index        | Dashboard pribadi |
| GET    | /marketing/prospek               | Marketing\ProspekController@index          | List prospek      |
| POST   | /marketing/prospek               | Marketing\ProspekController@store          | Input prospek     |
| PUT    | /marketing/prospek/{id}          | Marketing\ProspekController@update         | Update prospek    |
| GET    | /marketing/konsumen              | Marketing\KonsumenController@index         | List konsumen     |
| POST   | /marketing/konsumen              | Marketing\KonsumenController@store         | Input konsumen    |
| PUT    | /marketing/konsumen/{id}         | Marketing\KonsumenController@update        | Update            |
| GET    | /marketing/booking               | Marketing\BookingController@index          | List booking      |
| POST   | /marketing/booking               | Marketing\BookingController@store          | Buat booking      |
| GET    | /marketing/dokumen/{id_konsumen} | Marketing\DokumenController@index          | List dokumen      |
| POST   | /marketing/dokumen/upload        | Marketing\DokumenController@upload         | Upload dokumen    |
| GET    | /marketing/pembayaran            | Marketing\PembayaranController@index       | List pembayaran   |
| POST   | /marketing/pembayaran            | Marketing\PembayaranController@store       | Input pembayaran  |
| GET    | /marketing/pengajuan-kpr         | Marketing\PengajuanKprController@index     | List pengajuan    |
| POST   | /marketing/pengajuan-kpr         | Marketing\PengajuanKprController@store     | Ajukan KPR        |
| GET    | /marketing/simulasi              | Marketing\SimulasiController@index         | Simulasi          |
| POST   | /marketing/simulasi/hitung       | Marketing\SimulasiController@hitung        | Hitung            |
| GET    | /marketing/kinerja               | Marketing\KinerjaController@index          | Kinerja pribadi   |
| PUT    | /marketing/status-penjualan/{id} | Marketing\StatusPenjualanController@update | Update status     |

---

## Prefix: /manajemen (Role: manajemen)

| Method | URI                             | Controller                              | Description       |
| ------ | ------------------------------- | --------------------------------------- | ----------------- |
| GET    | /manajemen/dashboard            | Manajemen\DashboardController@index     | Dashboard         |
| GET    | /manajemen/laporan/penjualan    | Manajemen\LaporanController@penjualan   | Laporan           |
| GET    | /manajemen/laporan/marketing    | Manajemen\LaporanController@marketing   | Kinerja marketing |
| GET    | /manajemen/laporan/export-pdf   | Manajemen\LaporanController@exportPdf   | Export            |
| GET    | /manajemen/laporan/export-excel | Manajemen\LaporanController@exportExcel | Export            |

---

## Public Routes

| Method | URI     | Controller                     | Description   |
| ------ | ------- | ------------------------------ | ------------- |
| GET    | /login  | Auth\LoginController@showLogin | Halaman login |
| POST   | /login  | Auth\LoginController@login     | Proses login  |
| POST   | /logout | Auth\LoginController@logout    | Logout        |
