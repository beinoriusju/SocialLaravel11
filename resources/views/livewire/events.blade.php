<div class="content-inner" id="page_layout">
    <div class="container">
        <div class="row">
            <!-- Filters Section -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form wire:submit.prevent="filterEvents">
                            <div class="row">
                                <!-- Category Filter -->
                                <div class="col-md-4 mb-3">
                                    <label for="categoryFilter">{{ __('Select Category') }}</label>
                                    <select id="categoryFilter" class="form-select" wire:model.lazy="selectedCategory">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subcategory Filter -->
                                <div class="col-md-4 mb-3">
                                    <label for="subcategoryFilter">{{ __('Select Subcategory') }}</label>
                                    <select id="subcategoryFilter" class="form-select" wire:model.lazy="selectedSubcategory" {{ empty($subcategories) ? 'disabled' : '' }}>
                                        <option value="">{{ __('All Subcategories') }}</option>
                                        @foreach ($subcategories as $subcategory)
                                            <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Date Range Filter -->
                                <div class="col-md-4 mb-3">
                                    <label for="dateRange">{{ __('Event Date (From - To)') }}</label>
                                    <div class="d-flex">
                                        <input type="date" wire:model.lazy="startDate" class="form-control me-2" placeholder="{{ __('From Date') }}">
                                        <input type="date" wire:model.lazy="endDate" class="form-control" placeholder="{{ __('To Date') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Event Listing -->
            @foreach ($events as $index => $event)
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height event-list {{ $index % 2 == 0 ? '' : 'list-even' }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                @php
                                    $media = $event->media->where('file_type', 'image')->first()
                                        ?? $event->media->where('file_type', 'video')->first()
                                        ?? $event->media->where('file_type', 'youtube')->first();
                                @endphp

                                @if ($index % 2 == 0) <!-- Left media, right content -->
                                    @if ($media)
                                        <div class="col-md-6">
                                            <div class="image-block">
                                                @if ($media->file_type == 'image')
                                                    <img src="{{ asset('storage/' . $media->file) }}" class="img-fluid rounded w-100" alt="event-img" style="max-height: 400px; object-fit: cover;">
                                                @elseif ($media->file_type == 'video')
                                                    <video controls class="w-100">
                                                        <source src="{{ asset('storage/' . $media->file) }}" type="video/mp4">
                                                    </video>
                                                @elseif ($media->file_type == 'youtube')
                                                    @php
                                                        $youtubeUrl = $media->file;
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
                                        <div class="event-description p-2 rounded">
                                            <div class="event-meta d-flex align-items-center justify-content-between mb-2">
                                                <div class="date">{{ $event->event_date->format('M d, Y') }}</div>
                                            </div>
                                            <h5 class="mb-2">{{ $event->title }}</h5>
                                            <p>{{ $event->description }}</p>
                                            <a href="{{ route('event.post', $event->id) }}" class="d-flex align-items-center">
                                                More Details <i class="material-symbols-outlined fs-6 icon-rtl">arrow_forward_ios</i>
                                            </a>

                                            <!-- Show the Update Button only to the Event Creator -->
                                            @if (auth()->id() === $event->user_id)
                                                <button wire:click="editEvent({{ $event->id }})" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editModal">Update Event</button>
                                            @endif

                                            <!-- Attending / Not Attending Buttons -->
                                            @if ($event->attendees->contains(auth()->user()->id))
                                                <button wire:click="toggleAttendance({{ $event->id }})" class="btn btn-danger mt-3">Not Attending</button>
                                            @else
                                                <button wire:click="toggleAttendance({{ $event->id }})" class="btn btn-success mt-3">Attending</button>
                                            @endif

                                            <!-- Button to show attendees modal -->
                                            <button wire:click="showAttendees({{ $event->id }})" class="btn btn-info mt-3" data-bs-toggle="modal" data-bs-target="#attendeesModal">
                                                Show Attendees
                                            </button>
                                        </div>
                                    </div>
                                @else <!-- Right media, left content -->
                                    <div class="col-md-6">
                                        <div class="event-description p-2 rounded">
                                            <div class="event-meta mb-2">
                                                <small class="text-muted">{{ $event->event_date->format('M d, Y') }}</small>
                                            </div>
                                            <h5 class="mb-2">{{ $event->title }}</h5>
                                            <p>{{ $event->description }}</p>
                                            <a href="{{ route('event.post', $event->id) }}" class="d-flex align-items-center">
                                                More Details <i class="material-symbols-outlined fs-6 icon-rtl">arrow_forward_ios</i>
                                            </a>

                                            <!-- Show the Update Button only to the Event Creator -->
                                            @if (auth()->id() === $event->user_id)
                                                <button wire:click="editEvent({{ $event->id }})" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editModal">Update Event</button>
                                            @endif

                                            <!-- Attending / Not Attending Buttons -->
                                            @if ($event->attendees->contains(auth()->user()->id))
                                                <button wire:click="toggleAttendance({{ $event->id }})" class="btn btn-danger mt-3">Not Attending</button>
                                            @else
                                                <button wire:click="toggleAttendance({{ $event->id }})" class="btn btn-success mt-3">Attending</button>
                                            @endif

                                            <!-- Button to show attendees modal -->
                                            <button wire:click="showAttendees({{ $event->id }})" class="btn btn-info mt-3" data-bs-toggle="modal" data-bs-target="#attendeesModal">
                                                Show Attendees
                                            </button>
                                        </div>
                                    </div>
                                    @if ($media)
                                        <div class="col-md-6">
                                            <div class="image-block">
                                                @if ($media->file_type == 'image')
                                                    <img src="{{ asset('storage/' . $media->file) }}" class="img-fluid rounded w-100" alt="event-img" style="max-height: 400px; object-fit: cover;">
                                                @elseif ($media->file_type == 'video')
                                                    <video controls class="w-100">
                                                        <source src="{{ asset('storage/' . $media->file) }}" type="video/mp4">
                                                    </video>
                                                @elseif ($media->file_type == 'youtube')
                                                    <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- No More Events Message -->
            @if (!$hasMorePages)
                <div class="text-center mt-4">
                    <p>{{ __('translations.No more events available.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendees Modal -->
    <div wire:ignore.self class="modal fade" id="attendeesModal" tabindex="-1" aria-labelledby="attendeesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendeesModalLabel">Attendees</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(count($attendees) === 0)
                        <p>No attendees found for this event.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($attendees as $attendee)
                                <li class="list-group-item d-flex align-items-center">
                                    <a href="{{ route('userprofile', ['user' => $attendee['user']['id']]) }}" class="d-flex align-items-center text-decoration-none">
                                        <img src="{{ $attendee['user']['image'] ? asset('storage/' . $attendee['user']['image']) : asset('front/images/default.png') }}"
                                             alt="user-img"
                                             class="img-fluid rounded-circle me-2"
                                             style="width: 40px; height: 40px;">
                                        <span>{{ $attendee['user']['username'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateEvent">
                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="eventCategory">{{ __('translations.Category') }}</label>
                            <select id="eventCategory" class="form-select" wire:model.lazy="selectedCategory">
                                <option value="">{{ __('translations.Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory -->
                        <div class="form-group mb-3">
                            <label for="eventSubCategory">{{ __('translations.Subcategory') }}</label>
                            <select id="eventSubCategory" class="form-select" wire:model.lazy="selectedSubcategory" {{ empty($subcategories) ? 'disabled' : '' }}>
                                <option value="">{{ __('translations.Select Subcategory') }}</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Title -->
                        <div class="form-group mb-3">
                            <label for="eventTitle">{{ __('translations.Title') }}</label>
                            <input type="text" id="eventTitle" class="form-control" wire:model.lazy="title" placeholder="...">
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-3">
                            <label for="eventDescription">{{ __('translations.Description') }}</label>
                            <textarea id="eventDescription" class="form-control" wire:model.lazy="description" placeholder="..." rows="3"></textarea>
                        </div>

                        <!-- Details -->
                        <div class="form-group mb-3">
                            <label for="eventDetails">{{ __('translations.Details') }}</label>
                            <textarea id="eventDetails" class="form-control" wire:model.lazy="details" placeholder="..." rows="6"></textarea>
                        </div>

                        <!-- Event Date -->
                        <div class="form-group mb-3">
                            <label for="eventDate">{{ __('translations.Event Date') }}</label>
                            <input type="date" id="eventDate" class="form-control" wire:model.live="event_date">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('translations.Update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Infinite scroll script -->
<script>
    window.addEventListener('scroll', function () {
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
            @this.call('loadMoreEvents');
        }
    });
</script>
