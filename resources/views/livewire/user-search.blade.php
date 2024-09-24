<div class="iq-search-bar device-search position-relative">
    <form class="searchbox">
        <input wire:model="query" type="text" class="form-control bg-light-subtle" placeholder="{{ __('translations.Search') }}...">
    </form>

    @if (strlen($query) >= 2)
        <div class="dropdown-menu dropdown-menu-end show" style="max-height: 600px; overflow-y: auto;">
            <ul class="list-group list-group-flush">
                @forelse ($users as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('front/images/default.png') }}" class="img-fluid rounded-circle me-2" style="width: 40px; height: 40px;">
                            <a href="{{ route('userprofile', $user->id) }}" class="text-dark">{{ $user->username }}</a>
                        </div>

                        <div>
                            @if ($friendRequests->where('user_id', auth()->id())->where('friend_id', $user->id)->where('status', 'pending')->count() > 0)
                                <!-- Friend request sent by the logged-in user (Cancel option) -->
                                <button wire:click="removeFriend({{ $user->id }})" class="btn btn-warning btn-sm">{{ __('translations.Cancel') }}</button>
                            @elseif ($friendRequests->where('friend_id', auth()->id())->where('user_id', $user->id)->where('status', 'pending')->count() > 0)
                                <!-- Friend request received by the logged-in user (Accept/Reject options) -->
                                <button wire:click="acceptFriendRequest({{ $user->id }})" class="btn btn-primary btn-sm">{{ __('translations.Accept') }}</button>
                                <button wire:click="removeFriend({{ $user->id }})" class="btn btn-danger btn-sm">{{ __('translations.Reject') }}</button>
                            @elseif ($friendRequests->where('user_id', auth()->id())->where('friend_id', $user->id)->where('status', 'accepted')->count() > 0)
                                <!-- Users are already friends -->
                                <button class="btn btn-info btn-sm">{{ __('translations.Friend') }}</button>
                            @else
                                <!-- No friend request sent or received -->
                                <button wire:click="sendFriendRequest({{ $user->id }})" class="btn btn-success btn-sm">{{ __('translations.Add Friend') }}</button>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item">{{ __('translations.No users found') }}...</li>
                @endforelse
            </ul>
        </div>
    @endif
</div>
