# PlayTest ID - The Ultimate Game & App Testing Platform

**PlayTest ID** adalah platform *two-sided* inovatif yang dirancang untuk menjembatani **Developer Aplikasi/Game** (yang membutuhkan tester nyata untuk memenuhi syarat 14 hari pengujian Google Play Console) dengan **Tester** (pengguna internet yang mencari penghasilan tambahan).

Proyek ini dibangun menggunakan arsitektur **Hybrid Wrapper App**, menggabungkan ketangguhan **Laravel 12 & Filament v5** di sisi backend/web, dengan aplikasi **Native Android (Kotlin)** di sisi klien untuk fitur pelacakan otomatis (*Zero-Friction Auto-Tracking*).

---

## 🏗️ Teknologi & Arsitektur Utama

1. **Backend Framework**: Laravel 12
2. **Admin/Dashboard Panel**: Filament v5 (Terdiri dari 3 Panel: `Admin`, `Developer`, `Tester`)
3. **Frontend Asset Tooling**: Vite & Tailwind CSS
4. **Android Client**: Kotlin (WebView + `UsageStatsManager` Background Service)

---

## 📜 ATURAN KODE (WAJIB DIBACA OLEH AI AGENT)

Bagi AI Agent atau developer yang akan melanjutkan proyek ini, **ATURAN INI MUTLAK DAN TIDAK BOLEH DILANGGAR**:

1. **Konvensi Database (SINGULAR)**:
   Semua nama tabel di database **WAJIB berbentuk tunggal (Singular)**.
   - ✅ Benar: `user`, `misi`, `aplikasi`, `misi_anggota`, `paket`, `pembayaran`.
   - ❌ Salah: `users`, `misis`, `aplikasis`, `packages`.
   - *Catatan: Model Eloquent diatur dengan `protected $table = 'nama_tabel';` secara eksplisit.*

2. **Arsitektur Controller (INVOKABLE)**:
   Semua API Controller baru **WAJIB** menggunakan **Single Action Controller** (metode `__invoke()`). Jangan membuat controller tradisional berbasis REST/CRUD untuk endpoint API.

3. **Styling & Tampilan**:
   UI harus berkelas, modern, dan menggunakan *micro-animations* atau gradient (bisa pakai custom CSS di Filament).

---

## 🎯 Fitur Unggulan: Zero-Friction Auto-Tracking

Ini adalah fitur inti (flagship) dari PlayTest ID. Daripada meminta tester terus-menerus mengambil *screenshot* secara manual, sistem melacak durasi penggunaan aplikasi secara otomatis.

### Alur Kerja (Workflow):
1. **Developer Membuat Misi**: Developer membuat kampanye (Misi) dan mengunggah detail aplikasi.
2. **Perekrutan Tester**: Developer menyalin daftar email tester (`MisiAnggota`) untuk diundang via Google Play Console.
3. **Magic Auto-Detect (Input Link)**: 
   - Setelah Google meng-ACC aplikasi, Developer menekan tombol **"Input Link Aplikasi"** di menu *Kelola Tester*.
   - Developer memasukkan link Play Store (contoh: `https://play.google.com/store/apps/details?id=com.namadev.game`).
   - Sistem **secara otomatis mengekstrak `package_name`** (`com.namadev.game`) menggunakan *Regex* tanpa developer perlu mengetik manual. Status misi berubah menjadi `running`.
4. **Android Background Tracking**:
   - Tester menginstal aplikasi PlayTest ID (Hybrid Kotlin).
   - Aplikasi Android menjalankan *Background Service* menggunakan `UsageStatsManager` untuk memantau aplikasi apa yang sedang dibuka di layar depan (*foreground*).
   - Jika `package_name` cocok, aplikasi mengirimkan *Ping* (POST request) ke server Laravel setiap 30 detik.
5. **Real-Time Progress**:
   - **Dashboard Tester (`MisiSaya.php`)**: Menampilkan bar progress ungu secara *real-time* (contoh: `30 / 180 dtk`).
   - **Dashboard Developer (`PantauProgress.php`)**: Developer dapat melihat matriks 14-hari dan bar progress Auto-Tracking dari semua testernya.

---

## 🗄️ Struktur Database Penting (Modul Tracking)

- **`aplikasi`**: Menyimpan master data aplikasi milik developer (`id`, `developer_id`, `nama`, `package_name`).
- **`misi`**: Menyimpan detail kampanye (`id`, `id_user`/developer, `nama_aplikasi`, `aplikasi_id`, `status`, `total_durasi_detik`, dll).
- **`misi_anggota`**: Menyimpan relasi Tester yang bergabung ke Misi (`id`, `id_misi`, `id_user`/tester, `status`).
- **`misi_sub`**: Menyimpan history/tugas harian selama 14 hari (`id`, `id_misi`, `id_user`, `hari_ke`, `status`, `image`).

---

## 📡 API Endpoint (Android to Laravel)

**`POST /api/track-session`**
Dikelola oleh `TrackSessionController::__invoke()`.

**Payload (JSON):**
```json
{
    "tester_id": 123,
    "package_name": "com.namadev.game",
    "durasi_tambahan_detik": 30
}
```
**Logika Server:**
1. Mencari `Aplikasi` berdasarkan `package_name`.
2. Mencari `Misi` aktif yang terhubung dengan `tester_id` dan aplikasi tersebut (dengan `status = 'running'`).
3. Melakukan *increment* pada `misi.total_durasi_detik`.
4. Jika total mencapai target (misal 180 detik), status misi otomatis diselesaikan.

---

## 🧪 Panduan Testing & Pengembangan

1. **Membuat Data Dummy**:
   Jalankan script di terminal untuk mengisi database dengan sampel data lengkap (Developer, Tester, Paket, Aplikasi, dan Misi):
   ```bash
   php artisan tinker create_dummy.php
   ```
   * Akun Developer: `dev_dummy@playtest.id` / `password`
   * Akun Tester: `tester_dummy@playtest.id` / `password`

2. **Testing UI/Dashboard di Local**:
   - Jika menggunakan Emulator Android, terkadang aset CSS/JS Filament gagal dimuat karena Vite Dev Server berjalan di `localhost` komputer.
   - **Solusi Wajib**: Matikan `npm run dev`, dan jalankan:
     ```bash
     npm run build
     ```
   - Lalu jalankan server PHP:
     ```bash
     php artisan serve --host=0.0.0.0 --port=8000
     ```

3. **Simulasi Tracking (Tanpa Emulator)**:
   Gunakan tools seperti **Thunder Client / Postman** untuk menembak endpoint `/api/track-session` sesuai payload JSON di atas. Refresh Dashboard Developer/Tester untuk melihat pergerakan Progress Bar.

---

## 📌 Catatan Historis (Bugs Resolved)
- Regex rendering logo image yang rusak telah diperbaiki dengan endpoint Wikimedia `Special:FilePath`.
- Konflik `TesterPanelProvider` saat registrasi halaman `MisiSaya` telah diatasi.
- Input package_name tidak lagi manual di awal, melainkan menggunakan sistem ekstrak URL di aksi "Input Link Aplikasi".

---

## 🚀 LATEST UPDATES (12 Mei 2026)

### 🛠️ Android Auto-Tracking Improvements
- **Foreground Service**: Implementasi `startForeground` agar service pelacakan tidak dimatikan oleh sistem Android (tetap stabil di latar belakang).
- **UsageEvents Logic**: Sistem deteksi aplikasi aktif kini menggunakan `UsageEvents` (bukan hanya `queryUsageStats`) sehingga sangat responsif saat user berpindah aplikasi lewat *Recent Apps*.
- **API `/api/track-session`**:
  - Mendukung pencarian `LIKE` untuk mencocokkan *Package Name* murni dengan URL Play Store yang panjang.
  - Data durasi kini disimpan per-tester di tabel `misi_anggota.total_durasi_detik`.

### 📊 Web & Logic Refinement
- **Mission Progress Capping**: Penambahan logika pembatasan agar hari aktif tidak melebihi 14 hari (mencegah progress bar melampaui 100% atau data error).
- **Auto-Extraction UI**: Input `link_aplikasi` pada form misi kini otomatis mengekstrak *Package Name* (ID) saat user mem-paste URL lengkap Play Store.
- **Database Schema**: Penambahan kolom `total_durasi_detik` pada tabel `misi_anggota` melalui migrasi terbaru.

### 📝 Notes for Next Session
- **Local Testing**: Gunakan IP lokal laptop (contoh: `192.168.1.78`) di `.env` dan `UsageTrackerService.kt` jika ingin mencoba di HP fisik (Samsung J7 Prime).
- **Server**: Selalu jalankan `php artisan serve --host=0.0.0.0` untuk testing jaringan lokal.
- **Database**: Pastikan MySQL di Laragon menyala sebelum membuka emulator.