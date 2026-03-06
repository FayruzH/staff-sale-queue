@extends('layouts.employee')

@section('title', 'Staff Sale Events')

@push('styles')
  <style>
    :root {
      --ev-bg: #f0f4fb;
      --ev-surface: #ffffff;
      --ev-surface-soft: #e8eef8;
      --ev-border: rgba(30, 80, 200, 0.12);
      --ev-border-strong: rgba(30, 80, 220, 0.38);
      --ev-text: #0d1a3a;
      --ev-muted: #6278a8;
      --ev-accent: #1a4fd6;
      --ev-accent-2: #3b82f6;
      --ev-success: #059669;
    }

    .events-page {
      max-width: 860px;
      margin-inline: auto;
      color: var(--ev-text);
    }

    .events-head {
      margin-bottom: 1rem;
      border-bottom: 1px solid var(--ev-border);
      padding-bottom: 0.8rem;
    }

    .events-head-label {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      font-weight: 600;
      color: var(--ev-accent);
      margin-bottom: 0.35rem;
    }

    .events-head-title {
      font-size: clamp(1.6rem, 3.8vw, 2.5rem);
      line-height: 1.05;
      letter-spacing: 0.02em;
      font-weight: 700;
      margin: 0 0 0.25rem 0;
      text-transform: uppercase;
    }

    .events-head-sub {
      color: var(--ev-muted);
      margin: 0;
      font-size: 0.9rem;
    }

    .events-filter {
      background: var(--ev-surface);
      border: 1px solid var(--ev-border);
      border-radius: 12px;
      padding: 0.9rem;
      margin-bottom: 1rem;
    }

    .events-filter .form-label {
      font-size: 0.68rem;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      font-weight: 600;
      color: var(--ev-muted);
      margin-bottom: 0.35rem;
    }

    .events-filter .form-control,
    .events-filter .form-select {
      border-color: var(--ev-border);
      background: var(--ev-surface-soft);
      min-height: 40px;
      color: var(--ev-text);
    }

    .events-filter .form-control:focus,
    .events-filter .form-select:focus {
      border-color: var(--ev-accent);
      box-shadow: none;
    }

    .events-count {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      font-weight: 600;
      color: var(--ev-muted);
      margin-bottom: 0.8rem;
    }

    .events-list {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.95rem;
    }

    .event-card {
      background: var(--ev-surface);
      border: 1px solid var(--ev-border);
      border-radius: 14px;
      overflow: hidden;
      width: 100%;
      max-width: 860px;
      margin-inline: auto;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .event-card:hover {
      transform: translateY(-2px);
      border-color: var(--ev-border-strong);
      box-shadow: 0 10px 24px rgba(30, 80, 220, 0.08);
    }

    .event-card-top {
      height: 3px;
      background: linear-gradient(90deg, var(--ev-accent), var(--ev-accent-2));
      opacity: 0;
      transition: opacity .2s ease;
    }

    .event-card:hover .event-card-top {
      opacity: 1;
    }

    .event-card-body {
      padding: 1rem;
    }

    .event-head {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
      margin-bottom: 1.05rem;
    }

    .event-name {
      margin: 0;
      margin-bottom: .65rem;
      font-size: clamp(1.15rem, 2.2vw, 1.65rem);
      line-height: 1.1;
      letter-spacing: .02em;
      font-weight: 500;
      text-transform: uppercase;
    }

    .event-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.6rem;
      margin-top: 0.6rem;
    }

    .meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      border: 1px solid var(--ev-border);
      border-radius: 9px;
      padding: 0.3rem 0.58rem;
      background: var(--ev-surface-soft);
      color: var(--ev-muted);
      font-size: .8rem;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      border-radius: 999px;
      padding: .34rem .78rem;
      border: 1px solid rgba(5, 150, 105, 0.28);
      color: var(--ev-success);
      background: rgba(5, 150, 105, 0.1);
      font-size: 0.7rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      white-space: nowrap;
      align-self: flex-start;
    }

    .status-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--ev-success);
      box-shadow: 0 0 0 0 rgba(5, 150, 105, .45);
      animation: statusPulse 1.7s ease-in-out infinite;
    }

    .event-thumb-box {
      width: 100%;
      border: 1px solid var(--ev-border);
      border-radius: 10px;
      overflow: hidden;
      background: var(--ev-surface-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--ev-muted);
      font-size: .8rem;
      margin-top: .2rem;
    }

    .event-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .event-desc {
      margin-top: 1rem;
    }

    .event-desc-box {
      border: 1px solid var(--ev-border);
      border-radius: 10px;
      background: var(--ev-surface-soft);
      padding: .75rem .8rem;
      color: #4d6ca8;
      font-size: .92rem;
      line-height: 1.5;
    }

    .event-desc-text {
      margin: 0;
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    .event-desc-text.clamped {
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .event-desc-toggle {
      margin-top: .45rem;
      margin-left: auto;
      padding: 0;
      border: 0;
      background: transparent;
      color: #2b53d8;
      font-weight: 600;
      text-decoration: none;
      font-size: .9rem;
      display: flex;
      align-items: center;
      gap: .35rem;
      width: fit-content;
    }

    .event-desc-toggle:hover {
      text-decoration: underline;
    }

    .meta-pill-location i {
      color: #e34b7a;
    }

    .event-actions {
      border-top: 1px solid var(--ev-border);
      padding-top: 1rem;
      margin-top: 1rem;
      display: flex;
      gap: .75rem;
      flex-wrap: wrap;
    }

    .btn-batch {
      flex: 1 1 260px;
    }

    .btn-ticket {
      flex: 0 0 auto;
    }

    .empty-state {
      border: 1px dashed var(--ev-border);
      border-radius: 14px;
      padding: 2rem 1rem;
      background: var(--ev-surface);
      text-align: center;
      color: var(--ev-muted);
    }

    .empty-state h3 {
      margin: 0 0 .45rem 0;
      font-size: 1.2rem;
      color: var(--ev-text);
      font-weight: 700;
    }

    @keyframes statusPulse {
      0% { box-shadow: 0 0 0 0 rgba(5, 150, 105, .45); }
      70% { box-shadow: 0 0 0 5px rgba(5, 150, 105, 0); }
      100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
    }

    @media (max-width: 991.98px) {
      
    }

    @media (max-width: 575.98px) {
      .events-filter {
        padding: 0.75rem;
      }

      .event-card-body {
        padding: 1rem;
      }

      .event-name {
        font-size: 1.45rem;
      }

      .event-meta {
        gap: .5rem;
      }

      .meta-pill {
        font-size: .8rem;
      }

      .event-actions {
        flex-direction: column;
      }

      .btn-batch,
      .btn-ticket {
        width: 100%;
      }
    }
  </style>
@endpush

@section('content')
  @php
    $modalMsg = null;

    if (session('ticket_expired')) {
      $modalMsg = session('ticket_expired_message') ?? 'Ticket kamu sudah hangus.';
    } elseif (session('ticket_checked_in')) {
      $modalMsg = session('ticket_checked_in_message') ?? 'Ticket kamu sudah digunakan (check-in).';
    }
  @endphp

  <div class="events-page">
    <div class="events-head">
      <h1 class="events-head-title">Staff Sale Events</h1>
      <p class="events-head-sub">Pilih event untuk lihat batch yang tersedia.</p>
    </div>

    @if(($totalEvents ?? $events->count()) > 3)
      <div class="events-filter">
        <form id="eventsFilterForm" method="GET" action="{{ route('employee.events.index') }}" class="row g-2 align-items-end">
          <div class="col-12 col-md-8">
            <label class="form-label">Filter</label>
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              class="form-control"
              placeholder="Search name / location / code / date"
            >
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Sort</label>
            <select name="sort" class="form-select">
              <option value="nearest" @selected(request('sort', 'nearest') === 'nearest')>Nearest</option>
              <option value="farthest" @selected(request('sort') === 'farthest')>Farthest</option>
              <option value="name_az" @selected(request('sort') === 'name_az')>Name A-Z</option>
              <option value="name_za" @selected(request('sort') === 'name_za')>Name Z-A</option>
            </select>
          </div>
        </form>
      </div>
    @endif

    @if($events->count() === 0)
      <div class="empty-state">
        <h3>No Events Found</h3>
        <div>Coba ubah kata kunci pencarianmu atau cek lagi nanti.</div>
      </div>
    @else
      <div class="events-count">{{ $events->count() }} active events</div>

      <div class="events-list">
        @foreach($events as $event)
          @php
            $thumb = $event->thumbnail ?? null;
            $thumbUrl = $thumb ? asset('storage/'.$thumb) : null;
            $dateText = \Illuminate\Support\Carbon::parse($event->event_date)->format('d M Y');
            $startText = \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i');
            $endText = \Illuminate\Support\Carbon::parse($event->end_time)->format('H:i');
            $locationText = trim((string) ($event->location ?? ''));
          @endphp

          <article class="event-card">
            <div class="event-card-top"></div>

            <div class="event-card-body">
              <div class="event-head">
                <div>
                  <h2 class="event-name">{{ $event->name }}</h2>
                  <div class="event-meta">
                    <span class="meta-pill"><i class="bi bi-calendar3"></i> {{ $dateText }}</span>
                    <span class="meta-pill"><i class="bi bi-clock"></i> {{ $startText }} - {{ $endText }}</span>
                    <span class="meta-pill meta-pill-location">
                      <i class="bi bi-geo-alt-fill"></i>
                      {{ $locationText !== '' ? $locationText : 'No location' }}
                    </span>
                  </div>
                </div>

                <span class="status-pill"><span class="status-dot"></span> Active</span>
              </div>

              <div class="ratio mb-0" style="--bs-aspect-ratio: 30%;">
                <div class="event-thumb-box">
                  @if($thumbUrl)
                    <img
                      src="{{ $thumbUrl }}"
                      alt="thumbnail"
                      onerror="this.closest('.event-thumb-box').innerHTML = '<span>No thumbnail</span>';"
                    >
                  @else
                    <span>No thumbnail</span>
                  @endif
                </div>
              </div>

              <div class="event-desc">
                <div class="event-desc-box">
                  @if(!empty($event->description))
                    <div class="event-desc-text clamped" id="desc-{{ $event->id }}">
                      {!! $event->description !!}
                    </div>
                    <button type="button" class="event-desc-toggle" data-target="#desc-{{ $event->id }}">
                      <i class="bi bi-chevron-down"></i>
                      <span>Read More</span>
                    </button>
                  @else
                    <div class="small fst-italic text-muted">Belum ada deskripsi untuk event ini.</div>
                  @endif
                </div>
              </div>

              <div class="event-actions">
                <a href="{{ route('employee.events.show', $event) }}"
                   class="btn btn-primary btn-batch">
                  Lihat Batch
                </a>

                <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}"
                   class="btn btn-outline-secondary btn-ticket">
                  My Ticket
                </a>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>

  @if($modalMsg)
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body">
            <div class="fw-medium mb-2">{{ $modalMsg }}</div>
            <div class="text-muted small">Silakan kembali dan coba lagi.</div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
  <script>
    (function () {
      const filterForm = document.getElementById('eventsFilterForm');
      if (!filterForm) return;

      const qInput = filterForm.querySelector('input[name="q"]');
      const sortSelect = filterForm.querySelector('select[name="sort"]');
      let debounceTimer;

      const submitFilter = function () {
        filterForm.requestSubmit();
      };

      if (qInput) {
        qInput.addEventListener('input', function () {
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(submitFilter, 350);
        });
      }

      if (sortSelect) {
        sortSelect.addEventListener('change', submitFilter);
      }
    })();

    (function () {
      const clampedItems = document.querySelectorAll('.event-desc-text.clamped');
      clampedItems.forEach(function (el) {
        const parent = el.parentElement;
        const btn = parent ? parent.querySelector('.event-desc-toggle') : null;
        if (!btn) return;

        if (el.scrollHeight <= el.clientHeight + 2) {
          btn.classList.add('d-none');
        }
      });
    })();

    (function () {
      document.querySelectorAll('.event-desc-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
          const targetSelector = btn.getAttribute('data-target');
          if (!targetSelector) return;
          const desc = document.querySelector(targetSelector);
          if (!desc) return;

          const isExpanded = !desc.classList.contains('clamped');
          if (isExpanded) {
            desc.classList.add('clamped');
            btn.innerHTML = '<i class="bi bi-chevron-down"></i><span>Read More</span>';
          } else {
            desc.classList.remove('clamped');
            btn.innerHTML = '<i class="bi bi-chevron-up"></i><span>Read Less</span>';
          }
        });
      });
    })();

    (function () {
      const el = document.getElementById('ticketModal');
      if (el && window.bootstrap) {
        new bootstrap.Modal(el).show();
      }
    })();
  </script>
@endpush
