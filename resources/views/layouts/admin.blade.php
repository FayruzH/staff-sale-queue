<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light">
  <title>@yield('title', 'Admin - Staff Sale Queue')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <style>
    :root {
      --admin-bg: #eef2ff;
      --admin-surface: #ffffff;
      --admin-text: #1e1b4b;
      --admin-muted: #5b5f8a;
      --admin-border: #dbe4ff;
      --admin-shadow: 0 8px 22px rgba(79, 70, 229, 0.08);
    }

    body {
      background: linear-gradient(180deg, #f8f9ff 0%, #f1efff 48%, var(--admin-bg) 100%);
      color: var(--admin-text);
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    .admin-shell {
      max-width: 1120px;
      width: 100%;
    }

    .page-pad {
      padding-left: 12px;
      padding-right: 12px;
    }

    main {
      padding-top: 6px;
      padding-bottom: 12px;
    }

    .card {
      border: 1px solid var(--admin-border) !important;
      background: var(--admin-surface);
      box-shadow: var(--admin-shadow) !important;
      border-radius: 14px;
    }

    .text-muted {
      color: var(--admin-muted) !important;
    }

    footer {
      border-top: 1px solid var(--admin-border);
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
    @include('partials.navbar-admin')

    <main class="flex-grow-1">
      <div class="container admin-shell py-3 py-md-4 page-pad">
        @include('partials.flash')
        @yield('content')
      </div>
    </main>

    <footer class="py-3">
      <div class="container admin-shell text-center small text-muted page-pad">
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
