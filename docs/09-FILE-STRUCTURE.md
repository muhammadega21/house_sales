# PROJECT FILE STRUCTURE

```text
project-root/
├── app/
│ ├── Http/
│ │ ├── Controllers/
│ │ │ ├── Auth/
│ │ │ │ └── LoginController.php
│ │ │ ├── Admin/
│ │ │ │ ├── DashboardController.php
│ │ │ │ ├── UserController.php
│ │ │ │ ├── PerumahanController.php
│ │ │ │ ├── UnitRumahController.php
│ │ │ │ ├── KonsumenController.php
│ │ │ │ ├── BookingController.php
│ │ │ │ ├── PembayaranController.php
│ │ │ │ ├── DokumenController.php
│ │ │ │ ├── PengajuanKprController.php
│ │ │ │ ├── StatusPenjualanController.php
│ │ │ │ ├── MarketingController.php
│ │ │ │ ├── SimulasiController.php
│ │ │ │ ├── LaporanController.php
│ │ │ │ └── PengaturanController.php
│ │ │ ├── Marketing/
│ │ │ │ ├── DashboardController.php
│ │ │ │ ├── ProspekController.php
│ │ │ │ ├── KonsumenController.php
│ │ │ │ ├── BookingController.php
│ │ │ │ ├── DokumenController.php
│ │ │ │ ├── PembayaranController.php
│ │ │ │ ├── PengajuanKprController.php
│ │ │ │ ├── SimulasiController.php
│ │ │ │ ├── KinerjaController.php
│ │ │ │ └── StatusPenjualanController.php
│ │ │ └── Manajemen/
│ │ │ ├── DashboardController.php
│ │ │ └── LaporanController.php
│ │ ├── Middleware/
│ │ │ └── RoleMiddleware.php
│ │ └── Requests/
│ │ ├── UserRequest.php
│ │ ├── PerumahanRequest.php
│ │ ├── UnitRumahRequest.php
│ │ ├── KonsumenRequest.php
│ │ ├── BookingRequest.php
│ │ ├── PembayaranRequest.php
│ │ ├── DokumenRequest.php
│ │ ├── PengajuanKprRequest.php
│ │ ├── ProspekRequest.php
│ │ └── SimulasiRequest.php
│ ├── Models/
│ │ ├── User.php
│ │ ├── Perumahan.php
│ │ ├── UnitRumah.php
│ │ ├── Prospek.php
│ │ ├── Konsumen.php
│ │ ├── Booking.php
│ │ ├── Pembayaran.php
│ │ ├── DokumenKpr.php
│ │ ├── PengajuanKpr.php
│ │ ├── StatusPenjualan.php
│ │ ├── StatusHistory.php
│ │ ├── MarketingTarget.php
│ │ ├── SimulasiPembayaran.php
│ │ └── PengaturanSistem.php
│ ├── Services/
│ │ ├── BookingService.php
│ │ ├── PembayaranService.php
│ │ ├── SimulasiService.php
│ │ ├── StatusPenjualanService.php
│ │ ├── KomisiService.php
│ │ ├── LaporanService.php
│ │ └── DokumenService.php
│ ├── Observers/
│ │ ├── BookingObserver.php
│ │ └── StatusPenjualanObserver.php
│ ├── Policies/
│ │ ├── ProspekPolicy.php
│ │ ├── KonsumenPolicy.php
│ │ └── BookingPolicy.php
│ └── Enums/
│ ├── Role.php
│ ├── StatusUnit.php
│ ├── StatusPenjualan.php
│ ├── KategoriRumah.php
│ ├── JenisKetersediaan.php
│ ├── MetodePembayaran.php
│ ├── StatusVerifikasi.php
│ └── JenisDokumen.php
├── database/
│ ├── migrations/
│ │ ├── 0001_create_users_table.php
│ │ ├── 0002_create_perumahan_table.php
│ │ ├── 0003_create_unit_rumah_table.php
│ │ ├── 0004_create_prospek_table.php
│ │ ├── 0005_create_konsumen_table.php
│ │ ├── 0006_create_booking_table.php
│ │ ├── 0007_create_pembayaran_table.php
│ │ ├── 0008_create_dokumen_kpr_table.php
│ │ ├── 0009_create_pengajuan_kpr_table.php
│ │ ├── 0010_create_status_penjualan_table.php
│ │ ├── 0011_create_status_history_table.php
│ │ ├── 0012_create_marketing_target_table.php
│ │ ├── 0013_create_simulasi_pembayaran_table.php
│ │ └── 0014_create_pengaturan_sistem_table.php
│ ├── seeders/
│ │ ├── DatabaseSeeder.php
│ │ ├── UserSeeder.php
│ │ ├── PerumahanSeeder.php
│ │ ├── UnitRumahSeeder.php
│ │ └── PengaturanSeeder.php
│ └── factories/
│ ├── UserFactory.php
│ ├── KonsumenFactory.php
│ └── BookingFactory.php
├── resources/
│ ├── views/
│ │ ├── layouts/
│ │ │ ├── app.blade.php
│ │ │ ├── sidebar.blade.php
│ │ │ ├── topbar.blade.php
│ │ │ └── footer.blade.php
│ │ ├── components/
│ │ │ ├── card.blade.php
│ │ │ ├── data-table.blade.php
│ │ │ ├── modal.blade.php
│ │ │ ├── alert.blade.php
│ │ │ ├── badge.blade.php
│ │ │ ├── form-input.blade.php
│ │ │ ├── form-select.blade.php
│ │ │ ├── form-textarea.blade.php
│ │ │ ├── form-file.blade.php
│ │ │ ├── pagination.blade.php
│ │ │ └── confirm-delete.blade.php
│ │ ├── auth/
│ │ │ └── login.blade.php
│ │ ├── admin/
│ │ │ ├── dashboard.blade.php
│ │ │ ├── users/
│ │ │ ├── perumahan/
│ │ │ ├── unit-rumah/
│ │ │ ├── konsumen/
│ │ │ ├── booking/
│ │ │ ├── pembayaran/
│ │ │ ├── dokumen/
│ │ │ ├── pengajuan-kpr/
│ │ │ ├── status-penjualan/
│ │ │ ├── marketing/
│ │ │ ├── simulasi/
│ │ │ ├── laporan/
│ │ │ └── pengaturan/
│ │ ├── marketing/
│ │ │ ├── dashboard.blade.php
│ │ │ ├── prospek/
│ │ │ ├── konsumen/
│ │ │ ├── booking/
│ │ │ ├── dokumen/
│ │ │ ├── pembayaran/
│ │ │ ├── pengajuan-kpr/
│ │ │ ├── simulasi/
│ │ │ └── kinerja/
│ │ └── manajemen/
│ │ ├── dashboard.blade.php
│ │ └── laporan/
│ ├── css/
│ │ └── app.css
│ └── js/
│ ├── app.js
│ └── charts.js
├── routes/
│ ├── web.php
│ └── auth.php
├── storage/
│ └── app/
│ └── public/
│ ├── dokumen-kpr/
│ ├── bukti-bayar/
│ ├── foto-unit/
│ └── foto-profil/
├── docs/
│ └── (semua file dokumentasi ini)
├── tests/
│ ├── Feature/
│ │ ├── AuthTest.php
│ │ ├── BookingTest.php
│ │ ├── PembayaranTest.php
│ │ └── StatusPenjualanTest.php
│ └── Unit/
│ ├── SimulasiServiceTest.php
│ └── KomisiServiceTest.php
├── .env.example
├── composer.json
├── package.json
├── vite.config.js
└── README.md
``` 
