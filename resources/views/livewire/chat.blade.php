<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (Conversation List and User Search) -->
        <div class="col-md-3 sidebar">
            <h5>Chats</h5>

            <!-- User Search -->
            <div class="user-search-bar position-relative">
                <input wire:model.live="query" type="text" class="form-control" placeholder="Search for users...">

                @if (strlen($query) >= 2)
                    <ul class="list-group mt-2 position-absolute w-100" style="max-height: 600px; overflow-y: auto; z-index: 10;">
                        @forelse ($users as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('front/images/default.png') }}"
                                         alt="avatar" class="img-fluid rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <a href="{{ route('userprofile', $user->id) }}" class="text-dark">{{ $user->username }}</a>
                                </div>
                                <button wire:click="selectUser({{ $user->id }})" class="btn btn-primary btn-sm">Message</button>
                            </li>
                        @empty
                            <li class="list-group-item">No users found...</li>
                        @endforelse
                    </ul>
                @endif
            </div>

            <h6>Recent Chats</h6>

            <!-- Conversation List -->
            <ul class="list-group" style="max-height: 450px; overflow-y: scroll;">
                @foreach ($conversations as $conversation)
                    <li class="list-group-item d-flex justify-content-between align-items-center
                        {{ $selectedConversation && (is_object($selectedConversation) ? $selectedConversation->id : $selectedConversation) === $conversation->id ? 'active' : '' }}"
                        wire:click="conversationSelected({{ $conversation->id }})" style="cursor: pointer;">
                        <div>
                            <img src="{{ $conversation->sender_id === Auth::id() ?
                                ($conversation->receiver->image ? asset('storage/' . $conversation->receiver->image) : asset('front/images/default.png')) :
                                ($conversation->sender->image ? asset('storage/' . $conversation->sender->image) : asset('front/images/default.png')) }}"
                                 alt="avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <span>{{ $conversation->sender_id === Auth::id() ? $conversation->receiver->username : $conversation->sender->username }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Chat Area -->
        <div class="col-md-9 p-0">
            <!-- Chat Header -->
            <div class="chat-header d-flex justify-content-between align-items-center">
                @if ($selectedUser)
                    <div class="d-flex align-items-center">
                        <img src="{{ $selectedUser->image ? asset('storage/' . $selectedUser->image) : asset('front/images/default.png') }}"
                             alt="avatar" class="rounded-circle" style="width: 50px;">
                        <span class="ms-2">{{ $selectedUser->username }}</span>
                    </div>
                    <div>
                        <a href="#"><i class="bi bi-house-fill me-3"></i></a>
                        <i class="bi bi-telephone-fill me-3"></i>
                        <i class="bi bi-camera-video-fill me-3"></i>
                        <i class="bi bi-trash-fill"></i>
                    </div>
                @else
                    <div>
                        <a href="#"><i class="bi bi-house-fill me-3"></i></a>
                        <i class="bi bi-telephone-fill me-3"></i>
                        <i class="bi bi-camera-video-fill me-3"></i>
                        <i class="bi bi-trash-fill me-3"></i>
                    </div>
                @endif
            </div>

            <!-- Chat Body -->
            <div class="chat-body" id="chat-body" style="overflow-y: auto;" wire:scroll.debounce.200ms="loadMoreMessages">
                @if (count($messages))
                    @foreach ($messages as $message)
                        <div class="chat-message {{ $message['sender_id'] == Auth::id() ? 'user' : '' }}">
                            @php
                                // Fetch the user associated with the message
                                $messageUser = $message['sender_id'] === Auth::id() ? Auth::user() : $message['receiver'];
                            @endphp
                            <img src="{{ $messageUser && $messageUser['image'] ? asset('storage/' . $messageUser['image']) : asset('front/images/default.png') }}"
                                 alt="avatar" class="rounded-circle me-2" style="width: 40px;">
                            <p>{{ $message['body'] }}</p>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}</small>
                        </div>
                    @endforeach
                @else
                    <p>No messages to show</p>
                @endif
            </div>

            <!-- Chat Footer (Input Form) -->
            <div class="chat-footer d-flex align-items-center">
                <a href="#" class="me-2" onclick="document.getElementById('fileInput').click();">
                    <i class="bi bi-paperclip"></i>
                </a>
                <input type="file" id="fileInput" wire:model.lazy="attachments" class="d-none" multiple>

                <form wire:submit.prevent="sendMessage" class="d-flex w-100" onsubmit="scrollToBottom()">
                    <textarea wire:model.lazy="newMessage" class="form-control" rows="2" placeholder="Type your message"
                              onkeydown="if (event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); Livewire.dispatch('submitMessage'); }"></textarea>

                    <button type="submit" class="btn btn-primary ms-2">
                        <i class="bi bi-send-fill"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript function to scroll to the bottom -->
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('refreshMessages', function () {
            scrollToBottom();
        });

        Echo.private(`conversation.${conversationId}`)
            .listen('MessageSent', (e) => {
                Livewire.dispatch('refreshMessages');
            });

        // Initial scroll when the page loads
        scrollToBottom();
    });

    function scrollToBottom() {
        const chatBody = document.getElementById('chat-body');
        chatBody.scrollTop = chatBody.scrollHeight;  // Scroll to the bottom
    }
</script>
