<div class="content-inner" id="page_layout">
    <div class="container">
        <div class="row">
            @foreach ($friends as $friend)
                @php
                    $friendUser = $friend->user_id == auth()->id() ? $friend->friend : $friend->user;
                @endphp

                <div class="col-md-6">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body profile-page p-0">
                            <div class="profile-header-image">
                                <div class="cover-container">
                                    <img src="{{ asset('front/images/profile-bg1.jpg') }}" alt="profile-bg" class="rounded img-fluid w-100" loading="lazy">
                                </div>
                                <div class="profile-info p-4">
                                    <div class="user-detail">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                                            <div class="profile-detail d-flex">
                                                <div class="profile-img pe-lg-4">
                                                    <a href="{{ route('userprofile', ['user' => $friendUser->id]) }}">
                                                        <img src="{{ $friendUser->image ? asset('storage/' . $friendUser->image) : asset('front/images/default.png') }}" alt="profile-img" loading="lazy" class="avatar-130 img-fluid">
                                                    </a>
                                                </div>
                                                <div class="user-data-block mt-md-0 mt-2">
                                                    <h4>
                                                      <a href="{{ route('userprofile', ['user' => $friendUser->id]) }}">{{ $friendUser->name }} {{ $friendUser->last_name }}</a>
                                                    </h4>
                                                    <!-- Unfriend Button -->
                                                    <button wire:click="unfriend('{{ $friendUser->id }}')" class="btn btn-danger mt-2">Unfriend</button>
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
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @if ($friends->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $friends->previousPageUrl() }}">Previous</a>
                    </li>
                @endif

                @for ($i = 1; $i <= $friends->lastPage(); $i++)
                    <li class="page-item {{ $friends->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $friends->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                @if ($friends->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $friends->nextPageUrl() }}">Next</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
