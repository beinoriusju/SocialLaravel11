<div class="d-inline">
    <i class="icon material-symbols-outlined" style="{{ $unreadCount > 0 ? 'color: red;' : '' }}">
        message
    </i>
    @if ($unreadCount > 0)
        <span class="badge bg-danger text-white">{{ $unreadCount }}</span>
    @endif
</div>
