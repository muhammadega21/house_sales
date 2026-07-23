# DATABASE SCHEMA

## Entity Relationship Overview

users (1) ──── (N) prospek
users (1) ──── (N) konsumen
users (1) ──── (N) booking
users (1) ──── (N) marketing_target
perumahan (1) ──── (N) unit_rumah
konsumen (1) ──── (N) booking
konsumen (1) ──── (N) dokumen_kpr
konsumen (1) ──── (N) pengajuan_kpr
konsumen (1) ──── (N) simulasi_pembayaran
unit_rumah (1) ──── (N) booking
booking (1) ──── (N) pembayaran
booking (1) ──── (1) pengajuan_kpr
booking (1) ──── (N) status_history
prospek (1) ──── (0..1) konsumen

## Tabel: users

| Column         | Type                                  | Constraint         | Description |
| -------------- | ------------------------------------- | ------------------ | ----------- |
| id             | BIGINT UNSIGNED                       | PK, AUTO_INCREMENT |             |
| nama_lengkap   | VARCHAR(100)                          | NOT NULL           |             |
| username       | VARCHAR(50)                           | UNIQUE, NOT NULL   |             |
| password       | VARCHAR(255)                          | NOT NULL           | Bcrypt hash |
| email          | VARCHAR(100)                          | UNIQUE, NULLABLE   |             |
| no_hp          | VARCHAR(15)                           | NULLABLE           |             |
| role           | ENUM('admin','marketing','manajemen') | NOT NULL           |             |
| foto_profil    | VARCHAR(255)                          | NULLABLE           |             |
| status         | ENUM('aktif','non_aktif')             | DEFAULT 'aktif'    |             |
| remember_token | VARCHAR(100)                          | NULLABLE           |             |
| created_at     | TIMESTAMP                             | NULLABLE           |             |
| updated_at     | TIMESTAMP                             | NULLABLE           |             |

## Tabel: perumahan

| Column         | Type                      | Constraint         | Description |
| -------------- | ------------------------- | ------------------ | ----------- |
| id             | BIGINT UNSIGNED           | PK, AUTO_INCREMENT |             |
| nama_perumahan | VARCHAR(150)              | NOT NULL           |             |
| alamat         | TEXT                      | NOT NULL           |             |
| kota           | VARCHAR(50)               | NOT NULL           |             |
| provinsi       | VARCHAR(50)               | NOT NULL           |             |
| kode_pos       | VARCHAR(10)               | NULLABLE           |             |
| latitude       | DECIMAL(10,8)             | NULLABLE           |             |
| longitude      | DECIMAL(11,8)             | NULLABLE           |             |
| total_unit     | INT                       | DEFAULT 0          |             |
| deskripsi      | TEXT                      | NULLABLE           |             |
| foto_kawasan   | VARCHAR(255)              | NULLABLE           |             |
| status         | ENUM('aktif','non_aktif') | DEFAULT 'aktif'    |             |
| created_at     | TIMESTAMP                 | NULLABLE           |             |
| updated_at     | TIMESTAMP                 | NULLABLE           |             |

## Tabel: unit_rumah

| Column                 | Type                                               | Constraint         | Description       |
| ---------------------- | -------------------------------------------------- | ------------------ | ----------------- |
| id                     | BIGINT UNSIGNED                                    | PK, AUTO_INCREMENT |                   |
| id_perumahan           | BIGINT UNSIGNED                                    | FK → perumahan.id  |                   |
| kode_unit              | VARCHAR(20)                                        | UNIQUE, NOT NULL   | Blok/Nomor        |
| tipe_rumah             | VARCHAR(50)                                        | NOT NULL           | 36/60, 45/72, dll |
| kategori               | ENUM('subsidi','non_subsidi')                      | NOT NULL           |                   |
| jenis_ketersediaan     | ENUM('ready_stock','indent')                       | NOT NULL           |                   |
| luas_tanah             | DECIMAL(8,2)                                       | NOT NULL           | m²                |
| luas_bangunan          | DECIMAL(8,2)                                       | NOT NULL           | m²                |
| jumlah_kamar_tidur     | INT                                                | NULLABLE           |                   |
| jumlah_kamar_mandi     | INT                                                | NULLABLE           |                   |
| harga_jual             | DECIMAL(18,2)                                      | NOT NULL           | Rupiah            |
| dp_minimum_persen      | DECIMAL(5,2)                                       | NULLABLE           | %                 |
| status_unit            | ENUM('tersedia','dibooking','dijual','dibatalkan') | DEFAULT 'tersedia' |                   |
| foto_unit              | VARCHAR(255)                                       | NULLABLE           |                   |
| denah_unit             | VARCHAR(255)                                       | NULLABLE           |                   |
| tanggal_selesai_bangun | DATE                                               | NULLABLE           | Untuk indent      |
| created_at             | TIMESTAMP                                          | NULLABLE           |                   |
| updated_at             | TIMESTAMP                                          | NULLABLE           |                   |

## Tabel: prospek

| Column          | Type                                                                 | Constraint         | Description |
| --------------- | -------------------------------------------------------------------- | ------------------ | ----------- |
| id              | BIGINT UNSIGNED                                                      | PK, AUTO_INCREMENT |             |
| id_marketing    | BIGINT UNSIGNED                                                      | FK → users.id      |             |
| nama_prospek    | VARCHAR(100)                                                         | NOT NULL           |             |
| no_hp           | VARCHAR(15)                                                          | NOT NULL           |             |
| email           | VARCHAR(100)                                                         | NULLABLE           |             |
| sumber_prospek  | ENUM('facebook','instagram','tiktok','walk_in','referral','lainnya') | NULLABLE           |             |
| catatan         | TEXT                                                                 | NULLABLE           |             |
| status_prospek  | ENUM('baru','dihubungi','berminat','tidak_berminat','jadi_konsumen') | DEFAULT 'baru'     |             |
| tanggal_prospek | DATE                                                                 | NOT NULL           |             |
| created_at      | TIMESTAMP                                                            | NULLABLE           |             |
| updated_at      | TIMESTAMP                                                            | NULLABLE           |             |

## Tabel: konsumen

| Column              | Type                                                       | Constraint                | Description |
| ------------------- | ---------------------------------------------------------- | ------------------------- | ----------- |
| id                  | BIGINT UNSIGNED                                            | PK, AUTO_INCREMENT        |             |
| id_prospek          | BIGINT UNSIGNED                                            | FK → prospek.id, NULLABLE |             |
| id_marketing        | BIGINT UNSIGNED                                            | FK → users.id             |             |
| nama_lengkap        | VARCHAR(100)                                               | NOT NULL                  | Sesuai KTP  |
| nik                 | VARCHAR(16)                                                | UNIQUE, NOT NULL          |             |
| no_kk               | VARCHAR(16)                                                | NULLABLE                  |             |
| no_hp               | VARCHAR(15)                                                | NOT NULL                  |             |
| email               | VARCHAR(100)                                               | NULLABLE                  |             |
| alamat_lengkap      | TEXT                                                       | NOT NULL                  |             |
| tempat_lahir        | VARCHAR(50)                                                | NULLABLE                  |             |
| tanggal_lahir       | DATE                                                       | NULLABLE                  |             |
| jenis_kelamin       | ENUM('L','P')                                              | NULLABLE                  |             |
| status_pernikahan   | ENUM('belum_menikah','menikah','cerai_hidup','cerai_mati') | NULLABLE                  |             |
| pekerjaan           | VARCHAR(100)                                               | NULLABLE                  |             |
| nama_perusahaan     | VARCHAR(100)                                               | NULLABLE                  |             |
| penghasilan_bulanan | DECIMAL(15,2)                                              | NULLABLE                  |             |
| npwp                | VARCHAR(15)                                                | NULLABLE                  |             |
| foto_ktp            | VARCHAR(255)                                               | NULLABLE                  |             |
| foto_kk             | VARCHAR(255)                                               | NULLABLE                  |             |
| created_at          | TIMESTAMP                                                  | NULLABLE                  |             |
| updated_at          | TIMESTAMP                                                  | NULLABLE                  |             |

## Tabel: booking

| Column                | Type                                       | Constraint            | Description     |
| --------------------- | ------------------------------------------ | --------------------- | --------------- |
| id                    | BIGINT UNSIGNED                            | PK, AUTO_INCREMENT    |                 |
| kode_booking          | VARCHAR(20)                                | UNIQUE, NOT NULL      | BK-YYYYMMDD-XXX |
| id_konsumen           | BIGINT UNSIGNED                            | FK → konsumen.id      |                 |
| id_unit               | BIGINT UNSIGNED                            | FK → unit_rumah.id    |                 |
| id_marketing          | BIGINT UNSIGNED                            | FK → users.id         |                 |
| tanggal_booking       | DATE                                       | NOT NULL              |                 |
| booking_fee           | DECIMAL(15,2)                              | NOT NULL              |                 |
| status_pembayaran_fee | ENUM('belum_bayar','sudah_bayar','refund') | DEFAULT 'belum_bayar' |                 |
| tanggal_bayar_fee     | DATE                                       | NULLABLE              |                 |
| metode_bayar_fee      | ENUM('transfer','tunai','debit')           | NULLABLE              |                 |
| bukti_bayar_fee       | VARCHAR(255)                               | NULLABLE              |                 |
| catatan               | TEXT                                       | NULLABLE              |                 |
| created_at            | TIMESTAMP                                  | NULLABLE              |                 |
| updated_at            | TIMESTAMP                                  | NULLABLE              |                 |

## Tabel: pembayaran

| Column             | Type                                           | Constraint              | Description |
| ------------------ | ---------------------------------------------- | ----------------------- | ----------- |
| id                 | BIGINT UNSIGNED                                | PK, AUTO_INCREMENT      |             |
| id_booking         | BIGINT UNSIGNED                                | FK → booking.id         |             |
| id_konsumen        | BIGINT UNSIGNED                                | FK → konsumen.id        |             |
| jenis_pembayaran   | ENUM('booking_fee','dp','cicilan','pelunasan') | NOT NULL                |             |
| nominal            | DECIMAL(18,2)                                  | NOT NULL                |             |
| tanggal_bayar      | DATE                                           | NOT NULL                |             |
| metode_bayar       | ENUM('transfer','tunai','debit','kpr')         | NULLABLE                |             |
| no_referensi       | VARCHAR(50)                                    | NULLABLE                |             |
| bukti_bayar        | VARCHAR(255)                                   | NULLABLE                |             |
| status_verifikasi  | ENUM('pending','diverifikasi','ditolak')       | DEFAULT 'pending'       |             |
| diverifikasi_oleh  | BIGINT UNSIGNED                                | FK → users.id, NULLABLE |             |
| tanggal_verifikasi | DATE                                           | NULLABLE                |             |
| created_at         | TIMESTAMP                                      | NULLABLE                |             |
| updated_at         | TIMESTAMP                                      | NULLABLE                |             |

## Tabel: dokumen_kpr

| Column             | Type                                                                                                                                     | Constraint                   | Description |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------- | ----------- |
| id                 | BIGINT UNSIGNED                                                                                                                          | PK, AUTO_INCREMENT           |             |
| id_konsumen        | BIGINT UNSIGNED                                                                                                                          | FK → konsumen.id             |             |
| jenis_dokumen      | ENUM('ktp','kk','npwp','slip_gaji','rekening_koran','surat_kerja','surat_nikah','surat_keterangan_penghasilan','formulir_kpr','lainnya') | NOT NULL                     |             |
| nama_file          | VARCHAR(255)                                                                                                                             | NOT NULL                     |             |
| path_file          | VARCHAR(255)                                                                                                                             | NOT NULL                     |             |
| ukuran_file        | INT                                                                                                                                      | NULLABLE                     | bytes       |
| tipe_file          | VARCHAR(20)                                                                                                                              | NULLABLE                     | pdf/jpg/png |
| status_verifikasi  | ENUM('belum_diverifikasi','valid','tidak_valid','perlu_revisi')                                                                          | DEFAULT 'belum_diverifikasi' |             |
| catatan_verifikasi | TEXT                                                                                                                                     | NULLABLE                     |             |
| diupload_oleh      | BIGINT UNSIGNED                                                                                                                          | FK → users.id                |             |
| tanggal_upload     | TIMESTAMP                                                                                                                                | DEFAULT CURRENT_TIMESTAMP    |             |
| tanggal_verifikasi | TIMESTAMP                                                                                                                                | NULLABLE                     |             |

## Tabel: pengajuan_kpr

| Column            | Type                                                                            | Constraint         | Description |
| ----------------- | ------------------------------------------------------------------------------- | ------------------ | ----------- |
| id                | BIGINT UNSIGNED                                                                 | PK, AUTO_INCREMENT |             |
| id_konsumen       | BIGINT UNSIGNED                                                                 | FK → konsumen.id   |             |
| id_booking        | BIGINT UNSIGNED                                                                 | FK → booking.id    |             |
| id_unit           | BIGINT UNSIGNED                                                                 | FK → unit_rumah.id |             |
| nama_bank         | VARCHAR(100)                                                                    | NULLABLE           |             |
| plafon_kpr        | DECIMAL(18,2)                                                                   | NULLABLE           |             |
| tenor_tahun       | INT                                                                             | NULLABLE           |             |
| suku_bunga        | DECIMAL(5,2)                                                                    | NULLABLE           |             |
| tanggal_pengajuan | DATE                                                                            | NOT NULL           |             |
| status_pengajuan  | ENUM('draft','diajukan','verifikasi_bank','disetujui','ditolak','akad','batal') | DEFAULT 'draft'    |             |
| tanggal_keputusan | DATE                                                                            | NULLABLE           |             |
| catatan           | TEXT                                                                            | NULLABLE           |             |
| created_at        | TIMESTAMP                                                                       | NULLABLE           |             |
| updated_at        | TIMESTAMP                                                                       | NULLABLE           |             |

## Tabel: status_penjualan

| Column            | Type                                                                    | Constraint                | Description |
| ----------------- | ----------------------------------------------------------------------- | ------------------------- | ----------- |
| id                | BIGINT UNSIGNED                                                         | PK, AUTO_INCREMENT        |             |
| id_booking        | BIGINT UNSIGNED                                                         | FK → booking.id           |             |
| id_konsumen       | BIGINT UNSIGNED                                                         | FK → konsumen.id          |             |
| id_unit           | BIGINT UNSIGNED                                                         | FK → unit_rumah.id        |             |
| status_saat_ini   | ENUM('prospek','booking','pengajuan_kpr','akad','serah_terima','batal') | NOT NULL                  |             |
| tanggal_perubahan | TIMESTAMP                                                               | DEFAULT CURRENT_TIMESTAMP |             |
| diubah_oleh       | BIGINT UNSIGNED                                                         | FK → users.id             |             |
| catatan           | TEXT                                                                    | NULLABLE                  |             |

## Tabel: status_history

| Column         | Type                                                                    | Constraint                | Description             |
| -------------- | ----------------------------------------------------------------------- | ------------------------- | ----------------------- |
| id             | BIGINT UNSIGNED                                                         | PK, AUTO_INCREMENT        |                         |
| id_booking     | BIGINT UNSIGNED                                                         | FK → booking.id           |                         |
| status_sebelum | ENUM('prospek','booking','pengajuan_kpr','akad','serah_terima','batal') | NULLABLE                  | NULL untuk pertama kali |
| status_sesudah | ENUM('prospek','booking','pengajuan_kpr','akad','serah_terima','batal') | NOT NULL                  |                         |
| catatan        | TEXT                                                                    | NULLABLE                  |                         |
| diubah_oleh    | BIGINT UNSIGNED                                                         | FK → users.id             |                         |
| created_at     | TIMESTAMP                                                               | DEFAULT CURRENT_TIMESTAMP |                         |

## Tabel: marketing_target

| Column                | Type            | Constraint         | Description |
| --------------------- | --------------- | ------------------ | ----------- |
| id                    | BIGINT UNSIGNED | PK, AUTO_INCREMENT |             |
| id_marketing          | BIGINT UNSIGNED | FK → users.id      |             |
| periode_bulan         | INT             | NOT NULL           | 1-12        |
| periode_tahun         | INT             | NOT NULL           |             |
| target_unit           | INT             | NOT NULL           |             |
| realisasi_unit        | INT             | DEFAULT 0          |             |
| total_nilai_penjualan | DECIMAL(18,2)   | DEFAULT 0          |             |
| total_komisi          | DECIMAL(15,2)   | DEFAULT 0          |             |
| created_at            | TIMESTAMP       | NULLABLE           |             |
| updated_at            | TIMESTAMP       | NULLABLE           |             |

## Tabel: simulasi_pembayaran

| Column            | Type                                     | Constraint                 | Description |
| ----------------- | ---------------------------------------- | -------------------------- | ----------- |
| id                | BIGINT UNSIGNED                          | PK, AUTO_INCREMENT         |             |
| id_konsumen       | BIGINT UNSIGNED                          | FK → konsumen.id, NULLABLE |             |
| id_unit           | BIGINT UNSIGNED                          | FK → unit_rumah.id         |             |
| id_marketing      | BIGINT UNSIGNED                          | FK → users.id              |             |
| metode_pembayaran | ENUM('kpr','cash_bertahap','cash_keras') | NOT NULL                   |             |
| harga_rumah       | DECIMAL(18,2)                            | NOT NULL                   |             |
| dp_persen         | DECIMAL(5,2)                             | NULLABLE                   |             |
| dp_nominal        | DECIMAL(18,2)                            | NULLABLE                   |             |
| tenor_tahun       | INT                                      | NULLABLE                   |             |
| suku_bunga        | DECIMAL(5,2)                             | NULLABLE                   |             |
| cicilan_bulanan   | DECIMAL(15,2)                            | NULLABLE                   |             |
| total_pembayaran  | DECIMAL(18,2)                            | NULLABLE                   |             |
| total_bunga       | DECIMAL(18,2)                            | NULLABLE                   |             |
| tanggal_simulasi  | TIMESTAMP                                | DEFAULT CURRENT_TIMESTAMP  |             |

## Tabel: pengaturan_sistem

| Column     | Type            | Constraint         | Description |
| ---------- | --------------- | ------------------ | ----------- |
| id         | BIGINT UNSIGNED | PK, AUTO_INCREMENT |             |
| kunci      | VARCHAR(50)     | UNIQUE, NOT NULL   |             |
| nilai      | TEXT            | NULLABLE           |             |
| keterangan | VARCHAR(255)    | NULLABLE           |             |
| updated_at | TIMESTAMP       | NULLABLE           |             |

## Indexes yang Disarankan

- `unit_rumah`: INDEX on (id_perumahan, status_unit, kategori)
- `konsumen`: INDEX on (nik), INDEX on (id_marketing)
- `booking`: INDEX on (id_konsumen, id_unit, tanggal_booking)
- `pembayaran`: INDEX on (id_booking, status_verifikasi)
- `dokumen_kpr`: INDEX on (id_konsumen, status_verifikasi)
- `status_penjualan`: INDEX on (id_booking, status_saat_ini)
- `marketing_target`: INDEX on (id_marketing, periode_bulan, periode_tahun)
