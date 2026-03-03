<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light">
  <title>@yield('title', 'Staff Sale Queue')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <style>
    :root {
      --employee-bg: #eff6ff;
      --employee-surface: #ffffff;
      --employee-text: #0f172a;
      --employee-muted: #64748b;
      --employee-border: #dbeafe;
      --employee-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
    }

    body {
      background: linear-gradient(180deg, #f8fbff 0%, #f3f7ff 45%, var(--employee-bg) 100%);
      color: var(--employee-text);
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    .app-shell { max-width: 560px; width: 100%; }
    @media (min-width: 768px) { .app-shell { max-width: 760px; } }
    @media (min-width: 992px) { .app-shell { max-width: 980px; } }

    .page-pad {
      padding-left: 12px;
      padding-right: 12px;
    }

    main {
      padding-top: 6px;
      padding-bottom: 12px;
    }

    .card {
      border: 1px solid var(--employee-border) !important;
      background: var(--employee-surface);
      box-shadow: var(--employee-shadow) !important;
      border-radius: 14px;
    }

    .text-muted {
      color: var(--employee-muted) !important;
    }

    footer {
      border-top: 1px solid var(--employee-border);
      background: rgba(255, 255, 255, 0.72);
      backdrop-filter: blur(4px);
    }

    @media (max-width: 767.98px) {
      .page-pad {
        padding-left: 10px;
        padding-right: 10px;
      }

      main {
        padding-top: 4px;
      }
    }
  </style>

  @stack('styles')
</head>
<body>

  <div class="min-vh-100 d-flex flex-column">
    @include('partials.navbar-employee')

    <main class="flex-grow-1">
      <div class="container py-3 py-md-4 app-shell page-pad">
        @include('partials.flash')
        @yield('content')
      </div>
    </main>

    <footer class="py-3">
      <div class="container app-shell text-center small text-muted page-pad">
        &copy; {{ date('Y') }} Staff Sale Queue
      </div>
    </footer>
  </div>

  @include('partials.toast')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  @stack('scripts')
</body>
</html>
