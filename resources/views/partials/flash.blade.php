{{-- Flash Messages Partial --}}
  @if (session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-x-circle-fill"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if (session('warning'))
    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <div>{{ session('warning') }}</div>
    </div>
  @endif

  @if (session('info'))
    <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-info-circle-fill"></i>
      <div>{{ session('info') }}</div>
    </div>
  @endif
</noscript>
