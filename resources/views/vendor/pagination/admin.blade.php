@if ($paginator->hasPages())
    <div class="flex items-center justify-between gap-4 flex-wrap mt-4">
        <div style="color:var(--text-dim);font-size:0.82rem">
            @if ($paginator->firstItem())
                Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
            @else
                {{ $paginator->count() }} hasil
            @endif
        </div>
        <div class="flex gap-1.5">
            @if ($paginator->onFirstPage())
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;border:1px solid var(--glass-border);color:var(--text-dim);opacity:.4;cursor:default">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;color:var(--text-muted);text-decoration:none;border:1px solid var(--glass-border);transition:all 0.15s ease" onmouseover="this.style.background='var(--sidebar-hover)';this.style.color='var(--text)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;border:none;color:var(--text-dim)">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;background:linear-gradient(135deg,var(--accent),#6366f1);color:#fff;border-color:transparent">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;color:var(--text-muted);text-decoration:none;border:1px solid var(--glass-border);transition:all 0.15s ease" onmouseover="this.style.background='var(--sidebar-hover)';this.style.color='var(--text)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;color:var(--text-muted);text-decoration:none;border:1px solid var(--glass-border);transition:all 0.15s ease" onmouseover="this.style.background='var(--sidebar-hover)';this.style.color='var(--text)'" onmouseout="this.style.background='';this.style.color='var(--text-muted)'">&rsaquo;</a>
            @else
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 0.5rem;border-radius:10px;font-size:0.82rem;font-weight:500;border:1px solid var(--glass-border);color:var(--text-dim);opacity:.4;cursor:default">&rsaquo;</span>
            @endif
        </div>
    </div>
@endif
