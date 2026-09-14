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
                    <button type="button" class="chat-media chat-image-trigger" onclick="openChatImage('/media/{{ $msg->media_path }}', {{ $msg->id }})" aria-label="Buka gambar ukuran penuh"><img src="/media/{{ $msg->media_path }}" alt="Gambar yang dikirim di chat" loading="lazy"></button>
                    @elseif($msg->message_type === 'video' && $msg->media_path)
                    <div class="chat-media"><video src="/media/{{ $msg->media_path }}" controls preload="none" @if($msg->poster_path) poster="/media/{{ $msg->poster_path }}" @endif></video></div>
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
            <input type="file" id="admin-file-input" accept="image/*,video/*" multiple style="display:none" onchange="adminFileSelect(event)">
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
.chat-msg-user .chat-bubble { background: var(--purple, #8b5cf6); color: #fff; border-bottom-right-radius: 4px; }
.chat-msg-admin .chat-bubble { background: var(--surface-2, rgba(255,255,255,.06)); color: var(--text, #f5f3fb); border-bottom-left-radius: 4px; }
.chat-bubble-system { background: transparent; color: var(--text-mute, rgba(255,255,255,.4)); font-size: 11px; text-align: center; padding: 4px 12px; }
.chat-time { font-size: 10px; color: var(--text-mute, rgba(255,255,255,.3)); margin-top: 4px; display: flex; align-items: center; gap: 4px; }
.chat-msg-user .chat-time { justify-content: flex-end; }
.chat-media { max-width: 100%; border-radius: 8px; overflow: hidden; margin-bottom: 4px; }
.chat-image-trigger { display:block; padding:0; border:0; background:transparent; cursor:zoom-in; }
.chat-media img, .chat-media video { width: 100%; max-height: 200px; object-fit: cover; display: block; }
.chat-image-lightbox { position:fixed; inset:0; z-index:10050; display:flex; align-items:center; justify-content:center; padding:24px; background:rgba(8,6,16,.92); cursor:zoom-out; }
.chat-image-lightbox img { max-width:100%; max-height:100%; object-fit:contain; border-radius:10px; box-shadow:0 24px 80px rgba(0,0,0,.5); cursor:default; }
.chat-image-lightbox-tools { position:fixed; top:16px; right:18px; display:flex; align-items:center; gap:6px; z-index:1; }
.chat-image-lightbox-tools button, .chat-image-lightbox-tools a { width:40px; height:40px; border:0; border-radius:50%; background:rgba(255,255,255,.14); color:#fff; display:grid; place-items:center; text-decoration:none; cursor:pointer; }
.chat-image-lightbox-tools svg { width:19px; height:19px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
.chat-image-lightbox-tools [data-action="close"] { font-size:30px; line-height:1; }
.chat-image-lightbox-tools button:hover, .chat-image-lightbox-tools button:focus-visible, .chat-image-lightbox-tools a:hover, .chat-image-lightbox-tools a:focus-visible { background:rgba(255,255,255,.25); }
.chat-image-lightbox-tools .is-active { color:#facc15; }
.chat-image-reactions { position:absolute; top:48px; right:44px; display:flex; gap:4px; padding:6px; border-radius:14px; background:rgba(33,26,47,.96); box-shadow:0 10px 30px rgba(0,0,0,.3); }
.chat-image-reactions button { font-size:18px; background:transparent; }
.chat-reply { background: var(--surface-3, rgba(255,255,255,.04)); border-left: 3px solid var(--purple, #8b5cf6); padding: 4px 8px; margin-bottom: 6px; border-radius: 4px; font-size: 11px; color: var(--text-dim, rgba(255,255,255,.6)); cursor: pointer; }
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
let adminReplyToId = null;

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
        html += `<button type="button" class="chat-media chat-image-trigger" onclick="openChatImage('/media/${msg.media_path}', ${msg.id})" aria-label="Buka gambar ukuran penuh"><img src="/media/${msg.media_path}" alt="Gambar yang dikirim di chat" loading="lazy"></button>`;
    } else if (msg.message_type === 'video' && msg.media_path) {
        const posterAttr = msg.poster_path ? ` poster="/media/${msg.poster_path}"` : '';
        html += `<div class="chat-media"><video src="/media/${msg.media_path}" controls preload="none"${posterAttr}></video></div>`;
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

function openChatImage(imageUrl, messageId) {
    document.getElementById('chat-image-lightbox')?.remove();

    const lightbox = document.createElement('div');
    lightbox.id = 'chat-image-lightbox';
    lightbox.className = 'chat-image-lightbox';
    lightbox.setAttribute('role', 'dialog');
    lightbox.setAttribute('aria-modal', 'true');
    lightbox.setAttribute('aria-label', 'Pratinjau gambar');
    lightbox.innerHTML = `
        <div class="chat-image-lightbox-tools" aria-label="Aksi gambar">
            <button type="button" data-action="reply" title="Balas" aria-label="Balas"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 9 5 14l5 5"/><path d="M5 14h9a5 5 0 0 1 5 5"/></svg></button>
            <button type="button" data-action="react" title="Beri reaksi" aria-label="Beri reaksi"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M8 14s1.4 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg></button>
            <button type="button" data-action="star" title="Beri bintang" aria-label="Beri bintang" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9Z"/></svg></button>
            <a href="${imageUrl}" download title="Unduh" aria-label="Unduh gambar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v11"/><path d="m8 10 4 4 4-4M5 20h14"/></svg></a>
            <button type="button" data-action="close" title="Tutup" aria-label="Tutup gambar">×</button>
            <div class="chat-image-reactions" hidden><button type="button">👍</button><button type="button">❤️</button><button type="button">😂</button><button type="button">😮</button></div>
        </div><img src="${imageUrl}" alt="Gambar ukuran penuh">`;

    let onKeydown;
    const close = () => {
        lightbox.remove();
        document.removeEventListener('keydown', onKeydown);
    };
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) close();
    });
    lightbox.querySelector('[data-action="close"]')?.addEventListener('click', close);
    lightbox.querySelector('[data-action="reply"]')?.addEventListener('click', () => {
        close();
        adminReplyToId = messageId;
        const input = document.getElementById('admin-chat-input');
        if (input) {
            input.placeholder = `Balas gambar #${messageId}...`;
            input.focus();
        }
    });
    const reactionPicker = lightbox.querySelector('.chat-image-reactions');
    lightbox.querySelector('[data-action="react"]')?.addEventListener('click', () => { reactionPicker.hidden = !reactionPicker.hidden; });
    reactionPicker?.addEventListener('click', (event) => {
        if (event.target.tagName !== 'BUTTON') return;
        lightbox.querySelector('[data-action="react"]').textContent = event.target.textContent;
        reactionPicker.hidden = true;
    });
    lightbox.querySelector('[data-action="star"]')?.addEventListener('click', (event) => {
        const active = event.currentTarget.classList.toggle('is-active');
        event.currentTarget.setAttribute('aria-pressed', String(active));
    });
    onKeydown = function(event) {
        if (event.key === 'Escape') {
            close();
        }
    };
    document.addEventListener('keydown', onKeydown);
    document.body.appendChild(lightbox);
    lightbox.querySelector('[data-action="close"]')?.focus();
}

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
    const replyToId = adminReplyToId;

    input.value = '';
    input.style.height = 'auto';
    document.getElementById('admin-send-btn').disabled = true;

    try {
        const formData = new FormData();
        formData.append('message_type', 'text');
        formData.append('message', text);
        if (replyToId) formData.append('reply_to_message_id', replyToId);

        const res = await fetch(`/admin/live-chat/conversations/${CONVERSATION_ID}/reply`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        if (!res.ok) throw new Error();
        const msg = await res.json();
        appendMessage(msg);
        adminReplyToId = null;
        input.placeholder = 'Ketik pesan...';
        document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
    } catch (e) {
        input.value = text;
    }
}

let adminPreviewFiles = [];

function adminFileSelect(e) {
    const newFiles = Array.from(e.target.files);
    if (!newFiles.length) return;
    e.target.value = '';
    adminPreviewFiles.push(...newFiles);
    showAdminPreview();
}

function showAdminPreview() {
    if (adminPreviewFiles.length === 0) return;
    const o = document.getElementById('admin-preview-overlay');
    if (o) o.remove();
    const filesHtml = adminPreviewFiles.map((f, i) => {
        const url = URL.createObjectURL(f);
        const isImage = f.type.startsWith('image/');
        const isVideo = f.type.startsWith('video/');
        return `<div class="admin-preview-item" style="position:relative;display:inline-block;margin:4px">
            ${isImage ? `<img src="${url}" style="width:80px;height:80px;object-fit:cover;border-radius:8px">` : `<video src="${url}" style="width:80px;height:80px;object-fit:cover;border-radius:8px" muted preload="metadata"></video>`}
            <button onclick="removeAdminPreviewFile(${i})" style="position:absolute;top:-4px;right:-4px;width:18px;height:18px;border-radius:50%;border:none;background:rgba(0,0,0,.7);color:#fff;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center">&times;</button>
        </div>`;
    }).join('');
    const overlay = document.createElement('div');
    overlay.id = 'admin-preview-overlay';
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:10002;display:flex;align-items:center;justify-content:center';
    overlay.innerHTML = `
        <div style="background:var(--surface);border-radius:16px;padding:20px;max-width:400px;width:90%;text-align:center">
            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:4px;margin-bottom:12px">${filesHtml}</div>
            <input type="text" id="admin-preview-caption" class="input-field" placeholder="Caption (opsional)" style="width:100%;margin-bottom:12px;border-radius:12px">
            <div style="display:flex;gap:8px;justify-content:center">
                <button onclick="cancelAdminPreview()" style="padding:8px 20px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text-dim);cursor:pointer">Batal</button>
                <button onclick="confirmAdminSend()" style="padding:8px 20px;border-radius:8px;border:none;background:var(--purple);color:#fff;cursor:pointer;font-weight:600">Kirim</button>
            </div>
        </div>`;
    document.body.appendChild(overlay);
}

function removeAdminPreviewFile(idx) {
    adminPreviewFiles.splice(idx, 1);
    if (adminPreviewFiles.length === 0) {
        cancelAdminPreview();
    } else {
        showAdminPreview();
    }
}

function cancelAdminPreview() {
    adminPreviewFiles = [];
    const o = document.getElementById('admin-preview-overlay');
    if (o) o.remove();
}

async function confirmAdminSend() {
    if (!adminPreviewFiles.length) return;
    const caption = document.getElementById('admin-preview-caption')?.value || '';
    const files = [...adminPreviewFiles];
    const replyToId = adminReplyToId;
    cancelAdminPreview();

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const isImage = file.type.startsWith('image/');
        try {
            const formData = new FormData();
            formData.append('message_type', isImage ? 'image' : 'video');
            formData.append('media', file);
            if (i === 0 && caption) formData.append('message', caption);
            if (i === 0 && replyToId) formData.append('reply_to_message_id', replyToId);

            const res = await fetch(`/admin/live-chat/conversations/${CONVERSATION_ID}/reply`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            if (!res.ok) throw new Error();
            const msg = await res.json();
            appendMessage(msg);
        } catch (e) {}
    }
    adminReplyToId = null;
    const input = document.getElementById('admin-chat-input');
    if (input) input.placeholder = 'Ketik pesan...';
    document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
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
