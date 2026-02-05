<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <title>@yield('title', 'Staff Sale Queue')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <style>
    /* Mobile-first: tetap slim tapi ga kekecilan */
    .app-shell { max-width: 560px; }

    @media (min-width: 768px) { .app-shell { max-width: 760px; } }
    @media (min-width: 992px) { .app-shell { max-width: 980px; } }

    .page-pad { padding-left: 12px; padding-right: 12px; }
    .nav-shadow { box-shadow: 0 6px 18px rgba(0,0,0,.08); }
  </style>

  @stack('styles')
</head>

@include('partials.toast')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<body class="bg-light">

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
