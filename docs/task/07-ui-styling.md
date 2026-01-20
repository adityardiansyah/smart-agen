# Task 7: UI Styling Implementation

## 📋 **Objective**

Mengimplementasikan UI styling sesuai dengan style guide yang sudah ditentukan untuk konsistensi visual SMART AGEN LPG.

## 🎯 **Scope**

- Implementasi color scheme sesuai styles.json
- Update typography dengan Inter font
- Implementasi region card colors
- Styling untuk status indicators
- Responsive design implementation

## 📝 **Prerequisites**

- **Task 1-6** sudah selesai
- Semua components sudah ada
- Tailwind CSS sudah terkonfigurasi
- Base layout system sudah ada

## 🔧 **Technical Requirements**

### **Color Scheme Implementation**

```css
/* Brand Colors */
--brand-primary: #1c4ed8;
--brand-secondary: #64748b;

/* Background Colors */
--bg-main: #f1f5f9;
--bg-sidebar: #ffffff;
--bg-card: #ffffff;

/* Region Cards */
--region-sumsel: #10b981;
--region-sumsel-accent: #059669;
--region-lampung: #f59e0b;
--region-lampung-accent: #d97706;
--region-jambi: #0ea5e9;
--region-jambi-accent: #0284c7;
--region-bengkulu: #f43f5e;
--region-bengkulu-accent: #e11d48;
--region-bangka: #9929ea;
--region-bangka-accent: #b153d7;

/* Status Colors */
--status-delivered-bg: #dcfce7;
--status-delivered-text: #166534;
--status-inTransit-bg: #dbeafe;
--status-inTransit-text: #1e40af;
--status-loading-bg: #fef3c7;
--status-loading-text: #92400e;
--status-delayed-bg: #fee2e2;
--status-delayed-text: #991b1b;

/* Text Colors */
--text-primary: #111827;
--text-secondary: #6b7280;
--text-onColor: #ffffff;
```

### **Typography Implementation**

```css
/* Font Family */
--font-family: 'Inter', sans-serif;

/* Typography Scale */
--text-base: 14px;
--heading-h1: 24px / 700;
--heading-h2: 18px / 600;
```

### **Spacing System**

```css
--container-padding: 24px;
--card-gap: 20px;
--border-radius-small: 4px;
--border-radius-medium: 8px;
--border-radius-large: 12px;
```

## 📋 **Implementation Steps**

### **Step 1: Tailwind CSS Configuration**

1. Update `tailwind.config.js` dengan custom colors
2. Tambah custom font family configuration
3. Tambah custom spacing values
4. Tambah custom border radius values
5. Tambah custom shadow definitions

### **Step 2: CSS Variables Setup**

1. Buat `resources/css/variables.css` untuk CSS variables
2. Define semua color variables
3. Define typography variables
4. Define spacing variables
5. Import di main CSS file

### **Step 3: Base Typography Updates**

1. Update `resources/css/app.css` dengan Inter font
2. Setup font import dari Google Fonts
3. Update base typography styles
4. Update heading styles
5. Update text color utilities

### **Step 4: Component Styling Updates**

1. Update `Button.vue` component dengan brand colors
2. Update `Card.vue` component dengan proper styling
3. Update `Badge.vue` component dengan status colors
4. Update `Table.vue` component dengan proper styling
5. Update `Input.vue` component styling

### **Step 5: Region Card Implementation**

1. Buat `RegionCard.vue` component
2. Implementasi dynamic color berdasarkan region
3. Tambah hover effects dan transitions
4. Implementasi responsive design
5. Tambah proper accessibility

### **Step 6: Status Badge Implementation**

1. Update `StatusBadge.vue` component
2. Implementasi color coding untuk document status
3. Tambah icons untuk status indicators
4. Implementasi tooltips untuk additional info
5. Tambah proper contrast ratios

### **Step 7: Layout Styling Updates**

1. Update `AppLayout.vue` dengan proper background
2. Update sidebar styling dengan brand colors
3. Update header styling dengan proper hierarchy
4. Update main content area styling
5. Implementasi proper spacing system

### **Step 8: Dashboard Styling**

1. Update metric cards dengan region colors
2. Implementasi proper visual hierarchy
3. Update table styling untuk dashboard
4. Implementasi responsive grid layout
5. Tambah proper loading states

### **Step 9: Form Styling Updates**

1. Update form styling dengan proper validation states
2. Implementasi proper focus states
3. Update button styling dengan hierarchy
4. Implementasi proper error message styling
5. Tambah proper accessibility features

### **Step 10: Responsive Design Implementation**

1. Update mobile layouts
2. Implementasi proper breakpoints
3. Update touch-friendly interactions
4. Implementasi proper mobile navigation
5. Test di berbagai screen sizes

## 🎨 **UI Requirements**

### **Visual Hierarchy**

1. **Primary Actions**: Brand primary color (#1C4ED8)
2. **Secondary Actions**: Brand secondary color (#64748B)
3. **Success States**: Green color scheme
4. **Warning States**: Orange/Yellow color scheme
5. **Error States**: Red color scheme

### **Component Consistency**

- **Buttons**: Consistent sizing, colors, and states
- **Cards**: Consistent shadows, borders, and spacing
- **Forms**: Consistent input styling and validation
- **Tables**: Consistent styling and responsive behavior
- **Navigation**: Consistent styling and active states

### **Accessibility Requirements**

- **Color Contrast**: WCAG AA compliance (4.5:1)
- **Focus States**: Clear focus indicators
- **Screen Reader**: Proper ARIA labels
- **Keyboard Navigation**: Full keyboard accessibility
- **Touch Targets**: Minimum 44px touch targets

## ✅ **Acceptance Criteria**

### **Visual Design**

- [ ] Color scheme sesuai styles.json
- [ ] Typography sesuai specification
- [ ] Spacing system konsisten
- [ ] Visual hierarchy clear
- [ ] Brand consistency terjaga

### **Component Styling**

- [ ] Semua components styling konsisten
- [ ] Status indicators berfungsi
- [ ] Region card colors berfungsi
- [ ] Form validation states clear
- [ ] Interactive states berfungsi

### **Responsive Design**

- [ ] Mobile layout berfungsi
- [ ] Tablet layout berfungsi
- [ ] Desktop layout berfungsi
- [ ] Touch interactions berfungsi
- [ ] Breakpoints appropriate

### **Accessibility**

- [ ] Color contrast compliance
- [ ] Focus states clear
- [ ] Screen reader friendly
- [ ] Keyboard navigation complete
- [ ] Touch targets adequate

## 🚀 **Next Steps**

Setelah task ini selesai, lanjut ke **Task 8: Testing and Deployment** untuk final testing dan deployment preparation.

## 📝 **Notes**

- Fokus pada consistency dengan existing design system
- Pastikan accessibility compliance
- Test di berbagai browsers dan devices
- Document custom components untuk future reference
- Consider performance impact untuk custom styling
