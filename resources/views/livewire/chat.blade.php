<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (Conversation List and User Search) -->
        <div class="col-md-3 sidebar">
            <h5>Chats</h5>
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
                    <li class="list-group-item d-flex justify-content-between align-items-center
                        {{ $selectedConversation && (is_object($selectedConversation) ? $selectedConversation->id : $selectedConversation) === $conversation->id ? 'active' : '' }}"
                        wire:click="conversationSelected({{ $conversation->id }})" style="cursor: pointer;">
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

        <!-- Chat Area -->
        <div class="col-md-9 p-0">
            <!-- Chat Header -->
            <div class="chat-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('blog') }}" class="btn btn-secondary">Home</a>
                    @if ($selectedUser && $selectedConversation)
                        <img src="{{ $selectedUser->image ? asset('storage/' . $selectedUser->image) : asset('front/images/default.png') }}"
                             alt="avatar" class="rounded-circle" style="width: 50px;">
                        <span class="ms-2">{{ $selectedUser->username }}</span>
                    @endif
                </div>
                <div>
                    @if ($selectedConversation)
                        <button wire:click="deleteConversation({{ $selectedConversation->id }})" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash-fill"></i> Delete Conversation
                        </button>
                    @endif
                </div>
            </div>

            <!-- Chat Body -->
            <div class="chat-body" id="chat-body" style="overflow-y: auto;" wire:scroll.debounce.200ms="loadMoreMessages">
                @if (count($messages))
                    @foreach ($messages as $message)
                        <div class="chat-message {{ $message['sender_id'] == Auth::id() ? 'user' : '' }}">
                            @php
                                // Fetch the user associated with the message
                                $messageUser = $message['sender_id'] === Auth::id() ? Auth::user() : $message['receiver'];

                                // Decode file paths (stored as JSON)
                                $files = json_decode($message['file_path'], true);
                                $images = [];
                                $videos = [];
                                $otherFiles = [];
                                $youtubeLinks = [];

                                // Separate files by type
                                if ($message['file_type'] === 'youtube') {
                                    $youtubeLinks[] = $message['file_path'];
                                } elseif ($files) {
                                    foreach ($files as $file) {
                                        $fileType = mime_content_type(storage_path('app/public/' . $file));
                                        if (Str::contains($fileType, 'image')) {
                                            $images[] = $file;
                                        } elseif (Str::contains($fileType, 'video')) {
                                            $videos[] = $file;
                                        } else {
                                            $otherFiles[] = $file; // Handle all other file types
                                        }
                                    }
                                }
                            @endphp

                            <img src="{{ $messageUser && $messageUser['image'] ? asset('storage/' . $messageUser['image']) : asset('front/images/default.png') }}" alt="avatar" class="rounded-circle me-2" style="width: 40px;">
                            <p>{{ $message['body'] }}</p>

                            <!-- Display Images -->
                            @if (count($images) > 0)
                                <div class="row">
                                    @foreach ($images as $image)
                                        <div class="col-md-4 mb-3">
                                            <div class="border rounded p-2">
                                                <a href="{{ asset('storage/' . $image) }}" data-fslightbox="gallery-{{ $message['id'] }}" class="rounded">
                                                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded w-100" alt="Image" style="max-height: 300px;">
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Display Videos -->
                            @if (count($videos) > 0)
                                <div class="row mt-3">
                                    @foreach ($videos as $video)
                                        <div class="col-md-12 mb-3">
                                            <video controls class="w-100" style="max-height: 450px;">
                                                <source src="{{ asset('storage/' . $video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Display YouTube Links -->
                            @if (count($youtubeLinks) > 0)
                                <div class="row mt-3">
                                    @foreach ($youtubeLinks as $youtubeLink)
                                        @php
                                            parse_str(parse_url($youtubeLink, PHP_URL_QUERY), $params);
                                            $videoId = $params['v'] ?? null;
                                        @endphp
                                        @if ($videoId)
                                            <div class="col-md-12 mb-3">
                                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Display Download Links for other file types -->
                            @if (count($otherFiles) > 0)
                                <div class="row mt-3">
                                    @foreach ($otherFiles as $otherFile)
                                        <div class="col-md-12 mb-3">
                                            <a href="{{ asset('storage/' . $otherFile) }}" class="btn btn-link" download>Download File</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <small class="text-muted">{{ \Carbon\Carbon::parse($message['created_at'])->diffForHumans() }}</small>

                            <!-- Delete message button (only for the sender) -->
                            @if ($message['sender_id'] == Auth::id())
                                <button wire:click="deleteMessage({{ $message['id'] }})" class="btn btn-danger btn-sm mt-2">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p>No messages to show</p>
                @endif
            </div>

            <!-- Chat Footer (Input Form) -->
            <div class="chat-footer d-flex align-items-center">
                <a href="#" class="me-2" id="uploadTrigger">
                    <i class="bi bi-paperclip"></i>
                </a>
                <input type="file" id="fileInput" wire:model.lazy="attachments" class="d-none" multiple>

                <form wire:submit.prevent="sendMessage" class="d-flex w-100" id="messageForm">
                    <textarea wire:model.lazy="newMessage" class="form-control" rows="2" placeholder="Type your message" id="messageInput"></textarea>
                    <button type="submit" class="btn btn-primary ms-2" id="sendMessageButton">
                        <i class="bi bi-send-fill"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        const sendMessageButton = document.getElementById('sendMessageButton');
        const fileInput = document.getElementById('fileInput');
        const messageInput = document.getElementById('messageInput');
        const messageForm = document.getElementById('messageForm');

        // Echo for real-time messaging
        const conversationId = @json($selectedConversation ? $selectedConversation->id : null);
        if (conversationId) {
            Echo.private(`conversation.${conversationId}`)
                .listen('MessageSent', (event) => {
                    Livewire.dispatch('refreshMessages');
                });
        }

        // Disable send button on file selection
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                sendMessageButton.disabled = true;
            }
        });

        // Trim message and enable button only if trimmed message is not empty
        function checkMessage() {
            const trimmedMessage = messageInput.value.trim();
            sendMessageButton.disabled = trimmedMessage === '' && fileInput.files.length === 0;
        }

        // Handle message typing (trim the message)
        messageInput.addEventListener('input', () => {
            checkMessage();
        });

        // Livewire upload start: keep the button disabled
        Livewire.on('livewire-upload-start', () => {
            sendMessageButton.disabled = true;
        });

        // Livewire upload finish: enable the send button again
        Livewire.on('livewire-upload-finish', () => {
            sendMessageButton.disabled = false;
        });

        // Livewire upload error: enable the send button in case of errors
        Livewire.on('livewire-upload-error', () => {
            alert('There was an error during the file upload.');
            sendMessageButton.disabled = false;
        });

        // Form submit on Enter key
        messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
                checkMessage(); // Recheck the message after submission
            }
        });

        // Handle file input trigger for uploading files
        $('#uploadTrigger').click(function(event) {
            event.preventDefault();
            $('#fileInput').click();
        });

        // After form submission, clear the message input and check button status
        messageForm.addEventListener('submit', () => {
            messageInput.value = '';
            fileInput.value = ''; // Clear file input after submission
            sendMessageButton.disabled = true; // Disable the button until next input or file selection
        });

        // Refresh messages and scroll to the top
        Livewire.on('refreshMessages', () => {
            setTimeout(() => {
                scrollToTop();
            }, 100);
        });

        // Auto-scroll to top function
        function scrollToTop() {
            const chatBody = document.getElementById('chat-body');
            if (chatBody) {
                chatBody.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        // Initial scroll to top when page loads
        scrollToTop();
    });
</script>
