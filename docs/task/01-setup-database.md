# Task 1: Setup Database dan Model

## 📋 **Objective**

Membangun fondasi database dan model untuk sistem SMART AGEN LPG sesuai dengan kebutuhan PRD.

## 🎯 **Scope**

- Membuat migration untuk tabel baru
- Membuat Eloquent models dengan relasi
- Setup seeder untuk data awal
- Integrasi dengan sistem permission yang ada

## 📝 **Prerequisites**

- Project Laravel Vue sudah berjalan
- Database sudah terkoneksi
- Sistem authentication dan permission sudah setup

## 🔧 **Technical Requirements**

### **Database Schema**

```sql
-- Tabel Areas (Wilayah Sales Area)
areas:
- id (PK)
- name (string, unique)
- code (string, unique)
- is_active (boolean)
- created_at, updated_at

-- Tabel Agencies (Agen LPG)
agencies:
- id (PK)
- area_id (FK)
- name (string)
- address (text)
- regency (string)
- province (string)
- region_sbm (string)
- tube_ownership (integer)
- daily_allocation (integer)
- is_active (boolean)
- created_at, updated_at

-- Tabel Fleets (Armada Truk)
fleets:
- id (PK)
- agency_id (FK)
- license_plate (string, unique)
- year_manufacture (integer)
- keur_number (string)
- keur_expiry (date)
- keur_document (string)
- stnk_expiry (date)
- stnk_document (string)
- vehicle_expiry (date)
- is_active (boolean)
- created_at, updated_at

-- Tabel Drivers
drivers:
- id (PK)
- fleet_id (FK)
- name (string)
- age (integer)
- sim_expiry (date)
- sim_document (string)
- is_active (boolean)
- created_at, updated_at
```

### **Model Relations**

- `Area` hasMany `Agency`
- `Agency` hasMany `Fleet`
- `Fleet` hasMany `Driver`
- `Agency` belongsTo `Area`
- `Fleet` belongsTo `Agency`
- `Driver` belongsTo `Fleet`

## 📋 **Implementation Steps**

### **Step 1: Migrations**

1. Buat migration `areas`
2. Buat migration `agencies`
3. Buat migration `fleets`
4. Buat migration `drivers`
5. Jalankan migrations

### **Step 2: Models**

1. Buat `Area` model dengan relasi
2. Buat `Agency` model dengan relasi
3. Buat `Fleet` model dengan relasi
4. Buat `Driver` model dengan relasi
5. Tambah casting untuk dates dan booleans

### **Step 3: Seeders**

1. Buat `AreaSeeder` dengan 5 wilayah:
    - Sumatra Selatan
    - Lampung
    - Jambi
    - Bengkulu
    - Bangka Belitung
2. Buat `AgencySeeder` dengan data contoh
3. Buat `FleetSeeder` dengan data contoh
4. Buat `DriverSeeder` dengan data contoh
5. Daftarkan ke `DatabaseSeeder`

### **Step 4: Model Features**

1. Tambah Accessors untuk status dokumen
2. Tambah Scopes untuk data aktif
3. Tambah Methods untuk validasi dokumen
4. Tambah Activity Logging untuk semua model

### **Step 5: Integration**

1. Test relasi antar model
2. Test seeder data
3. Integrasi dengan existing permission system
4. Setup model factories untuk testing

## ✅ **Acceptance Criteria**

### **Database**

- [ ] Semua migrations berhasil dijalankan
- [ ] Foreign key constraints berfungsi
- [ ] Indexes untuk performa query

### **Models**

- [ ] Semua relasi berfungsi (hasMany, belongsTo)
- [ ] Casting dates dan booleans benar
- [ ] Accessors dan scopes berfungsi
- [ ] Activity logging tercatat

### **Seeders**

- [ ] 5 wilayah terisi dengan benar
- [ ] Data contoh agencies terisi
- [ ] Data contoh fleets dan drivers terisi
- [ ] Relasi data konsisten

### **Testing**

- [ ] Model unit tests passing
- [ ] Relasi tests passing
- [ ] Seeder tests passing
- [ ] Integration tests passing

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 2: Area Management** untuk implementasi fitur manajemen wilayah.

## 📝 **Notes**

- Pastikan nama tabel dan field konsisten dengan PRD
- Gunakan convention Laravel untuk timestamps
- Tambah soft deletes jika diperlukan untuk audit trail
- Pertimbangkan validation rules untuk data integrity
