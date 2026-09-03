# Checklist Registrasi Xendit — Johen Marketplace

Panduan ini menyiapkan apa saja yang harus diverifikasi / disetel sebelum dan saat
mengajukan **produksi live** ke Xendit. Seluruh prasyarat teknis kode sudah tersedia;
yang perlu dilakukan adalah konfigurasi di dashboard Xendit, deployment, dan isi `.env`.

> Status integrasi otomatis bisa dipantau di **Admin → Gateway Status** (`/admin/gateway-status`).

---

## 1. Prasyarat yang HARUS selesai sebelum kirim aplikasi live

| # | Item | Status | Cara memenuhi |
|---|------|--------|---------------|
| 1 | **Domain publik (bukan localhost)** | ❌ belum | `APP_URL` harus `https://domainanda.com`, bukan `http://localhost`. Deploy dulu ke hosting/VPS + pasang SSL. |
| 2 | **Xendit API key terisi** | ✅ sudah | `XENDIT_SECRET_KEY=xnd_development_...` (masih key test — ini yang nanti diganti). |
| 3 | **Xendit done mode test → live** | ❌ belum | Ganti `XENDIT_IS_PRODUCTION=true` + key live setelah Xendit approve. |
| 4 | **Callback token terisi** | ✅ sudah | `XENDIT_CALLBACK_TOKEN` sudah di-generate → salin nilai yang sama ke dashboard Xendit **Settings → Webhooks** (token webhook). |
| 5 | **CSRF dikecualikan di webhook** | ✅ sudah | `payment/notification` & `digiflazz/callback` sudah di-exclude di `bootstrap/app.php`. |
| 6 | **Digiflazz dikonfigurasi** | ✅ sudah | Username & key terisi di `.env`. |
| 7 | **Simulasi dimatikan** | ❌ belum | Set `PAYMENT_SIMULATION=false` saat siap produksi. |

---

## 2. Yang harus disetel di dashboard Xendit

Buka https://dashboard.xendit.co → **Settings → Webhooks**, lalu daftarkan **Callback URL**:

### Webhook 1 — QRIS (dipakai jika `PAYMENT_CHANNEL=qris`)
- URL: `https://domainanda.com/payment/notification`
- Token: nilai `XENDIT_CALLBACK_TOKEN` dari `.env`
- Event: **QR Code payment** (`qr.payment`)
- Pastikan versi API webhook = **2022-07-31** (dipakai kode `createQr`/`getQr`).

### Webhook 2 — Invoice (dipakai jika `PAYMENT_CHANNEL=invoice`)
- URL: `https://domainanda.com/payment/notification`
- Token: nilai `XENDIT_CALLBACK_TOKEN` dari `.env`
- Event: **Invoice** → `invoice.paid`, `invoice.expired` (dan `invoice.settled` jika ada).

> Token webhook di dashboard **wajib sama persis** dengan `XENDIT_CALLBACK_TOKEN`,
> karena `XenditService::verifyCallbackToken()` menolak jika tidak cocok.

---

## 3. Urutan pengaktifan produksi (setelah Xendit approve)

1. Deploy app & pastikan diakses lewat `https://domainanda.com`.
2. Di `.env` setel:
   ```
   APP_URL=https://domainanda.com
   XENDIT_SECRET_KEY=xnd_production_<KEY_LIVE>
   XENDIT_CALLBACK_TOKEN=<token live yg sama dgn dashboard>
   XENDIT_IS_PRODUCTION=true
   PAYMENT_CHANNEL=qris          # atau 'invoice'
   PAYMENT_SIMULATION=false
   ```
3. Jangan lupa ubah status Digiflazz ke produksi: `DIGIFLAZZ_PRODUCTION=true` + key live.
4. Flush konfigurasi:
   ```
   php artisan config:clear
   php artisan config:cache
   ```
5. Buka **Admin → Gateway Status** → pastikan semua item hijau.

---

## 4. Uji coba sebelum produksi sungguhan

Semua uji coba payment sebaiknya dilakukan di **mode development/test** Xendit
(`XENDIT_IS_PRODUCTION=false`, key `xnd_development_...`) lewat **domain publik**
(webhook tidak bisa menjangkau `localhost`).

> **Penting:** QRIS yang di-generate di mode test TIDAK bisa dibayar dengan aplikasi
> QRIS/e-wallet sungguhan (QR tersebut fiktif). Ini wajar — bukan bug. Untuk memvalidasi
> pipeline di mode test, gunakan **simulate payment** Xendit (lihat langkah 4.3) yang
> memicu webhook `qr.payment` ke endpoint kamu tanpa pembayaran riil.

### 4.1 Siapkan environment test di server
Pastikan di `.env` server:
```
XENDIT_SECRET_KEY=xnd_development_...   # mode test
XENDIT_IS_PRODUCTION=false
PAYMENT_SIMULATION=false                # WAJIB false agar QRIS Xendit benar-benar dibuat
PAYMENT_CHANNEL=qris
```
lalu `php artisan config:clear`.

### 4.2 Buat order & buka halaman pembayaran
- Buat 1 order sungguhan di `https://marketplace.johengaming.id/`, pilih QRIS.
- Pastikan halaman payment menampilkan **QR code** (berarti `createQr` sukses → tersimpan
  `gateway_invoice_id` = id QR `qr_...`).

### 4.3 Simulasikan pembayaran QRIS (tanpa bayar sungguhan)
Di server, jalankan command berikut (pakai **Order ID** dari order yang barusan dibuat):
```
php artisan xendit:simulate-qris JM2026XXXXXX
```
Command ini memanggil endpoint test Xendit
`POST /qr_codes/{id}/payments/simulate` yang menandai QR sebagai bayar, dan Xendit
langsung mengirim webhook `qr.payment` ke `https://marketplace.johengaming.id/payment/notification`.

### 4.4 Verifikasi
- Buka **https://dashboard.xendit.co/callbacks** → cari event `qr.payment` untuk order tsb →
  status respons endpoint = **200** dan body `{"status":"ok"}`.
- Cek status order via `GET /payment/status/{order_id}` → harus berubah
  `pending` → `processing` (Dgiflazz topUp) → `success`.
- Kalau webhook tidak kunjung sampai, di tab Callbacks klik **Resend**.

### 4.5 Test flow Invoice (opsional, jika `PAYMENT_CHANNEL=invoice`)
Ulangi dengan `PAYMENT_CHANNEL=invoice`, buat order, pastikan ter-redirect ke halaman
invoice Xendit, lalu simulasikan `invoice.paid` (di dasbor test) & cek webhook `invoice.paid`.

---

## 5. Sinkronkan metode pembayaran (agar tampilan server sama dengan lokal)

Data metode pembayaran disimpan di tabel `payment_methods` (database), bukan di kode.
Jika di server hanya tampil satu metode (mis. hanya QRIS), artinya data `payment_methods`
di server belum sama dengan lokal.

Seeder `PaymentMethodSeeder` sudah disinkronkan dengan data lokal (10 metode:
QRIS, GoPay, Dana, OVO, ShopeePay, BCA/BNI/Mandiri VA, Alfamart, Indomaret — lengkap
dengan kategori & path foto). Untuk menerapkannya di **server**:

```
php artisan db:seed --class="Database\Seeders\PaymentMethodSeeder" --force
```

Seeder ini *idempotent*: meng-`updateOrCreate` 10 metode di atas dan menonaktifkan
metode lain (tidak menghapus datanya). Jalankan berulang kali aman.

### Pastikan foto metode tampil
Metode pembayaran memakai `photo` berikut yang tersimpan di `storage/app/public/payments/`:
```
alfamart.svg  bca.svg  bni.svg  bri.svg(*opsional)  dana.svg
gopay.svg  gopay-dark.png  indomaret.svg  mandiri.svg  ovo.svg
qris.svg  qris-dark.png  shopeepay.svg
```
Agar foto muncul, pastikan di server:
1. File-file di atas ikut ter-deploy (folder `storage/app/public/payments/`), dan
2. `php artisan storage:link` sudah dijalankan (agar `/storage/...` bisa diakses publik).

> ### ⚠️ Catatan penting
> Saat ini aplikasi **hanya benar-benar memproses pembayaran via QRIS** (atau Invoice)
> Xendit. Metode lain (Dana, GoPay, VA, minimarket) **hanya tampilan pilihan** — pembayaran
> riil tetap dibuat sebagai QRIS Xendit. Jangan menonaktifkan QRIS, karena itu satu-satunya
> channel yang terintegrasi.

---

## 6. Catatan teknis kode (sudah diimplementasikan)

- `app/Services/XenditService.php` — `createInvoice`, `getInvoice`, `createQr`,
  `getQr`, `verifyCallbackToken`.
- `app/Http/Controllers/PaymentController.php@notificationHandler` — verifikasi
  callback token + proses webhook QRIS & Invoice, mencakup idempotency (`handlePaid`).
- `app/Http/Controllers/OrderController.php` — membuat QRIS/Invoice saat checkout,
  menyimpan `gateway_type`, `gateway_invoice_id`, `qr_string`.
- `config/xendit.php` + variabel `.env` (XENDIT_*).
- `bootstrap/app.php` — CSRF dikecualikan untuk `payment/notification` &
  `digiflazz/callback`.
