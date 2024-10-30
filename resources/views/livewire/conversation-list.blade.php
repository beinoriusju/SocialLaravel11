<div>
      <div class="chat-header pt-4 px-4 d-flex align-items-center justify-content-between">
          <h5 class="fw-500">Chats</h5>
          <a href="{{ route('blog') }}" class="btn btn-link text-decoration-none">
             <i class="bi bi-house-fill" style="font-size: 18px;"></i>
           </a>
      </div>
      <div class="user-search-bar position-relative">
        <input wire:model.live="query" type="text" class="form-control" placeholder="Search for users...">
        @if (strlen($query) >= 2)
            <ul class="list-group mt-2 position-absolute w-100" style="max-height: 600px; overflow-y: auto; z-index: 10;">
                @foreach ($users as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('front/images/default.png') }}"
                                 alt="avatar" class="img-fluid rounded-circle me-2" style="width: 40px; height: 40px;">
                            <a href="{{ route('userprofile', $user->id) }}" class="text-dark">{{ $user->username }}</a>
                        </div>
                        <button wire:click="selectUser({{ $user->id }})" class="btn btn-primary btn-sm">Message</button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <h6>Recent Chats</h6>
    <ul class="list-group" style="max-height: 450px; overflow-y: scroll;">
        @foreach ($conversations as $conversation)
            <li class="list-group-item d-flex justify-content-between align-items-center"
                style="cursor: pointer;" onclick="window.location.href='{{ route('chat.show', ['id' => $conversation->id]) }}'">
                <div>
                    <img src="{{ $conversation->sender_id === Auth::id() ?
                        ($conversation->receiver->image ? asset('storage/' . $conversation->receiver->image) : asset('front/images/default.png')) :
                        ($conversation->sender->image ? asset('storage/' . $conversation->sender->image) : asset('front/images/default.png')) }}"
                        alt="avatar" class="rounded-circle me-2" style="width: 40px;">
                    <span>{{ $conversation->sender_id === Auth::id() ? $conversation->receiver->username : $conversation->sender->username }}</span>
                </div>
                @if ($conversation->hasUnreadMessages)
                    <span class="badge bg-danger text-white">Unread</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
