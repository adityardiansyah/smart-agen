# Task 2: Manajemen Area

## 📋 **Objective**

Mengimplementasikan fitur Manajemen Area untuk mengelola data wilayah operasional sesuai PRD.

## 🎯 **Scope**

- CRUD operations untuk Area
- Validasi data area
- Integrasi dengan sistem permission
- UI components untuk manajemen area

## 📝 **Prerequisites**

- **Task 1: Setup Database dan Model** sudah selesai
- Model `Area` sudah terbuat dengan relasi
- Migration `areas` sudah dijalankan

## 🔧 **Technical Requirements**

### **Features**

1. **List Areas** - Tampilkan semua wilayah dengan status
2. **Create Area** - Tambah wilayah baru
3. **Edit Area** - Update data wilayah
4. **Delete Area** - Hapus wilayah (dengan validasi relasi)
5. **Toggle Status** - Aktif/Non-aktifkan wilayah

### **Validasi Rules**

- `name` required, unique, max:100
- `code` required, unique, max:10, uppercase
- `is_active` boolean

### **Permission Mapping**

- `areas.view` - Lihat daftar area
- `areas.create` - Tambah area
- `areas.edit` - Edit area
- `areas.delete` - Hapus area

## 📋 **Implementation Steps**

### **Step 1: Backend - Controller**

1. Buat `AreaController` dengan CRUD methods
2. Implementasi index dengan pagination dan search
3. Implementasi store dengan validasi
4. Implementasi update dengan validasi
5. Implementasi destroy dengan relasi check
6. Implementasi toggle status

### **Step 2: Backend - Request Validation**

1. Buat `StoreAreaRequest`
2. Buat `UpdateAreaRequest`
3. Tambah custom validation rules
4. Tambah error messages dalam Bahasa Indonesia

### **Step 3: Backend - Resources**

1. Buat `AreaResource` untuk API response
2. Format data sesuai kebutuhan frontend
3. Include relasi data jika diperlukan

### **Step 4: Backend - Routes**

1. Tambah route group untuk areas
2. Apply permission middleware
3. Tambah resource route dengan exceptions

### **Step 5: Frontend - Pages**

1. Buat `Areas/Index.vue` untuk daftar area
2. Buat `Areas/Create.vue` untuk form tambah
3. Buat `Areas/Edit.vue` untuk form edit
4. Integrasikan dengan layout system

### **Step 6: Frontend - Components**

1. Buat `AreasTable.vue` component
2. Buat `AreaForm.vue` component
3. Buat `AreaStatusToggle.vue` component
4. Tambah konfirmasi dialog untuk delete

### **Step 7: Frontend - Composables**

1. Buat `useAreas.js` composable
2. Implementasi CRUD operations
3. Implementasi error handling
4. Implementasi loading states

### **Step 8: Integration**

1. Tambah menu di sidebar
2. Setup permissions untuk role
3. Tambah breadcrumbs
4. Tambah flash notifications

## 🎨 **UI Requirements**

### **Table Design**

- Kolom: Kode, Nama Area, Status, Aksi
- Search box untuk filter nama
- Pagination dengan data count
- Status badge (Aktif/Non-aktif)

### **Form Design**

- Input: Kode (uppercase), Nama Area
- Toggle: Status Aktif
- Buttons: Simpan, Batal
- Validasi real-time

### **Style Guide Compliance**

- Gunakan primary color #1C4ED8
- Card background #FFFFFF
- Border radius 8px
- Inter font family

## ✅ **Acceptance Criteria**

### **Functional**

- [ ] CRUD operations berfungsi
- [ ] Validasi data berfungsi
- [ ] Search dan filter berfungsi
- [ ] Pagination berfungsi
- [ ] Status toggle berfungsi

### **Security**

- [ ] Permission system berfungsi
- [ ] Input sanitization berfungsi
- [ ] Authorization checks berfungsi

### **UI/UX**

- [ ] Responsive design
- [ ] Loading states
- [ ] Error handling
- [ ] Success notifications
- [ ] Konfirmasi dialogs

### **Data Integrity**

- [ ] Tidak bisa hapus area dengan relasi
- [ ] Unique validation berfungsi
- [ ] Required field validation

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 3: User Area Integration** untuk menghubungkan user dengan area.

## 📝 **Notes**

- Gunakan pattern yang sudah ada (User management)
- Ikuti component structure yang sudah ada
- Pastikan activity logging tercatat
- Test dengan berbagai role permissions
