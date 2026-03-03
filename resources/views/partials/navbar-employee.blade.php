<nav class="navbar navbar-expand-md navbar-dark sticky-top border-bottom" style="background: linear-gradient(90deg, #2563eb 0%, #4f46e5 65%, #6366f1 100%); backdrop-filter: blur(8px); border-color: rgba(255,255,255,.22) !important;">
  <div class="container app-shell page-pad">

    <a class="navbar-brand fw-semibold d-flex align-items-center gap-2 text-white"
       href="{{ route('employee.events.index') }}">
      <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background: rgba(255,255,255,.2); color: #fff; font-size: 14px;">
        <i class="bi bi-ticket-perforated"></i>
      </span>
      <span>Staff Sale Queue</span>
    </a>

    <div class="d-none d-md-flex align-items-center gap-2 ms-auto me-2">
      @if(\Illuminate\Support\Facades\Route::has('employee.events.index'))
        <a
          href="{{ route('employee.events.index') }}"
          class="btn btn-sm fw-semibold {{ request()->routeIs('employee.events.index') ? 'btn-light text-primary' : 'btn-outline-light' }}"
        >
          <i class="bi bi-calendar2-week"></i> Events
        </a>
      @endif

      @if(\Illuminate\Support\Facades\Route::has('employee.ticket.login') && !request()->routeIs('employee.ticket.login'))
        <a
          href="{{ route('employee.ticket.login') }}"
          class="btn btn-sm fw-semibold btn-outline-light"
        >
          <i class="bi bi-qr-code-scan"></i> My Ticket
        </a>
      @endif

      @hasSection('nav_right')
        <div class="d-flex align-items-center gap-2 ms-1">
          @yield('nav_right')
        </div>
      @endif
    </div>

    <button class="navbar-toggler d-md-none border-0 shadow-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#employeeMenu"
            aria-controls="employeeMenu"
            aria-label="Open menu">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="employeeMenu" aria-labelledby="employeeMenuLabel">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title fw-semibold" id="employeeMenuLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <div class="list-group list-group-flush">

      @if(\Illuminate\Support\Facades\Route::has('employee.events.index'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('employee.events.index') ? 'active' : '' }}"
           href="{{ route('employee.events.index') }}">
          <i class="bi bi-calendar2-week"></i> Events
        </a>
      @endif

      @if(\Illuminate\Support\Facades\Route::has('employee.ticket.login'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('employee.ticket.login') ? 'active' : '' }}"
           href="{{ route('employee.ticket.login') }}">
          <i class="bi bi-qr-code-scan"></i> My Ticket
        </a>
      @endif

      @hasSection('nav_menu')
        <div class="mt-3">
          @yield('nav_menu')
        </div>
      @endif

      <div class="mt-3 text-muted small">
        Buka My Ticket untuk lihat QR dan status batch.
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

