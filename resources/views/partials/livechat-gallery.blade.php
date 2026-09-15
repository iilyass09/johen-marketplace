@php
    $lcAtts = $msg->attachments->isNotEmpty() ? $msg->attachments : collect();
    if ($lcAtts->isEmpty() && $msg->media_path) {
        $lcAtts = collect([$msg]);
    }
@endphp

@if($msg->message_type === 'video' && $lcAtts->isNotEmpty())
    @php($lcFirst = $lcAtts->first())
    <div class="chat-media chat-video-wrap">
        <button type="button" class="chat-video-play" onclick="playChatVideo(this)" aria-label="Putar video"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></button>
        <video src="/media/{{ $lcFirst->media_path }}" controls preload="none" @if($lcFirst->poster_path) poster="/media/{{ $lcFirst->poster_path }}" @endif></video>
    </div>
@elseif($lcAtts->isNotEmpty())
    @php
        $lcUrls = $lcAtts->map(fn($a) => '/media/' . $a->media_path)->values()->all();
        $lcUrlsJson = json_encode($lcUrls, JSON_UNESCAPED_SLASHES);
        $lcCount = count($lcUrls);
        $lcGrid = $lcCount === 1 ? 'chat-grid-1' : ($lcCount === 2 ? 'chat-grid-2' : ($lcCount === 3 ? 'chat-grid-3' : 'chat-grid-4'));
        $lcVisible = array_slice($lcUrls, 0, 4);
        $lcHidden = $lcCount - 4;
    @endphp
    <div class="chat-media chat-msg-gallery {{ $lcGrid }}">
        @foreach($lcVisible as $lcIdx => $lcUrl)
            <button type="button" class="chat-gallery-item" data-urls="{{ e($lcUrlsJson) }}" onclick="openAdminGallery(this, {{ $msg->id }}, {{ $lcIdx }})" aria-label="Buka gambar ukuran penuh">
                <img src="{{ $lcUrl }}" alt="Gambar" loading="lazy">
                @if($lcIdx === 3 && $lcHidden > 0)
                    <span class="chat-gallery-more">+{{ $lcHidden }}</span>
                @endif
            </button>
        @endforeach
    </div>
@endif