<div>
    <div class="position-relative">
        <div class="header-for-bg">
            <div class="background-header position-relative">
                <img src="{{ asset('front/images/profilebg3.jpg') }}" class="img-fluid w-100" alt="header-bg" loading="lazy">
                <div class="title-on-header">
                    <div class="data-block">
                        <h2>Users list</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-inner" id="page_layout">
        <div class="container">
            <div class="row">
                @foreach ($users as $user)
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
                                                <a href="{{ route('userprofile', ['user' => $user->id]) }}">
                                                    <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('front/images/default.png') }}" alt="profile-img" loading="lazy" class="avatar-130 img-fluid">
                                                </a>
                                              </div>
                                                <div class="user-data-block mt-md-0 mt-2">
                                                    <h4>
                                                      <a href="{{ route('userprofile', ['user' => $user->id]) }}">{{ $user->username }}</a>
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="mt-2 d-flex align-items-center justify-content-center position-absolute right-15 top-10 me-2">
                                                @if (auth()->id() == $user->id)
                                                    <!-- <a href="#" class="p-3 text-white bg-primary d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Edit</a> -->
                                                @elseif ($friendRequests->where('user_id', auth()->id())->where('friend_id', $user->id)->where('status', 'pending')->count() > 0)
                                                    <!-- Friend request sent by the logged-in user (Cancel option) -->
                                                    <button wire:click="removeFriend('{{ $user->id }}')" class="p-3 text-white bg-warning d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Cancel</button>
                                                @elseif ($friendRequests->where('friend_id', auth()->id())->where('user_id', $user->id)->where('status', 'pending')->count() > 0)
                                                    <!-- Friend request received by the logged-in user (Accept/Reject options) -->
                                                    <button wire:click="acceptFriend('{{ $user->id }}')" class="p-3 text-white bg-primary d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Accept</button>
                                                    <button wire:click="removeFriend('{{ $user->id }}')" class="p-3 text-white bg-danger d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Reject</button>
                                                @elseif ($user->is_friend(auth()->id()))
                                                    <!-- Users are already friends -->
                                                    <button class="p-3 text-white bg-info d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Friend</button>
                                                @else
                                                    <!-- No friend request sent or received -->
                                                    <button wire:click="addFriend('{{ $user->id }}')" class="p-3 text-white bg-success d-none d-lg-block z-index-1 rounded-3 font-xsssss text-uppercase fw-700 ls-3">Add Friend</button>
                                                @endif

                                                <a href="/chat/{{ $user->id }}" class="d-none d-lg-block bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700">
                                                    <i class="font-md" style="margin-top: -10px"></i>
                                                </a>
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
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @if ($users->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link" href="{{ $users->previousPageUrl() }}">Previous</a>
                </li>
                @endif

                @for ($i = 1; $i <= $users->lastPage(); $i++)
                <li class="page-item {{ $users->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                </li>
                @endfor

                @if ($users->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $users->nextPageUrl() }}">Next</a>
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
