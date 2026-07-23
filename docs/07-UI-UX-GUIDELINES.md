# UI/UX GUIDELINES

## Design System

### Framework CSS

- Tailwind CSS 4 (Oxide Engine)
- Konfigurasi via CSS `@theme`
- Custom color palette sesuai branding perusahaan

### Setup Tailwind CSS 4 (Berbeda dari v3!)

````css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
  --color-primary: #2563EB;
  --color-primary-dark: #1D4ED8;
  --color-secondary: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;
  --color-info: #6366F1;
  --color-neutral: #6B7280;
  --color-surface: #F9FAFB;
  --color-sidebar: #1F2937;

  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

### Color Palette

Primary:    #2563EB (Blue-600)    → Header, tombol utama
Secondary:  #10B981 (Emerald-500) → Sukses, tersedia
Warning:    #F59E0B (Amber-500)   → Pending, warning
Danger:     #EF4444 (Red-500)     → Error, batal, hapus
Info:       #6366F1 (Indigo-500)  → Informasi
Neutral:    #6B7280 (Gray-500)    → Teks sekunder
Background: #F9FAFB (Gray-50)     → Background halaman
Sidebar:    #1F2937 (Gray-800)    → Sidebar navigation

### Typography

- Font: Inter (Google Fonts)
- Heading: font-bold
- Body: font-normal, text-sm/text-base
- Tabel: text-sm

### Layout

┌─────────────────────────────────────────────────────┐
│ SIDEBAR (fixed, w-64) │ MAIN CONTENT │
│ │ ┌─────────────────────┐ │
│ Logo Perusahaan │ │ TOP BAR │ │
│ Menu Navigation │ │ (Search, Notif, │ │
│ - Dashboard │ │ User Profile) │ │
│ - Master Data │ └─────────────────────┘ │
│ - Transaksi │ ┌─────────────────────┐ │
│ - Laporan │ │ PAGE CONTENT │ │
│ - Pengaturan │ │ │ │
│ │ │ (Tables, Forms, │ │
│ │ │ Cards, Charts) │ │
│ │ │ │ │
│ │ └─────────────────────┘ │
└─────────────────────────────────────────────────────┘

### Komponen UI

#### Card Ringkasan (Dashboard)

```html
<div class="bg-white rounded-lg shadow p-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm text-gray-500">Total Unit Tersedia</p>
      <p class="text-2xl font-bold text-gray-800">150</p>
    </div>
    <div class="p-3 bg-blue-100 rounded-full">
      <svg><!-- icon --></svg>
    </div>
  </div>
</div>
````

### Tabel Data

- Header: bg-gray-50, font-semibold, text-xs uppercase
- Row: hover:bg-gray-50, border-b
- Aksi: tombol icon (edit, hapus, lihat)
- Pagination: bawah tabel
- Search & Filter: atas tabel

### Form Input

- Label: text-sm font-medium text-gray-700, mb-1
- Input: rounded-lg border-gray-300, focus:ring-blue-500
- Error: text-red-500 text-xs mt-1
- Required: tanda \* merah di label

### Modal

- Konfirmasi hapus
- Detail record
- Form cepat

### Alert/Toast

- Success: green
- Error: red
- Warning: yellow
- Auto-dismiss 5 detik

### Responsive

- Desktop: sidebar tetap terlihat
- Tablet: sidebar collapse (hamburger)
- Mobile: sidebar overlay, tabel horizontal scroll

### Status Badge Colors

- Tersedia : bg-green-100 text-green-800
- Dibooking : bg-yellow-100 text-yellow-800
- Dijual : bg-blue-100 text-blue-800
- Dibatalkan : bg-red-100 text-red-800
- Prospek : bg-gray-100 text-gray-800
- Booking : bg-yellow-100 text-yellow-800
- Pengajuan KPR : bg-indigo-100 text-indigo-800
- Akad : bg-blue-100 text-blue-800
- Serah Terima : bg-green-100 text-green-800
- Batal : bg-red-100 text-red-800
- Pending : bg-yellow-100 text-yellow-800
- Diverifikasi : bg-green-100 text-green-800
- Ditolak : bg-red-100 text-red-800
