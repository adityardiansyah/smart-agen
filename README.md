# Smart Agen LPG - Sistem Manajemen Distribusi LPG

Aplikasi berbasis web untuk mengelola distribusi LPG, termasuk manajemen data agen, armada, supir, dan wilayah distribusi. Dibangun menggunakan teknologi modern untuk performa dan pengalaman pengguna yang optimal.

## Fitur Utama

-   **Dashboard Interaktif**: Visualisasi statistik real-time mengenai jumlah agen, armada, supir, dan alokasi harian per wilayah.
-   **Manajemen Wilayah (Area & Region)**: Pengaturan hierarki distribusi dari tingkat Area hingga Region (Kabupaten/Kota).
-   **Database Agen**: Pencatatan detail agen termasuk alamat, status aktif, jumlah kepemilikan tabung, dan alokasi harian.
-   **Manajemen Armada (Fleet)**:
    -   Pencatatan kendaraan operasional.
    -   Monitoring masa berlaku KEUR, STNK, dan usia kendaraan.
    -   Status operasional armada (Aktif/Non-aktif).
-   **Manajemen Supir (Driver)**:
    -   Pencatatan data supir dan penugasan ke armada.
    -   Monitoring masa berlaku SIM.
    -   Status aktif supir.
-   **Role & Permission**: Sistem hak akses pengguna yang fleksibel (menggunakan Spatie Permission).

## Teknologi yang Digunakan

-   **Backend**: [Laravel 12](https://laravel.com)
-   **Frontend**: [Vue.js 3](https://vuejs.org) (Composition API, TypeScript)
-   **Full-stack Adapter**: [Inertia.js 2.0](https://inertiajs.com)
-   **Styling**: [Tailwind CSS](https://tailwindcss.com) & [Shadcn Vue](https://www.shadcn-vue.com)
-   **Icons**: [Lucide Vue](https://lucide.dev)

## Persyaratan Sistem

-   PHP >= 8.2
-   Node.js (LTS Version)
-   MySQL / MariaDB
-   Composer

## Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/repo-name.git
    cd repo-name
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies**
    ```bash
    npm install
    ```

4.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
    ```bash
    cp .env.example .env
    ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Migrasi Database**
    Jalankan migrasi untuk membuat tabel database.
    ```bash
    php artisan migrate --seed
    ```
    *(Gunakan `--seed` jika ingin mengisi data awal/dummy)*

## Menjalankan Aplikasi

Untuk lingkungan pengembangan (Development), jalankan perintah berikut:

```bash
composer run dev
```

Perintah ini akan menjalankan server Laravel dan Vite secara bersamaan. Akses aplikasi melalui browser di `http://localhost:8000`.

## Struktur Direktori Utama

-   `app/Models`: Model Eloquent (Agency, Area, Fleet, Driver, dll).
-   `app/Http/Controllers/Admin`: Controller untuk logika bisnis admin.
-   `resources/js/pages`: Halaman-halaman Vue.js (Admin/Agencies, Admin/Fleets, dll).
-   `resources/js/components`: Komponen Vue reusable.
-   `routes/admin.php`: Definisi rute untuk halaman admin.
