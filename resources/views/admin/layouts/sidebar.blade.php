<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <!-- <a href="index.html" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bolder ms-2">Admin</span>
    </a> -->

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item active">
      <a href="{{route('admin.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>

    <li class="menu-item">
      <a href="{{route('admin.users')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Users</div>
      </a>
    </li>

    <!-- Shop -->

    <!-- Calendar -->
    <!-- <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Calendar</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.calendarcategory.index')}}" class="menu-link">
            <div data-i18n="Without menu">Categories</div>
          </a>
        </li>
      </ul>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.calendarsub-category.index')}}" class="menu-link">
            <div data-i18n="Without menu">Subcategories</div>
          </a>
        </li>
      </ul>
    </li> -->
    <!-- Blog -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Blog</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.blogcategory.index')}}" class="menu-link">
            <div data-i18n="Without menu">Categories</div>
          </a>
        </li>
      </ul>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.blogsub-category.index')}}" class="menu-link">
            <div data-i18n="Without menu">Subcategories</div>
          </a>
        </li>
      </ul>
    </li>
    <!-- Event -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Events</div>
      </a>

      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.eventcategory.index')}}" class="menu-link">
            <div data-i18n="Without menu">Categories</div>
          </a>
        </li>
      </ul>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.eventsub-category.index')}}" class="menu-link">
            <div data-i18n="Without menu">Subcategories</div>
          </a>
        </li>
      </ul>
    </li>



    <li class="menu-item">
      <a href="{{route('admin.translations.index')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-table"></i>
        <div data-i18n="Tables">Translations</div>
      </a>
    </li>


  </ul>
</aside>
<!-- / Menu -->
