@extends('admin.layouts.app')
@section('title', 'Live Chat Conversations')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Conversations</h1>
        <p class="page-subtitle">Daftar percakapan live chat</p>
    </div>
</div>

<div class="card-glass">
    <div class="card-header" style="flex-wrap:wrap;gap:12px">
        <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;width:100%">
            <select name="channel_id" class="input-field" style="width:auto;min-width:160px">
                <option value="">Semua Channel</option>
                @foreach($channels ?? [] as $channel)
                <option value="{{ $channel->id }}" {{ request('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option>
                @endforeach
            </select>
            <select name="status" class="input-field" style="width:auto;min-width:120px">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <input type="text" name="search" class="input-field" placeholder="Cari user..." value="{{ request('search') }}" style="width:auto;min-width:200px">
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['channel_id', 'status', 'search']))
            <a href="{{ route('admin.live-chat.conversations') }}" class="btn">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        <table class="w-full">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Channel</th>
                    <th>Last Message</th>
                    <th>Time</th>
                    <th>Unread</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conv)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:32px;height:32px;border-radius:8px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:11px;flex-shrink:0">
                                {{ substr($conv->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:var(--text)">{{ $conv->user->name ?? 'User' }}</div>
                                <div style="font-size:11px;color:var(--text-mute)">{{ $conv->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-info">{{ $conv->channel->name ?? '' }}</span></td>
                    <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text-dim);font-size:13px">
                        {{ $conv->last_message?->message ?? '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--text-mute)">{{ $conv->last_message_at?->diffForHumans() ?? '—' }}</td>
                    <td>
                        @if($conv->admin_unread_count > 0)
                        <span class="badge badge-error">{{ $conv->admin_unread_count }}</span>
                        @else
                        <span style="color:var(--text-mute)">0</span>
                        @endif
                    </td>
                    <td>
                        @if($conv->status === 'open')
                        <span class="badge badge-success">Open</span>
                        @elseif($conv->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @else
                        <span class="badge badge-neutral">Closed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.live-chat.conversations.show', $conv) }}" class="btn btn-sm">Buka</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">💬</div>
                            <div>Belum ada percakapan</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:16px">
        {{ $conversations->withQueryString()->links('vendor.pagination.admin') }}
    </div>
</div>
@endsection
