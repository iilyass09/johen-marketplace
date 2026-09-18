@extends('admin.layouts.app')
@section('title', 'Live Chat Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Live Chat Dashboard</h1>
        <p class="page-subtitle">Overview percakapan live chat</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--accent)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Total Conversations</div>
            <div class="stat-value">{{ $totalConversations }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--success)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Open</div>
            <div class="stat-value">{{ $openConversations }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--warning)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ $pendingConversations }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--error)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Unread</div>
            <div class="stat-value">{{ $totalUnread }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--info)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Operator Online</div>
            <div class="stat-value">{{ $onlineOperators }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-accent" style="background:var(--text-mute, #666)"></div>
        <div class="stat-card-body">
            <div class="stat-label">Operator Offline</div>
            <div class="stat-value">{{ $offlineOperators }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
    <div class="card-glass">
        <div class="card-header">
            <h3 class="card-title">Conversations per Channel</h3>
        </div>
        <div class="card-body">
            @forelse($conversationsPerChannel as $channel)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
                <span style="color:var(--text)">{{ $channel->name }}</span>
                <span class="badge badge-info">{{ $channel->conversations_count }}</span>
            </div>
            @empty
            <div class="empty-state">Belum ada data</div>
            @endforelse
        </div>
    </div>

    <div class="card-glass">
        <div class="card-header">
            <h3 class="card-title">Recent Conversations</h3>
            <a href="{{ route('admin.live-chat.conversations') }}" class="btn btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body">
            @forelse($recentConversations as $conv)
            <a href="{{ route('admin.live-chat.conversations.show', $conv) }}" style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);text-decoration:none;color:inherit">
                <div style="width:36px;height:36px;border-radius:10px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;flex-shrink:0">
                    {{ substr($conv->channel->name, 0, 2) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <strong style="font-size:13px;color:var(--text)">{{ $conv->isGuest() ? ($conv->guest_name ?? 'Guest') : ($conv->user->name ?? 'User') }}</strong>
                        <span style="font-size:11px;color:var(--text-mute)">{{ $conv->last_message_at?->diffForHumans() ?? '' }}</span>
                    </div>
                    <div style="font-size:12px;color:var(--text-dim);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $conv->last_message?->message ?? 'Belum ada pesan' }}
                    </div>
                </div>
                @if($conv->admin_unread_count > 0)
                <span class="badge badge-error" style="font-size:10px">{{ $conv->admin_unread_count }}</span>
                @endif
            </a>
            @empty
            <div class="empty-state">Belum ada percakapan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
