{{-- Toast Container: TOP CENTER --}}
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3"
     style="z-index: 1200; width: min(520px, calc(100vw - 24px));">
  <div id="appToast"
       class="toast align-items-center text-bg-primary border-0 shadow"
       role="alert"
       aria-live="assertive"
       aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-semibold" id="appToastBody">...</div>
      <button type="button"
              class="btn-close btn-close-white me-2 m-auto"
              data-bs-dismiss="toast"
              aria-label="Close"></button>
    </div>
  </div>
</div>

<style>
  /* Biar toast gak nabrak navbar sticky-top */
  .toast-container { margin-top: 64px; }

  /* Mobile: kasih napas */
  @media (max-width: 576px){
    .toast-container { margin-top: 56px; }
  }
</style>

<script>
  // Global helper: showToast(message, type)
  window.showToast = function(message, type = 'primary') {
    const toastEl = document.getElementById('appToast');
    const bodyEl  = document.getElementById('appToastBody');

    if (!toastEl || !bodyEl || !window.bootstrap) return;

    const map = {
      success: 'text-bg-success',
      error:   'text-bg-danger',
      danger:  'text-bg-danger',
      warning: 'text-bg-warning',
      info:    'text-bg-info',
      primary: 'text-bg-primary',
      secondary: 'text-bg-secondary',
      dark:    'text-bg-dark',
    };

    toastEl.className = 'toast align-items-center border-0 shadow ' + (map[type] || map.primary);
    bodyEl.textContent = message;

    const toast = bootstrap.Toast.getOrCreateInstance(toastEl, {
      delay: 3200,
      autohide: true
    });

    toast.show();
  };

  // Auto-toast from Laravel flash
  document.addEventListener('DOMContentLoaded', () => {
    @if (session('success'))
      showToast(@json(session('success')), 'success');
    @endif

    @if (session('error'))
      showToast(@json(session('error')), 'danger');
    @endif

    @if (session('warning'))
      showToast(@json(session('warning')), 'warning');
    @endif

    @if (session('info'))
      showToast(@json(session('info')), 'info');
    @endif
  });
</script>
