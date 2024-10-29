<div class="card tab-pane mb-0">

  <div class="chat-head">
      <!-- Back Button for Mobile -->
      <header class="d-flex justify-content-between align-items-center pt-3 ps-3 pe-3 pb-3">
          <div class="d-block">
              <button class="btn btn-sm btn-primary rounded btn-icon" wire:click="goBackToConversationList">
                  <i class="bi bi-arrow-left"></i>
              </button>
          </div>
          <div class="d-flex align-items-center gap-3">
              <!-- <div class="d-block d-xl-none">
                  <button class="btn btn-sm btn-primary rounded btn-icon" data-toggle="sidebar" data-active="true">
                      <span class="btn-inner">
                        <i class="bi bi-house-fill"></i>
                      </span>
                  </button>
              </div> -->
              <div class="avatar chat-user-profile m-0">
                  <img src="{{ $selectedUser->image ? asset('storage/' . $selectedUser->image) : asset('front/images/default.png') }}" alt="avatar" class="avatar-50 rounded-pill" loading="lazy">
              </div>
              <div>
                  <h5 class="mb-0">{{ $selectedUser->username }}</h5>
              </div>
          </div>

          <div class="chat-header-icons d-inline-flex ms-auto">
              <!-- Home Button -->
              <a href="{{ route('blog') }}" class="chat-icon-home bg-primary-subtle d-flex align-items-center justify-content-center">
                  <i class="bi bi-house-fill" style="font-size: 18px;"></i>
              </a>

              <!-- Optional: Uncomment these if needed -->
              <!--
              <a href="#" class="chat-icon-phone bg-primary-subtle d-flex align-items-center justify-content-center">
                  <i class="material-symbols-outlined md-18">phone</i>
              </a>
              <a href="#" class="chat-icon-video bg-primary-subtle d-flex align-items-center justify-content-center">
                  <i class="material-symbols-outlined md-18">videocam</i>
              </a>
              -->
              <a href="#" wire:click="deleteConversation" class="chat-icon-delete bg-primary-subtle d-flex align-items-center justify-content-center">
                  <i class="material-symbols-outlined md-18">delete</i>
              </a>
          </div>
      </header>
  </div>

    <!-- Chat Footer -->
    <div class="card-footer d-flex align-items-center p-0 border-top rounded-0">
        <a href="#" class="me-2" id="uploadTrigger">
            <i class="bi bi-paperclip p-4"></i>
        </a>
        <input type="file" id="fileInput" wire:model="attachments" class="d-none" multiple>

        <form wire:submit.prevent="sendMessage" class="d-flex w-100" id="messageForm">
            <textarea wire:model.lazy="newMessage" wire:keydown.enter.prevent="sendMessage" class="form-control" rows="2" placeholder="Type your message" id="messageInput"></textarea>
            <button type="submit" class="btn btn-primary ms-2" id="sendMessageButton">
                <i class="bi bi-send-fill"></i> Send
            </button>
        </form>
    </div>

    <!-- Chat Body -->
    <!-- Chat Body -->
    <div class="chat-body card-body bg-body" id="chat-body" style="overflow-y: auto;" wire:scroll.debounce.200ms="loadMoreMessages">
        @if (count($messages))
            @foreach ($messages as $message)
                <div class="iq-message-body {{ $message['sender_id'] == Auth::id() ? 'iq-current-user' : 'iq-other-user' }}">
                    @php
                        // Fetch the user associated with the message
                        $messageUser = $message['sender_id'] === Auth::id() ? Auth::user() : $message['receiver'];

                        // Decode file paths (stored as a comma-separated string)
                        $files = $message['file_path'] ? explode(',', $message['file_path']) : [];
                        $images = [];
                        $videos = [];
                        $otherFiles = [];
                        $youtubeLinks = [];

                        // Separate files by type
                        foreach ($files as $file) {
                            if (Str::contains($file, 'youtu')) {
                                $youtubeLinks[] = $file;
                            } else {
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

                    <div class="chat-profile text-center">
                        <small class="iq-chating p-0 mb-0 d-block">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</small>
                    </div>
                    <div class="iq-chat-text">
                        <div class="d-flex align-items-center {{ $message['sender_id'] == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} gap-1 gap-md-2">
                            <div class="iq-chating-content d-flex align-items-center">
                                <p class="mr-2 mb-0">{{ $message['body'] }}</p>
                            </div>
                            @if ($message['sender_id'] == Auth::id())
                                <div class="dropdown cursor-pointer more" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="More">
                                    <div class="lh-1" id="post-option" data-bs-toggle="dropdown">
                                        <span class="material-symbols-outlined text-dark">
                                            more_vert
                                        </span>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="post-option" style="">
                                        <a class="dropdown-item" href="#" wire:click="deleteMessage({{ $message['id'] }})">
                                            <span class="material-symbols-outlined align-middle font-size-20 me-1">delete</span>Delete
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Display Images -->
                        @if (count($images) > 0)
                            <div class="row">
                                @foreach ($images as $image)
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-2">
                                            <a href="{{ asset('storage/' . $image) }}" data-fslightbox="gallery-{{ $message['id'] }}" class="rounded">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded w80" alt="Image" style="max-height: 300px;">
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

                        <!-- Display Download Links for Other File Types -->
                        @if (count($otherFiles) > 0)
                            <div class="row mt-3">
                                @foreach ($otherFiles as $otherFile)
                                    <div class="col-md-12 mb-3">
                                        <a href="{{ asset('storage/' . $otherFile) }}" class="btn btn-link" download>Download File</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>No messages to show</p>
        @endif
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    const sendMessageButton = document.getElementById('sendMessageButton');
    const fileInput = document.getElementById('fileInput');
    const messageInput = document.getElementById('messageInput');
    const messageForm = document.getElementById('messageForm');

    // Echo for real-time messaging
    const conversationId = @json($conversation ? $conversation->id : null);
    if (conversationId) {
        Echo.private(`conversation.${conversationId}`)
            .listen('MessageSent', (event) => {
                console.log('MessageSent event received:', event);
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
            messageForm.dispatchEvent(new Event('submit')); // Submit form, same as clicking the send button
        }
    });

    // After form submission, clear the message input and check button status
    messageForm.addEventListener('submit', () => {
        messageInput.value = '';  // Clear the input
        fileInput.value = '';     // Clear file input after submission
        sendMessageButton.disabled = true; // Disable the button until next input or file selection

        // Immediately focus on the message input field again, for all browsers
        messageInput.focus();
    });

    // Handle file input trigger for uploading files
    document.getElementById('uploadTrigger').addEventListener('click', (event) => {
        event.preventDefault();
        document.getElementById('fileInput').click();
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
