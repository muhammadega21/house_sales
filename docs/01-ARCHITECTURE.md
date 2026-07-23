# SYSTEM ARCHITECTURE

## Arsitektur Umum

Sistem menggunakan arsitektur MVC (Model-View-Controller) dengan framework Laravel.
┌─────────────────────────────────────────────────┐
│ BROWSER (Client) │
│ Admin / Marketing / Manajemen │
└──────────────────────┬──────────────────────────┘
│ HTTP/HTTPS
▼
┌─────────────────────────────────────────────────┐
│ WEB SERVER (Nginx/Apache) │
└──────────────────────┬──────────────────────────┘
│
▼
┌─────────────────────────────────────────────────┐
│ LARAVEL APPLICATION │
│ ┌───────────┐ ┌───────────┐ ┌───────────┐ │
│ │ Routes │→ │Middleware │→ │Controllers│ │
│ └───────────┘ └───────────┘ └─────┬─────┘ │
│ │ │
│ ┌───────────┐ ┌───────────┐ ┌─────▼─────┐ │
│ │ Views │← │ Models │← │ Services │ │
│ │ (Blade) │ │ (Eloquent)│ │ Layer │ │
│ └───────────┘ └─────┬─────┘ └───────────┘ │
│ │ │
└────────────────────────┼─────────────────────────┘
│
▼
┌─────────────────────────────────────────────────┐
│ MySQL DATABASE │
└─────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────┐
│ FILE STORAGE │
│ (Dokumen, Foto, Bukti Bayar) │
└─────────────────────────────────────────────────┘

## Layer Arsitektur

### 1. Route Layer

- Mendefinisikan URL dan memetakan ke controller
- Middleware untuk autentikasi dan otorisasi role

### 2. Middleware Layer

- `Authenticate`: Memastikan user sudah login
- `RoleMiddleware`: Memastikan user memiliki role yang sesuai
- `PreventBackHistory`: Mencegah akses halaman setelah logout

### 3. Controller Layer

- Menangani HTTP request dan response
- Validasi input
- Memanggil Service/Model

### 4. Service Layer (Business Logic)

- Logika bisnis kompleks (simulasi pembayaran, perhitungan komisi)
- Transaksi database yang melibatkan banyak model
- Aturan bisnis dan validasi domain

### 5. Model Layer

- Representasi tabel database
- Relasi antar entitas
- Scope dan accessor/mutator

### 6. View Layer

- Blade template
- Komponen reusable (components)
- Layout utama dan partial

## Design Patterns yang Digunakan

- **Repository Pattern** (opsional, untuk query kompleks)
- **Service Pattern** (untuk business logic)
- **Observer Pattern** (untuk event saat status berubah)
- **Policy Pattern** (untuk authorization per-model)

## Tech Stack Detail

### Laravel 12

- PHP 8.3+ required (dukungan 8.4)
- Menggunakan Laravel Fortify sebagai auth stack
- Default: Vite 6, Tailwind CSS 4, Alpine.js 3
- Support: PHP 8.3, 8.4
- Middleware didaftarkan di `bootstrap/app.php`
- Service provider didaftarkan di `bootstrap/providers.php`

### Tailwind CSS 4 (Perubahan Penting dari v3)

- Konfigurasi via CSS: `@theme` directive di file CSS
- Menggunakan engine baru (Oxide) — lebih cepat
- Import via: `@import "tailwindcss";`
- Tidak memerlukan file konfigurasi klasik seperti pada Tailwind v3

### Vite 6

- Default bundler Laravel 12
- Konfigurasi di `vite.config.js`
- Support HMR (Hot Module Replacement)

### Authentication

- Laravel 12 menggunakan Laravel Fortify / starter kit baru
- Menggunakan Fortify + custom Blade views
