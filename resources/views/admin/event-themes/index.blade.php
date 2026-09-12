@extends('admin.layouts.app')
@section('title', 'Tema Event')
@section('content')
<div class="flex flex-col flex-wrap justify-between items-start gap-4 mb-6">
    <div class="flex items-center flex-wrap gap-3">
        <h2 class="text-lg font-semibold">Tema Event</h2>
        <span class="badge badge-neutral">{{ $themes->count() }} tema</span>
        @if($aktif)
            <span class="badge badge-success"><i class="fas fa-circle badge-pulse mr-1" style="font-size:.45rem;color:var(--success)"></i> Aktif sekarang: {{ $aktif->name }}</span>
        @else
            <span class="badge badge-warning">Belum ada tema aktif</span>
        @endif
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('admin.event-themes.reset-default') }}" class="inline">
            @csrf
            <button type="submit" class="btn btn-ghost" title="Batalkan semua penonaktifan manual">
                <i class="fas fa-undo-alt"></i><span>Reset Default</span>
            </button>
        </form>
        <a href="{{ route('admin.event-themes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i><span>Buat Tema Baru</span>
        </a>
    </div>
</div>

<div class="card-glass p-4 mb-6" style="background:var(--glass-bg);border:1px solid var(--glass-border)">
    <div style="display:flex;gap:.9rem;align-items:flex-start;font-size:.86rem;color:var(--text-muted);line-height:1.6">
        <i class="fas fa-info-circle mt-1" style="color:var(--info)"></i>
        <div>
            Tema event mengubah tampilan <b>website utama</b> (warna, logo, banner & dekorasi).
            Tema yang <b>aktif</b> (manual) atau sedang dalam <b>jadwal</b> akan otomatis dipakai.
            Jika lebih dari satu aktif, <b>priority</b> tertinggi yang menang. Tema <b>default</b> dipakai sebagai cadangan.
            Lihat hasil dengan tombol <b>Preview</b> (URL <code>?preview_theme=slug</code>).
        </div>
    </div>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Warna</th>
                    <th>Nama</th>
                    <th class="text-center">Default</th>
                    <th class="text-center">Status</th>
                    <th>Jadwal</th>
                    <th class="text-center">Priority</th>
                    <th>Efek Partikel</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($themes as $theme)
                @php
                    $c = is_array($theme->colors) ? $theme->colors : [];
                    $aktifSekarang = $theme->isAktifSekarang();
                    $isAktif = $aktif && $theme->id === $aktif->id;
                    $statusLabel = $theme->is_default ? 'Default' : ($theme->is_active ? 'Aktif (manual)' : ($theme->start_date ? 'Jadwal' : 'Nonaktif'));
                    $statusBadge = $theme->is_default ? 'badge-info' : ($aktifSekarang ? 'badge-success' : ($theme->start_date ? 'badge-warning' : 'badge-neutral'));
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;gap:4px;align-items:center">
                            @foreach(['primary','accent','bg','card_bg'] as $k)
                                <span title="{{ $k }}" style="width:18px;height:18px;border-radius:6px;background:{{ $c[$k] ?? 'transparent' }};border:1px solid var(--glass-border);display:inline-block"></span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <div class="font-semibold">{{ $theme->name }}</div>
                        <div style="font-size:.72rem;color:var(--text-dim)">/{{ $theme->slug }}</div>
                    </td>
                    <td class="text-center">
                        @if($theme->is_default)
                            <span class="badge badge-info"><i class="fas fa-star mr-1"></i>Default</span>
                        @else
                            <form method="POST" action="{{ route('admin.event-themes.set-default', $theme) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-xs" title="Jadikan default">Jadikan Default</button>
                            </form>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        @if($isAktif)
                            <div style="font-size:.68rem;color:var(--success);margin-top:.2rem">Dipakai saat ini</div>
                        @endif
                    </td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        @if($theme->start_date)
                            {{ $theme->start_date->format('d/m/Y') }} —<br>{{ $theme->end_date ? $theme->end_date->format('d/m/Y') : 'selamanya' }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">{{ $theme->priority }}</td>
                    <td style="font-size:.8rem;color:var(--text-muted)">
                        {{ $theme->particle_effect ? ucfirst($theme->particle_effect) : 'Tidak ada' }}
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.event-themes.edit', $theme) }}" class="btn btn-ghost btn-xs" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="{{ route('admin.event-themes.preview', $theme) }}" class="btn btn-ghost btn-xs" target="_blank" title="Preview di website"><i class="fas fa-eye"></i></a>
                            @if(!$theme->is_default)
                                <form action="{{ route('admin.event-themes.toggle', $theme) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-ghost btn-xs" title="{{ $theme->is_active ? 'Nonaktifkan manual' : 'Aktifkan manual' }}">
                                        <i class="fas {{ $theme->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}" style="font-size:1rem"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('admin.event-themes.destroy', $theme) }}', 'Hapus tema {{ $theme->name }}? Gambar terkait ikut dihapus.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-palette"></i>
                            <p>Belum ada tema event. Buat tema pertama untuk mulai.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection