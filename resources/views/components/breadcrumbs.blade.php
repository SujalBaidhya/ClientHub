@props(['links' => []])

@if(!empty($links))
    <nav aria-label="Breadcrumb" style="display: flex; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 20px; flex-wrap: wrap;">
        @foreach($links as $index => $link)
            @if($index > 0)
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="9 18 15 12 9 6"></polyline></svg>
            @endif

            @if(!empty($link['url']) && !$loop->last)
                <a href="{{ $link['url'] }}" style="color: var(--muted, #64748b); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; transition: color 0.15s ease;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#64748b'">
                    @if(!empty($link['icon']))
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    @endif
                    {{ $link['label'] }}
                </a>
            @else
                <span style="color: #1e1b4b; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    {{ $link['label'] }}
                </span>
            @endif
        @endforeach
    </nav>
@endif
