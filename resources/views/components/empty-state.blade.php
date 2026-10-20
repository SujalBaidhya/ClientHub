@props(['title' => 'Nothing here yet', 'message' => null, 'icon' => null])

<div class="empty-state">
    <div class="empty-state-icon">
        @if($icon)
            <x-icon :name="$icon" size="28" />
        @else
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="3" stroke-dasharray="3 3"/>
            </svg>
        @endif
    </div>

    <p class="empty-state-title">{{ $title }}</p>

    @if($message)
        <p class="empty-state-message">{{ $message }}</p>
    @endif
</div>
