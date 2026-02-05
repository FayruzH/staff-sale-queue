<nav class="navbar navbar-dark sticky-top nav-shadow" style="background:#0b1c6d;">
  <div class="container admin-shell">

    {{-- Left: Brand --}}
    <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="{{ route('admin.events.index') }}">
      <i class="bi bi-shield-lock"></i>
      <span>Admin • Staff Sale Queue</span>
    </a>

    {{-- Right: Desktop actions + Mobile toggler --}}
    <div class="d-flex align-items-center gap-2">

      {{-- Slot kanan buat page tertentu (optional) --}}
      @hasSection('nav_right')
        <div class="d-none d-md-flex align-items-center gap-2">
          @yield('nav_right')
        </div>
      @endif

      {{-- Offcanvas toggler (mobile) --}}
      <button class="navbar-toggler border-0"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#adminMenu"
              aria-controls="adminMenu"
              aria-label="Open menu">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</nav>

{{-- Offcanvas Menu --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="adminMenu" aria-labelledby="adminMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-semibold" id="adminMenuLabel">Admin Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <div class="list-group">

      @if(\Illuminate\Support\Facades\Route::has('admin.events.index'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
           href="{{ route('admin.events.index') }}">
          <i class="bi bi-calendar-event"></i> Events
        </a>
      @endif

      {{-- Kalau kamu punya route create --}}
      @if(\Illuminate\Support\Facades\Route::has('admin.events.create'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
           href="{{ route('admin.events.create') }}">
          <i class="bi bi-plus-circle"></i> Create Event
        </a>
      @endif

      {{-- Slot tambahan menu khusus admin --}}
      @hasSection('nav_menu')
        <div class="mt-2">
          @yield('nav_menu')
        </div>
      @endif

      <div class="mt-3 text-muted small">
        Tip: “Display” biasanya ada tombol di Event Detail (biar open tab baru).
      </div>

      {{-- Logout: jangan dipaksain kalau route kamu beda --}}
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
