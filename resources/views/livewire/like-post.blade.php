<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div class="like-block position-relative d-flex align-items-center flex-shrink-0">
        <div class="like-data">
            <div class="dropdown">
                <span class="dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                    <!-- Show the current reaction image or default to 'thumb_up' image -->
                    <img src="{{ asset($currentReactionImage) }}" class="img-fluid reaction-image" alt="{{ $currentReaction }}" loading="lazy" style="height: 20px; width: 20px;">
                </span>
                <div class="dropdown-menu py-2 shadow">
                    <!-- Reaction options with images -->
                    <a class="ms-2 me-2" href="#" wire:click.prevent="react('like')" data-bs-toggle="tooltip" data-bs-placement="top" title="Like">
                        <img src="{{ asset('front/images/like.png') }}" class="img-fluid" alt="like" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('love')" data-bs-toggle="tooltip" data-bs-placement="top" title="Love">
                        <img src="{{ asset('front/images/love.png') }}" class="img-fluid" alt="love" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('happy')" data-bs-toggle="tooltip" data-bs-placement="top" title="Happy">
                        <img src="{{ asset('front/images/happy.png') }}" class="img-fluid" alt="happy" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('haha')" data-bs-toggle="tooltip" data-bs-placement="top" title="HaHa">
                        <img src="{{ asset('front/images/haha.png') }}" class="img-fluid" alt="haha" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('think')" data-bs-toggle="tooltip" data-bs-placement="top" title="Think">
                        <img src="{{ asset('front/images/think.png') }}" class="img-fluid" alt="think" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('sad')" data-bs-toggle="tooltip" data-bs-placement="top" title="Sad">
                        <img src="{{ asset('front/images/sad.png') }}" class="img-fluid" alt="sad" loading="lazy">
                    </a>
                    <a class="me-2" href="#" wire:click.prevent="react('lovely')" data-bs-toggle="tooltip" data-bs-placement="top" title="Lovely">
                        <img src="{{ asset('front/images/lovely.png') }}" class="img-fluid" alt="lovely" loading="lazy">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="comment-block">
        <span class="fw-medium small" data-bs-toggle="collapse" data-bs-target="#commentcollapse{{ $post->id }}" role="button" aria-expanded="false" aria-controls="commentcollapse{{ $post->id }}">
            {{ $post->comments}} {{ __('translations.comments') }}
        </span>
    </div>
</div>
