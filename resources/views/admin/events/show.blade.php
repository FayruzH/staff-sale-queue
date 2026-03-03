@extends('layouts.admin')

@section('title', 'Event Detail - ' . $event->name)

@push('styles')
  <style>
    .event-thumb-box {
      border: 1px solid #d8dfec;
      border-radius: .72rem;
      overflow: hidden;
      background: #f4f6fb;
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
      border-radius: .72rem;
      background: #f7f9fd;
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
      font-size: .95rem;
      line-height: 1.58;
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
      margin-top: auto;
      margin-left: auto;
      align-self: flex-end;
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

    .event-info-badges {
      display: flex;
      flex-wrap: wrap;
      gap: .45rem;
      align-items: center;
    }

    .event-info-badges .badge {
      display: inline-flex;
      align-items: center;
      min-height: 30px;
      padding: .34rem .68rem;
      font-size: .82rem;
      line-height: 1.1;
    }

    @media (max-width: 767.98px) {
      .event-desc-rich {
        max-height: 96px;
      }

      .event-info-badges .badge {
        font-size: .79rem;
      }
    }
  </style>
@endpush

@section('content')

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Event Detail</h1>
      <div class="text-muted small">
        <span class="fw-semibold">{{ $event->name }}</span>
        - Code: {{ $event->code }}
        - Location: {{ $event->location ?: '-' }}
        - Date: {{ $event->event_date }}
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
        <i class="bi bi-arrow-left"></i> Back
      </a>

      <a href="{{ route('admin.attendance.index', $event) }}" class="btn btn-dark btn-sm fw-semibold">
        <i class="bi bi-qr-code-scan"></i> Attendance
      </a>

      <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary btn-sm fw-semibold">
        <i class="bi bi-pencil"></i> Edit
      </a>

      <a href="{{ route('display.event', $event) }}" target="_blank" class="btn btn-outline-primary btn-sm fw-semibold">
        <i class="bi bi-box-arrow-up-right"></i> Display
      </a>
    </div>
  </div>

  {{-- Thumbnail + Description --}}
  @php
    $thumb = $event->thumbnail ?? null;
    $thumbUrl = $thumb ? asset('storage/'.$thumb) : null;
  @endphp

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-stretch">
        <div class="col-12 col-md-4 col-lg-3">
          <div class="ratio" style="--bs-aspect-ratio: 62.5%;">
            <div class="event-thumb-box" id="thumbWrap">
              @if($thumbUrl)
                <img
                  src="{{ $thumbUrl }}"
                  alt="Thumbnail"
                  onerror="document.getElementById('thumbWrap').innerHTML = `<span>No thumbnail</span>`;"
                >
              @else
                <span>No thumbnail</span>
              @endif
            </div>
          </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9">
          <div class="event-desc-card h-100">
            <div class="small text-muted mb-1">Description</div>
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
                  <span>Read more</span>
                </button>
                <template id="eventDescFull">{!! $event->description !!}</template>
              @else
                <div class="small fst-italic text-muted">Belum ada deskripsi untuk event ini.</div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <hr class="my-3">

      <div class="event-info-badges">
        @php
          $statusClass = match($event->status) {
            'draft'  => 'text-bg-warning text-dark',
            'active' => 'text-bg-success',
            'ended'  => 'text-bg-dark',
            default  => 'text-bg-light border',
          };
        @endphp

        <span class="badge rounded-pill {{ $statusClass }}">
          {{ strtoupper($event->status) }}
        </span>

        <span class="badge rounded-pill {{ $event->is_auto_mode ? 'text-bg-success' : 'text-bg-secondary' }}">
          Auto: {{ $event->is_auto_mode ? 'ON' : 'OFF' }}
        </span>

        <span class="badge rounded-pill text-bg-light border text-dark">
          {{ $event->start_time }} - {{ $event->end_time }}
        </span>

        <span class="badge rounded-pill text-bg-light border text-dark">
          Location: {{ $event->location ?: '-' }}
        </span>

        <span class="badge rounded-pill text-bg-light border text-dark">
          Break: {{ $event->break_start ?? '-' }} - {{ $event->break_end ?? '-' }}
        </span>
      </div>
    </div>
  </div>

  <div class="modal fade" id="eventDescModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-semibold">Event Description</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-2">
          <div id="eventDescModalBody" class="event-desc-rich" style="max-height:none;overflow:visible;"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Summary --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-6 col-lg-3">
          <div class="text-muted small">Batch Duration</div>
          <div class="fw-semibold">{{ $event->batch_duration_min }} min</div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="text-muted small">Gap Between Batch</div>
          <div class="fw-semibold">{{ $event->gap_min }} min</div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="text-muted small">Capacity / Batch</div>
          <div class="fw-semibold">{{ $event->capacity_per_batch }}</div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="text-muted small">Total Batches</div>
          <div class="fw-semibold">{{ $event->batches->count() }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2">

        <form method="POST" action="{{ route('admin.events.reset', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-warning fw-semibold" type="submit">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Demo
          </button>
        </form>

        <form method="POST" action="{{ route('admin.events.generateBatches', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-outline-secondary fw-semibold" type="submit">
            <i class="bi bi-grid-3x3-gap"></i> Generate Batches
          </button>
        </form>

        <form method="POST" action="{{ route('admin.events.start', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-success fw-semibold" type="submit" {{ $event->status !== 'draft' ? 'disabled' : '' }}>
            <i class="bi bi-play-fill"></i> Start Event
          </button>
        </form>

        <form method="POST" action="{{ route('admin.events.end', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-danger fw-semibold" type="submit" {{ $event->status !== 'active' ? 'disabled' : '' }}>
            <i class="bi bi-stop-fill"></i> End Event
          </button>
        </form>

        <form method="POST" action="{{ route('admin.events.autoMode', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-outline-secondary fw-semibold" type="submit">
            <i class="bi bi-toggles"></i> Auto Mode: {{ $event->is_auto_mode ? 'ON' : 'OFF' }}
          </button>
        </form>

        <button type="button"
                class="btn btn-outline-danger fw-semibold"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal">
          <i class="bi bi-trash3"></i> Delete
        </button>

      </div>
    </div>
  </div>

  {{-- Batches --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
    <div class="h6 fw-semibold mb-0">Batches</div>
    <div class="text-muted small">Total: {{ $event->batches->count() }}</div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      @if($event->batches->isEmpty())
        <div class="text-muted">No batches yet.</div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:70px;">#</th>
                <th style="width:180px;">Time</th>
                <th style="width:110px;">Capacity</th>
                <th style="width:130px;">Status</th>
                <th style="width:260px;">Color</th>
                <th style="width:240px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($event->batches as $b)
                @php
                  $bStatusClass = match($b->status) {
                    'upcoming' => 'text-bg-primary',
                    'running'  => 'text-bg-success',
                    'done'     => 'text-bg-secondary',
                    default    => 'text-bg-light border',
                  };
                @endphp

                <tr>
                  <td class="fw-semibold">{{ $b->batch_number }}</td>
                  <td>{{ $b->start_time }} - {{ $b->end_time }}</td>
                  <td>{{ $b->capacity }}</td>

                  <td>
                    <span class="badge rounded-pill {{ $bStatusClass }}">
                      {{ strtoupper($b->status) }}
                    </span>
                  </td>

                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <span class="d-inline-block rounded-2 border"
                            style="width:16px;height:16px;background:{{ $b->color_code }};"></span>

                      <span class="badge rounded-pill"
                            style="background: {{ $b->color_code }}; color: #fff;">
                        Batch {{ $b->batch_number }}
                      </span>

                      <span class="text-muted small">{{ $b->color_code }}</span>
                    </div>
                  </td>

                  <td>
                    @if($event->status !== 'active')
                      <span class="text-muted small">Start event dulu</span>
                    @else
                      <div class="d-flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.batches.start', $b) }}">
                          @csrf
                          <button class="btn btn-outline-primary btn-sm fw-semibold" type="submit"
                                  {{ $b->status !== 'upcoming' ? 'disabled' : '' }}>
                            Start Batch
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.batches.complete', $b) }}">
                          @csrf
                          <button class="btn btn-outline-success btn-sm fw-semibold" type="submit"
                                  {{ $b->status !== 'running' ? 'disabled' : '' }}>
                            Complete
                          </button>
                        </form>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- Delete Modal --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body">
          <div class="fw-semibold mb-2">Confirm Delete</div>
          <div class="text-muted small">
            Hapus event:
            <div class="mt-2">
              <span class="fw-semibold text-dark">{{ $event->name }}</span>
            </div>
          </div>

          <div class="alert alert-warning small mt-3 mb-0">
            Semua batch & ticket terkait akan ikut terhapus.
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger fw-semibold">Yes, Delete</button>
          </form>

          <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

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
