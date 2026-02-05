<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <title>@yield('title', 'Admin - Staff Sale Queue')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


  <style>
    /* Admin boleh lebih lebar dari employee */
    .admin-shell { max-width: 1100px; }

    /* biar halaman ga “nempel” ke tepi di mobile */
    .page-pad { padding-left: 12px; padding-right: 12px; }

    /* sticky navbar shadow halus */
    .nav-shadow { box-shadow: 0 6px 18px rgba(0,0,0,.08); }
  </style>

  @stack('styles')
</head>

@include('partials.toast')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<body class="bg-light">

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
