<nav class="navbar navbar-dark sticky-top nav-shadow" style="background:#0b1c6d;">
  <div class="container app-shell">

    {{-- Brand --}}
    <a class="navbar-brand fw-semibold d-flex align-items-center gap-2"
       href="{{ route('employee.events.index') }}">
      <i class="bi bi-ticket-perforated"></i>
      <span>Staff Sale Queue</span>
    </a>

    {{-- Right slot + mobile toggler --}}
    <div class="d-flex align-items-center gap-2">

      {{-- slot kanan (optional) --}}
      @hasSection('nav_right')
        <div class="d-none d-md-flex align-items-center gap-2">
          @yield('nav_right')
        </div>
      @endif

      <button class="navbar-toggler border-0"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#employeeMenu"
              aria-controls="employeeMenu"
              aria-label="Open menu">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</nav>

{{-- Offcanvas Menu --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="employeeMenu" aria-labelledby="employeeMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-semibold" id="employeeMenuLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <div class="list-group">

      @if(\Illuminate\Support\Facades\Route::has('employee.events.index'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
           href="{{ route('employee.events.index') }}">
          <i class="bi bi-calendar2-week"></i> Events
        </a>
      @endif

      {{-- Jika kamu punya route ticket login --}}
      @if(\Illuminate\Support\Facades\Route::has('employee.ticket.login'))
        <a class="list-group-item list-group-item-action d-flex align-items-center gap-2"
           href="{{ route('employee.ticket.login') }}">
          <i class="bi bi-qr-code-scan"></i> My Ticket
        </a>
      @endif

      {{-- Slot menu tambahan --}}
      @hasSection('nav_menu')
        <div class="mt-2">
          @yield('nav_menu')
        </div>
      @endif

      <div class="mt-3 text-muted small">
        Buka “My Ticket” buat QR & status batch.
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
