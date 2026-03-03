@extends('layouts.employee')

@section('title', $event->name)

@push('styles')
  <style>
    .event-thumb-box {
      border: 1px solid #d8dfec;
      border-radius: .7rem;
      overflow: hidden;
      background: #f5f7fc;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #8b97b0;
      font-size: .86rem;
      height: 100%;
    }

    .event-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .event-desc-card {
      border: 1px solid #d8dfec;
      background: #f7f9fd;
      border-radius: .72rem;
      padding: .8rem .9rem;
      min-height: 140px;
    }

    .event-desc-content {
      min-height: 0;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .event-desc-rich {
      color: #4f5b73;
      line-height: 1.58;
      font-size: .95rem;
      max-height: 76px;
      overflow: hidden;
      position: relative;
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    .event-desc-rich p {
      margin: 0 0 .35rem;
    }

    .event-desc-rich p:last-child {
      margin-bottom: 0;
    }

    .event-desc-rich ul,
    .event-desc-rich ol {
      margin: 0;
      padding-left: 1.1rem;
    }

    .event-desc-rich li {
      margin: 0 0 .2rem;
    }

    .event-desc-rich.is-clamped::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      height: 24px;
      background: linear-gradient(to bottom, rgba(247, 249, 253, 0), rgba(247, 249, 253, 1));
      pointer-events: none;
    }

    .event-show-more {
      margin-top: .35rem;
      margin-left: auto;
      padding: 0;
      border: 0;
      background: transparent;
      color: #3751bd;
      font-size: .86rem;
      font-weight: 600;
      line-height: 1.2;
      text-decoration: none;
      width: fit-content;
      display: inline-flex;
      align-items: center;
      gap: .35rem;
    }

    .event-show-more:hover,
    .event-show-more:focus {
      color: #2b3f98;
      text-decoration: underline;
    }

    .event-ticket-action {
      display: flex;
      justify-content: flex-start;
    }

    .event-media-wrap {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

    .event-media-thumb {
      width: 100%;
    }

    .event-media-desc {
      min-width: 0;
      flex: 1 1 auto;
    }

    .batch-color-dot {
      width: 12px;
      height: 12px;
      border-radius: 999px;
      display: inline-block;
      border: 1px solid rgba(0,0,0,.12);
      flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
      .event-ticket-action .btn {
        width: 100%;
      }

      .event-desc-rich {
        max-height: 96px;
      }
    }

    @media (min-width: 768px) {
      .event-media-wrap {
        flex-direction: row;
      }

      .event-media-thumb {
        width: 220px;
        flex: 0 0 220px;
      }
    }
  </style>
@endpush

@section('content')
  <div class="mb-3 d-flex justify-content-between align-items-start gap-2">
    <div class="min-w-0">
      <h1 class="h4 fw-semibold mb-1 text-truncate">{{ $event->name }}</h1>
      <div class="text-muted small">Pilih batch yang tersedia untuk lanjut register.</div>
    </div>

    <a href="{{ route('employee.events.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold flex-shrink-0">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3 p-md-4">
      <div class="row g-3 align-items-center">
        <div class="col-12 col-lg">
          <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-light border text-dark d-inline-flex align-items-center gap-1 px-2 py-1">
              <i class="bi bi-calendar3"></i> {{ $event->event_date }}
            </span>
            <span class="badge text-bg-light border text-dark d-inline-flex align-items-center gap-1 px-2 py-1">
              <i class="bi bi-clock"></i> {{ $event->start_time }} - {{ $event->end_time }}
            </span>
            <span class="badge text-bg-light border text-dark d-inline-flex align-items-center gap-1 px-2 py-1">
              <i class="bi bi-geo-alt"></i> {{ $event->location ?: 'No location' }}
            </span>
            @if($event->break_start && $event->break_end)
              <span class="badge text-bg-light border text-dark d-inline-flex align-items-center gap-1 px-2 py-1">
                <i class="bi bi-cup-hot"></i> Break {{ $event->break_start }} - {{ $event->break_end }}
              </span>
            @endif
          </div>
        </div>

        <div class="col-12 col-lg-auto">
          <div class="event-ticket-action">
            <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}" class="btn btn-dark fw-semibold">
              <i class="bi bi-qr-code-scan"></i> Check your ticket
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @php
    $thumb = $event->thumbnail ?? null;
    $thumbUrl = $thumb ? asset('storage/'.$thumb) : null;
  @endphp

  @if($thumbUrl || !empty($event->description))
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body p-3 p-md-4">
        <div class="event-media-wrap">
          <div class="event-media-thumb">
            <div class="ratio" style="--bs-aspect-ratio: 62.5%;">
              <div class="event-thumb-box" id="thumbWrap">
                @if($thumbUrl)
                  <img
                    src="{{ $thumbUrl }}"
                    alt="thumbnail"
                    onerror="document.getElementById('thumbWrap').innerHTML = `<span>No thumbnail</span>`;"
                  >
                @else
                  <span>No thumbnail</span>
                @endif
              </div>
            </div>
          </div>

          <div class="event-media-desc">
            <div class="event-desc-card h-100">
              <div class="event-desc-content">
                @if(!empty($event->description))
                  <div class="event-desc-rich is-clamped" id="eventDescClamp">{!! $event->description !!}</div>
                  <button
                    type="button"
                    class="event-show-more"
                    data-bs-toggle="modal"
                    data-bs-target="#eventDescModal"
                    data-desc-source="#eventDescFull"
                  >
                    <i class="bi bi-chevron-down"></i>
                    <span>Show more</span>
                  </button>
                  <template id="eventDescFull">{!! $event->description !!}</template>
                @else
                  <div class="small fst-italic text-muted">Belum ada deskripsi untuk event ini.</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  <div class="modal fade" id="eventDescModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-2">
          <div id="eventDescModalBody" class="event-desc-rich" style="max-height:none;overflow:visible;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h2 class="h6 fw-semibold mb-0">Pilih Batch</h2>
      <div class="text-muted small">Available batches only</div>
    </div>

    <span class="badge text-bg-light border">Total: {{ $batches->count() }}</span>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="list-group list-group-flush">
      @foreach($batches as $b)
        @php
          $disabled = ($b->status !== 'upcoming' || $b->remaining_slots <= 0);

          $statusBadgeClass = match($b->status) {
            'upcoming' => 'text-bg-primary',
            'ongoing'  => 'text-bg-warning',
            'done'     => 'text-bg-secondary',
            default    => 'text-bg-secondary',
          };

          $slotBadgeClass = ($b->remaining_slots <= 0) ? 'text-bg-danger' : 'text-bg-success';

          $c = $b->color_code ?? '#CBD5E1';
        @endphp

        <div class="list-group-item py-3 {{ $disabled ? 'opacity-50' : '' }}" style="border-left: 6px solid {{ $c }};">
          <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="flex-grow-1 min-w-0">
              <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="batch-color-dot" style="background: {{ $c }};"></span>

                <div class="fw-semibold">
                  Batch {{ $b->batch_number }}
                  <span class="fw-normal text-muted">({{ $b->start_time }} - {{ $b->end_time }})</span>
                </div>

                <span class="badge rounded-pill {{ $statusBadgeClass }}">{{ strtoupper($b->status) }}</span>
                <span class="badge rounded-pill {{ $slotBadgeClass }}">Slot: {{ $b->remaining_slots }} / {{ $b->capacity }}</span>
              </div>

              <div class="text-muted small mt-1">
                @if($b->status !== 'upcoming')
                  Batch ini belum bisa dipilih.
                @elseif($b->remaining_slots <= 0)
                  Slot batch sudah penuh.
                @else
                  Siap dipilih, lanjut ke form registrasi.
                @endif
              </div>
            </div>

            <div class="text-end flex-shrink-0">
              @if($b->status !== 'upcoming')
                <span class="badge text-bg-light border">Not available</span>
              @elseif($b->remaining_slots <= 0)
                <span class="badge text-bg-danger">Full</span>
              @else
                <a href="{{ route('employee.events.registerForm', ['event' => $event->id, 'batch' => $b->id]) }}" class="btn btn-dark fw-semibold">
                  Pilih
                </a>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  @if(session('already_registered'))
    <div class="modal fade" id="alreadyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body">
            <div class="fw-semibold mb-2">
              {{ session('already_registered_message') ?? 'Kamu sudah terdaftar.' }}
            </div>
            <div class="text-muted small">Silakan cek ticket kamu, atau kembali ke daftar batch.</div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}" class="btn btn-dark fw-semibold">
              Check your ticket
            </a>
            <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
              Back
            </button>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const el = document.getElementById('alreadyModal');
          if (el && window.bootstrap) new bootstrap.Modal(el).show();
        });
      </script>
    @endpush
  @endif
@endsection

@push('scripts')
  <script>
    (function () {
      const clamp = document.getElementById('eventDescClamp');
      if (clamp) {
        const btn = document.querySelector('.event-show-more');
        if (btn && clamp.scrollHeight <= clamp.clientHeight + 2) {
          clamp.classList.remove('is-clamped');
          btn.classList.add('d-none');
        }
      }
    })();

    (function () {
      const modal = document.getElementById('eventDescModal');
      const body = document.getElementById('eventDescModalBody');
      if (!modal || !body) return;

      modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;
        const sourceSelector = btn.getAttribute('data-desc-source');
        const source = sourceSelector ? document.querySelector(sourceSelector) : null;
        body.innerHTML = source ? source.innerHTML : '<div class="text-muted small">No description.</div>';
      });
    })();
  </script>
@endpush
