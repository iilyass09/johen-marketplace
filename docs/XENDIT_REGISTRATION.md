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

Buka https://dashboard.xendit.co → **Settings → Webhooks**, lalu daftarkan **Callback URL**
untuk setiap event di bawah. Semua mengarah ke endpoint yang sama
(`payment/notification`) dengan token = `XENDIT_CALLBACK_TOKEN`.

| Webhook | Event | Kapan dipakai |
|---------|-------|---------------|
| QRIS | **QR Code payment** (`qr.payment`) | Pembayaran QRIS (self-hosted/embed) |
| Virtual Account | **FVA paid** (`fva.paid`) | Pembayaran VA BCA/BNI/BRI/Mandiri/Permata |
| E-wallet | **EWallet charge** (`ewallet.charge`) + **EWallet capture** (`ewallet.capture`) | Pembayaran GoPay/DANA/OVO/ShopeePay |
| Retail Outlet | **Fixed payment code paid** (`ro_fpc.paid`) | Pembayaran Alfamart/Indomaret |
| Invoice (V2) | **Invoice** (`invoice.paid`, `invoice.expired`, dan `invoice.settled` jika ada) | Fallback `PAYMENT_CHANNEL=invoice` |

URL webhook: `https://domainanda.com/payment/notification`

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

### 4.3 Simulasikan pembayaran (tanpa bayar sungguhan)

Setiap channel punya command simulasi mode-test yang menandai charge sebagai lunas
dan memicu webhook ke endpoint kamu:

| Channel | Command |
|---------|---------|
| QRIS | `php artisan xendit:simulate-qris JM2026XXXXXX` |
| Virtual Account | `php artisan xendit:simulate-va TUP-ABC123XYZ` |
| Retail Outlet | `php artisan xendit:simulate-retail TUP-ABC123XYZ` |

> E-wallet (GoPay/DANA/OVO/ShopeePay) **tidak punya endpoint simulasi** di mode test
> untuk `/ewallets/charges`. Cara memvalidasi alur e-wallet: buat order → pelanggan
> di-redirect ke checkout Xendit → selesaikan di halaman sandbox, lalu webhook
> `ewallet.charge/capture` masuk ke endpoint kamu.

### 4.4 Verifikasi koneksi semua metode sekaligus

Siapkan key test di `.env`, lalu jalankan diagnosa per-channel yang membuat charge
Xendit sungguhan untuk setiap metode aktif (reference acak `CHK-…`, pembayaran fiktif):

```
php artisan xendit:check-channels
```

- Output berupa tabel: `Metode | Channel | Koneksi | Detail` (OK/GAGAL + pesan error API).
- Filter satu metode: `php artisan xendit:check-channels --method=bca_va`
- Ubah nominal uji: `php artisan xendit:check-channels --amount=50000`
- Gagal untuk OVO? Pastikan `XENDIT_OVO_APP_ID` terisi & pengguna menginput nomor HP
  (field "No. WhatsApp" di halaman top-up tersimpan ke `orders.customer_phone`).
- Error `CALLBACK_URL_NOT_FOUND` pada e-wallet (`/ewallets/charges`)? Set callback URL
  tipe `ewallet` via API (header `X-Callback-URL` & body `callback_url` IGNORED oleh Xendit):
  ```
  POST https://api.xendit.co/callback_urls/ewallet   { "url": "https://.../payment/notification" }
  ```
  atau lewat dashboard **Settings → Callbacks**. Di mode TEST ini sudah dilakukan dan
  hasil akhirnya **12/12 metode OK** (QRIS, BCA/BNI/BRI/Mandiri/Permata VA, Alfamart,
  Indomaret, DANA, GoPay, OVO, ShopeePay).
- GoPay wajib `channel_properties.cancel_redirect_url` selain `success`/`failure_redirect_url`
  (sudah ditambahkan di `PaymentGatewayService::chargeEwallet`, LG226-229).

### 4.5 Verifikasi
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

Seeder `PaymentMethodSeeder` sudah disinkronkan dengan data lokal
(QRIS, GoPay, Dana, OVO, ShopeePay, BCA/BRI/BNI/Mandiri/Permata VA, Alfamart, Indomaret).
LinkAja dinonaktifkan karena sudah discontinued di Xendit. Untuk menerapkannya di **server**:

```
php artisan db:seed --class="Database\Seeders\PaymentMethodSeeder" --force
php artisan cache:clear
```

Seeder ini *idempotent*: meng-`updateOrCreate` metode di atas dan menonaktifkan
metode lain (tidak menghapus datanya). Jalankan berulang kali aman.

> Setelah seeder, channel yang benar-benar tampil ke pelanggan disaring otomatis
> oleh `PaymentGatewayService::filterAvailableMethods()` berdasarkan channel yang
> aktif di akun Xendit (GET `/payment_channels`, cache 10 menit → `cache:clear` jika perlu).

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

## 6. Catatan teknis kode (sudah diimplementasikan)

- `app/Services/XenditService.php` — `createInvoice`, `getInvoice`, `createQr`/`getQr`,
  `createVirtualAccount`/`getVirtualAccount`, `createEwalletCharge`/`getEwalletCharge`,
  `createRetailOutlet`/`getRetailOutlet`, `availableChannels`, `verifyCallbackToken`.
- `app/Services/PaymentGatewayService.php` — resolve & charge per metode (QRIS / VA /
  e-wallet / minimarket / Invoice), `filterAvailableMethods()`, payload OVO (`app_id`,
  `mobile_number`) & redirect URL e-wallet.
- `app/Http/Controllers/PaymentController.php@notificationHandler` — verifikasi callback
  token + proses semua webhook (qr.payment, fva.paid, ewallet.*, ro_fpc, invoice.*),
  mencakup idempotency (`handlePaid`) & polling `syncFromGateway`.
- `app/Http/Controllers/OrderController.php` — membuat charge saat checkout (memakai
  `payment_method` dari form), menyimpan `gateway_type`, `gateway_invoice_id`, `qr_string`,
  `va_number`, `payment_code`, `checkout_url`, `customer_phone`.
- Command uji:
  - `php artisan xendit:check-channels` — diagnosa koneksi semua metode (mode test).
  - `php artisan xendit:simulate-qris`, `xendit:simulate-va`, `xendit:simulate-retail` —
    simulasi pembayaran test per channel.
- `config/xendit.php` + variabel `.env` (`XENDIT_SECRET_KEY`, `XENDIT_CALLBACK_TOKEN`,
  `XENDIT_IS_PRODUCTION`, `XENDIT_OVO_APP_ID`).
- `bootstrap/app.php` — CSRF dikecualikan untuk `payment/notification` &
  `digiflazz/callback`.
