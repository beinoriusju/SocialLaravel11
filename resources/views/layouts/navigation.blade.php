<div class="iq-top-navbar border-bottom">
  <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar p-lg-0">
    <div class="container-fluid navbar-inner">
      <div class="d-flex align-items-center pb-2 pb-lg-0 d-xl-none">
        <a class="sidebar-toggle" data-toggle="sidebar" data-active="true" href="javascript:void(0);">
          <div class="icon material-symbols-outlined iq-burger-menu">menu</div>
        </a>
        <a class="nav-link menu-arrow justify-content-start" href="{{ url('/') }}">
          <span class="nav-text">{{ __('translations.Home') }}</span>
        </a>
        <a class="nav-link menu-arrow justify-content-start" href="{{ route('newsfeed') }}">
          <span class="nav-text">{{ __('translations.Newsfeed') }}</span>
        </a>
        <a class="nav-link menu-arrow justify-content-start" href="{{ route('events') }}">
          <span class="nav-text">{{ __('translations.Events') }}</span>
        </a>
      </div>
      <div class="d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between product-offcanvas">
          <div class="offcanvas offcanvas-end shadow-none iq-product-menu-responsive d-none d-xl-block" tabindex="-1" id="offcanvasBottomNav">
            <div class="offcanvas-body">
              <ul class="iq-nav-menu list-unstyled">
                <li class="nav-item">
                  <a class="nav-link menu-arrow justify-content-start" href="{{ url('/') }}">
                    <span class="nav-text">{{ __('translations.Home') }}</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link menu-arrow justify-content-start" href="{{ route('newsfeed') }}">
                    <span class="nav-text">{{ __('translations.Newsfeed') }}</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link menu-arrow justify-content-start" href="{{ route('events') }}">
                    <span class="nav-text">{{ __('translations.Events') }}</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Make the search bar visible on all screens -->
        <div class="iq-search-bar device-search position-relative">
          <livewire:user-search /> <!-- Your Livewire search component -->

        </div>
        <div class="">
          <ul class="navbar-nav navbar-list">
            <!-- Mobile Search Icon -->
            <!-- <li class="nav-item d-lg-none">
              <div class="iq-search-bar device-search">
                <form class="searchbox open-modal-search">
                  <a class="d-lg-none d-flex text-body" href="javascript:void(0);">
                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="7.82491" cy="7.82495" r="6.74142" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                      <path d="M12.5137 12.8638L15.1567 15.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                  </a>
                </form>
              </div>
            </li> -->

            <!-- Notification Dropdown -->
            <livewire:notification-dropdown />

            <li class="nav-item d-none d-lg-none">
              <a href="../app/chat.html" class="dropdown-toggle d-flex align-items-center" id="mail-drop-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="material-symbols-outlined">mail</i>
                <span class="mobile-text ms-3">Message</span>
              </a>
            </li>
          </ul>
        </div>
      </div>


    </div>
  </nav>
</div>







    <script>
        document.addEventListener('livewire:init', () => {
            const userId = @json(Auth::id());

            Echo.private(`notifications.${userId}`)
                .listen('NotificationSent', (event) => {
                    Livewire.dispatch('notificationsUpdated'); // Emit event to refresh notifications
                });
        });
    </script>
</div>
