<li class="nav-item dropdown">
    <a href="javascript:void(0);" class="search-toggle dropdown-toggle d-flex align-items-center" id="notification-drop" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="material-symbols-outlined position-relative">notifications
            @if ($unreadCount > 0)
                <span class="bg-primary text-white notification-badge">{{ $unreadCount }}</span>
            @endif
        </span>
    </a>

    <!-- Notification Dropdown -->
    <div class="sub-drop dropdown-menu header-notification" aria-labelledby="notification-drop" data-bs-popper="static">
        <div class="card m-0 shadow">
            <div class="card-header d-flex justify-content-between px-0 pb-4 mx-5 border-bottom">
                <div class="header-title">
                    <h5 class="fw-semibold">{{ __('translations.Notifications') }}</h5>
                </div>
            </div>

            <div class="card-body">
                <div class="item-header-scroll">
                    @foreach ($notifications as $notification)
                        <a href="{{ $notification->url ?? 'javascript:void(0);' }}"
                           wire:click.prevent="markAsRead({{ $notification->id }})">
                            <div class="d-flex gap-3 mb-4">
                                <div>
                                    <h6 class="font-size-14">
                                        @if ($notification->sender)
                                            {{ $notification->sender->username }}
                                        @else
                                            {{ __('Unknown User') }}
                                        @endif
                                        <span class="text-body fw-normal">{{ $notification->message }}</span>
                                        @if (!empty($notification->related_item))
                                            <span class="text-primary fw-semibold">{{ $notification->related_item }}</span>
                                        @endif
                                    </h6>
                                    <small class="text-body fw-500">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button type="button" class="btn btn-primary fw-500 w-100" wire:click="markAllAsRead">
                    {{ __('translations.Clear All Notifications') }}
                </button>
            </div>
        </div>
    </div>
</li>
