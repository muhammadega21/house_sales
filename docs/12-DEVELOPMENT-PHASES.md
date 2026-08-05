# DEVELOPMENT PHASES

## Fase 1: Setup & Fondasi (Minggu 1)

### Tasks:

- [✅] Install Laravel 12 + konfigurasi awal
- [✅] Setup Tailwind CSS 4 + Vite 6
- [✅] Setup MySQL database + .env
- [✅] Buat layout utama (sidebar, topbar, footer)
- [✅] Buat komponen Blade reusable (card, table, modal, alert, badge)
- [✅] Setup authentication (Laravel Fortify)
- [✅] Buat RoleMiddleware
- [✅] Buat migrasi semua tabel
- [✅] Buat seeder (admin, marketing, manajemen default)
- [✅] Setup route groups per role

### Deliverables:

- Aplikasi bisa login/logout
- Redirect sesuai role
- Layout dasar tampil
- Database ter-migrate

---

## Fase 2: Master Data (Minggu 2)

### Tasks:

- [✅] CRUD Data Pengguna (Admin)
- [✅] CRUD Data Perumahan (Admin)
- [✅] CRUD Data Unit Rumah (Admin)
- [✅] CRUD Kategori Rumah (filter di unit)
- [✅] CRUD Data Marketing + Target (Admin)
- [✅] Halaman listing dengan search, filter, pagination
- [✅] Upload foto (perumahan, unit)

### Deliverables:

- Admin bisa kelola semua master data
- Data tersimpan dan tampil di tabel
- Foto terupload dan tampil

---

## Fase 3: CRM - Prospek & Konsumen (Minggu 3)

### Tasks:

- [✅] CRUD Prospek (Marketing)
- [✅] CRUD Konsumen (Marketing + Admin)
- [✅] Konversi prospek → konsumen
- [✅] Upload Dokumen Identitas (KTP, KK)
- [✅] Policy & Filter Data (Marketing hanya lihat milik sendiri)
- [✅] Dashboard Prospek & Pipeline

### Deliverables:

- Marketing bisa input dan kelola prospek
- Data konsumen terstruktur
- NIK unik (tidak duplikat)

---

## Fase 4: Booking & Pembayaran (Minggu 4)

### Tasks:

<!-- - [ ] Form booking (pilih konsumen + unit)
- [ ] Validasi ketersediaan unit
- [ ] Generate kode booking otomatis
- [ ] Update status unit saat booking
- [ ] Input pembayaran (booking fee, DP, cicilan)
- [ ] Upload bukti bayar
- [ ] Verifikasi pembayaran (Admin)
- [ ] Status pembayaran (pending/diverifikasi/ditolak) -->

- [✅] CRUD Booking (Marketing + Admin)
- [✅] Generate Kode Booking Otomatis
- [✅] Input Pembayaran (Marketing)
- [✅] Verifikasi Pembayaran (Admin)
- [✅] Status Unit Otomatis & Side Effects
- [✅] Detail Booking & Riwayat Pembayaran

### Deliverables:

- Proses booking lengkap
- Pembayaran tercatat dan terverifikasi
- Status unit otomatis berubah

---

## Fase 5: Dokumen & Pengajuan KPR (Minggu 5)

### Tasks:

- [✅] Upload Dokumen KPR (Marketing)
- [✅] Checklist Kelengkapan Dokumen
- [✅] Verifikasi dokumen (Admin)
- [✅] Form Pengajuan KPR (Marketing)
- [✅] Status Pengajuan KPR & Side Effects
- [✅] Detail Pengajuan KPR

### Deliverables:

- Dokumen terupload dan terverifikasi
- Pengajuan KPR tercatat
- Alur dokumen → pengajuan → approval

---

## Fase 6: Status Penjualan & Simulasi (Minggu 6)

### Tasks:

- [✅] State Machine Status Penjualan (Implementasi Lengkap)
- [✅] Halaman Tracking Status per Booking
- [✅] Riwayat Perubahan Status (History Log)
- [✅] Simulasi Pembayaran (Service & Kalkulator)
- [✅] UI Simulasi Pembayaran (Form & Hasil)
- [ ] Perbandingan 3 Metode & Export PDF

### Deliverables:

- Status penjualan ter-track dengan benar
- Simulasi pembayaran fungsional
- History log tercatat

---

## Fase 7: Dashboard & Laporan (Minggu 7)

### Tasks:

- [ ] Dashboard Admin (ringkasan + grafik)
- [ ] Dashboard Marketing (kinerja pribadi)
- [ ] Dashboard Manajemen (overview perusahaan)
- [ ] Chart.js: bar, pie, line charts
- [ ] Laporan penjualan (per bulan, per marketing, per unit)
- [ ] Laporan kinerja marketing (target vs realisasi)
- [ ] Filter laporan (periode, perumahan, kategori)
- [ ] Export PDF (DomPDF)
- [ ] Export Excel (Laravel Excel)
- [ ] Perhitungan komisi marketing

### Deliverables:

- 3 dashboard fungsional
- Laporan lengkap dengan filter
- Export PDF & Excel
- Komisi terhitung otomatis

---

## Fase 8: Testing & Finishing (Minggu 8)

### Tasks:

- [ ] Unit test (Service layer)
- [ ] Feature test (CRUD, booking, status)
- [ ] Bug fixing
- [ ] UI polishing (responsive, konsistensi)
- [ ] Security review (validasi, authorization)
- [ ] Performance optimization (query, caching)
- [ ] User acceptance testing (UAT)
- [ ] Dokumentasi manual pengguna
- [ ] Deployment preparation

### Deliverables:

- Aplikasi stabil dan ter-test
- UI konsisten dan responsive
- Siap deploy ke production

---

## Prioritas Pengembangan

CRITICAL (harus ada):
├── Login & RBAC
├── Master Data (Perumahan, Unit, Konsumen)
├── Booking
├── Status Penjualan
└── Dashboard dasar
HIGH (sangat penting):
├── Pembayaran & Verifikasi
├── Dokumen KPR
├── Pengajuan KPR
├── Laporan Penjualan
└── Data Marketing & Komisi
MEDIUM (penting):
├── Simulasi Pembayaran
├── Dashboard lengkap dengan grafik
├── Export PDF/Excel
└── Kinerja Marketing detail
LOW (nice to have):
├── Notifikasi email/WA
├── Jadwal pembayaran (reminder)
├── Tabel amortisasi detail
└── Backup otomatis
