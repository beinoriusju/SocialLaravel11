<div class="iq-top-navbar border-bottom">
  <nav class="nav navbar navbar-expand-lg navbar-light iq-navbar p-lg-0">
    <div class="container-fluid navbar-inner">
      <div class="d-flex align-items-center pb-2 pb-lg-0 flex-wrap">
        <a class="sidebar-toggle me-2" data-toggle="sidebar" data-active="true" href="javascript:void(0);">
          <div class="icon material-symbols-outlined iq-burger-menu">menu</div>
        </a>

        <!-- Home Icon -->
        <a class="nav-link menu-arrow justify-content-start" href="{{ url('/') }}">
          <i class="icon material-symbols-outlined">table_chart</i>
        </a>

        <!-- Newsfeed Icon -->
        <a class="nav-link menu-arrow justify-content-start" href="{{ route('newsfeed') }}">
          <i class="icon material-symbols-outlined">newspaper</i>
        </a>

        <!-- Events Icon -->
        <a class="nav-link menu-arrow justify-content-start" href="{{ route('events') }}">
          <i class="icon material-symbols-outlined">calendar_month</i>
        </a>

        <!-- Messages Icon with Unread Count -->
        <a class="nav-link" href="{{ route('conversations') }}">
          <i class="icon material-symbols-outlined">mail</i>
          <livewire:unread-messages-badge /> <!-- Unread messages handled by Livewire -->
        </a>

        <!-- Notifications Icon with Unread Count -->
        <a class="nav-link" href="{{ route('notifications') }}">
          <i class="icon material-symbols-outlined">notifications</i>
          <livewire:notification-dropdown /> <!-- Unread notifications handled by Livewire -->
        </a>

        <!-- Search Bar -->
        <div class="iq-search-bar device-search position-relative ms-3 d-none d-md-block">
          <livewire:user-search />
        </div>
      </div>

      <!-- Mobile Search Bar (Full Width on Mobile) -->
      <div class="d-md-none">
        <div class="iq-search-bar device-search position-relative">
          <livewire:user-search />
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

        Echo.private(`user.${userId}`)
            .listen('MessageSent', (event) => {
                Livewire.dispatch('refreshUnreadMessages'); // Emit event to refresh unread messages
            });
    });

    function changeLanguage(url) {
        window.location.href = url; // Redirect to the selected language route
    }
</script>
