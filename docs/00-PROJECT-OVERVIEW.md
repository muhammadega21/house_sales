# PROJECT OVERVIEW

## Nama Project

Aplikasi Sistem Penjualan Rumah Subsidi dan Non-Subsidi

## Client

PT. Sriwijaya Griya Cemerlang

## Deskripsi

Sistem informasi berbasis web untuk mengelola seluruh proses bisnis penjualan rumah
subsidi dan non-subsidi secara terintegrasi. Sistem menggunakan konsep CRM
(Customer Relationship Management) untuk mengelola hubungan dengan calon konsumen
sejak tahap pemasaran hingga serah terima rumah.

## Tujuan

1. Mengelola data perumahan dan unit rumah secara terintegrasi
2. Mempermudah marketing mencatat prospek dan mengelola penjualan
3. Mengelola data konsumen secara terstruktur (minim duplikasi)
4. Mempermudah proses booking dan pencatatan pembayaran
5. Mengelola dokumen KPR secara digital
6. Memantau status pengajuan KPR hingga akad kredit
7. Menyediakan dashboard real-time untuk manajemen
8. Menyediakan laporan penjualan dan kinerja marketing
9. Meningkatkan kualitas pelayanan kepada calon pembeli

## Pengguna Sistem (Internal)

- **Admin**: Mengelola seluruh data master dan operasional
- **Marketing**: Input prospek, booking, upload dokumen, input pembayaran
- **Manajemen**: Monitoring dashboard, laporan, dan evaluasi

## Batasan Sistem

- TIDAK membahas proses pembangunan fisik rumah
- TIDAK membahas proses administrasi internal bank
- TIDAK menyediakan akses langsung bagi pelanggan/konsumen
- Sistem digunakan secara INTERNAL oleh perusahaan

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Frontend**: Blade Template + Tailwind CSS 4 + Alpine.js 3
- **Database**: MySQL 8.4
- **Bundler**: Vite 6
- **Authentication**: Laravel Fortify
- **Charts**: Chart.js 4
- **File Storage**: Laravel Storage (local/S3)
- **PDF Export**: barryvdh/laravel-dompdf 3.x
- **Excel Export**: maatwebsite/excel 3.1+
- **Version Control**: Git
- **Runtime**: Node.js 22 LTS, Composer 2.7+

## Konsep Utama

- CRM (Customer Relationship Management)
- Role-Based Access Control (RBAC)
- MVC Architecture
- RESTful routing
