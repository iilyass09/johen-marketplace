@extends('admin.layouts.app')

@section('title', 'Detail Pesan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Detail Pesan</h2>
    <a href="{{ route('admin.contact-inquiries') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:2rem;max-width:700px">
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Nama</div>
            <div class="font-semibold">{{ $inquiry->name }}</div>
        </div>
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Kategori</div>
            <span class="badge badge-neutral" style="text-transform:capitalize">{{ $inquiry->category }}</span>
        </div>
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Email</div>
            <div>{{ $inquiry->email }}</div>
        </div>
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Telepon</div>
            <div>{{ $inquiry->phone }}</div>
        </div>
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Dikirim</div>
            <div>{{ $inquiry->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div>
            <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.25rem">Status</div>
            @if($inquiry->is_read)
                <span style="color:var(--text-dim);font-size:0.85rem"><i class="far fa-envelope-open mr-1"></i> Sudah dibaca</span>
            @else
                <span style="color:var(--accent)"><i class="fas fa-envelope mr-1"></i> Belum dibaca</span>
            @endif
        </div>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:1.5rem">
        <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.5rem">Pesan</div>
        <div style="font-size:0.9rem;line-height:1.6;white-space:pre-wrap">{{ $inquiry->message }}</div>
    </div>

    <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border)">
        <form method="POST" action="{{ route('admin.contact-inquiries.mark-read', $inquiry) }}" style="display:inline">
            @csrf
            @method('PATCH')
            @if(!$inquiry->is_read)
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-check mr-1"></i> Tandai Sudah Dibaca
                </button>
            @endif
        </form>
        <form method="POST" action="{{ route('admin.contact-inquiries.destroy', $inquiry) }}" style="display:inline;margin-left:0.5rem" onsubmit="return confirm('Hapus pesan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--error)">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </form>
    </div>

    <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border)">
        <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:0.5rem">Balas Pesan</h3>
        <p style="font-size:0.82rem;color:var(--text-dim);margin-bottom:1rem">
            Balasan akan dikirim ke <strong>{{ $inquiry->email }}</strong>
        </p>
        <form method="POST" action="{{ route('admin.contact-inquiries.reply', $inquiry) }}">
            @csrf
            <textarea name="reply" rows="5" class="input-field w-full" placeholder="Tulis balasan anda..." style="resize:vertical;min-height:100px;margin-bottom:1rem">{{ old('reply') }}</textarea>
            @error('reply') <p style="color:var(--error);font-size:0.8rem;margin-bottom:0.5rem">{{ $message }}</p> @enderror
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-reply mr-1"></i> Kirim Balasan
            </button>
        </form>
    </div>
</div>
@endsection
