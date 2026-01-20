# Task 3: User Area Integration

## 📋 **Objective**

Mengintegrasikan sistem User dengan Area untuk implementasi pembatasan akses berdasarkan wilayah sesuai PRD.

## 🎯 **Scope**

- Modifikasi User model untuk relasi dengan Area
- Update User Management UI untuk area assignment
- Implementasi area-based access control
- Update permission system untuk area restrictions

## 📝 **Prerequisites**

- **Task 1: Setup Database dan Model** sudah selesai
- **Task 2: Area Management** sudah selesai
- Model `Area` dan `User` sudah ada
- Sistem permission sudah berfungsi

## 🔧 **Technical Requirements**

### **Database Schema Update**

```sql
-- Tabel Pivot User-Area
area_user:
- user_id (FK, users.id)
- area_id (FK, areas.id)
- assigned_at (timestamp)
- assigned_by (FK, users.id)
- created_at, updated_at
```

### **User-Area Relations**

- `User` belongsToMany `Area`
- `Area` belongsToMany `User`
- Many-to-many dengan pivot table

### **Access Control Rules**

1. **Manager/Asmen/Admin**: Full access ke semua area
2. **Operator**: Hanya access ke area yang ditugaskan
3. **Data Filtering**: Filter data berdasarkan area user
4. **Menu Visibility**: Sembunyikan menu jika tidak ada area akses

## 📋 **Implementation Steps**

### **Step 1: Database Migration**

1. Buat migration `area_user` pivot table
2. Tambah foreign key constraints
3. Tambah indexes untuk performa
4. Jalankan migration

### **Step 2: Model Relations**

1. Update `User` model dengan `belongsToMany Area`
2. Update `Area` model dengan `belongsToMany User`
3. Tambah pivot attributes (`assigned_at`, `assigned_by`)
4. Tambah methods untuk area checking

### **Step 3: User Model Updates**

1. Tambah `hasAreaAccess($areaId)` method
2. Tambah `getAccessibleAreas()` method
3. Tambah `canAccessAllAreas()` method untuk super users
4. Override permission methods untuk area checks

### **Step 4: Backend - User Controller Updates**

1. Update `UserController` untuk handle area assignment
2. Tambah area validation untuk operator role
3. Update user resource dengan area data
4. Tambah bulk area assignment

### **Step 5: Backend - Request Validation**

1. Update `StoreUserRequest` dengan area validation
2. Update `UpdateUserRequest` dengan area validation
3. Tambah custom rule untuk operator area restriction
4. Tambah error messages Bahasa Indonesia

### **Step 6: Backend - Middleware**

1. Buat `CheckAreaAccess` middleware
2. Implementasi area-based data filtering
3. Apply middleware ke route yang perlu area restriction
4. Handle unauthorized access

### **Step 7: Frontend - User Management UI**

1. Update `Users/Create.vue` dengan area selection
2. Update `Users/Edit.vue` dengan area assignment
3. Buat `AreaSelector.vue` component
4. Tambah area validation untuk operator role

### **Step 8: Frontend - Components**

1. Buat `UserAreaBadge.vue` untuk display area user
2. Buat `AreaAssignmentList.vue` untuk management
3. Update `UsersTable.vue` dengan area column
4. Tambah area filter di user management

### **Step 9: Permission System Integration**

1. Update `usePermission` composable untuk area checks
2. Tambah area-based permission checking
3. Update menu visibility logic
4. Update route access checking

### **Step 10: Data Filtering**

1. Update `useDataTable` composable untuk area filter
2. Implementasi automatic area filtering untuk operator
3. Tambah area selector untuk admin users
4. Update semua data tables dengan area filter

## 🎨 **UI Requirements**

### **User Form Updates**

- Multi-select dropdown untuk area assignment
- Validasi: Operator minimal 1 area
- Display: Area badges di user table
- Search: Filter area di dropdown

### **Area Management**

- Checkbox selection untuk multiple areas
- Indikator user count per area
- Bulk assignment untuk multiple users
- Visual feedback untuk area restrictions

### **Style Guide Compliance**

- Gunakan region card colors untuk area badges
- Consistent dengan existing form design
- Responsive multi-select component
- Clear visual hierarchy

## ✅ **Acceptance Criteria**

### **Functional**

- [ ] User dapat diassign ke multiple areas
- [ ] Operator hanya dapat akses area yang ditugaskan
- [ ] Manager/Asmen/Admin dapat akses semua area
- [ ] Area filtering berfungsi di semua data tables

### **Security**

- [ ] Area-based access control berfungsi
- [ ] Operator tidak bisa override area restrictions
- [ ] Data leakage prevention berfungsi
- [ ] Permission inheritance berfungsi

### **UI/UX**

- [ ] Area assignment interface intuitive
- [ ] Clear visual feedback untuk area restrictions
- [ ] Responsive design untuk mobile
- [ ] Error handling untuk invalid assignments

### **Data Integrity**

- [ ] Pivot data konsisten
- [ ] Cascade delete berfungsi
- [ ] Audit trail tercatat (assigned_by, assigned_at)
- [ ] Activity logging berfungsi

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 4: Fleet Management** untuk implementasi database armada.

## 📝 **Notes**

- Pertimbangkan performance untuk area filtering di large datasets
- Gunakan eager loading untuk area relations
- Test dengan berbagai role combinations
- Pastikan backward compatibility untuk existing users
