# Product Requirement Document (PRD)

## Nama Produk

**SMART AGEN LPG**
Sistem Monitoring Armada Truk Agen LPG

---

## 1. Latar Belakang

SMART AGEN LPG dibuat untuk membantu monitoring dan pengelolaan armada truk agen LPG secara terpusat dan terstruktur berdasarkan wilayah sales area. Sistem ini diharapkan mampu memberikan visibilitas data armada, agen, dan pengemudi secara real-time, serta mempermudah proses update dan manajemen data.

---

## 2. Tujuan Produk

- Memantau data armada LPG berdasarkan wilayah
- Menyediakan ringkasan operasional untuk pengambilan keputusan
- Memastikan kelengkapan dan masa berlaku dokumen armada dan pengemudi
- Mempermudah proses input dan pembaruan data armada
- Mengelola hak akses pengguna sistem

---

## 3. Ruang Lingkup Sistem

Sistem memiliki **5 menu utama**:

1. Dashboard
2. Database Armada
3. Update Data Armada
4. Manajemen User
5. Manajemen Area

Wilayah Sales Area yang didukung:

- Sumatra Selatan
- Lampung
- Jambi
- Bengkulu
- Bangka Belitung

---

## 4. Tipe Pengguna (User Persona)

1. **Manager**

    - Full akses ke seluruh fitur
    - Full akses ke seluruh area

2. **Asmen**

    - Full akses ke seluruh fitur
    - Full akses ke seluruh area

3. **Admin**

    - Full akses ke seluruh fitur
    - Full akses ke seluruh area

4. **Operator**

    - Dapat melakukan input dan update data armada
    - Akses dibatasi hanya pada area yang ditugaskan

---

## 5. Fitur dan Kebutuhan Fungsional

### 5.0 Manajemen Area

#### Deskripsi

Menu untuk mengelola data area/wilayah operasional yang digunakan sebagai dasar pembatasan akses user dan data armada.

#### Data yang Dikelola

- Nama Area

#### Aturan

- Data area bersifat master data
- Area digunakan sebagai relasi pada tabel user dan data armada
- Penghapusan area hanya dapat dilakukan jika tidak memiliki relasi aktif

---

### 5.1 Dashboard

#### Deskripsi

Halaman utama yang menampilkan ringkasan data berdasarkan wilayah sales area.

#### Alur Penggunaan

- User memilih salah satu wilayah (contoh: **Sumatra Selatan**)
- Sistem menampilkan ringkasan data wilayah tersebut

#### Data yang Ditampilkan (per wilayah)

- Jumlah Agen
- Jumlah Armada
- Jumlah Kepemilikan Tabung
- Jumlah Supir
- Alokasi Harian

---

### 5.2 Database Armada

#### Deskripsi

Menu untuk melihat detail data armada per wilayah dan per agen.

#### Alur Penggunaan

- User memilih wilayah (contoh: **Sumatra Selatan**)
- Sistem menampilkan daftar agen beserta detail armada

#### Data yang Ditampilkan

- Nama Agen
- Jumlah Armada Aktif
- Masa Uji Kendaraan (KEUR)
- Data SIM Driver
- Data STNK Kendaraan

#### Status Dokumen (UI Only)

**Masa Uji Kendaraan (KEUR)**

- **NEAR EXPIRY**: Jika masa berlaku kurang dari 60 hari
- **NOT EXPIRED**: Jika masa berlaku lebih dari 60 hari

**Masa Habis Kendaraan**

- **NOT EXPIRED**: Jika usia kendaraan kurang dari 9 tahun
- **NEAR EXPIRED**: Jika usia kendaraan lebih dari 9 tahun

#### Fitur Tambahan (Opsional / Future Scope)

- Filter berdasarkan masa berlaku dokumen
- Indikator warna untuk dokumen hampir habis

---

### 5.3 Update Data Armada

#### Deskripsi

Menu untuk menambahkan atau memperbarui data armada dan pengemudi.

#### Field Data Armada

**Data Agen & Lokasi**

- Nama Agen
- Alamat
- Kabupaten
- Provinsi
- Wilayah SBM

**Data Armada**

- Nomor Polisi Armada
- Tahun Pembuatan Armada
- Nomor Dokumen KEUR
- Dokumen KEUR
- Masa Berlaku KEUR
- Masa Berlaku STNK
- Dokumen STNK
- Masa Habis Kendaraan

**Data Supir**

- Nama Supir
- Umur
- Masa Berlaku SIM
- Dokumen SIM

#### Validasi Data

- Field wajib tidak boleh kosong
- Format tanggal harus valid
- Nomor Polisi unik (tidak boleh duplikat)

---

### 5.4 Manajemen User

#### Deskripsi

Menu untuk mengatur pengguna sistem, hak akses, dan keterkaitan user dengan area.

#### Relasi Data

- Setiap user **wajib** terhubung dengan satu atau lebih Area
- Tabel User berelasi langsung dengan tabel Area

#### Role & Hak Akses

1. **Manager**

    - Full Akses ke seluruh fitur dan seluruh area

2. **Asmen**

    - Full Akses ke seluruh fitur dan seluruh area

3. **Admin**

    - Full Akses ke seluruh fitur dan seluruh area

4. **Operator**

    - Dapat melakukan input dan update data armada
    - Akses dibatasi hanya pada area yang ditugaskan

#### Fitur

- Tambah User
- Edit User
- Nonaktifkan User
- Pengaturan Role (Manager, Asmen, Admin, Operator)
- Penentuan Area per User

---

#### Deskripsi

Menu untuk mengatur pengguna sistem dan hak akses.

#### Fitur

- Tambah User
- Edit User
- Nonaktifkan User
- Pengaturan Role (Admin Pusat, Admin Wilayah, Viewer)
- Pembatasan akses berdasarkan wilayah

---

## 6. Kebutuhan Non-Fungsional

- **Keamanan**: Autentikasi login & role-based access control
- **Kinerja**: Respon halaman < 3 detik untuk data normal
- **Skalabilitas**: Mudah menambahkan wilayah baru
- **Audit Trail**: Pencatatan perubahan data (created_at, updated_at, updated_by)

---

## 7. Asumsi & Ketergantungan

- Data awal wilayah dan agen tersedia
- Sistem digunakan via web browser
- Tidak mencakup GPS tracking (fase awal)

---

## 8. Risiko

- Data tidak di-update secara berkala
- Ketergantungan pada input manual

Mitigasi:

- Reminder masa berlaku dokumen
- Validasi dan notifikasi data kritikal

---

## 9. Future Enhancement (Opsional)

- Notifikasi otomatis (WA / Email) untuk dokumen hampir habis
- Export laporan (PDF / Excel)
- Integrasi GPS tracking armada
- Dashboard analitik tren operasional

---

## 10. Indikator Keberhasilan (Success Metrics)

- 100% data armada terdaftar di sistem
- Penurunan kasus dokumen kedaluwarsa
- Peningkatan efisiensi monitoring wilayah

---

**Dokumen ini menjadi acuan utama pengembangan SMART AGEN LPG.**
