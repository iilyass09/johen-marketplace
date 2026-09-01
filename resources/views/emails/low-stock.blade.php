<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stok Menipis - Johen Gaming</title>
</head>
<body style="margin:0;padding:0;background:#100821;font-family:'Inter',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#100821;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background:#1e1136;border-radius:16px;border:1px solid rgba(255,255,255,.06);overflow:hidden;">
                    <tr>
                        <td align="center" style="padding:32px 32px 0;">
                            <img src="{{ asset('logo.png') }}" alt="Johen Gaming" width="48" style="border-radius:10px;margin-bottom:8px;">
                            <h1 style="color:#f5f3fb;font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:800;margin:0 0 4px;">JOHEN<span style="color:#9d5cf5;">GAMING</span></h1>
                            <p style="color:#7c6ea3;font-size:.82rem;margin:0 0 24px;">Peringatan Stok Menipis</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 32px;">
                            <p style="color:#b3a6d6;font-size:.88rem;margin:0 0 16px;">Stok produk di bawah ini sudah hampir habis.</p>
                            <div style="background:#271746;border-radius:12px;padding:20px 24px;margin-bottom:20px;border:1px solid rgba(245,158,11,.35);text-align:left;">
                                <p style="color:var(--text-dim);font-size:.75rem;margin:0 0 8px;">{{ $product->buyer_sku_code }}</p>
                                <p style="color:#f5f3fb;font-size:1rem;font-weight:700;margin:0 0 12px;">{{ $product->product_name }}</p>
                                <p style="color:#f59e0b;font-size:1.2rem;font-weight:800;margin:0;">Sisa Stok: {{ $product->stock }}</p>
                            </div>
                            <a href="{{ route('admin.products', ['brand' => $product->brand]) }}" style="display:inline-block;background:#f59e0b;color:#1a1440;font-weight:700;font-size:.85rem;text-decoration:none;padding:12px 24px;border-radius:10px;margin-bottom:20px;">
                                Segera Tambah Stok
                            </a>
                            <p style="color:#7c6ea3;font-size:.75rem;margin:0 0 16px;">Mohon isi ulang stok produk ini agar transaksi tetap dapat diproses.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 32px 32px;border-top:1px solid rgba(255,255,255,.06);padding-top:20px;">
                            <p style="color:#5a4a7a;font-size:.7rem;margin:0;">© {{ date('Y') }} Johen Gaming. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
