# Task 6: Fleet Update (Update Data Armada)

## 📋 **Objective**

Mengimplementasikan fitur Update Data Armada untuk menambahkan atau memperbarui data armada dan pengemudi sesuai PRD.

## 🎯 **Scope**

- Form input untuk data agen, armada, dan supir
- Validasi data yang komprehensif
- Area-based access control untuk input data
- Document upload dan tracking
- Bulk operations untuk efficiency

## 📝 **Prerequisites**

- **Task 1: Setup Database dan Model** sudah selesai
- **Task 2: Area Management** sudah selesai
- **Task 3: User Area Integration** sudah selesai
- **Task 4: Fleet Management** sudah selesai
- Models dan relasi sudah berfungsi

## 🔧 **Technical Requirements**

### **Form Fields (sesuai PRD)**

#### **Data Agen & Lokasi**

- Nama Agen (required, max:100)
- Alamat (required, text)
- Kabupaten (required, max:50)
- Provinsi (required, max:50, dropdown dari areas)
- Wilayah SBM (required, max:50)

#### **Data Armada**

- Nomor Polisi Armada (required, unique, format: XX-1234-XX)
- Tahun Pembuatan Armada (required, integer, min:1990, max:current_year+1)
- Nomor Dokumen KEUR (required, max:50)
- Masa Berlaku KEUR (required, date)
- Upload Dokumen KEUR (required, file)
- Masa Berlaku STNK (required, date)
- Upload Dokumen STNK (required, file)
- Masa Habis Kendaraan (required, date)

#### **Data Supir**

- Nama Supir (required, max:100)
- Umur (required, integer, min:18, max:65)
- Masa Berlaku SIM (required, date)
- Upload Dokumen SIM (required, file)

### **Validasi Rules**

- Field wajib tidak boleh kosong
- Format tanggal harus valid
- Nomor Polisi unik (tidak boleh duplikat)
- Umur supir harus reasonable (18-65)
- Tahun pembuatan harus reasonable (tidak boleh > tahun depan)
- KEUR expiry harus valid dan tidak expired saat input
- Document dates tidak boleh expired saat input
- Vehicle age warning untuk kendaraan ≥ 9 tahun (warning, bukan error)

### **Permission Rules**

- **Operator**: Dapat input/update data armada untuk area yang ditugaskan
- **Admin/Manager/Asmen**: Full access ke seluruh area
- **View Only**: Role tertentu hanya bisa view, tidak bisa input

## 📋 **Implementation Steps**

### **Step 1: Backend - Fleet Update Controller**

1. Buat `FleetUpdateController` khusus untuk input data
2. Implementasi `create()` method untuk form input
3. Implementasi `store()` method untuk save data
4. Implementasi `edit()` dan `update()` methods
5. Implementasi area-based validation

### **Step 2: Backend - Request Validation**

1. Buat `FleetUpdateRequest` dengan comprehensive validation
2. Tambah custom validation rules:
    - License plate format validation
    - Document date validation (KEUR minimum 60 hari)
    - Age validation for drivers
    - Area access validation
    - Vehicle year validation (not future date)
3. Tambah error messages dalam Bahasa Indonesia
4. Tambah custom KEUR validation rule (minimum 60 hari dari hari ini)

**Custom KEUR Validation Example:**

```php
// FleetUpdateRequest.php
public function rules(): array
{
    return [
        'keur_expiry' => [
            'required',
            'date',
            'after_or_equal:today',
            function ($attribute, $value, $fail) {
                $minExpiryDate = now()->addDays(60);
                if (Carbon::parse($value)->lt($minExpiryDate)) {
                    $fail('Masa berlaku KEUR minimal 60 hari dari tanggal hari ini.');
                }
            },
        ],
    ];
}
```

### **Step 3: Backend - Service Layer**

1. Buat `FleetUpdateService`
2. Implementasi transaction untuk multiple entity saves
3. Implementasi document validation
4. Implementasi area-based access checking
5. Implementasi audit trail logging

### **Step 4: Backend - Model Updates**

1. Update `Agency` model dengan validation rules
2. Update `Fleet` model dengan document validation
3. Update `Driver` model dengan age validation
4. Tambah custom methods untuk data integrity
5. Tambah events untuk audit trail

### **Step 5: Backend - Routes**

1. Tambah route group untuk fleet update
2. Apply permission middleware
3. Apply area-based middleware
4. Tambah bulk operation routes

### **Step 6: Frontend - Fleet Update Page**

1. Buat `FleetUpdate/Create.vue` untuk form input
2. Buat `FleetUpdate/Edit.vue` untuk form edit
3. Implementasi multi-step form (Agen → Armada → Supir)
4. Implementasi form wizard dengan progress indicator
5. Implementasi auto-save untuk draft

### **Step 7: Frontend - Form Components**

1. Buat `AgencyForm.vue` component
2. Buat `FleetForm.vue` component
3. Buat `DriverForm.vue` component
4. Buat `FormWizard.vue` component
5. Buat `DocumentDateInput.vue` component dengan KEUR validation
6. Buat `VehicleAgeWarning.vue` component untuk display warning usia kendaraan

### **Step 8: Frontend - Validation**

1. Implementasi client-side validation
2. Implementasi real-time validation feedback
3. Implementasi custom validation rules
4. Implementasi error state handling
5. Implementasi success confirmation
6. Implementasi KEUR expiry validation di client-side
7. Implementasi vehicle age calculation dan warning display

### **Step 8.1: Frontend - Vehicle Age Warning Component**

```vue
<!-- VehicleAgeWarning.vue -->
<template>
    <div v-if="showWarning" class="mt-2 rounded-md border border-orange-200 bg-orange-50 p-3">
        <div class="flex">
            <ExclamationTriangleIcon class="h-5 w-5 text-orange-400" />
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">Peringatan Usia Kendaraan</h3>
                <div class="mt-2 text-sm text-orange-700">
                    <p>Kendaraan berusia {{ vehicleAge }} tahun (≥ 9 tahun). Status akan ditandai sebagai "NEAR EXPIRED".</p>
                </div>
            </div>
        </div>
    </div>
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

const showWarning = computed(() => {
    return vehicleAge.value >= 9;
});
</script>
```

### **Step 8.2: Frontend - KEUR Validation Component**

```vue
<!-- KEURDateInput.vue -->
<template>
    <div>
        <label class="block text-sm font-medium text-gray-700"> Masa Berlaku KEUR * </label>
        <input
            v-model="inputValue"
            type="date"
            :min="minDate"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            @input="validateKEUR"
        />
        <p v-if="errorMessage" class="mt-2 text-sm text-red-600">
            {{ errorMessage }}
        </p>
        <p v-if="warningMessage" class="mt-2 text-sm text-orange-600">
            {{ warningMessage }}
        </p>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const inputValue = ref(props.modelValue);
const errorMessage = ref('');
const warningMessage = ref('');

const minDate = computed(() => {
    return now().addDays(60).format('YYYY-MM-DD');
});

const validateKEUR = () => {
    errorMessage.value = '';
    warningMessage.value = '';

    if (!inputValue.value) {
        errorMessage.value = 'Masa Berlaku KEUR wajib diisi';
        return;
    }

    const selectedDate = new Date(inputValue.value);
    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 60);

    if (selectedDate < minDate) {
        errorMessage.value = 'Masa berlaku KEUR minimal 60 hari dari hari ini';
    } else {
        const daysUntilExpiry = Math.ceil((selectedDate - new Date()) / (1000 * 60 * 60 * 24));
        warningMessage.value = `KEUR akan expired dalam ${daysUntilExpiry} hari`;
    }
};

watch(inputValue, (newValue) => {
    emit('update:modelValue', newValue);
    validateKEUR();
});

watch(
    () => props.modelValue,
    (newValue) => {
        inputValue.value = newValue;
    },
);
</script>
```

### **Step 9: Frontend - Advanced Features**

1. Implementasi auto-populate untuk area data
2. Implementasi license plate format helper
3. Implementasi date picker dengan min/max validation
4. Implementasi duplicate checking untuk license plates
5. Implementasi form data persistence

### **Step 10: Integration**

1. Tambah menu item untuk Update Data Armada
2. Setup breadcrumbs navigation
3. Integrasikan dengan permission system
4. Tambah activity logging untuk semua changes
5. Tambah notification system untuk success/error

## 🎨 **UI Requirements**

### **Form Layout**

- **Multi-step Wizard**: 3 steps (Agen → Armada → Supir)
- **Progress Indicator**: Visual progress bar
- **Card-based Layout**: Setiap step dalam card terpisah
- **Responsive Design**: Mobile-friendly form layout

### **Form Design**

- **Input Fields**: Consistent dengan existing form design
- **Date Pickers**: Custom date picker dengan validation
- **Dropdowns**: Searchable dropdown untuk area selection
- **Validation States**: Clear visual feedback untuk errors

### **Style Guide Compliance**

- Gunakan primary color #1C4ED8 untuk buttons
- Card background #FFFFFF
- Border radius 8px
- Inter font family
- Consistent spacing dengan existing forms

## ✅ **Acceptance Criteria**

### **Functional**

- [ ] Multi-step form berfungsi
- [ ] Validasi data berfungsi
- [ ] Area-based access control berfungsi
- [ ] Document validation berfungsi
- [ ] Auto-save draft berfungsi

### **Data Integrity**

- [ ] Required field validation berfungsi
- [ ] Unique validation untuk license plates berfungsi
- [ ] Date validation berfungsi
- [ ] Age validation untuk drivers berfungsi
- [ ] Transaction rollback berfungsi

### **Security**

- [ ] Area-based input restriction berfungsi
- [ ] Permission system berfungsi
- [ ] Input sanitization berfungsi
- [ ] SQL injection prevention berfungsi

### **UI/UX**

- [ ] Form wizard intuitive
- [ ] Real-time validation feedback
- [ ] Responsive design
- [ ] Loading states
- [ ] Error handling dan recovery

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 7: UI Styling** untuk implementasi styling sesuai style guide.

## 📝 **Notes**

- Fokus pada user experience untuk data input
- Implementasi comprehensive validation untuk data quality
- Gunakan transactions untuk data consistency
- Consider bulk import untuk future enhancement
- Test dengan berbagai user roles dan area assignments
