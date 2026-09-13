(function() {
  'use strict';

  const AUTH_USER = window.LIVECHAT_USER || null;
  const CHANNELS_API = '/api/live-chat/channels';
  const POLL_INTERVAL = 3000;

  let state = {
    isOpen: false,
    view: 'panel',
    channels: [],
    activeChannel: null,
    conversation: null,
    messages: [],
    pollingTimer: null,
    previewFile: null,
    previewUrl: null,
    replyToMsg: null,
    activeMenuMsgId: null,
  };

  function getToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  function headers(json = true) {
    const h = { 'X-CSRF-TOKEN': getToken() };
    if (json) h['Accept'] = 'application/json';
    return h;
  }

  function timeAgo(date) {
    if (!date) return '';
    const now = new Date();
    const d = new Date(date);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function getInitials(name) {
    return name.replace('Johen ', '').replace('Monkey ', 'M').substring(0, 2).toUpperCase();
  }

  function closeAllMenus() {
    state.activeMenuMsgId = null;
    document.querySelectorAll('.lc-context-menu').forEach(el => el.remove());
    document.querySelectorAll('.lc-msg-has-menu').forEach(el => el.classList.remove('lc-msg-has-menu'));
  }

  function toggleContextMenu(msgId, anchorEl) {
    const existing = document.querySelector(`.lc-context-menu[data-msg-id="${msgId}"]`);
    closeAllMenus();
    if (existing) return;

    state.activeMenuMsgId = msgId;
    const msg = state.messages.find(m => m.id === msgId);
    if (!msg) return;

    const isUser = msg.sender_type === 'user';
    const menu = document.createElement('div');
    menu.className = 'lc-context-menu';
    menu.dataset.msgId = msgId;

    let items = `
      <button class="lc-menu-item" onclick="window.LiveChat.replyTo(${msg.id})">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 00-4-4H4"/></svg>
        Balas
      </button>`;

    if (isUser) {
      items += `
        <button class="lc-menu-item lc-menu-danger" onclick="window.LiveChat.deleteMsg(${msg.id})">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
          Hapus
        </button>`;
    }

    menu.innerHTML = items;
    document.body.appendChild(menu);

    const rect = anchorEl.getBoundingClientRect();
    const popupRect = document.getElementById('lc-popup').getBoundingClientRect();
    let top = rect.bottom - popupRect.top + 4;
    let left = rect.right - popupRect.left - 150;

    if (left + 150 > popupRect.width) left = popupRect.width - 156;
    if (left < 4) left = 4;
    if (top + 120 > popupRect.height) top = rect.top - popupRect.top - 80;

    menu.style.top = top + 'px';
    menu.style.left = left + 'px';

    anchorEl.closest('.lc-msg')?.classList.add('lc-msg-has-menu');
  }

  function getSenderName(msg) {
    if (msg.sender_type === 'user') return AUTH_USER?.name || 'Anda';
    if (msg.sender_type === 'admin') return msg.sender?.name || 'Admin';
    return 'System';
  }

  async function fetchChannels() {
    try {
      const res = await fetch(CHANNELS_API, { headers: headers() });
      if (!res.ok) throw new Error('Failed');
      state.channels = await res.json();
      renderPanel();
    } catch (e) {
      console.error('LiveChat: fetch channels error', e);
    }
  }

  function renderPanel() {
    const container = document.getElementById('lc-body');
    if (!container) return;

    if (!AUTH_USER) {
      container.innerHTML = `
        <div class="lc-login-prompt">
          <h4>Masuk untuk Chat</h4>
          <p>Silakan masuk atau daftar untuk mengirim pesan live chat</p>
          <a href="/login" class="lc-login-btn">Masuk</a>
        </div>`;
      return;
    }

    const gameCards = state.channels.map(ch => {
      const isOnline = ch.is_online;
      const initial = getInitials(ch.name);
      const adminName = ch.operator_name || null;
      const avatarHtml = ch.admin_photo
        ? `<div class="lc-game-avatar has-photo"><img src="${ch.admin_photo}" alt="" style="width:100%;height:100%;object-fit:cover"></div>`
        : `<div class="lc-game-avatar">${initial}</div>`;
      const statusHtml = isOnline
        ? `<div class="lc-game-admin active">Nama Admin: ${escapeHtml(adminName)}</div>`
        : `<div class="lc-game-admin inactive">Admin belum tersedia</div>`;
      const clickAttr = isOnline
        ? `onclick="window.LiveChat.openChannel('${ch.slug}')" style="cursor:pointer"`
        : `style="cursor:default;opacity:0.55"`;
      return `
        <div class="lc-game-card" ${clickAttr}>
          ${avatarHtml}
          <div class="lc-game-info">
            <div class="lc-game-name">${escapeHtml(ch.name)}</div>
            ${statusHtml}
          </div>
        </div>`;
    }).join('');

    const activeConvs = state.channels
      .filter(ch => ch.has_conversation)
      .sort((a, b) => {
        if (a.last_message_at && b.last_message_at) return new Date(b.last_message_at) - new Date(a.last_message_at);
        if (a.last_message_at) return -1;
        if (b.last_message_at) return 1;
        return 0;
      });

    const convHtml = activeConvs.length > 0
      ? activeConvs.map(ch => {
          const initial = getInitials(ch.name);
          const preview = ch.last_message
            ? (ch.last_message.message_type !== 'text' ? '📷 Media' : escapeHtml(ch.last_message.message?.substring(0, 50) || ''))
            : 'Mulai percakapan';
          const time = ch.last_message_at ? timeAgo(ch.last_message_at) : '';
          const badge = ch.unread_count > 0 ? `<span class="lc-conv-badge">${ch.unread_count}</span>` : '';
          const avatarSrc = ch.admin_photo;
          return `
            <div class="lc-conv-item" onclick="window.LiveChat.openChannel('${ch.slug}')">
              <div class="lc-conv-avatar">
                ${avatarSrc ? `<img src="${avatarSrc}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:10px">` : initial}
              </div>
              <div class="lc-conv-info">
                <div class="lc-conv-name">${escapeHtml(ch.name)}</div>
                <div class="lc-conv-preview">${preview}</div>
              </div>
              <div class="lc-conv-meta">
                <div class="lc-conv-time">${time}</div>
                ${badge}
              </div>
            </div>`;
        }).join('')
      : '<div class="lc-conv-empty">Belum ada percakapan aktif</div>';

    container.innerHTML = `
      <div class="lc-panel">
        <div class="lc-panel-header">
          <div class="lc-panel-title">LIVE CHAT ADMIN</div>
        </div>
        <div class="lc-panel-grid">${gameCards}</div>
        <div class="lc-panel-divider"></div>
        <div class="lc-panel-section-title">PERCAKAPAN AKTIF</div>
        <div class="lc-conv-list">${convHtml}</div>
      </div>`;
  }

  async function openChannel(slug) {
    if (!AUTH_USER) { window.location.href = '/login'; return; }
    closeAllMenus();
    try {
      const res = await fetch(`/api/live-chat/conversation/${slug}`, { headers: headers() });
      if (!res.ok) throw new Error('Failed');
      const data = await res.json();
      state.activeChannel = state.channels.find(c => c.slug === slug);
      state.conversation = data.conversation;
      state.view = 'chat';
      state.replyToMsg = null;
      renderChat(data.active_operator);
      await fetchMessages();
      startPolling();
      fetch(`/api/live-chat/conversation/${state.conversation.id}/read`, { method: 'PATCH', headers: headers() });
      updateBadge();
    } catch (e) {
      console.error('LiveChat: open channel error', e);
    }
  }

  async function fetchMessages() {
    if (!state.conversation) return;
    try {
      const url = state.messages.length > 0
        ? `/api/live-chat/messages/${state.conversation.id}?after=${state.messages[state.messages.length - 1].id}`
        : `/api/live-chat/messages/${state.conversation.id}`;
      const res = await fetch(url, { headers: headers() });
      if (!res.ok) throw new Error('Failed');
      const newMessages = await res.json();
      state.messages = state.messages.length === 0 ? newMessages : [...state.messages, ...newMessages];
      renderMessages();
      scrollToBottom();
      if (state.conversation.user_unread_count > 0) {
        fetch(`/api/live-chat/conversation/${state.conversation.channel?.slug || state.activeChannel?.slug}`, { headers: headers() });
      }
    } catch (e) {
      console.error('LiveChat: fetch messages error', e);
      renderMessages();
    }
  }

  function renderMessages() {
    const container = document.getElementById('lc-messages-body');
    if (!container) return;

    if (state.messages.length === 0) {
      container.innerHTML = '<div class="lc-empty">Mulai percakapan dengan mengirim pesan</div>';
      return;
    }

    container.innerHTML = state.messages.map(msg => {
      if (msg.sender_type === 'system') {
        return `<div class="lc-msg lc-msg-system" data-msg-id="${msg.id}"><div class="lc-msg-bubble">${escapeHtml(msg.message)}</div></div>`;
      }

      const isUser = msg.sender_type === 'user';
      const cls = isUser ? 'lc-msg-user' : 'lc-msg-admin';
      const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

      let quoteHtml = '';
      if (msg.reply_to) {
        const rSender = msg.reply_to.sender?.name || (msg.reply_to.sender_type === 'user' ? (AUTH_USER?.name || 'Anda') : 'Admin');
        let rContent = '';
        if (msg.reply_to.message_type === 'image' && msg.reply_to.media_path) {
          rContent = '<span class="lc-quote-media">📷 Foto</span>';
        } else if (msg.reply_to.message_type === 'video' && msg.reply_to.media_path) {
          rContent = '<span class="lc-quote-media">🎬 Video</span>';
        } else {
          rContent = escapeHtml((msg.reply_to.message || 'Pesan telah dihapus').substring(0, 80));
        }
        quoteHtml = `
          <div class="lc-msg-quote" onclick="event.stopPropagation();window.LiveChat.scrollToMsg(${msg.reply_to.id})">
            <div class="lc-quote-bar"></div>
            <div class="lc-quote-content">
              <div class="lc-quote-sender">${escapeHtml(rSender)}</div>
              <div class="lc-quote-text">${rContent}</div>
            </div>
          </div>`;
      }

      let mediaHtml = '';
      if (msg.message_type === 'image' && msg.media_path) {
        mediaHtml = `<div class="lc-msg-media"><img src="/media/${msg.media_path}" alt="Image" loading="lazy"></div>`;
      } else if (msg.message_type === 'video' && msg.media_path) {
        mediaHtml = `<div class="lc-msg-media"><video src="/media/${msg.media_path}" controls preload="none"></video></div>`;
      }

      const bubbleContent = msg.message
        ? `<div class="lc-msg-bubble">${escapeHtml(msg.message)}</div>`
        : '';

      return `
        <div class="lc-msg ${cls}" data-msg-id="${msg.id}">
          <div class="lc-msg-body">
            ${quoteHtml}
            ${mediaHtml}
            ${bubbleContent}
            <div class="lc-msg-time">${time}</div>
          </div>
          <div class="lc-msg-anchor" onclick="window.LiveChat.showMenu(event, ${msg.id})">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
        </div>`;
    }).join('');
  }

  function scrollToBottom() {
    const container = document.getElementById('lc-messages-body');
    if (container) {
      setTimeout(() => { container.scrollTop = container.scrollHeight; }, 50);
    }
  }

  function scrollToMsg(id) {
    const el = document.querySelector(`[data-msg-id="${id}"]`);
    if (el) {
      const container = document.getElementById('lc-messages-body');
      const isVisible = el.offsetTop >= container.scrollTop && el.offsetTop + el.offsetHeight <= container.scrollTop + container.offsetHeight;
      if (!isVisible) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      el.classList.add('lc-msg-highlight');
      setTimeout(() => { el.classList.remove('lc-msg-highlight'); }, 2000);
    }
  }

  function renderChat(operator) {
    const container = document.getElementById('lc-body');
    if (!container) return;

    const operatorAvatar = operator && operator.photo
      ? `<img src="${operator.photo}" alt="" style="width:36px;height:36px;border-radius:10px;object-fit:cover;margin-right:10px;flex-shrink:0">`
      : '';
    const operatorHtml = operator
      ? `<div style="display:flex;align-items:center">${operatorAvatar}<div>Sedang dilayani oleh <strong>${escapeHtml(operator.name)}</strong></div></div><div><span class="online">● Online</span> • Shift ${operator.schedule || ''}</div>`
      : '<div><span class="offline">● Admin sedang offline</span></div><div>Pesan akan dibalas pada jam operasional</div>';

    container.innerHTML = `
      <div class="lc-chat">
        <div class="lc-chat-header">
          <button class="lc-chat-back" onclick="window.LiveChat.backToList()">←</button>
          <div class="lc-chat-info">
            <div class="lc-chat-title">${escapeHtml(state.activeChannel?.name || '')}</div>
            <div class="lc-chat-operator">${operatorHtml}</div>
          </div>
        </div>
        <div class="lc-messages" id="lc-messages-body">
          <div class="lc-empty">Memuat pesan...</div>
        </div>
        <div id="lc-reply-preview" class="lc-reply-preview" style="display:none"></div>
        <div id="lc-inline-preview" class="lc-inline-preview" style="display:none"></div>
        <div class="lc-composer">
          <input type="file" id="lc-file-input" accept="image/*,video/*" style="display:none" onchange="window.LiveChat.onFileSelect(event)">
          <button class="lc-composer-attach" onclick="document.getElementById('lc-file-input').click()" title="Kirim gambar/video">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
          </button>
          <textarea class="lc-composer-input" id="lc-input" rows="1" placeholder="Ketik pesan..." onkeydown="window.LiveChat.onKeydown(event)" oninput="window.LiveChat.autoResize(this)"></textarea>
          <button class="lc-composer-btn" id="lc-send-btn" onclick="window.LiveChat.sendText()" disabled>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </div>
      </div>`;

    const input = document.getElementById('lc-input');
    if (input) input.addEventListener('input', updateSendBtn);
  }

  function updateSendBtn() {
    const btn = document.getElementById('lc-send-btn');
    if (!btn) return;
    const input = document.getElementById('lc-input');
    btn.disabled = !input?.value?.trim() && !state.previewFile;
  }

  function backToList() {
    stopPolling();
    closeAllMenus();
    state.view = 'panel';
    state.activeChannel = null;
    state.conversation = null;
    state.messages = [];
    state.replyToMsg = null;
    fetchChannels();
  }

  function onKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendText();
    }
  }

  function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 80) + 'px';
  }

  async function sendText() {
    const input = document.getElementById('lc-input');
    const text = input?.value?.trim();
    const hasFile = !!state.previewFile;
    const hasText = !!text;
    if (!hasFile && !hasText) return;
    if (!state.conversation) return;

    if (hasFile && hasText) { await sendMediaWithText(text); return; }
    if (hasFile) { confirmSend(); return; }

    const replyId = state.replyToMsg?.id || null;
    input.value = '';
    input.style.height = 'auto';
    document.getElementById('lc-send-btn').disabled = true;
    clearReplyPreview();

    try {
      const formData = new FormData();
      formData.append('conversation_id', state.conversation.id);
      formData.append('message_type', 'text');
      formData.append('message', text);
      if (replyId) formData.append('reply_to_message_id', replyId);

      const res = await fetch('/api/live-chat/messages', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
        body: formData,
      });
      if (!res.ok) throw new Error('Failed');
      const msg = await res.json();
      state.messages.push(msg);
      renderMessages();
      scrollToBottom();
    } catch (e) {
      console.error('LiveChat: send error', e);
      input.value = text;
    }
  }

  async function sendMediaWithText(text) {
    const file = state.previewFile;
    const msgType = file.type.startsWith('image/') ? 'image' : 'video';
    const replyId = state.replyToMsg?.id || null;
    cancelPreview();
    const input = document.getElementById('lc-input');
    input.value = '';
    input.style.height = 'auto';
    document.getElementById('lc-send-btn').disabled = true;
    clearReplyPreview();

    try {
      const formData = new FormData();
      formData.append('conversation_id', state.conversation.id);
      formData.append('message_type', msgType);
      formData.append('message', text);
      formData.append('media', file);
      if (replyId) formData.append('reply_to_message_id', replyId);

      const res = await fetch('/api/live-chat/messages', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
        body: formData,
      });
      if (!res.ok) throw new Error('Failed');
      const msg = await res.json();
      state.messages.push(msg);
      renderMessages();
      scrollToBottom();
    } catch (e) {
      console.error('LiveChat: send media error', e);
    }
  }

  function onFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    e.target.value = '';
    const url = URL.createObjectURL(file);
    state.previewFile = file;
    state.previewUrl = url;
    const isImage = file.type.startsWith('image/');
    const isVideo = file.type.startsWith('video/');
    let mediaHtml = isImage ? `<img src="${url}" alt="Preview">` : isVideo ? `<video src="${url}" controls preload="none"></video>` : '';
    const composer = document.querySelector('.lc-composer');
    if (!composer) return;
    let previewBar = document.getElementById('lc-inline-preview');
    if (!previewBar) {
      previewBar = document.createElement('div');
      previewBar.id = 'lc-inline-preview';
      composer.parentNode.insertBefore(previewBar, composer);
    }
    previewBar.innerHTML = `<div class="lc-inline-preview-thumb">${mediaHtml}<button class="lc-inline-preview-remove" onclick="window.LiveChat.cancelPreview()">✕</button></div>`;
    previewBar.style.display = 'block';
    updateSendBtn();
  }

  function cancelPreview() {
    state.previewFile = null;
    state.previewUrl = null;
    const previewBar = document.getElementById('lc-inline-preview');
    if (previewBar) { previewBar.innerHTML = ''; previewBar.style.display = 'none'; }
    updateSendBtn();
  }

  async function confirmSend() {
    if (!state.previewFile || !state.conversation) return;
    const file = state.previewFile;
    const msgType = file.type.startsWith('image/') ? 'image' : 'video';
    const replyId = state.replyToMsg?.id || null;
    cancelPreview();
    clearReplyPreview();

    try {
      const formData = new FormData();
      formData.append('conversation_id', state.conversation.id);
      formData.append('message_type', msgType);
      formData.append('media', file);
      if (replyId) formData.append('reply_to_message_id', replyId);

      const res = await fetch('/api/live-chat/messages', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
        body: formData,
      });
      if (!res.ok) throw new Error('Failed');
      const msg = await res.json();
      state.messages.push(msg);
      renderMessages();
      scrollToBottom();
    } catch (e) {
      console.error('LiveChat: send media error', e);
    }
  }

  function showMenu(e, msgId) {
    e.stopPropagation();
    toggleContextMenu(msgId, e.currentTarget);
  }

  function replyTo(id) {
    closeAllMenus();
    const msg = state.messages.find(m => m.id === id);
    if (!msg) return;
    state.replyToMsg = msg;

    const senderName = getSenderName(msg);
    let preview = '';
    if (msg.message_type === 'image' && msg.media_path) {
      preview = '📷 Foto';
    } else if (msg.message_type === 'video' && msg.media_path) {
      preview = '🎬 Video';
    } else {
      preview = escapeHtml((msg.message || '').substring(0, 80));
    }

    const replyPreview = document.getElementById('lc-reply-preview');
    if (replyPreview) {
      replyPreview.innerHTML = `
        <div class="lc-reply-preview-inner">
          <div class="lc-reply-preview-bar"></div>
          <div class="lc-reply-preview-content">
            <div class="lc-reply-preview-sender">↩ ${escapeHtml(senderName)}</div>
            <div class="lc-reply-preview-text">${preview}</div>
          </div>
          <button class="lc-reply-preview-close" onclick="window.LiveChat.cancelReply()">✕</button>
        </div>`;
      replyPreview.style.display = 'block';
    }

    const input = document.getElementById('lc-input');
    if (input) {
      input.placeholder = `Balas ${escapeHtml(senderName)}...`;
      input.focus();
    }
  }

  function cancelReply() {
    state.replyToMsg = null;
    const replyPreview = document.getElementById('lc-reply-preview');
    if (replyPreview) { replyPreview.innerHTML = ''; replyPreview.style.display = 'none'; }
    const input = document.getElementById('lc-input');
    if (input) input.placeholder = 'Ketik pesan...';
  }

  function clearReplyPreview() {
    state.replyToMsg = null;
    const replyPreview = document.getElementById('lc-reply-preview');
    if (replyPreview) { replyPreview.innerHTML = ''; replyPreview.style.display = 'none'; }
    const input = document.getElementById('lc-input');
    if (input) input.placeholder = 'Ketik pesan...';
  }

  async function deleteMsg(id) {
    closeAllMenus();
    if (!confirm('Hapus pesan ini?')) return;
    try {
      const res = await fetch(`/api/live-chat/messages/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getToken(), 'Accept': 'application/json' },
      });
      if (!res.ok) throw new Error('Failed');
      state.messages = state.messages.filter(m => m.id !== id);
      renderMessages();
    } catch (e) {
      console.error('LiveChat: delete error', e);
    }
  }

  function startPolling() {
    stopPolling();
    state.pollingTimer = setInterval(() => {
      if (state.view === 'chat' && state.conversation) fetchMessages();
    }, POLL_INTERVAL);
  }

  function stopPolling() {
    if (state.pollingTimer) { clearInterval(state.pollingTimer); state.pollingTimer = null; }
  }

  function toggle() {
    state.isOpen = !state.isOpen;
    const popup = document.getElementById('lc-popup');
    const overlay = document.getElementById('lc-overlay');
    if (state.isOpen) {
      popup?.classList.add('active');
      overlay?.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (state.view === 'panel') fetchChannels();
    } else {
      popup?.classList.remove('active');
      overlay?.classList.remove('active');
      document.body.style.overflow = '';
      closeAllMenus();
      stopPolling();
    }
  }

  function close() {
    state.isOpen = false;
    document.getElementById('lc-popup')?.classList.remove('active');
    document.getElementById('lc-overlay')?.classList.remove('active');
    document.body.style.overflow = '';
    closeAllMenus();
    stopPolling();
  }

  function updateBadge() {
    const badge = document.getElementById('lc-fab-badge');
    if (!badge) return;
    fetch('/api/live-chat/unread', { headers: headers() })
      .then(r => r.json())
      .then(data => {
        const count = data.unread_count || 0;
        if (count > 0) { badge.textContent = count > 99 ? '99+' : count; badge.style.display = 'flex'; }
        else { badge.style.display = 'none'; }
      })
      .catch(() => {});
  }

  window.LiveChat = {
    toggle, close, openChannel, backToList, onKeydown, autoResize,
    sendText, onFileSelect, cancelPreview, confirmSend,
    replyTo, cancelReply, deleteMsg, scrollToMsg, showMenu, updateBadge,
  };

  document.addEventListener('DOMContentLoaded', function() {
    const fab = document.getElementById('lc-fab');
    if (fab) fab.addEventListener('click', toggle);
    const overlay = document.getElementById('lc-overlay');
    if (overlay) overlay.addEventListener('click', close);
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.lc-context-menu') && !e.target.closest('.lc-msg-anchor')) {
        closeAllMenus();
      }
    });
  });
})();
