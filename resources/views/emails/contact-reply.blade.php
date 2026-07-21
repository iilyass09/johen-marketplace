<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balasan Pesan - Johen Gaming</title>
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
                            <p style="color:#7c6ea3;font-size:.82rem;margin:0 0 24px;">Balasan Pesan</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 32px;">
                            <p style="color:#b3a6d6;font-size:.88rem;margin:0 0 16px;">Halo <strong style="color:#f5f3fb;">{{ $inquiry->name }}</strong>,</p>
                            <p style="color:#b3a6d6;font-size:.82rem;margin:0 0 20px;">Terima kasih telah menghubungi Johen Gaming. Berikut balasan dari tim CS kami:</p>
                            <div style="background:#271746;border-radius:12px;padding:20px 24px;margin-bottom:20px;border:1px solid rgba(124,58,237,.2);text-align:left;">
                                <p style="color:#f5f3fb;font-size:.88rem;line-height:1.6;margin:0;white-space:pre-wrap;">{{ $reply }}</p>
                            </div>
                            <p style="color:#7c6ea3;font-size:.75rem;margin:0 0 16px;">Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami kembali.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:0 32px 20px;">
                            <p style="color:#b3a6d6;font-size:.78rem;margin:0;">Salam,<br><strong style="color:#9d5cf5;">Tim Customer Service</strong></p>
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
