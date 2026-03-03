<nav class="navbar navbar-expand-md navbar-dark sticky-top border-bottom" style="background: linear-gradient(90deg, #1d4ed8 0%, #4f46e5 55%, #7c3aed 100%); backdrop-filter: blur(8px); border-color: rgba(255,255,255,.22) !important;">
  <div class="container admin-shell page-pad">

    <a class="navbar-brand fw-semibold d-flex align-items-center gap-2 text-white" href="{{ route('admin.events.index') }}">
      <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background: rgba(255,255,255,.2); color: #fff; font-size: 14px;">
        <i class="bi bi-shield-lock"></i>
      </span>
      <span>Admin Panel</span>
    </a>

    <div class="d-none d-md-flex align-items-center gap-2 ms-auto me-2">
      @if(\Illuminate\Support\Facades\Route::has('admin.events.index'))
        <a
          href="{{ route('admin.events.index') }}"
          class="btn btn-sm fw-semibold {{ request()->routeIs('admin.events.index') || request()->routeIs('admin.events.show') ? 'btn-light text-primary' : 'btn-outline-light' }}"
        >
          <i class="bi bi-calendar-event"></i> Events
        </a>
      @endif

      @if(\Illuminate\Support\Facades\Route::has('admin.events.create') && !request()->routeIs('admin.events.create'))
        <a
          href="{{ route('admin.events.create') }}"
          class="btn btn-sm fw-semibold btn-outline-light"
        >
          <i class="bi bi-plus-circle"></i> Create Event
        </a>
      @endif

      @hasSection('nav_right')
        <div class="d-flex align-items-center gap-2 ms-1">
          @yield('nav_right')
        </div>
      @endif
    </div>

    <button
      class="navbar-toggler d-md-none border-0 shadow-none"
      type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#adminMenu"
      aria-controls="adminMenu"
      aria-label="Open menu"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="adminMenu" aria-labelledby="adminMenuLabel">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title fw-semibold" id="adminMenuLabel">Admin Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <div class="list-group list-group-flush">

      @if(\Illuminate\Support\Facades\Route::has('admin.events.index'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('admin.events.index') || request()->routeIs('admin.events.show') ? 'active' : '' }}"
           href="{{ route('admin.events.index') }}">
          <i class="bi bi-calendar-event"></i> Events
        </a>
      @endif

      @if(\Illuminate\Support\Facades\Route::has('admin.events.create'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('admin.events.create') ? 'active' : '' }}"
           href="{{ route('admin.events.create') }}">
          <i class="bi bi-plus-circle"></i> Create Event
        </a>
      @endif

      @hasSection('nav_menu')
        <div class="mt-3">
          @yield('nav_menu')
        </div>
      @endif

      <div class="mt-3 text-muted small">
        Tip: tombol display biasanya ada di halaman detail event.
      </div>

      @if(\Illuminate\Support\Facades\Route::has('logout'))
        <form class="mt-3" method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-danger w-100 fw-semibold">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </form>
      @endif
    </div>
  </div>
</div>


