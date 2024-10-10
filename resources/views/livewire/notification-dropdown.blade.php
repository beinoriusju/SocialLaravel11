<div class="nav-item dropdown">
    <a href="{{ route('notifications') }}" class="nav-link d-flex align-items-center">
          <span class="material-symbols-outlined position-relative">notifications
        @if ($unreadCount > 0)
            <span class="bg-primary text-white notification-badge">{{ $unreadCount }}</span>
        @endif
          </span>
    </a>
</div>
