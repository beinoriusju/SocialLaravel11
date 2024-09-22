<div class="content-inner" id="page_layout">
    <div class="container">
        <div class="row gx-4">
            <div class="col-lg-2"></div>
            <div class="col-lg-8" id="dynamicDivContainer">
                <div id="content">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-5">
                                <div id="stories" class="storiesWrapper d-flex gap-3"></div>
                            </div>
                        </div>
                    </div>

                    @livewire("components.create-post")

                    <!-- Posts -->
                    @foreach($posts as $post)
                    <div class="row social-post-container">
                        <div class="col-sm-12 social-post">
                            <div class="card card-block card-stretch card-height">
                                <div class="card-body">
                                    <!-- Edit delete options -->
                                    <div class="user-post-data">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="me-3 flex-shrink-0">
                                                <a href="{{ route('userprofile', ['user' => $post->user->id]) }}">
                                                    <img src="{{ $post->user->image && file_exists(public_path('storage/' . $post->user->image))
                                                        ? asset('storage/' . $post->user->image)
                                                        : asset('front/images/default.png') }}"
                                                        alt="{{ __('translations.User Image') }}"
                                                        loading="lazy"
                                                        style="height: 50px; width: 40px;">
                                                </a>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="mb-0 d-inline-block">{{ $post->user->username }} {{ $post->user->last_username }}</h6>
                                                        <p class="mb-0">{{ $post->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <div class="card-post-toolbar">
                                                        <div class="dropdown">
                                                            <span class="dropdown-toggle material-symbols-outlined" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false" role="button">
                                                                more_horiz
                                                            </span>
                                                            <div class="dropdown-menu m-0 p-0">
                                                                @if (auth()->id() === $post->user_id)
                                                                <a class="dropdown-item p-3" href="#" wire:click.prevent="startEditing({{ $post->id }})">
                                                                    <div class="d-flex align-items-top">
                                                                        <span class="material-symbols-outlined">edit</span>
                                                                        <div class="data ms-2">
                                                                            <h6>{{ __('translations.Edit') }}</h6>
                                                                        </div>
                                                                    </div>
                                                                </a>

                                                                <a class="dropdown-item p-3" href="#" wire:click.prevent="deletePost({{ $post->id }})">
                                                                    <div class="d-flex align-items-top">
                                                                        <span class="material-symbols-outlined">cancel</span>
                                                                        <div class="data ms-2">
                                                                            <h6>{{ __('translations.Delete') }}</h6>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        @if ($editingPostId === $post->id)
                                        <!-- Render edit post component -->
                                        @livewire('edit-post', ['postId' => $post->id])
                                        @else
                                        <p class="m-0">{{ $post->content }}</p>
                                        @endif
                                    </div>

                                    <!-- Media Display -->
                                    @php
                                    $post_media = App\Models\PostMedia::where('post_id', $post->id)->get();
                                    $images = [];
                                    $videos = [];
                                    $youtubeLinks = [];

                                    foreach ($post_media as $media) {
                                        if ($media->file_type == 'image') {
                                            $imageFiles = json_decode($media->file, true);
                                            if (is_array($imageFiles)) {
                                                foreach ($imageFiles as $file) {
                                                    if (str_contains($file, "posts/{$post->user_id}/images")) {
                                                        $images[] = $file;
                                                    }
                                                }
                                            }
                                        } elseif ($media->file_type == 'video') {
                                            $videoFiles = json_decode($media->file, true);
                                            if (is_array($videoFiles)) {
                                                foreach ($videoFiles as $file) {
                                                    if (str_contains($file, "posts/{$post->user_id}/videos")) {
                                                        $videos[] = $file;
                                                    }
                                                }
                                            } else {
                                                if (str_contains($media->file, "posts/{$post->user_id}/videos")) {
                                                    $videos[] = $media->file;
                                                }
                                            }
                                        } elseif ($media->file_type == 'youtube') {
                                            $youtubeLinks[] = $media->file;
                                        }
                                    }

                                    // Count total media
                                    $totalMedia = count($images) + count($videos) + count($youtubeLinks);
                                    @endphp

                                    <div class="user-post mt-4">
                                        @if ($post_media->isNotEmpty())
                                            <!-- Display Images -->
                                            @if (count($images) > 0)
                                                @php
                                                $displayLimit = 5;
                                                $showMoreImages = count($images) > $displayLimit;
                                                @endphp

                                                <div class="row">
                                                    @foreach(array_slice($images, 0, $displayLimit) as $image)
                                                    <div class="col-md-4 mb-3">
                                                        <a href="{{ asset('storage/' . $image) }}" target="_blank" class="rounded">
                                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ __('translations.Post Image') }}" class="img-fluid rounded w-100" loading="lazy">
                                                        </a>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Display First Video if exists -->
                                            @if (count($videos) > 0)
                                                <div class="col-md-12 mb-3" id="video-container">
                                                    <video controls class="w-100">
                                                        <source src="{{ asset('storage/' . $videos[0]) }}" type="video/mp4">
                                                        {{ __('translations.Your browser does not support the video tag.') }}
                                                    </video>
                                                </div>
                                                @php array_shift($videos); @endphp
                                            @endif

                                            <!-- Display First YouTube Video if exists -->
                                            @if (count($youtubeLinks) > 0 && count($videos) == 0)
                                                <div class="col-md-12 mb-3" id="youtube-container">
                                                    @php
                                                    $videoId = \Illuminate\Support\Str::after($youtubeLinks[0], 'v=');
                                                    @endphp
                                                    <div class="ratio ratio-16x9">
                                                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="{{ __('translations.YouTube video player') }}" controls allowfullscreen></iframe>
                                                    </div>
                                                </div>
                                                @php array_shift($youtubeLinks); @endphp
                                            @endif

                                            <!-- Show More Media Button -->
                                            @if ($totalMedia > 5 || count($videos) > 0 || count($youtubeLinks) > 0)
                                                <p class="cursor-pointer text-primary" data-bs-toggle="modal" data-bs-target="#mediaModal-{{ $post->id }}">
                                                    +{{ count($images) + count($videos) + count($youtubeLinks) }} {{ __('translations.more media') }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Modal for showing all media -->
                                    <div class="modal fade" id="mediaModal-{{ $post->id }}" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="mediaModalLabel">{{ __('translations.All Media') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Display All Images -->
                                                    @if (count($images) > 0)
                                                    <div class="row">
                                                        @foreach($images as $image)
                                                        <div class="col-md-4 mb-3">
                                                            <a href="{{ asset('storage/' . $image) }}" target="_blank" class="rounded">
                                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ __('translations.Post Image') }}" class="img-fluid rounded w-100" loading="lazy">
                                                            </a>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    @endif

                                                    <!-- Display All Videos -->
                                                    @if (count($videos) > 0)
                                                    <div class="row">
                                                        @foreach($videos as $video)
                                                        <div class="col-md-12 mb-3">
                                                            <video controls class="w-100">
                                                                <source src="{{ asset('storage/' . $video) }}" type="video/mp4">
                                                                {{ __('translations.Your browser does not support the video tag.') }}
                                                            </video>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    @endif

                                                    <!-- Display All YouTube Links -->
                                                    @if (count($youtubeLinks) > 0)
                                                    <div class="row">
                                                        @foreach($youtubeLinks as $youtubeLink)
                                                        @php
                                                        $videoId = \Illuminate\Support\Str::after($youtubeLink, 'v=');
                                                        @endphp
                                                        <div class="col-md-12 mb-3">
                                                            <div class="ratio ratio-16x9">
                                                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="{{ __('translations.YouTube video player') }}" controls allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Post Meta (Likes and Comments) -->
                                    <div class="post-meta-likes mt-4">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <span class="text-capitalize font-size-14 fw-medium" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#likemodal{{ $post->id }}">
                                                    {{ $post->likes }} {{ __('translations.Likes') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="comment-area mt-4 pt-4 border-top">
                                        @livewire('like-post', ['post' => $post], key('like-post-' . $post->id))

                                        <div class="comment-list" id="commentcollapse{{ $post->id }}">
                                            <ul class="list-inline m-0 p-0 comment-list" style="max-height: 200px; overflow-y: auto;">
                                                @foreach ($post->comments()->latest()->take(3)->get() as $comment)
                                                <li class="mb-3" wire:key="comment-{{ $comment->id }}">
                                                    <div class="comment-list-block">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="comment-list-user-img flex-shrink-0">
                                                                <a href="{{ route('userprofile', ['user' => $comment->user->id]) }}">
                                                                    <img src="{{ $comment->user->image ? asset('storage/' . $comment->user->image) : asset('front/images/default.png') }}"
                                                                         alt="{{ __('translations.User Image') }}" class="avatar-48 rounded-circle img-fluid" loading="lazy">
                                                                </a>
                                                            </div>
                                                            <div class="comment-list-user-data">
                                                                <div class="d-inline-flex align-items-center gap-1 flex-wrap">
                                                                    <h6 class="m-0">{{ $comment->user->username }}</h6>
                                                                    <span class="fw-medium small text-capitalize">{{ $comment->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            </div>

                                                            @if ($comment->user_id == auth()->id() || $post->user_id == auth()->id())
                                                            <div class="ms-auto d-inline-flex align-items-center">
                                                                <span class="material-symbols-outlined" style="cursor: pointer;" id="commentOptions{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    more_horiz
                                                                </span>
                                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="commentOptions{{ $comment->id }}">
                                                                    <li>
                                                                        <button class="dropdown-item" wire:click="editComment({{ $comment->id }})">{{ __('translations.Edit') }}</button>
                                                                    </li>
                                                                    <li>
                                                                        <button class="dropdown-item" wire:click="deleteComment({{ $comment->id }})">{{ __('translations.Delete') }}</button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        @if ($editingCommentId === $comment->id)
                                                        <div class="mt-2">
                                                            <input type="text" wire:model.defer="editedComment" class="form-control" placeholder="{{ __('translations.Edit your comment...') }}">
                                                            <div class="mt-2">
                                                                <button class="btn btn-sm btn-primary" wire:click="updateComment({{ $comment->id }})">{{ __('translations.Update') }}</button>
                                                                <button class="btn btn-sm btn-secondary" wire:click="cancelEdit">{{ __('translations.Cancel') }}</button>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="comment-list-user-comment mt-2">
                                                            {{ $comment->comment }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>

                                            <!-- Add comment form -->
                                            <div class="add-comment-form-block">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="flex-shrink-0">
                                                        <a href="{{ route('userprofile', ['user' => auth()->id()]) }}">
                                                            <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : asset('front/images/default.png') }}"
                                                                 alt="{{ __('translations.User Image') }}" class="avatar-48 rounded-circle img-fluid" loading="lazy">
                                                        </a>
                                                    </div>
                                                    <div class="add-comment-form">
                                                        <form wire:submit.prevent="saveComment({{ $post->id }})">
                                                            <input type="text" wire:model.defer="comments.{{ $post->id }}" class="form-control" placeholder="{{ __('translations.Write a comment...') }}" required>
                                                            <button type="submit" class="btn btn-primary font-size-12 text-capitalize px-5 mt-2">{{ __('translations.Post') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal for Likes -->
                                    <div class="modal fade likemodal" id="likemodal{{ $post->id }}" tabindex="-1" aria-labelledby="likemodalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <ul class="nav nav-tabs liked-tabs" id="liked-tabs" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <span class="nav-link active" id="reaction-tab-1" data-bs-toggle="tab" data-bs-target="#reaction-tab-all{{ $post->id }}"
                                                                type="button" role="tab" aria-controls="reaction-tab-all" aria-selected="true">
                                                                <span class="align-middle">{{ __('translations.All') }}</span>
                                                            </span>
                                                        </li>
                                                        @foreach (['like', 'love', 'happy', 'haha', 'think', 'sad', 'lovely'] as $reactionType)
                                                            @if (!empty($reactionCounts[$post->id][$reactionType]))
                                                            <li class="nav-item" role="presentation">
                                                                <span class="nav-link" id="reaction-tab-{{ $reactionType }}-{{ $post->id }}"
                                                                    data-bs-toggle="tab" data-bs-target="#reaction-tab-{{ $reactionType }}-list-{{ $post->id }}"
                                                                    type="button" role="tab">
                                                                    <img src="{{ asset('front/images/' . $reactionType . '.png') }}" class="img-fluid reaction-img"
                                                                        alt="{{ __('translations.' . ucfirst($reactionType)) }}" loading="lazy">
                                                                    <span class="align-middle">{{ $reactionCounts[$post->id][$reactionType] }}</span>
                                                                </span>
                                                            </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('translations.Close') }}"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="tab-content liked-tabs-content" id="liked-tabs-content-{{ $post->id }}">
                                                        <!-- All Reactions Tab -->
                                                        <div class="tab-pane fade show active" id="reaction-tab-all{{ $post->id }}" role="tabpanel">
                                                            <ul class="list-inline m-0 p-0">
                                                                @foreach ($likes[$post->id] ?? [] as $reactionType => $users)
                                                                @foreach ($users as $like)
                                                                <li class="mb-3">
                                                                    <div class="reaction-user-container d-flex align-items-center justify-content-between gap-3">
                                                                        <div class="d-flex align-items-center gap-3">
                                                                            <div class="reaction-user-image">
                                                                                @if(!empty($like['user_id']))
                                                                                    <a href="{{ route('userprofile', ['user' => $like['user_id']]) }}">
                                                                                        <img class="border border-2 rounded-circle avatar-50"
                                                                                             src="{{ asset('storage/' . ($like['user_image'] ?? 'front/images/user_default.jpg')) }}"
                                                                                             alt="{{ $like['user'] ?? 'Unknown User' }}" loading="lazy">
                                                                                    </a>
                                                                                @else
                                                                                    <img class="border border-2 rounded-circle avatar-50"
                                                                                         src="{{ asset('storage/' . ($like['user_image'] ?? 'front/images/user_default.jpg')) }}"
                                                                                         alt="{{ $like['user'] ?? 'Unknown User' }}" loading="lazy">
                                                                                @endif
                                                                            </div>

                                                                            <div class="reaction-user-meta">
                                                                                <h6 class="mb-0">{{ $like['user'] }}</h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="reaction">
                                                                            <img src="{{ asset('front/images/' . $reactionType . '.png') }}" class="img-fluid reaction-img"
                                                                                alt="{{ __('translations.' . ucfirst($reactionType)) }}" loading="lazy">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @endforeach
                                                                @endforeach
                                                            </ul>
                                                        </div>

                                                        <!-- Specific Reaction Tabs -->
                                                        @foreach (['like', 'love', 'happy', 'haha', 'think', 'sad', 'lovely'] as $reactionType)
                                                        <div class="tab-pane fade" id="reaction-tab-{{ $reactionType }}-list-{{ $post->id }}" role="tabpanel">
                                                            <ul class="list-inline m-0 p-0">
                                                                @foreach ($likes[$post->id][$reactionType] ?? [] as $like)
                                                                <li class="mb-3">
                                                                    <div class="reaction-user-container d-flex align-items-center justify-content-between gap-3">
                                                                        <div class="d-flex align-items-center gap-3">
                                                                            <div class="reaction-user-image">
                                                                                @if(isset($like['user_id']) && !empty($like['user_id']))
                                                                                    <a href="{{ route('userprofile', ['user' => $like['user_id']]) }}">
                                                                                        <img class="border border-2 rounded-circle avatar-50"
                                                                                             src="{{ isset($like['user_image']) && $like['user_image'] ? asset('storage/' . $like['user_image']) : asset('front/images/default.png') }}"
                                                                                             alt="user" loading="lazy">
                                                                                    </a>
                                                                                @else
                                                                                    <img class="border border-2 rounded-circle avatar-50"
                                                                                         src="{{ isset($like['user_image']) && $like['user_image'] ? asset('storage/' . $like['user_image']) : asset('front/images/default.png') }}"
                                                                                         alt="user" loading="lazy">
                                                                                @endif
                                                                            </div>

                                                                            <div class="reaction-user-meta">
                                                                                <h6 class="mb-0">{{ $like['user'] }}</h6>
                                                                            </div>
                                                                        </div>
                                                                        <div class="reaction">
                                                                            <img src="{{ asset('front/images/' . $reactionType . '.png') }}" class="img-fluid reaction-img"
                                                                                alt="{{ __('translations.' . ucfirst($reactionType)) }}" loading="lazy">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Infinite scroll logic
                        window.addEventListener('scroll', function () {
                            // Check if the user has scrolled to the bottom of the page
                            if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
                                // Trigger Livewire to load more posts when reaching the bottom
                                @this.call('loadMorePostsNewsfeed');
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
