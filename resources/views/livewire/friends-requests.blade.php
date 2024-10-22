<div>
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
                                                        <img src="{{ $request->user->image ? asset('storage/' . $request->user->image) : asset('front/images/default.png') }}" alt="profile-img" class="avatar-130 img-fluid" >
                                                    </a>
                                                </div>
                                                <div class="user-data-block mt-md-0 mt-2">
                                                    <h4>
                                                        <a href="{{ route('userprofile', ['user' => $request->user_id]) }}">{{ $request->user->username }}</a>
                                                    </h4>
                                                    <button wire:click="acceptFriend('{{ $request->user_id }}')" class="btn btn-primary mt-2">{{ __('translations.Accept') }}</button>
                                                    <button wire:click="rejectFriend('{{ $request->user_id }}')" class="btn btn-danger mt-2">{{ __('translations.Reject') }}</button>
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

        <!-- Loading and no more requests message -->
        <div class="text-center mt-4" wire:loading>
            <span>{{ __('translations.Loading more') }}</span>
        </div>
        <div class="text-center mt-4" id="noMoreRequestsMessage" style="display: none;">
            <span>{{ __('translations.No more requests') }}</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('scroll', function () {
                if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 100) {
                    @this.call('loadMoreRequests');
                }
            });

            Livewire.on('noMoreRequests', function () {
                document.getElementById('noMoreRequestsMessage').style.display = 'block';
            });

            Livewire.on('requestsLoaded', function (event) {
                if (!event.hasMorePages) {
                    document.getElementById('noMoreRequestsMessage').style.display = 'block';
                }
            });
        });
    </script>
</div>
