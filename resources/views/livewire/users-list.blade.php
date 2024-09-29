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
                <div class="col-md-6" style="padding-bottom: 30px">
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
                                                    <a href="{{ route('userprofile', ['user' => $user['id']]) }}">
                                                        <img src="{{ $user['image'] ? asset('storage/' . $user['image']) : asset('front/images/default.png') }}" alt="profile-img" loading="lazy" class="avatar-130 img-fluid">
                                                    </a>
                                                </div>
                                                <div class="user-data-block mt-md-0 mt-2">
                                                    <h4>
                                                        <a href="{{ route('userprofile', ['user' => $user['id']]) }}">{{ $user['username'] }}</a>
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="mt-4 d-flex align-items-center justify-content-center position-absolute right-15 top-10 me-2">
                                                @if (auth()->id() !== $user->id && $friendRequests->where('user_id', auth()->id())->where('friend_id', $user->id)->where('status', 'pending')->count() > 0)
                                                    <button wire:click="removeFriend('{{ $user->id }}')" class="p-3 text-white bg-warning rounded-3 font-xsssss text-uppercase fw-700 ls-3">{{ __('translations.Cancel') }}</button>
                                                @elseif ($friendRequests->where('friend_id', auth()->id())->where('user_id', $user->id)->where('status', 'pending')->count() > 0)
                                                    <button wire:click="acceptFriend('{{ $user->id }}')" class="p-3 text-white bg-primary rounded-3 font-xsssss text-uppercase fw-700 ls-3">{{ __('translations.Accept') }}</button>
                                                    <button wire:click="removeFriend('{{ $user->id }}')" class="p-3 text-white bg-danger rounded-3 font-xsssss text-uppercase fw-700 ls-3">{{ __('translations.Reject') }}</button>
                                                @elseif ($user->is_friend(auth()->id()))
                                                    <button class="p-3 text-white bg-info rounded-3 font-xsssss text-uppercase fw-700 ls-3">{{ __('translations.Friend') }}</button>
                                                @else
                                                    <button wire:click="addFriend('{{ $user->id }}')" class="p-3 text-white bg-success rounded-3 font-xsssss text-uppercase fw-700 ls-3">{{ __('translations.Add Friend') }}</button>
                                                @endif

                                                @if(auth()->user()->role === 'admin')
                                                    <!-- Make this button visible on all screen sizes -->
                                                    <button onclick="confirmDeletion({{ $user->id }}, @this)" class="p-3 text-white bg-danger rounded-3 font-xsssss text-uppercase fw-700 ls-3">
                                                        {{ __('translations.Delete User') }}
                                                    </button>
                                                @endif

                                                <!-- Chat Icon Button -->
                                                <!-- <a href="/chat/{{ $user->id }}" class="bg-greylight btn-round-lg ms-2 rounded-3 text-grey-700">
                                                    <i class="material-icons">chat</i>
                                                </a> -->
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

    <div wire:loading class="text-center mt-4 loading-text">
        <span>{{ __('translations.Loading more') }}</span>
    </div>
</div>

<!-- The confirmDeletion function needs to be outside of any other function for global access -->
<script>
    function confirmDeletion(userId, component) {
        if (confirm('Are you sure you want to delete this user?')) {
            // Call the deleteUser method on the Livewire component
            component.call('deleteUser', userId);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('scroll', function () {
            if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 100) {
                @this.call('loadMoreUsers');
            }
        });

        Livewire.on('noMoreUsers', function () {
            const loadingText = document.querySelector('.loading-text');
            if (loadingText) {
                loadingText.textContent = "No more users to load.";
                loadingText.classList.add('text-warning');
            }
        });

        Livewire.on('usersLoaded', function (event) {
            if (!event.hasMorePages) {
                const loadingText = document.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = "No more users to load.";
                    loadingText.classList.add('text-warning');
                }
            }
        });
    });
</script>
