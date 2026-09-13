@extends('admin.layouts.app')
@section('title', 'Chat - ' . $conversation->channel->name)

@section('content')
<div style="display:flex;gap:24px;height:calc(100vh - 120px)">
    <div class="card-glass" style="flex:1;display:flex;flex-direction:column;overflow:hidden">
        <div class="card-header" style="flex-shrink:0;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:12px">
                <a href="{{ route('admin.live-chat.conversations') }}" style="color:var(--purple-light);text-decoration:none;font-size:18px">←</a>
                <div>
                    <h3 class="card-title" style="margin:0">{{ $conversation->channel->name }}</h3>
                    <div style="font-size:12px;color:var(--text-dim)">
                        User: {{ $conversation->user->name ?? 'User' }} ({{ $conversation->user->email ?? '' }})
                        @if($activeOperator)
                        • Operator: {{ $activeOperator->user->name }} <span style="color:var(--success)">● Online</span>
                        @else
                        • <span style="color:var(--error)">● Offline</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px">
                @if($conversation->status === 'open')
                <button class="btn btn-sm" onclick="closeConversation({{ $conversation->id }})">Tutup</button>
                @else
                <button class="btn btn-sm btn-primary" onclick="reopenConversation({{ $conversation->id }})">Buka Kembali</button>
                @endif
            </div>
        </div>

        <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:8px">
            @forelse($messages as $msg)
            <div class="chat-msg {{ $msg->sender_type === 'user' ? 'chat-msg-user' : ($msg->sender_type === 'admin' ? 'chat-msg-admin' : 'chat-msg-system') }}" data-msg-id="{{ $msg->id }}">
                @if($msg->sender_type === 'system')
                    <div class="chat-bubble-system">{{ $msg->message }}</div>
                @else
                    @if($msg->reply_to)
                    <div class="chat-reply" onclick="scrollToMsg({{ $msg->reply_to_id }})">
                        ↳ {{ Str::limit($msg->reply_to->message ?? 'Media', 50) }}
                    </div>
                    @endif
                    @if($msg->message_type === 'image' && $msg->media_path)
                    <div class="chat-media"><img src="/media/{{ $msg->media_path }}" alt="Image" loading="lazy"></div>
                    @elseif($msg->message_type === 'video' && $msg->media_path)
                    <div class="chat-media"><video src="/media/{{ $msg->media_path }}" controls preload="none"></video></div>
                    @endif
                    @if($msg->message)
                    <div class="chat-bubble">{{ $msg->message }}</div>
                    @endif
                    <div class="chat-time">
                        {{ $msg->created_at->format('H:i') }}
                        @if($msg->sender_type === 'admin')
                            @if($msg->read_at) ✓✓ @else ✓ @endif
                        @endif
                        @if(auth('admin')->id() === $msg->sender_id || auth('admin')->user()->isAdmin())
                        <button class="chat-delete-btn" onclick="deleteMsg({{ $msg->id }})">🗑</button>
                        @endif
                    </div>
                @endif
            </div>
            @empty
            <div class="empty-state">Belum ada pesan</div>
            @endforelse
        </div>

        @if($conversation->status !== 'closed')
        <div class="chat-composer" style="flex-shrink:0;border-top:1px solid var(--border);padding:12px">
            <input type="file" id="admin-file-input" accept="image/*,video/*" style="display:none" onchange="adminFileSelect(event)">
            <div style="display:flex;align-items:flex-end;gap:8px">
                <button class="btn-icon" onclick="document.getElementById('admin-file-input').click()" title="Kirim gambar/video">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                </button>
                <textarea id="admin-chat-input" class="input-field" rows="1" placeholder="Ketik pesan..." style="flex:1;resize:none;max-height:80px;border-radius:20px" onkeydown="adminKeydown(event)" oninput="autoResizeInput(this)"></textarea>
                <button class="btn btn-primary" id="admin-send-btn" onclick="adminSendText()" disabled style="border-radius:50%;width:36px;height:36px;padding:0;display:flex;align-items:center;justify-content:center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        </div>
        @else
        <div style="padding:16px;text-align:center;color:var(--text-mute);font-size:13px;border-top:1px solid var(--border)">
            Percakapan telah ditutup
        </div>
        @endif
    </div>
</div>

<style>
.chat-msg { max-width: 70%; position: relative; }
.chat-msg-user { align-self: flex-end; }
.chat-msg-admin { align-self: flex-start; }
.chat-msg-system { align-self: center; max-width: 100%; }
.chat-bubble { padding: 8px 12px; border-radius: 12px; font-size: 13px; line-height: 1.5; word-break: break-word; }
.chat-msg-user .chat-bubble { background: var(--purple, #7c3aed); color: #fff; border-bottom-right-radius: 4px; }
.chat-msg-admin .chat-bubble { background: var(--surface-2, rgba(255,255,255,.06)); color: var(--text, #f5f3fb); border-bottom-left-radius: 4px; }
.chat-bubble-system { background: transparent; color: var(--text-mute, rgba(255,255,255,.4)); font-size: 11px; text-align: center; padding: 4px 12px; }
.chat-time { font-size: 10px; color: var(--text-mute, rgba(255,255,255,.3)); margin-top: 4px; display: flex; align-items: center; gap: 4px; }
.chat-msg-user .chat-time { justify-content: flex-end; }
.chat-media { max-width: 100%; border-radius: 8px; overflow: hidden; margin-bottom: 4px; }
.chat-media img, .chat-media video { width: 100%; max-height: 200px; object-fit: cover; display: block; }
.chat-reply { background: var(--surface-3, rgba(255,255,255,.04)); border-left: 3px solid var(--purple, #7c3aed); padding: 4px 8px; margin-bottom: 6px; border-radius: 4px; font-size: 11px; color: var(--text-dim, rgba(255,255,255,.6)); cursor: pointer; }
.chat-reply:hover { background: var(--surface-2, rgba(255,255,255,.06)); }
.chat-delete-btn { background: none; border: none; cursor: pointer; font-size: 10px; opacity: 0.5; }
.chat-delete-btn:hover { opacity: 1; }
.btn-icon { width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--border); background: transparent; color: var(--text-dim); cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.btn-icon:hover { background: var(--surface-2); }
</style>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const CONVERSATION_ID = {{ $conversation->id }};

let pollTimer = null;

function startPoll() {
    stopPoll();
    pollTimer = setInterval(pollMessages, 3000);
}

function stopPoll() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

async function pollMessages() {
    const container = document.getElementById('chat-messages');
    const lastMsg = container.querySelector('[data-msg-id]:last-child');
    const lastId = lastMsg ? lastMsg.dataset.msgId : 0;

    try {
        const res = await fetch(`/admin/live-chat/conversations/${CONVERSATION_ID}/poll?after=${lastId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) return;
        const msgs = await res.json();
        if (msgs.length > 0) {
            msgs.forEach(msg => appendMessage(msg));
            container.scrollTop = container.scrollHeight;
        }
    } catch (e) {}
}

function appendMessage(msg) {
    const container = document.getElementById('chat-messages');
    const emptyState = container.querySelector('.empty-state');
    if (emptyState) emptyState.remove();

    const div = document.createElement('div');
    div.className = `chat-msg ${msg.sender_type === 'user' ? 'chat-msg-user' : 'chat-msg-admin'}`;
    div.dataset.msgId = msg.id;

    let html = '';
    if (msg.reply_to) {
        html += `<div class="chat-reply">↳ ${escapeHtml((msg.reply_to.message || '').substring(0, 50))}</div>`;
    }
    if (msg.message_type === 'image' && msg.media_path) {
        html += `<div class="chat-media"><img src="/media/${msg.media_path}" alt="Image" loading="lazy"></div>`;
    } else if (msg.message_type === 'video' && msg.media_path) {
        html += `<div class="chat-media"><video src="/media/${msg.media_path}" controls preload="none"></video></div>`;
    }
    if (msg.message) {
        html += `<div class="chat-bubble">${escapeHtml(msg.message)}</div>`;
    }
    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    html += `<div class="chat-time">${time}</div>`;

    div.innerHTML = html;
    container.appendChild(div);
}

function escapeHtml(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function scrollToMsg(id) {
    const el = document.querySelector(`[data-msg-id="${id}"]`);
    if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
}

function adminKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); adminSendText(); }
}

function autoResizeInput(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 80) + 'px';
}

const adminInput = document.getElementById('admin-chat-input');
if (adminInput) {
    adminInput.addEventListener('input', function() {
        document.getElementById('admin-send-btn').disabled = !this.value.trim();
    });
}

async function adminSendText() {
    const input = document.getElementById('admin-chat-input');
    const text = input?.value?.trim();
    if (!text) return;

    input.value = '';
    input.style.height = 'auto';
    document.getElementById('admin-send-btn').disabled = true;

    try {
        const formData = new FormData();
        formData.append('message_type', 'text');
        formData.append('message', text);

        const res = await fetch(`/admin/live-chat/conversations/${CONVERSATION_ID}/reply`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (!res.ok) throw new Error();
        const msg = await res.json();
        appendMessage(msg);
        document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
    } catch (e) {
        input.value = text;
    }
}

let adminPreviewFile = null;

function adminFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    e.target.value = '';
    adminPreviewFile = file;
    showAdminPreview(file);
}

function showAdminPreview(file) {
    const url = URL.createObjectURL(file);
    const isImage = file.type.startsWith('image/');
    const overlay = document.createElement('div');
    overlay.id = 'admin-preview-overlay';
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:10002;display:flex;align-items:center;justify-content:center';
    overlay.innerHTML = `
        <div style="background:var(--surface);border-radius:16px;padding:20px;max-width:400px;width:90%;text-align:center">
            ${isImage ? `<img src="${url}" style="max-width:100%;max-height:300px;border-radius:8px;margin-bottom:12px">` : `<video src="${url}" style="max-width:100%;max-height:300px;border-radius:8px;margin-bottom:12px" controls preload="none"></video>`}
            <input type="text" id="admin-preview-caption" class="input-field" placeholder="Caption (opsional)" style="width:100%;margin-bottom:12px;border-radius:12px">
            <div style="display:flex;gap:8px;justify-content:center">
                <button onclick="cancelAdminPreview()" style="padding:8px 20px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text-dim);cursor:pointer">Batal</button>
                <button onclick="confirmAdminSend()" style="padding:8px 20px;border-radius:8px;border:none;background:var(--purple);color:#fff;cursor:pointer;font-weight:600">Kirim</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
}

function cancelAdminPreview() {
    adminPreviewFile = null;
    const o = document.getElementById('admin-preview-overlay');
    if (o) o.remove();
}

async function confirmAdminSend() {
    if (!adminPreviewFile) return;
    const caption = document.getElementById('admin-preview-caption')?.value || '';
    const file = adminPreviewFile;
    const isImage = file.type.startsWith('image/');
    cancelAdminPreview();

    try {
        const formData = new FormData();
        formData.append('message_type', isImage ? 'image' : 'video');
        formData.append('message', caption);
        formData.append('media', file);

        const res = await fetch(`/admin/live-chat/conversations/${CONVERSATION_ID}/reply`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (!res.ok) throw new Error();
        const msg = await res.json();
        appendMessage(msg);
        document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
    } catch (e) {}
}

async function deleteMsg(id) {
    if (!confirm('Hapus pesan ini?')) return;
    try {
        const res = await fetch(`/admin/live-chat/messages/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error();
        const el = document.querySelector(`[data-msg-id="${id}"]`);
        if (el) el.remove();
    } catch (e) {}
}

async function closeConversation(id) {
    if (!confirm('Tutup percakapan ini?')) return;
    try {
        await fetch(`/admin/live-chat/conversations/${id}/close`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        location.reload();
    } catch (e) {}
}

async function reopenConversation(id) {
    try {
        await fetch(`/admin/live-chat/conversations/${id}/reopen`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        location.reload();
    } catch (e) {}
}

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('chat-messages');
    if (container) container.scrollTop = container.scrollHeight;
    startPoll();
});
</script>
@endsection
