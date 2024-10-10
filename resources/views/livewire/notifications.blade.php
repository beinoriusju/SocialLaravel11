<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="card-title mb-3">Notifications</h4>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <button wire:click="markAllAsRead" class="btn btn-primary mb-3">Mark All as Read</button>
                    <ul class="notification-list m-0 p-0">
                        @foreach ($notifications as $notification)
                            <li class="d-flex align-items-center justify-content-between">
                                <div class="user-img img-fluid">
                                    <img src="{{ $notification->sender->image ? asset('storage/' . $notification->sender->image) : asset('front/images/default.png') }}" alt="user-img" class="rounded-circle avatar-40">
                                </div>
                                <div class="w-100">
                                    <div class="d-flex justify-content-between">
                                        <div class="ms-3">
                                            <h6>{{ $notification->message }}</h6>
                                            <p class="mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-icon btn-success-subtle btn-sm me-3">
                                                <span class="btn-inner">
                                                    <i class="material-symbols-outlined md-18">check</i>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
