<div class="container">
    <div class="row">
        <!-- Blog Post Details Section -->
        <div class="col-sm-12">
            <div class="card card-block card-stretch card-height blog blog-detail">
                <div class="card-body">
                    <!-- Blog Title and Meta Information -->
                    <div class="blog-description mt-3">
                        <h5 class="mb-3 pb-3 border-bottom">{{ $post->title }}</h5>
                        <div class="blog-meta d-flex align-items-center mb-3 position-right-side flex-wrap">
                            <div class="date me-4 d-flex align-items-center">
                                <i class="material-symbols-outlined pe-2 md-18 text-primary">calendar_month</i>{{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <p>{{ __('translations.Description') }}:{{ $post->description }}</p>
                        <div>{{ __('translations.Details') }}: {!! $post->details !!}</div>

                        <!-- Media Display (Images, Videos, YouTube Links) -->
                        @if ($post->media->isNotEmpty())
                            @php
                                $images = [];
                                $videos = [];
                                $youtubeLinks = [];

                                // Separate media by type (images, videos, YouTube links)
                                foreach ($post->media as $media) {
                                    if ($media->file_type == 'image') {
                                        $images[] = $media->file;
                                    } elseif ($media->file_type == 'video') {
                                        $videos[] = $media->file;
                                    } elseif ($media->file_type == 'youtube') {
                                        $youtubeLinks[] = $media->file;
                                    }
                                }
                            @endphp

                            <!-- Display Images -->
                            @if (count($images) > 0)
                                @php
                                    $displayLimit = 5;
                                    $showMore = count($images) > $displayLimit;
                                @endphp
                                <div class="row">
                                    @foreach (array_slice($images, 0, $displayLimit) as $image)
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ asset('storage/' . $image) }}" data-fslightbox="gallery-{{ $post->id }}" class="rounded">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded w-100" alt="Image" loading="lazy" style="max-height: 300px;">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Show More Images -->
                                @if ($showMore)
                                    <p>+{{ count($images) - $displayLimit }} </p>
                                @endif

                                <!-- Lightbox for remaining images -->
                                @foreach (array_slice($images, $displayLimit) as $image)
                                    <a href="{{ asset('storage/' . $image) }}" data-fslightbox="gallery-{{ $post->id }}" class="d-none"></a>
                                @endforeach
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
                                            $videoId = \Illuminate\Support\Str::after($youtubeLink, 'v=');
                                            if (str_contains($videoId, '&')) {
                                                $videoId = strtok($videoId, '&');
                                            }
                                        @endphp
                                        <div class="col-md-12 mb-3">
                                            <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
