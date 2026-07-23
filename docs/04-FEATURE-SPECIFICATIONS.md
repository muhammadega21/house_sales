# FEATURE SPECIFICATIONS

## Fitur 1: Login & Autentikasi

### Deskripsi

Sistem login dengan username/password untuk 3 role berbeda.

### Input

- Username (required)
- Password (required)

### Proses

1. User input username & password
2. Sistem validasi kredensial
3. Jika valid → redirect ke dashboard sesuai role
4. Jika tidak valid → tampilkan error, max 5x percobaan
5. Setelah 5x gagal → lockout 15 menit

### Output

- Dashboard Admin (untuk role admin)
- Dashboard Marketing (untuk role marketing)
- Dashboard Manajemen (untuk role manajemen)

### Validasi

- Username: required, exists in users table
- Password: required, min 8 karakter
- Status user harus 'aktif'

---

## Fitur 2: Master Data Perumahan

### Deskripsi

CRUD data kawasan perumahan.

### Field Form

- Nama Perumahan (required, max 150)
- Alamat (required, textarea)
- Kota (required)
- Provinsi (required)
- Kode Pos (optional)
- Latitude/Longitude (optional)
- Deskripsi (optional)
- Foto Kawasan (optional, image, max 5MB)
- Status (aktif/non-aktif)

### Relasi

- Satu perumahan memiliki banyak unit rumah

### Aturan

- Perumahan non-aktif tidak muncul di dropdown saat booking
- Tidak bisa hapus jika masih ada unit terkait

---

## Fitur 3: Master Data Unit Rumah

### Deskripsi

CRUD data unit rumah dalam suatu perumahan.

### Field Form

- Perumahan (dropdown, required)
- Kode Unit (required, unique per perumahan)
- Tipe Rumah (required, e.g., "36/60")
- Kategori (required: subsidi/non-subsidi)
- Jenis Ketersediaan (required: ready_stock/indent)
- Luas Tanah m² (required, numeric)
- Luas Bangunan m² (required, numeric)
- Jumlah Kamar Tidur (optional)
- Jumlah Kamar Mandi (optional)
- Harga Jual Rp (required, numeric > 0)
- DP Minimum % (optional)
- Foto Unit (optional)
- Denah Unit (optional)
- Tanggal Selesai Bangun (optional, untuk indent)

### Status Unit

- tersedia → bisa dibooking
- dibooking → sedang dalam proses booking
- dijual → sudah akad/serah terima
- dibatalkan → booking batal, unit dikembalikan ke tersedia

---

## Fitur 4: Kategori Rumah

### Deskripsi

Klasifikasi rumah berdasarkan subsidi dan ketersediaan.

### Kategori

1. **Subsidi**: Rumah dengan bantuan pemerintah (FLPP/BP2BT)
2. **Non-Subsidi**: Rumah komersial
3. **Ready Stock**: Unit sudah selesai dibangun
4. **Indent**: Unit belum dibangun, dibangun setelah pemesanan

### Implementasi

- Field `kategori` di tabel unit_rumah: ENUM('subsidi','non_subsidi')
- Field `jenis_ketersediaan` di tabel unit_rumah: ENUM('ready_stock','indent')
- Filter dan pencarian berdasarkan kategori di halaman listing

---

## Fitur 5: Data Konsumen

### Deskripsi

Pengelolaan data konsumen (pembeli rumah).

### Field Form

- Nama Lengkap (required, sesuai KTP)
- NIK/KTP (required, 16 digit, unique)
- No. KK (optional, 16 digit)
- No. HP (required)
- Email (optional)
- Alamat Lengkap (required, textarea)
- Tempat Lahir (optional)
- Tanggal Lahir (optional)
- Jenis Kelamin (optional: L/P)
- Status Pernikahan (optional)
- Pekerjaan (optional)
- Nama Perusahaan (optional)
- Penghasilan Bulanan (optional, numeric)
- NPWP (optional)
- Foto KTP (optional, image)
- Foto KK (optional, image)

### Aturan

- NIK harus unik (satu orang = satu record)
- Data tidak bisa dihapus jika sudah ada transaksi booking
- Marketing hanya bisa lihat/edit konsumen miliknya

---

## Fitur 6: Data Booking Rumah

### Deskripsi

Proses pemesanan unit rumah oleh konsumen melalui marketing.

### Field Form

- Konsumen (dropdown/search, required)
- Unit Rumah (dropdown/search, required, hanya status 'tersedia')
- Tanggal Booking (required, default today)
- Booking Fee (required, numeric)
- Metode Bayar (optional: transfer/tunai/debit)
- Bukti Bayar (optional, file)
- Catatan (optional)

### Proses

1. Marketing memilih konsumen dan unit
2. Sistem memeriksa ketersediaan unit
3. Jika tersedia → input booking fee
4. Sistem menghasilkan kode booking (BK-YYYYMMDD-XXX)
5. Sistem mengubah status unit menjadi 'dibooking'
6. Sistem membuat record status_penjualan = 'booking'
7. Tampilkan konfirmasi berhasil

### Aturan

- Unit harus berstatus 'tersedia'
- Booking fee min: Rp 1.000.000 (subsidi), Rp 5.000.000 (non-subsidi)
- Satu unit hanya bisa dibooking satu konsumen

---

## Fitur 7: Simulasi Pembayaran

### Deskripsi

Kalkulator estimasi cicilan untuk 3 metode pembayaran.

### Input

- Unit Rumah (dropdown → auto-fill harga)
- Metode Pembayaran (KPR / Cash Bertahap / Cash Keras)
- DP % (slider/input)
- Tenor tahun (khusus KPR & Cash Bertahap)
- Suku Bunga % (khusus KPR)

### Output

- Nominal DP
- Plafon KPR (khusus KPR)
- Cicilan Bulanan
- Total Pembayaran
- Total Bunga (khusus KPR)
- Perbandingan 3 metode (side-by-side)

### Rumus

**KPR (Bunga Flat):**
Cicilan = (Plafon × (1 + (Bunga% / 100 × Tenor))) / (Tenor × 12)

**KPR (Bunga Efektif/Anuitas):**
i = Bunga% / 12
n = Tenor × 12
Cicilan = Plafon × [i × (1+i)^n] / [(1+i)^n - 1]

**Cash Bertahap:**
DP nominal = Harga × (DP% / 100)
Cicilan = (Harga - DP nominal) / (Tenor × 12)

**Cash Keras:**
Total = Harga - Diskon (jika ada)

---

## Fitur 8: Upload Dokumen Konsumen

### Deskripsi

Upload dan verifikasi dokumen persyaratan KPR.

### Jenis Dokumen

| Dokumen                | Wajib        | Format      | Max Size |
| ---------------------- | ------------ | ----------- | -------- |
| KTP                    | Ya           | PDF/JPG/PNG | 5 MB     |
| KK                     | Ya           | PDF/JPG/PNG | 5 MB     |
| NPWP                   | Ya           | PDF/JPG/PNG | 5 MB     |
| Slip Gaji (3 bln)      | Ya           | PDF         | 10 MB    |
| Rekening Koran (3 bln) | Ya           | PDF         | 10 MB    |
| Surat Kerja            | Ya           | PDF         | 5 MB     |
| Surat Nikah            | Jika menikah | PDF/JPG/PNG | 5 MB     |
| Formulir KPR           | Ya           | PDF         | 5 MB     |
| Lainnya                | Opsional     | PDF/JPG/PNG | 10 MB    |

### Alur

1. Marketing upload dokumen → status "belum_diverifikasi"
2. Admin review → "valid" / "tidak_valid" / "perlu_revisi"
3. Jika semua dokumen wajib "valid" → bisa ajukan KPR

---

## Fitur 9: Status Penjualan

### Deskripsi

Tracking status penjualan dari prospek hingga serah terima.

### Alur Status

prospek → booking → pengajuan_kpr → akad → serah_terima
↓ ↓ ↓ ↓
batal batal batal batal

### Aturan Transisi

- Tidak boleh melompat status
- Batal bersifat final
- Wajib isi catatan saat ubah status
- Unit kembali 'tersedia' jika batal sebelum akad
- Setiap perubahan dicatat di status_history

---

## Fitur 10: Data Marketing & Kinerja

### Deskripsi

Pengelolaan data marketing dan tracking kinerja.

### Data Marketing

- Nama, username, password, no HP, email
- Target bulanan (jumlah unit)
- Persentase komisi

### Kinerja (per bulan)

- Jumlah prospek
- Jumlah booking
- Jumlah closing (akad)
- Jumlah batal
- Conversion rate
- Total nilai penjualan
- Total komisi
- Pencapaian target (%)

### Perhitungan Komisi

Komisi per unit = Harga Jual × Persentase Komisi
Komisi dihitung saat status = 'akad'

---

## Fitur 11: Laporan Penjualan

### Deskripsi

Laporan penjualan dengan berbagai filter dan parameter.

### Jenis Laporan

1. **Penjualan per Bulan**: Total unit, total nilai, per kategori
2. **Penjualan per Marketing**: Closing, nilai, komisi per marketing
3. **Unit Terjual vs Tersedia**: Stok unit per perumahan
4. **Total Nilai Penjualan**: Akumulasi per periode

### Filter

- Periode (bulan/tahun/custom range)
- Perumahan
- Kategori (subsidi/non-subsidi)
- Marketing
- Status

### Export

- PDF (untuk cetak)
- Excel (untuk olah data)

---

## Fitur 12: Dashboard Manajemen

### Deskripsi

Dashboard overview untuk monitoring perusahaan.

### Komponen

1. **Kartu Ringkasan**:
   - Total rumah tersedia
   - Total rumah terjual
   - Total booking aktif
   - Total nilai penjualan (bulan ini)

2. **Grafik**:
   - Penjualan per bulan (bar chart)
   - Subsidi vs Non-subsidi (pie chart)
   - Tren prospek vs closing (line chart)

3. **Tabel**:
   - Marketing terbaik (top 5)
   - Booking terbaru
   - Unit tersedia per perumahan

4. **Widget**:
   - Conversion rate perusahaan
   - Rata-rata waktu closing
   - Dokumen pending verifikasi
