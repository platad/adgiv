# Prototipe BIMA - Agentic AI Voice (cPanel Deployment Guide)

Panduan ini dikhususkan untuk instalasi dan penyetelan proyek **Prototipe BIMA (Supervisory AI)** di lingkungan **cPanel Shared Hosting**. Karena keterbatasan akses root dan terminal pada shared hosting, sistem telah dioptimalkan agar dapat berjalan *seamless* tanpa memerlukan software pihak ketiga.

---

## 1. Fitur Utama yang Dioptimalkan untuk cPanel

- **Bypass FFmpeg (Pure-PHP Audio Slicer)**: Pemotongan audio `.wav` dilakukan murni menggunakan PHP. Untuk format lain seperti `.mp3` atau `.m4a`, sistem akan meloloskannya langsung ke API OpenAI (maksimum 25MB) sehingga Anda **TIDAK PERLU** menginstal FFmpeg/FFprobe di server cPanel.
- **Server-Sent Events (SSE)**: Sistem *progress bar* analisis audio berjalan menggunakan SSE standar (HTTP). Oleh karena itu, fitur WebSocket Reverb tidak lagi wajib berjalan.

---

## 2. Konfigurasi `.env` untuk cPanel

Ubah file `.env` di cPanel Anda dengan penyesuaian wajib berikut:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

# ── Konfigurasi Queue & Cache (Tanpa Redis) ──
# Di cPanel, pastikan kita menggunakan driver database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

# ── Konfigurasi Broadcast (Abaikan Reverb) ──
# Karena Reverb membutuhkan perintah CLI yang selalu berjalan (daemon),
# Anda bisa menggunakan log/null untuk driver broadcast di shared hosting.
BROADCAST_CONNECTION=log
```

---

## 3. Menjalankan Queue Worker di cPanel (Cron Job)

Karena pemrosesan AI membutuhkan waktu lama, sistem ini menggunakan Laravel Queue. Di komputer lokal (laptop), Anda menjalankan `php artisan queue:work`. Namun, di cPanel, Anda harus menggunakan **Cron Jobs**.

1. Buka menu **Cron Jobs** di cPanel Anda.
2. Tambahkan Cron Job baru yang berjalan setiap **1 Menit** (*Once Per Minute*).
3. Masukkan perintah berikut pada kolom *Command*:
   ```bash
   /usr/local/bin/php /home/irhamkar/public_html/artisan schedule:run >> /dev/null 2>&1
   ```
   *(Catatan: Sesuaikan `/home/irhamkar/public_html/` dengan path asli direktori tempat Anda meletakkan file Laravel).*

Untuk memastikan antrean (queue) terproses lewat *schedule:run*, pastikan di dalam `routes/console.php` atau `app/Console/Kernel.php` terdapat perintah:
```php
use Illuminate\Support\Facades\Schedule;
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
```
*(File proyek ini sudah dikonfigurasi untuk itu).*

---

## 4. Cara Mengatasi Error (Troubleshooting)

### A. Jika Progress Bar "Tersendat" atau Error:
- Pastikan versi PHP cPanel Anda adalah **PHP 8.2** atau lebih tinggi.
- Aktifkan ekstensi `zip`, `fileinfo`, dan `curl` di menu **Select PHP Version** cPanel.
- Jika progress bar berhenti di tengah jalan, halaman dapat di-*refresh* dan sistem akan melanjutkan otomatis karena setiap langkah (chunk) disimpan di Database.

### B. Jika Muncul Pesan Error Lama di Halaman Pemrosesan:
Jika Anda melihat error historis seperti gagalnya FFmpeg, ini adalah log dari database. Cukup kembali ke halaman utama, **unggah ulang file audio baru**, dan proses akan berjalan mulus menggunakan sistem Pure-PHP terbaru.

---

## 5. Deployment Sederhana
1. **Upload File ZIP**: Kompres seluruh folder proyek di laptop (kecuali folder `vendor` dan `node_modules`).
2. **Extract di cPanel**: Ekstrak di dalam folder `/home/username/nama-folder`.
3. **Point Domain**: Pastikan document root domain Anda mengarah ke folder `public`.
4. **Jalankan Composer**: Buka terminal cPanel dan jalankan `composer install --optimize-autoloader --no-dev`.
5. **Migrasi DB**: Jalankan `php artisan migrate --force`.
6. **Selesai**: Aplikasi siap digunakan!
