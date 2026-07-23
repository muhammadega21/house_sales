# BUSINESS RULES

## Master Data

| ID    | Rule                                         | Implementasi                                |
| ----- | -------------------------------------------- | ------------------------------------------- |
| BR-01 | Kode unit harus unik dalam satu perumahan    | UNIQUE constraint (id_perumahan, kode_unit) |
| BR-02 | Harga jual harus > 0                         | Validasi input                              |
| BR-03 | Luas tanah >= luas bangunan                  | Validasi input                              |
| BR-04 | Unit "dijual" tidak bisa diubah datanya      | Policy check                                |
| BR-05 | Perumahan non-aktif tidak bisa ditambah unit | Validasi di controller                      |

## Prospek & Konsumen

| ID    | Rule                                                | Implementasi                 |
| ----- | --------------------------------------------------- | ---------------------------- |
| BR-06 | NIK (KTP) harus unik                                | UNIQUE constraint            |
| BR-07 | Prospek → konsumen saat booking                     | Otomatis ubah status_prospek |
| BR-08 | No. HP wajib diisi                                  | Required validation          |
| BR-09 | Satu konsumen bisa > 1 booking                      | Tidak ada unique constraint  |
| BR-10 | Data konsumen tidak bisa dihapus jika ada transaksi | Soft delete / policy         |

## Booking

| ID    | Rule                                                                    | Implementasi                                 |
| ----- | ----------------------------------------------------------------------- | -------------------------------------------- |
| BR-11 | Satu unit hanya bisa dibooking satu konsumen pada satu waktu            | Cek status_unit = 'tersedia' sebelum booking |
| BR-12 | Booking fee minimum: Rp 1.000.000 (subsidi), Rp 5.000.000 (non-subsidi) | Validasi berdasarkan kategori unit           |
| BR-13 | Booking hangus jika tidak membayar DP dalam 14 hari                    | Scheduled command / manual check             |
| BR-14 | Booking fee non-refundable (kecuali kebijakan khusus)                   | Business logic                               |
| BR-15 | Kode booking auto-generate: BK-YYYYMMDD-XXX                             | Service layer                                |

## Pembayaran

| ID    | Rule                                             | Implementasi             |
| ----- | ------------------------------------------------ | ------------------------ |
| BR-16 | Pembayaran harus diverifikasi Admin              | Status default 'pending' |
| BR-17 | Total DP + cicilan = harga rumah (cash bertahap) | Validasi di service      |
| BR-18 | Bukti pembayaran wajib diupload                  | Required file validation |
| BR-19 | Alert H-3 sebelum jatuh tempo cicilan            | Scheduled notification   |

## Dokumen & KPR

| ID    | Rule                                              | Implementasi             |
| ----- | ------------------------------------------------- | ------------------------ |
| BR-20 | Dokumen wajib lengkap sebelum pengajuan KPR       | Checklist validation     |
| BR-21 | Dokumen harus diverifikasi Admin sebelum diajukan | Status check             |
| BR-22 | Max upload 5MB per file (gambar), 10MB (PDF)      | Validation rule          |
| BR-23 | Format: PDF, JPG, JPEG, PNG                       | MIME type validation     |
| BR-24 | Satu konsumen bisa ajukan KPR ke > 1 bank         | Multiple records allowed |

## Status Penjualan

| ID    | Rule                                              | Implementasi              |
| ----- | ------------------------------------------------- | ------------------------- |
| BR-25 | Status tidak boleh melompat (harus berurutan)     | State machine validation  |
| BR-26 | Status "batal" bersifat final                     | Tidak ada transisi keluar |
| BR-27 | Perubahan status wajib disertai catatan           | Required field            |
| BR-28 | Unit kembali "tersedia" jika batal (sebelum akad) | Observer/event            |
| BR-29 | Setelah "serah_terima", status final              | State machine             |

## Marketing & Komisi

| ID    | Rule                                  | Implementasi    |
| ----- | ------------------------------------- | --------------- |
| BR-30 | Komisi dihitung saat status = "akad"  | Event listener  |
| BR-31 | Booking batal → komisi tidak dihitung | Rollback logic  |
| BR-32 | Target marketing reset setiap bulan   | Periode bulanan |
| BR-33 | Marketing non-aktif tidak bisa login  | Auth check      |

## Simulasi Pembayaran

| ID    | Rule                                            | Implementasi      |
| ----- | ----------------------------------------------- | ----------------- |
| BR-34 | Simulasi tidak mengikat                         | Disclaimer di UI  |
| BR-35 | DP min subsidi: 1-5%, non-subsidi: 10-30%       | Validasi range    |
| BR-36 | Tenor KPR: 1-20 tahun                           | Validasi range    |
| BR-37 | Cash keras bisa dapat diskon (diatur manajemen) | Pengaturan sistem |
