@extends('admin.csadmin.layout')
@section('title', 'Admin CS - Conversations')

<style>
    .cs-page { height: 100%; overflow-y: auto; padding: 24px; }
    .cs-card {
        background: #102E4D;
        border: 1px solid #1A4168;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .cs-title { font-size: 18px; font-weight: 700; }
    .cs-sub { font-size: 12px; color: #6F89A7; margin-top: 2px; }
    .cs-table { width: 100%; border-collapse: collapse; }
    .cs-table th {
        text-align: left; font-size: 11px; text-transform: uppercase;
        letter-spacing: .8px; color: #6F89A7; padding: 10px 12px;
        border-bottom: 1px solid #1A4168;
    }
    .cs-table td { padding: 12px; border-bottom: 1px solid #102A47; font-size: 13px; vertical-align: middle; }
    .cs-filter { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
    .cs-select, .cs-input {
        background: #102A47; border: 1px solid #1A4168; color: #F5F7FB;
        padding: 8px 12px; border-radius: 10px; font-size: 13px; font-family: 'Poppins', sans-serif;
        outline: none;
    }
    .cs-select:focus, .cs-input:focus { border-color: #3F6DF5; }
    .cs-btn {
        background: #3F6DF5; border: none; color: #fff; padding: 8px 16px;
        border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 600;
        font-family: 'Poppins', sans-serif;
    }
    .cs-btn:hover { filter: brightness(1.1); }
    .cs-btn.ghost { background: #102A47; border: 1px solid #1A4168; font-weight: 500; }
    .cs-btn.sm { padding: 5px 12px; font-size: 12px; border-radius: 8px; }
    .cs-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600;
    }
    .cs-badge.channel { background: rgba(63,109,245,.15); color: #7fa2ff; }
    .cs-badge.open { background: rgba(16,185,129,.15); color: #34d399; }
    .cs-badge.pending { background: rgba(245,158,11,.15); color: #fbbf24; }
    .cs-badge.closed { background: rgba(148,163,184,.15); color: #94a3b8; }
    .cs-avatar {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        background: linear-gradient(135deg, #14b8a6, #3F6DF5);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff;
    }
    .cs-unread {
        min-width: 20px; height: 20px; border-radius: 10px; background: #ef4444; color: #fff;
        font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; padding: 0 6px;
    }
    .cs-empty { text-align: center; padding: 48px 0; color: #6F89A7; font-size: 14px; }
    .cs-pagination { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; padding: 12px 0 0; }
    .cs-pagination a, .cs-pagination span {
        padding: 6px 12px; border-radius: 8px; background: #102A47; border: 1px solid #1A4168;
        color: #F5F7FB; text-decoration: none; font-size: 12px;
    }
    .cs-pagination .active { background: #3F6DF5; }
</style>

@section('content')
<div class="cs-page">
    <div class="cs-card">
        <div class="cs-title">Percakapan Guest</div>
        <div class="cs-sub">Balas pesan dari pengguna yang belum login (Guest)</div>
    </div>

    <div class="cs-card">
        <form method="GET" class="cs-filter">
            <select name="channel_id" class="cs-select">
                <option value="">Semua Channel</option>
                @foreach($channels as $channel)
                <option value="{{ $channel->id }}" {{ request('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                @endforeach
            </select>
            <select name="status" class="cs-select">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <input type="text" name="search" class="cs-input" placeholder="Cari nama guest..." value="{{ request('search') }}">
            <button type="submit" class="cs-btn">Filter</button>
            @if(request()->hasAny(['channel_id', 'status', 'search']))
            <a href="{{ route('csadmin.conversations') }}" class="cs-btn ghost">Reset</a>
            @endif
        </form>
    </div>

    <div class="cs-card">
        <table class="cs-table">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Channel</th>
                    <th>Last Message</th>
                    <th>Time</th>
                    <th>Unread</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conv)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="cs-avatar">G</div>
                            <div>
                                <div style="font-weight:600">{{ $conv->guest_name ?? 'Guest' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="cs-badge channel">{{ $conv->channel->name ?? '' }}</span></td>
                    <td style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#8FA8C4">
                        @if($conv->last_message)
                            @if($conv->last_message->message_type === 'image') 📷 Foto
                            @elseif($conv->last_message->message_type === 'video') 🎬 Video
                            @elseif($conv->last_message->message_type === 'audio') 🎤 Pesan suara
                            @else {{ $conv->last_message->message }} @endif
                        @else —
                        @endif
                    </td>
                    <td style="font-size:12px;color:#6F89A7">{{ $conv->last_message_at?->diffForHumans() ?? '—' }}</td>
                    <td>
                        @if($conv->admin_unread_count > 0)
                        <span class="cs-unread">{{ $conv->admin_unread_count }}</span>
                        @else
                        <span style="color:#6F89A7">0</span>
                        @endif
                    </td>
                    <td>
                        <span class="cs-badge {{ $conv->status }}">{{ ucfirst($conv->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('csadmin.conversations.show', $conv) }}" class="cs-btn sm">Buka</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="cs-empty">Belum ada percakapan dari guest</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="cs-pagination">
            {{ $conversations->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
</div>
@endsection