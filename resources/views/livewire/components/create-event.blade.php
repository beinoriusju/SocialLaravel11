<div class="card-body">
    <!-- Main Form for Event Creation -->
    <form wire:submit.prevent="createEvent">
        <!-- Event Category Selection -->
        <div class="form-group mb-3">
            <label for="eventCategory">{{ __('translations.Category') }}</label>
            <select id="eventCategory" class="form-select" wire:model.lazy="eventCategory">
                <option value="">{{ __('translations.Select Category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ __('translations.' . $category->name) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Event Subcategory Selection -->
        <div class="form-group mb-3">
            <label for="eventSubCategory">{{ __('translations.Subcategory') }}</label>
            <select id="eventSubCategory" class="form-select" wire:model.lazy="eventSubCategory" {{ $eventCategory && $subcategories->isEmpty() ? 'disabled' : '' }}>
                <option value="">{{ __('translations.Select Subcategory') }}</option>
                @foreach ($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}">{{ __('translations.' . $subcategory->name) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Event Title -->
        <div class="form-group mb-3">
            <label for="eventTitle">{{ __('translations.Title') }}</label>
            <input type="text" id="eventTitle" class="form-control" wire:model.lazy="title" placeholder="...">
        </div>

        <!-- Event Description -->
        <div class="form-group mb-3">
            <label for="eventDescription">{{ __('translations.Description') }}</label>
            <textarea id="eventDescription" class="form-control" wire:model.lazy="description" placeholder="..." rows="3"></textarea>
        </div>

          <!-- Event Details (New Field) -->
         <div class="form-group mb-3">
             <label for="eventDetails">{{ __('translations.Details') }}</label>
             <textarea id="eventDetails" class="form-control" wire:model.lazy="details" placeholder="Enter event details" rows="5"></textarea>
         </div>

        <!-- Event Date -->
        <div class="form-group mb-3">
            <label for="eventDate">{{ __('translations.Event Date') }}</label>
            <input type="date" id="eventDate" class="form-control" wire:model.lazy="event_date">
        </div>

        <!-- Image and Video Upload Options -->
        <ul class="d-flex flex-wrap align-items-center list-inline m-0 p-0">
            <!-- Image Upload -->
            <li class="col-md-6 mb-3">
                <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('eventImageInput').click();">
                    <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                        <span class="material-symbols-outlined align-middle font-size-20 me-1">add_a_photo</span>
                        {{ __('translations.Photos') }}
                    </a>
                </div>
                <input type="file" wire:model.lazy="images" accept="image/*" id="eventImageInput" style="display: none;" multiple>
            </li>

            <!-- Video Upload -->
            <li class="col-md-6 mb-3">
                <div class="bg-primary-subtle rounded p-2 pointer me-3 position-relative" onclick="document.getElementById('eventVideoInput').click();">
                    <a href="javascript:void(0);" class="d-inline-block fw-medium text-body">
                        <span class="material-symbols-outlined align-middle font-size-20 me-1">live_tv</span>
                        {{ __('translations.Videos') }}
                    </a>
                </div>
                <input type="file" wire:model.lazy="videos" accept="video/*" id="eventVideoInput" style="display: none;" multiple>
            </li>
        </ul>

        <!-- Create Event Button -->
        <button type="submit" class="btn btn-primary d-block w-100 mt-3">{{ __('translations.Post') }}</button>
    </form>

    <!-- Loading Indicators -->
    <div wire:loading wire:target="images">{{ __('translations.Uploading images...') }}</div>
    <div wire:loading wire:target="videos">{{ __('translations.Uploading videos...') }}</div>

    <!-- Preview Section -->
    <div class="mt-3">
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

        @if (!empty($videos))
            <div class="mt-3">
                @foreach ($videos as $video)
                    <video src="{{ $video->temporaryUrl() }}" controls class="w-100" style="height: auto;"></video>
                @endforeach
            </div>
        @endif
    </div>
</div>
