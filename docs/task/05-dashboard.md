# Task 5: Dashboard Implementation

## 📋 **Objective**

Mengimplementasikan Dashboard untuk menampilkan ringkasan data berdasarkan wilayah sales area sesuai PRD.

## 🎯 **Scope**

- Dashboard dengan area selector
- Ringkasan data per wilayah
- Visualisasi data dengan cards
- Area-based data filtering
- Real-time data updates

## 📝 **Prerequisites**

- **Task 1: Setup Database dan Model** sudah selesai
- **Task 2: Area Management** sudah selesai
- **Task 3: User Area Integration** sudah selesai
- **Task 4: Fleet Management** sudah selesai
- Data agencies, fleets, drivers sudah ada

## 🔧 **Technical Requirements**

### **Dashboard Metrics (per wilayah)**

1. **Jumlah Agen** - Total agencies per area
2. **Jumlah Armada** - Total fleets per area
3. **Jumlah Kepemilikan Tabung** - Total cylinder ownership
4. **Jumlah Supir** - Total drivers per area
5. **Alokasi Harian** - Daily allocation data

### **Features**

1. **Area Selector** - Dropdown untuk pilih wilayah
2. **Metric Cards** - Display KPI dengan icon dan color
3. **Data Tables** - Summary tables untuk detail data
4. **Charts** - Visualisasi data (opsional)
5. **Export** - Export dashboard data (future scope)

### **Permission Rules**

- **Manager/Asmen/Admin**: Lihat semua area
- **Operator**: Hanya area yang ditugaskan
- **Default**: Tampilkan area pertama yang tersedia

## 📋 **Implementation Steps**

### **Step 1: Backend - Dashboard Controller**

1. Buat `DashboardController`
2. Implementasi `index()` method
3. Implementasi `getAreaMetrics($areaId)` method
4. Implementasi `getDashboardData($areaId)` method
5. Implementasi area-based data filtering

### **Step 2: Backend - Dashboard Service**

1. Buat `DashboardService`
2. Implementasi metric calculations
3. Implementasi data aggregation
4. Implementasi caching untuk performance
5. Implementasi area-based filtering logic

### **Step 3: Backend - Model Methods**

1. Tambah `getAgencyCount($areaId)` di Agency model
2. Tambah `getFleetCount($areaId)` di Fleet model
3. Tambah `getDriverCount($areaId)` di Driver model
4. Tambah `getCylinderCount($areaId)` (placeholder)
5. Tambah `getDailyAllocation($areaId)` (placeholder)

### **Step 4: Backend - Resources**

1. Buat `DashboardResource`
2. Format data untuk frontend consumption
3. Include metric calculations
4. Include area information

### **Step 5: Backend - Routes**

1. Tambah dashboard routes
2. Apply permission middleware
3. Apply area-based filtering
4. Tambah API endpoint untuk real-time updates

### **Step 6: Frontend - Dashboard Page**

1. Buat `Dashboard/Index.vue`
2. Implementasi area selector
3. Implementasi metric cards
4. Implementasi data tables
5. Implementasi responsive design

### **Step 7: Frontend - Components**

1. Buat `MetricCard.vue` component
2. Buat `AreaSelector.vue` component
3. Buat `DashboardTable.vue` component
4. Buat `MetricIcon.vue` component
5. Buat `LoadingSpinner.vue` component

### **Step 8: Frontend - Composables**

1. Buat `useDashboard.js` composable
2. Implementasi data fetching
3. Implementasi area switching
4. Implementasi real-time updates
5. Implementasi error handling

### **Step 9: Frontend - Data Display**

1. Implementasi metric cards dengan icons
2. Implementasi summary tables
3. Implementasi area-based filtering
4. Implementasi loading states
5. Implementasi error states

### **Step 10: Integration**

1. Set dashboard sebagai default route
2. Tambah menu item di sidebar
3. Setup breadcrumbs
4. Integrasikan dengan permission system
5. Tambah activity logging untuk dashboard access

## 🎨 **UI Requirements**

### **Layout Structure**

```
Dashboard Layout:
├── Header: Page Title + Area Selector
├── Metrics Row: 5 Metric Cards
├── Summary Tables Row: 2-3 Tables
└── Footer: Export Options (future)
```

### **Metric Cards Design**

- **Size**: Consistent card size
- **Colors**: Gunakan region card colors dari style guide
- **Icons**: Lucide icons untuk setiap metric
- **Layout**: Responsive grid (5 columns desktop, 2-3 mobile)

### **Color Mapping**

- **Sumatra Selatan**: Green (#10B981)
- **Lampung**: Orange (#F59E0B)
- **Jambi**: Blue (#0EA5E9)
- **Bengkulu**: Red (#F43F5E)
- **Bangka Belitung**: Purple (#9929EA)

### **Typography**

- **Card Title**: Inter, 14px, medium weight
- **Metric Value**: Inter, 24px, bold weight
- **Card Description**: Inter, 12px, regular weight

## ✅ **Acceptance Criteria**

### **Functional**

- [ ] Area selector berfungsi
- [ ] Metric cards menampilkan data benar
- [ ] Area-based filtering berfungsi
- [ ] Data tables menampilkan summary benar
- [ ] Responsive design berfungsi

### **Data Accuracy**

- [ ] Agency count per area benar
- [ ] Fleet count per area benar
- [ ] Driver count per area benar
- [ ] Real-time data updates berfungsi
- [ ] Caching berfungsi untuk performance

### **Security**

- [ ] Area-based access control berfungsi
- [ ] Operator hanya lihat area yang ditugaskan
- [ ] Permission system berfungsi
- [ ] Data leakage prevention berfungsi

### **UI/UX**

- [ ] Visual design sesuai style guide
- [ ] Loading states berfungsi
- [ ] Error handling berfungsi
- [ ] Responsive design untuk mobile
- [ ] Smooth area switching

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 6: Fleet Update** untuk implementasi Update Data Armada.

## 📝 **Notes**

- Fokus pada user experience untuk area switching
- Gunakan caching untuk improve performance
- Pastikan data accuracy untuk business decisions
- Consider charts untuk future enhancement
- Test dengan berbagai user roles
