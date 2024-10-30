<div class="content-inner" id="page_layout">
    <div class="container">
        <div class="row">
            <!-- Filters Section -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <!-- Category Filter -->
                                <div class="col-md-6 mb-3">
                                    <label for="categoryFilter">{{ __('translations.Select Category') }}</label>
                                    <select id="categoryFilter" class="form-select" wire:model.lazy="selectedCategory">
                                        <option value="">{{ __('translations.All Categories') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ __('translations.' . $category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subcategory Filter -->
                                <div class="col-md-6 mb-3">
                                    <label for="subcategoryFilter">{{ __('translations.Select Subcategory') }}</label>
                                    <select id="subcategoryFilter" class="form-select" wire:model.lazy="selectedSubcategory" {{ empty($subcategories) ? 'disabled' : '' }}>
                                        <option value="">{{ __('translations.All Subcategories') }}</option>
                                        @foreach ($subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}">{{ __('translations.' . $subcategory->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Blog Post Listing -->
            @foreach ($blogPosts as $index => $post)
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height blog-list {{ $index % 2 == 0 ? '' : 'list-even' }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            @php
                                $media = collect($post['media'])->where('file_type', 'image')->first()
                                    ?? collect($post['media'])->where('file_type', 'video')->first()
                                    ?? collect($post['media'])->where('file_type', 'youtube')->first();
                                $additionalMediaCount = count($post['media']) - 1; // Count excluding the first media
                            @endphp

                            @if ($index % 2 == 0) <!-- Left media, right content -->
                                @if ($media)
                                    <div class="col-md-6">
                                        <div class="image-block">
                                            @if ($media['file_type'] == 'image')
                                                <img src="{{ asset('storage/' . $media['file']) }}" class="img-fluid rounded w-100" alt="blog-img" style="max-height: 400px; object-fit: cover;">
                                            @elseif ($media['file_type'] == 'video')
                                                <video controls class="w-100">
                                                    <source src="{{ asset('storage/' . $media['file']) }}" type="video/mp4">
                                                </video>
                                            @elseif ($media['file_type'] == 'youtube')
                                                @php
                                                    $youtubeUrl = $media['file'];
                                                    $videoId = \Illuminate\Support\Str::after($youtubeUrl, 'v=');
                                                    if (str_contains($videoId, '&')) {
                                                        $videoId = strtok($videoId, '&');
                                                    }
                                                @endphp
                                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="blog-description p-2 rounded">
                                        <div class="blog-meta d-flex align-items-center justify-content-between mb-2">
                                            <div class="date">{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</div>
                                        </div>
                                        <h5 class="mb-2">{{ $post['title'] }}</h5>
                                        <p>{{ $post['description'] }}</p>
                                        @if ($additionalMediaCount > 0)
                                            <div class="text-muted mb-2">
                                                {{ __('+ :count', ['count' => $additionalMediaCount]) }}
                                            </div>
                                        @endif
                                        <a href="{{ route('blog.post', $post['id']) }}" class="d-flex align-items-center">
                                            {{ __('translations.Read more') }} <i class="material-symbols-outlined fs-6 icon-rtl">arrow_forward_ios</i>
                                        </a>
                                    </div>
                                </div>
                            @else <!-- Right media, left content -->
                                <div class="col-md-6">
                                    <div class="blog-description p-2 rounded">
                                        <div class="blog-meta mb-2">
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</small>
                                        </div>
                                        <h5 class="mb-2">{{ $post['title'] }}</h5>
                                        <p>{{ $post['description'] }}</p>
                                        @if ($additionalMediaCount > 0)
                                            <div class="text-muted mb-2">
                                                {{ __('+ :count more media', ['count' => $additionalMediaCount]) }}
                                            </div>
                                        @endif
                                        <div class="blog-meta mb-2">
                                            <a href="{{ route('blog.post', $post['id']) }}" class="d-flex align-items-center">
                                                {{ __('translations.Read more') }} <i class="material-symbols-outlined fs-6 icon-rtl">arrow_forward_ios</i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @if ($media)
                                    <div class="col-md-6">
                                        <div class="image-block">
                                            @if ($media['file_type'] == 'image')
                                                <img src="{{ asset('storage/' . $media['file']) }}" class="img-fluid rounded w-100" alt="blog-img" style="max-height: 400px; object-fit: cover;">
                                            @elseif ($media['file_type'] == 'video')
                                                <video controls class="w-100">
                                                    <source src="{{ asset('storage/' . $media['file']) }}" type="video/mp4">
                                                </video>
                                                @elseif ($media['file_type'] == 'youtube')
                                                    @php
                                                        $youtubeUrl = $media['file'];
                                                        $videoId = \Illuminate\Support\Str::after($youtubeUrl, 'v=');
                                                        if (str_contains($videoId, '&')) {
                                                            $videoId = strtok($videoId, '&');
                                                        }
                                                    @endphp
                                                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                                @endif
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <!-- Edit/Delete buttons -->
                            <div class="col-md-12 mt-3">
                              @if(auth()->check() && auth()->user()->role === 'admin')
                                    <button wire:click="editPost({{ $post['id'] }})" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">{{ __('translations.Edit') }}</button>
                                    <button wire:click="deletePost({{ $post['id'] }})" class="btn btn-danger btn-sm">{{ __('translations.Delete') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Infinite Scroll Trigger -->
            <div wire:loading class="col-lg-12 text-center mb-4">
                <p>{{ __('translations.Loading more') }}</p>
            </div>

            @if (!$hasMorePages)
                <div class="col-lg-12 text-center mb-4">
                    <p>{{ __('translations.No more too load') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Blog Post Modal -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateBlogPost">
                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="blogCategory">{{ __('translations.Category') }}</label>
                            <select id="blogCategory" class="form-select" wire:model.lazy="selectedCategory">
                                <option value="">{{ __('translations.Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ __('translations.' . $category->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory -->
                        <div class="form-group mb-3">
                            <label for="blogSubCategory">{{ __('translations.Subcategory') }}</label>
                            <select id="blogSubCategory" class="form-select" wire:model.lazy="selectedSubcategory" {{ empty($subcategories) ? 'disabled' : '' }}>
                                <option value="">{{ __('translations.Select Subcategory') }}</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ __('translations.' . $subcategory->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Title -->
                        <div class="form-group mb-3">
                            <label for="blogTitle">{{ __('translations.Title') }}</label>
                            <input type="text" id="blogTitle" class="form-control" wire:model.lazy="title" placeholder="...">
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="blogDescription">{{ __('translations.Description') }}</label>
                            <textarea id="blogDescription" class="form-control" wire:model.lazy="description" placeholder="..." rows="3"></textarea>
                        </div>

                        <!-- Details -->
                        <div class="form-group mb-3">
                            <label for="blogDetails">{{ __('translations.Details') }}</label>
                            <textarea id="blogDetails" class="form-control" wire:model.lazy="details" placeholder="..." rows="6"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('translations.Update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('scroll', function () {
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
            @this.call('loadMorePosts');
        }
    });
</script>
