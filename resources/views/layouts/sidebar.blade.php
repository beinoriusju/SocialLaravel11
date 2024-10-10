<aside class="sidebar sidebar-default sidebar-base navs-rounded-all" id="first-tour" data-toggle="main-sidebar" data-sidebar="responsive">
    <div class="sidebar-header d-flex align-items-center justify-content-start position-relative">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 iq-header-logo">
            <h3 class="logo-title" data-setting="app_name">Social</h3>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <span class="menu-btn d-inline-block is-active">
                <i class="right-icon material-symbols-outlined icon-rtl">chevron_left</i>
            </span>
        </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ url('/') }}">
                        <i class="icon material-symbols-outlined">table_chart</i>
                        <span class="item-name">{{ __('translations.Home') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('newsfeed') }}">
                        <i class="icon material-symbols-outlined">newspaper</i>
                        <span class="item-name">{{ __('translations.Newsfeed') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('events') }}">
                        <i class="icon material-symbols-outlined">calendar_month</i>
                        <span class="item-name">{{ __('translations.Events') }}</span>
                    </a>
                </li>
                @if (auth()->check())
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('userprofile', ['user' => auth()->id()]) }}">
                        <i class="icon material-symbols-outlined">person</i>
                        <span class="item-name">{{ __('translations.Profile') }}</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('chat') }}">
                        <!-- Unread Messages Badge with icon -->
                        <livewire:unread-messages-badge />
                        <span class="item-name">{{ __('translations.Messages') }}</span>
                    </a>
                </li>
                <!-- Separate links for Friends, Friend Requests, and Users -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('friends') }}">
                        <i class="icon material-symbols-outlined">group</i>
                        <span class="item-name">{{ __('translations.Friends') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('friendsrequests') }}">
                        <i class="icon material-symbols-outlined">group_add</i>
                        <span class="item-name">{{ __('translations.Friends requests') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users') }}">
                        <i class="icon material-symbols-outlined">group</i>
                        <span class="item-name">{{ __('translations.Users') }}</span>
                    </a>
                </li>

                @if (auth()->check())
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="icon material-symbols-outlined">logout</i>
                            <span class="item-name">{{ __('translations.Logout') }}</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="{{ route('register') }}">
                        <i class="icon material-symbols-outlined">person</i>
                        <span class="item-name">{{ __('translations.Register') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="icon material-symbols-outlined">login</i>
                        <span class="item-name">{{ __('translations.Login') }}</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#language-list" role="button" aria-expanded="false" aria-controls="sidebar-special">
                        <i class="icon material-symbols-outlined">language</i>
                        <span class="item-name">{{ __('translations.Language') }}</span>
                        <i class="right-icon material-symbols-outlined">chevron_right</i>
                    </a>
                    <ul class="sub-nav collapse" id="language-list" data-bs-parent="#sidebar-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="changeLanguage('{{ route('lt') }}')">
                                <i class="icon material-symbols-outlined filled">turned_in_not</i>
                                <i class="sidenav-mini-icon">LT</i>
                                <span class="item-name">{{ __('translations.Lithuanian') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="changeLanguage('{{ route('en') }}')">
                                <i class="icon material-symbols-outlined filled">turned_in_not</i>
                                <i class="sidenav-mini-icon">EN</i>
                                <span class="item-name">{{ __('translations.English') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:init', () => {
            const userId = @json(Auth::id());

            Echo.private(`user.${userId}`)
                .listen('MessageSent', (event) => {
                    Livewire.dispatch('refreshUnreadMessages');
                });
        });
    </script>
</aside>
