<div class="card-body">
    <!-- Main Form for Blog Posting -->
    <form wire:submit.prevent="createBlogPost">
        <!-- Blog Category Selection -->
        <div class="form-group mb-3">
            <label for="blogCategory">{{ __('translations.Category') }}</label>
            <select id="blogCategory" class="form-select" wire:model.lazy="blogCategory">
                <option value="">{{ __('translations.Select Category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ __('translations.' . $category->name) }}</option> <!-- Translate category names -->
                @endforeach
            </select>
        </div>

        <!-- Blog Subcategory Selection -->
        <div class="form-group mb-3">
            <label for="blogSubCategory">{{ __('translations.Subcategory') }}</label>
            <select id="blogSubCategory" class="form-select" wire:model.lazy="blogSubCategory" {{ $blogCategory && $subcategories->isEmpty() ? 'disabled' : '' }}>
                <option value="">{{ __('translations.Select Subcategory') }}</option>
                @foreach ($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}">{{ __('translations.' . $subcategory->name) }}</option> <!-- Translate subcategory names -->
                @endforeach
            </select>
        </div>

        <!-- Blog Title -->
        <div class="form-group mb-3">
            <label for="blogTitle">{{ __('translations.Title') }}</label>
            <input type="text" id="blogTitle" class="form-control" wire:model.lazy="title" placeholder="...">
        </div>

        <!-- Blog Description -->
        <div class="form-group mb-3">
            <label for="blogDescription">{{ __('translations.Description') }}</label>
            <textarea id="blogDescription" class="form-control" wire:model.lazy="description" placeholder="..." rows="3"></textarea>
        </div>

        <!-- Blog Details -->
        <div class="form-group mb-3">
            <label for="blogDetails">{{ __('translations.Detailed description') }}</label>
            <textarea id="blogDetails" class="form-control" wire:model.lazy="details" placeholder="..." rows="6"></textarea>
        </div>

        <!-- Image and Video Upload Options -->
        <ul class="d-flex flex-wrap align-items-center list-inline m-0 p-0">
            <!-- Image Upload -->
            <li class="col-md-6 mb-3">
                <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('blogImageInput').click();">
                    <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                        <span class="material-symbols-outlined align-middle font-size-20 me-1">add_a_photo</span>
                        {{ __('translations.Photos') }}
                    </a>
                </div>
                <input type="file" wire:model="images" accept="image/*" id="blogImageInput" style="display: none;" multiple>
            </li>

            <!-- Video Upload -->
            <li class="col-md-6 mb-3">
                <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('blogVideoInput').click();">
                    <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                        <span class="material-symbols-outlined align-middle font-size-20 me-1">live_tv</span>
                        {{ __('translations.Videos') }}
                    </a>
                </div>
                <input type="file" wire:model="video" accept="video/*" id="blogVideoInput" style="display: none;" multiple>
            </li>
        </ul>

        <!-- Post Button -->
        <button type="submit" class="btn btn-primary d-block w-100 mt-3">{{ __('translations.Post') }}</button>
    </form>

    <!-- Loading Indicators -->
    <div wire:loading wire:target="images">{{ __('translations.Uploading images...') }}</div>
    <div wire:loading wire:target="video">{{ __('translations.Uploading video...') }}</div>

    <!-- Preview Section -->
    <div class="mt-3">
        <!-- Display Uploaded Images -->
        @if (!empty($images) && is_array($images))
            <div class="row">
                @foreach ($images as $image)
                    @if ($image)
                        <div class="col-md-4 mb-3">
                            <img src="{{ $image->temporaryUrl() }}" alt="{{ __('translations.Uploaded image preview') }}" class="img-fluid" style="max-width: 100%;">
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Display Uploaded Videos -->
        @if (!empty($video) && is_array($video))
            <div class="row mt-3">
                @foreach ($video as $vid)
                    @if ($vid)
                        <div class="col-md-12 mb-3">
                            <video src="{{ $vid->temporaryUrl() }}" controls class="w-100" style="height: auto;"></video>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
