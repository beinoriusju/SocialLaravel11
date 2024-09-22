<div class="content-inner" id="page_layout">
    <div class="container">
        <div class="row">
            @foreach ($friendRequests as $request)
                <div class="col-md-6">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body profile-page p-0">
                            <div class="profile-info p-4">
                                <div class="user-detail">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start">
                                        <div class="profile-detail d-flex">
                                            <div class="profile-img pe-lg-4">
                                                <a href="{{ route('userprofile', ['user' => $request->user_id]) }}">
                                                    <img src="{{ $request->user->image ? asset('storage/' . $request->user->image) : asset('front/images/default.png') }}" alt="profile-img" class="avatar-130 img-fluid">
                                                </a>
                                            </div>
                                            <div class="user-data-block mt-md-0 mt-2">
                                                <h4>
                                                    <a href="{{ route('userprofile', ['user' => $request->user_id]) }}">{{ $request->user->name }} {{ $request->user->last_name }}</a>
                                                </h4>
                                                <button wire:click="acceptFriend('{{ $request->user_id }}')" class="btn btn-primary mt-2">Accept</button>
                                                <button wire:click="rejectFriend('{{ $request->user_id }}')" class="btn btn-danger mt-2">Reject</button>
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
                @if ($friendRequests->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $friendRequests->previousPageUrl() }}">Previous</a>
                    </li>
                @endif

                @for ($i = 1; $i <= $friendRequests->lastPage(); $i++)
                    <li class="page-item {{ $friendRequests->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $friendRequests->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                @if ($friendRequests->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $friendRequests->nextPageUrl() }}">Next</a>
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
