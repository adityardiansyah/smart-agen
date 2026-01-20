# Task 4: Fleet Management (Database Armada)

## 📋 **Objective**

Mengimplementasikan fitur Database Armada untuk melihat detail data armada per wilayah dan per agen sesuai PRD.

## 🎯 **Scope**

- CRUD operations untuk Agencies, Fleets, dan Drivers
- Data filtering berdasarkan wilayah dan agen
- Document expiration tracking
- Area-based data access control

## 📝 **Prerequisites**

- **Task 1: Setup Database dan Model** sudah selesai
- **Task 2: Area Management** sudah selesai
- **Task 3: User Area Integration** sudah selesai
- Models `Area`, `Agency`, `Fleet`, `Driver` sudah ada

## 🔧 **Technical Requirements**

### **Features**

1. **Agency Management**

    - List agencies per area
    - CRUD operations
    - Area-based filtering

2. **Fleet Management**

    - List fleets per agency
    - Document tracking (KEUR, STNK)
    - Vehicle expiration monitoring

3. **Driver Management**

    - List drivers per fleet
    - SIM expiration tracking
    - Driver data management

4. **Document Status**
    - Color-coded expiration indicators
    - Expiration date warnings
    - Document validity status

### **Document Status Rules**

**Masa Uji Kendaraan (KEUR)**

- **NEAR EXPIRY**: Jika masa berlaku kurang dari 60 hari
- **NOT EXPIRED**: Jika masa berlaku lebih dari 60 hari

**Masa Habis Kendaraan** (Berdasarkan Tahun Pembuatan)

- **NOT EXPIRED**: Jika usia kendaraan kurang dari 9 tahun
- **NEAR EXPIRED**: Jika usia kendaraan lebih dari 9 tahun

**SIM Driver** (Existing Logic)

- **Green**: Valid > 30 hari
- **Yellow**: Expired dalam 30 hari
- **Red**: Already expired

### **Permission Mapping**

- `agencies.view` - Lihat daftar agen
- `agencies.create` - Tambah agen
- `agencies.edit` - Edit agen
- `agencies.delete` - Hapus agen
- `fleets.view` - Lihat daftar armada
- `fleets.create` - Tambah armada
- `fleets.edit` - Edit armada
- `fleets.delete` - Hapus armada
- `drivers.view` - Lihat daftar supir
- `drivers.create` - Tambah supir
- `drivers.edit` - Edit supir
- `drivers.delete` - Hapus supir

## 📋 **Implementation Steps**

### **Step 1: Backend - Controllers**

1. Buat `AgencyController` dengan CRUD methods
2. Buat `FleetController` dengan CRUD methods
3. Buat `DriverController` dengan CRUD methods
4. Implementasi area-based filtering
5. Implementasi document status checking

### **Step 2: Backend - Request Validation**

1. Buat `StoreAgencyRequest` dan `UpdateAgencyRequest`
2. Buat `StoreFleetRequest` dan `UpdateFleetRequest`
3. Buat `StoreDriverRequest` dan `UpdateDriverRequest`
4. Tambah document date validation
5. Tambah area assignment validation

### **Step 3: Backend - Resources**

1. Buat `AgencyResource` dengan relasi data
2. Buat `FleetResource` dengan document status
3. Buat `DriverResource` dengan SIM status
4. Include document expiration indicators

### **Step 4: Backend - Model Features**

1. Tambah document status accessors
2. Tambah expiration warning scopes
3. Tambah area-based scopes
4. Tambah document validation methods

**Fleet Model - KEUR Status Logic**

```php
// Accessor untuk status KEUR
public function getKeurStatusAttribute(): string
{
    if (!$this->keur_expiry) {
        return 'NOT EXPIRED';
    }

    $daysUntilExpiry = now()->diffInDays($this->keur_expiry, false);

    return $daysUntilExpiry < 60 ? 'NEAR EXPIRY' : 'NOT EXPIRED';
}

// Accessor untuk status usia kendaraan
public function getVehicleAgeStatusAttribute(): string
{
    if (!$this->year_manufacture) {
        return 'NOT EXPIRED';
    }

    $vehicleAge = now()->year - $this->year_manufacture;

    return $vehicleAge >= 9 ? 'NEAR EXPIRED' : 'NOT EXPIRED';
}
```

### **Step 5: Backend - Routes**

1. Tambah route groups untuk agencies, fleets, drivers
2. Apply area-based middleware
3. Apply permission middleware
4. Tambah nested routes (area.agencies, agency.fleets)

### **Step 6: Frontend - Pages**

1. Buat `Agencies/Index.vue` untuk daftar agen
2. Buat `Fleets/Index.vue` untuk daftar armada
3. Buat `Drivers/Index.vue` untuk daftar supir
4. Buat detail pages untuk masing-masing entity

### **Step 7: Frontend - Components**

1. Buat `AgenciesTable.vue` dengan area filter
2. Buat `FleetsTable.vue` dengan document status
3. Buat `DriversTable.vue` dengan SIM status
4. Buat `DocumentStatusBadge.vue` component
5. Buat `ExpirationWarning.vue` component
6. Buat `KEURStatusBadge.vue` component untuk status KEUR
7. Buat `VehicleAgeStatusBadge.vue` component untuk status usia kendaraan

### **Step 8: Frontend - Forms**

1. Buat `AgencyForm.vue` dengan area selection
2. Buat `FleetForm.vue` dengan document dates
3. Buat `DriverForm.vue` dengan SIM expiry
4. Tambah date picker components
5. Tambah document validation

### **Step 9: Frontend - Filtering**

1. Implementasi area selector
2. Implementasi agency selector
3. Implementasi document status filter
4. Implementasi search functionality
5. Update `useDataTable` untuk complex filtering

### **Step 9.1: Frontend - KEUR Status Component**

```vue
<!-- KEURStatusBadge.vue -->
<template>
    <span :class="badgeClasses" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
        {{ status }}
        <ExclamationTriangleIcon v-if="isNearExpiry" class="ml-1 h-3 w-3" />
    </span>
</template>

<script setup>
import { computed } from 'vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    keurExpiry: {
        type: String,
        required: true,
    },
});

const status = computed(() => {
    const expiryDate = new Date(props.keurExpiry);
    const today = new Date();
    const daysUntilExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

    return daysUntilExpiry < 60 ? 'NEAR EXPIRY' : 'NOT EXPIRED';
});

const isNearExpiry = computed(() => status.value === 'NEAR EXPIRY');

const badgeClasses = computed(() => {
    return isNearExpiry.value ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800';
});
</script>
```

### **Step 9.2: Frontend - Vehicle Age Status Component**

```vue
<!-- VehicleAgeStatusBadge.vue -->
<template>
    <span :class="badgeClasses" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
        {{ status }}
        <ExclamationTriangleIcon v-if="isNearExpired" class="ml-1 h-3 w-3" />
    </span>
</template>

<script setup>
import { computed } from 'vue';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    yearManufacture: {
        type: Number,
        required: true,
    },
});

const vehicleAge = computed(() => {
    return new Date().getFullYear() - props.yearManufacture;
});

const status = computed(() => {
    return vehicleAge.value >= 9 ? 'NEAR EXPIRED' : 'NOT EXPIRED';
});

const isNearExpired = computed(() => status.value === 'NEAR EXPIRED');

const badgeClasses = computed(() => {
    return isNearExpired.value ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800';
});
</script>
```

### **Step 10: Integration**

1. Tambah menu items di sidebar
2. Setup breadcrumbs navigation
3. Tambah area-based data filtering
4. Tambah document expiration alerts
5. Integrasikan dengan permission system

## 🎨 **UI Requirements**

### **Table Design**

- **Agencies Table**: Nama, Area, Alamat, Jumlah Armada, Status, Aksi
- **Fleets Table**: Nopol, Agen, Status KEUR, STNK, Status Usia Kendaraan, Aksi
- **Drivers Table**: Nama, Armada, Umur, SIM, Status, Aksi

### **Document Status Indicators**

**KEUR Status**

- **NEAR EXPIRY**: Orange badge (60 hari atau kurang)
- **NOT EXPIRED**: Green badge (lebih dari 60 hari)
- **Tooltips**: Tampilkan "Expire pada [tanggal]" untuk NEAR EXPIRY

**Status Usia Kendaraan**

- **NOT EXPIRED**: Green badge (usia < 9 tahun)
- **NEAR EXPIRED**: Orange badge (usia ≥ 9 tahun)
- **Tooltips**: Tampilkan "Usia [X] tahun" untuk NEAR EXPIRED

**Badge Design Specifications**

- **Colors**:
    - Green: `bg-green-100 text-green-800`
    - Orange: `bg-orange-100 text-orange-800`
- **Size**: `text-xs font-medium`, `px-2.5 py-0.5`
- **Shape**: `rounded-full`
- **Icons**: Warning icon untuk NEAR EXPIRY/NEAR EXPIRED status
- **Tooltips**: Hover information untuk detail status

**SIM Status** (Existing)

- **Badge Colors**: Green (valid), Yellow (warning), Red (expired)
- **Date Format**: DD/MM/YYYY
- **Tooltips**: Show exact expiration date
- **Icons**: Warning icon untuk near expiration

### **Style Guide Compliance**

- Gunakan region card colors untuk area badges
- Consistent dengan existing table design
- Responsive design untuk mobile
- Clear visual hierarchy

## ✅ **Acceptance Criteria**

### **Functional**

- [ ] CRUD operations untuk agencies, fleets, drivers
- [ ] Area-based data filtering berfungsi
- [ ] Document status tracking berfungsi
- [ ] KEUR status calculation berfungsi (60 hari rule)
- [ ] Vehicle age status berfungsi (9 tahun rule)
- [ ] Expiration warnings berfungsi
- [ ] Search dan filter berfungsi

### **Data Management**

- [ ] Relasi data konsisten
- [ ] Document validation berfungsi
- [ ] Area assignment berfungsi
- [ ] Activity logging tercatat

### **Security**

- [ ] Area-based access control berfungsi
- [ ] Permission system berfungsi
- [ ] Data filtering untuk operator berfungsi
- [ ] Unauthorized access prevention

### **UI/UX**

- [ ] Document status indicators clear
- [ ] Responsive design
- [ ] Loading states
- [ ] Error handling
- [ ] Success notifications

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 5: Dashboard** untuk implementasi dashboard dengan ringkasan data.

## 📝 **Notes**

- Fokus pada document expiration tracking
- Gunakan color coding yang sudah ditentukan di style guide
- Pastikan area filtering berfungsi untuk semua data levels
- Consider performance untuk large datasets
