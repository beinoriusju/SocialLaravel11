<div>
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
                                                            <a href="{{ route('userprofile', ['user' => $friendUser->id]) }}">{{ $friendUser->username }}</a>
                                                        </h4>
                                                        <!-- Unfriend Button -->
                                                        <button wire:click="unfriend('{{ $friendUser->id }}')" class="btn btn-danger mt-2">{{ __('translations.Unfriend') }}</button>
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

        <!-- Loading and no more friends message -->
        <div class="text-center mt-4" wire:loading>
            <span>{{ __('translations.Loading more') }}</span>
        </div>
        <div class="text-center mt-4" id="noMoreFriendsMessage" style="display: none;">
            <span>{{ __('translations.No more to load') }}</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('scroll', function () {
                if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 100) {
                    @this.call('loadMoreFriends');
                }
            });

            Livewire.on('noMoreFriends', function () {
                document.getElementById('noMoreFriendsMessage').style.display = 'block';
            });

            Livewire.on('friendsLoaded', function (event) {
                if (!event.hasMorePages) {
                    document.getElementById('noMoreFriendsMessage').style.display = 'block';
                }
            });
        });
    </script>
</div>
