@extends('layouts.employee')

@section('title', 'My Ticket')

@push('styles')
  <style>
    .ticket-accent {
      border-left: 6px solid var(--ticket-color);
    }

    .ticket-meta dt {
      color: #64748b;
      font-size: .82rem;
      margin-bottom: .2rem;
    }

    .ticket-meta dd {
      margin-bottom: .7rem;
      font-weight: 600;
    }

    .ticket-queue-box {
      background: rgba(0,0,0,.03);
      border: 2px solid var(--ticket-color);
      border-radius: .8rem;
      padding: 1rem;
    }

    .ticket-qr-wrap {
      border: 2px dashed var(--ticket-color);
      border-radius: .8rem;
      background: #f8fafc;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: .6rem;
      min-height: 250px;
    }

    .ticket-thumb-box {
      border: 1px solid #d8dfec;
      border-radius: .65rem;
      overflow: hidden;
      background: #f4f6fb;
      color: #8f9bb2;
      font-size: .84rem;
      min-height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .ticket-thumb-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .ticket-media-wrap {
      display: flex;
      flex-direction: column;
      gap: .5rem;
      align-items: stretch;
    }

    .ticket-media-thumb {
      width: 100%;
    }

    .ticket-media-desc {
      min-width: 0;
      flex: 1 1 auto;
    }

    .ticket-desc {
      color: #4f5b73;
      font-size: .94rem;
      line-height: 1.58;
      margin: 0;
      max-height: 58px;
      overflow: hidden;
      position: relative;
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    .ticket-desc p {
      margin: 0 0 .35rem;
    }

    .ticket-desc p:last-child {
      margin-bottom: 0;
    }

    .ticket-desc ul,
    .ticket-desc ol {
      margin: 0 0 .35rem;
      padding-left: 1.1rem;
    }

    .ticket-desc li {
      margin-bottom: .1rem;
    }

    .ticket-desc-box {
      border: 1px solid #d8dfec;
      background: #f7f9fd;
      border-radius: .65rem;
      padding: .6rem .7rem;
      min-height: 112px;
      display: flex;
      flex-direction: column;
    }

    .ticket-desc-content {
      min-height: 0;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .ticket-desc.is-clamped::after {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      height: 22px;
      background: linear-gradient(to bottom, rgba(247, 249, 253, 0), rgba(247, 249, 253, 1));
      pointer-events: none;
    }

    .ticket-show-more {
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

    .ticket-show-more:hover,
    .ticket-show-more:focus {
      color: #2b3f98;
      text-decoration: underline;
    }

    @media (max-width: 991.98px) {
      .ticket-page {
        padding-bottom: 90px;
      }

      .ticket-mobile-sticky {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1030;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(6px);
        border-top: 1px solid rgba(0,0,0,.1);
      }
    }

    @media (min-width: 768px) {
      .ticket-media-wrap {
        flex-direction: row;
      }

      .ticket-media-thumb {
        width: 140px;
        flex: 0 0 140px;
      }
    }
  </style>
@endpush

@section('content')

@php
  // Gunakan "-" sebagai separator utama karena sebagian scanner menghapus "|".
  $qrQueue = str_replace('-', '', strtoupper((string) $registration->queue_number)); // B01001
  $qrNip = preg_replace('/[^A-Za-z0-9]/', '', (string) $registration->employee_identifier);
  $qrName = preg_replace('/[^A-Za-z0-9]/', '', (string) $registration->employee_name);
  $qrPayload = $registration->event_id
    . '-' . $qrQueue
    . '-' . $qrNip
    . '-' . $qrName;

  $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
    ->size(220)->margin(1)->generate($qrPayload);

  $qrSvgBig = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
    ->size(320)->margin(1)->generate($qrPayload);

  $batchColor = $registration->batch->color_code ?? '#0d6efd';

  $thumb = !empty($registration->event->thumbnail)
    ? asset('storage/'.$registration->event->thumbnail)
    : null;

  $desc = $registration->event->description ?? null;
@endphp

<div class="ticket-page" style="--ticket-color: {{ $batchColor }};">
<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
  <div class="min-w-0">
    <h1 class="h4 fw-semibold mb-1">My Ticket</h1>
    <div class="text-muted small">
      {{ $registration->event->name }} - {{ $registration->event->event_date }} - {{ $registration->event->location ?: 'No location' }}
    </div>
  </div>

  <a class="btn btn-outline-secondary btn-sm fw-semibold flex-shrink-0" href="{{ route('employee.events.index') }}">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

@if($thumb || !empty($desc))
  <div class="card border-0 shadow-sm mb-3" style="--ticket-color: {{ $batchColor }};">
    <div class="card-body p-3">
      <div class="border rounded-3 bg-body-tertiary p-2">
        <div class="ticket-media-wrap">
          <div class="ticket-media-thumb">
            <div class="ratio" style="--bs-aspect-ratio: 62.5%;">
              <div class="ticket-thumb-box">
                @if($thumb)
                  <img
                    src="{{ $thumb }}"
                    onerror="this.closest('.ticket-thumb-box').innerHTML='No thumbnail';"
                    alt="thumbnail"
                  >
                @else
                  <span>No thumbnail</span>
                @endif
              </div>
            </div>
          </div>

          <div class="ticket-media-desc">
            <div class="ticket-desc-box">
              <div class="ticket-desc-content">
                @if(!empty($desc))
                  <div class="ticket-desc is-clamped" id="ticketDescClamp">{!! $desc !!}</div>
                  <button
                    type="button"
                    class="ticket-show-more"
                    data-bs-toggle="modal"
                    data-bs-target="#ticketDescModal"
                    data-desc-source="#ticketDescFull"
                  >
                    <i class="bi bi-chevron-down"></i>
                    <span>Show more</span>
                  </button>
                  <template id="ticketDescFull">{!! $desc !!}</template>
                @else
                  <p class="ticket-desc">Tidak ada deskripsi event.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endif

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm h-100 ticket-accent">
      <div class="card-body p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
          <div>
            <div class="text-muted small">Ticket</div>
            <div class="fw-semibold">{{ $registration->employee_name }}</div>
          </div>
          <span class="badge rounded-pill" style="background: var(--ticket-color); color: #fff;">Batch {{ $registration->batch->batch_number }}</span>
        </div>

        <dl class="ticket-meta mb-3">
          <dt>NIP / ID</dt>
          <dd>{{ $registration->employee_identifier }}</dd>

          <dt>Batch Time</dt>
          <dd>{{ $registration->batch->start_time }} - {{ $registration->batch->end_time }}</dd>

          <dt>Event Time</dt>
          <dd>{{ $registration->event->start_time }} - {{ $registration->event->end_time }}</dd>

          <dt>Location</dt>
          <dd>{{ $registration->event->location ?: '-' }}</dd>
        </dl>

        <div class="ticket-queue-box mb-3">
          <div class="text-muted small">Queue Number</div>
          <div class="display-5 fw-bold mb-1">{{ $registration->queue_number }}</div>
          <div class="text-muted small">Datang sesuai batch. Idealnya 5 menit sebelum mulai.</div>
        </div>

        <div class="border rounded-3 p-3 bg-light small">
          <div class="text-muted">Now Serving: <span id="lsNow" class="fw-semibold">-</span></div>
          <div class="text-muted">Your Batch: <span id="lsYour" class="fw-semibold">-</span></div>
          <div class="text-muted">Status: <span id="lsStatus" class="fw-semibold">-</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5 d-none d-lg-block">
    <div class="card border-0 shadow-sm ticket-accent">
      <div class="card-body p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
          <div>
            <div class="text-muted small">QR Check-in</div>
            <div class="fw-semibold">Scan saat dipanggil</div>
          </div>
          <span class="badge text-bg-primary rounded-pill">SCAN</span>
        </div>

        <div class="ticket-qr-wrap">{!! $qrSvg !!}</div>

        <div class="d-grid mt-3">
          <button type="button" class="btn btn-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#qrModal">
            <i class="bi bi-qr-code-scan"></i> Show QR
          </button>
        </div>

        <div class="alert alert-warning small mt-3 mb-0 py-2">
          Simpan halaman ini sampai selesai check-in.
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ticketDescModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <div id="ticketDescModalBody" class="ticket-desc" style="max-height:none;overflow:visible;"></div>
      </div>
    </div>
  </div>
</div>

<div class="ticket-mobile-sticky d-lg-none">
  <div class="container app-shell page-pad py-2">
    <div class="d-flex align-items-center gap-2">
      <div class="flex-grow-1 min-w-0">
        <div class="small text-muted lh-1">Queue</div>
        <div class="fw-semibold lh-1 mt-1">{{ $registration->queue_number }}</div>
        <div class="small mt-1">
          <span class="text-muted">Status:</span>
          <span id="lsStatusMini" class="fw-semibold text-muted">-</span>
        </div>
      </div>

      <button type="button" class="btn btn-dark fw-semibold flex-shrink-0"
              data-bs-toggle="modal" data-bs-target="#qrModal">
        <i class="bi bi-qr-code-scan"></i> Show QR
      </button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="--ticket-color: {{ $batchColor }};">
      <div class="modal-body">
        <div class="d-flex justify-content-between align-items-start gap-2">
          <div>
            <div class="text-muted small fw-semibold">QUEUE NUMBER</div>
            <div class="display-6 fw-bold">{{ $registration->queue_number }}</div>
            <div class="small text-muted mt-1">
              NIP: <span class="fw-semibold text-dark">{{ $registration->employee_identifier }}</span> |
              Nama: <span class="fw-semibold text-dark">{{ $registration->employee_name }}</span> |
              Batch: <span class="fw-semibold">{{ $registration->batch->batch_number }}</span>
            </div>
          </div>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-center" style="border: 2px dashed var(--ticket-color);">
          {!! $qrSvgBig !!}
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const liveUrl = "{{ route('employee.ticket.live', $registration) }}";

  const els = {
    now: document.getElementById('lsNow'),
    your: document.getElementById('lsYour'),
    status: document.getElementById('lsStatus'),
    statusMini: document.getElementById('lsStatusMini'),
  };

  function computeTicketStatus(nowServingText, yourBatchText) {
    const ns = parseInt((nowServingText || '').replace(/\D/g,''), 10);
    const yb = parseInt((yourBatchText || '').replace(/\D/g,''), 10);

    if (!yb) return '-';
    if (!ns) return 'WAITING';
    if (ns === yb) return 'RUNNING';
    if (ns < yb) return 'WAITING';
    return 'ENDED';
  }

  function applyStatusStyle(status) {
    const cls = status === 'RUNNING'
      ? 'text-success'
      : status === 'ENDED'
        ? 'text-secondary'
        : status === 'WAITING'
          ? 'text-warning'
          : 'text-muted';

    els.status.className = 'fw-semibold ' + cls;
    if (els.statusMini) {
      els.statusMini.className = 'fw-semibold ' + cls;
    }
  }

  async function syncLive() {
    try {
      const res = await fetch(liveUrl, { cache: 'no-store' });
      const j = await res.json();

      const nowServing = j.running_batch?.batch_number
        ? `Batch ${j.running_batch.batch_number}`
        : '-';

      const yourBatch = j.your_batch?.batch_number
        ? `Batch ${j.your_batch.batch_number}`
        : '-';

      els.now.textContent = nowServing;
      els.your.textContent = yourBatch;

      const st = computeTicketStatus(nowServing, yourBatch);
      els.status.textContent = st;
      if (els.statusMini) {
        els.statusMini.textContent = st;
      }
      applyStatusStyle(st);
    } catch(e) {
      // silent fail
    }
  }

  syncLive();
  setInterval(syncLive, 4000);

  (function () {
    const clamp = document.getElementById('ticketDescClamp');
    if (clamp) {
      const btn = document.querySelector('.ticket-show-more');
      if (btn && clamp.scrollHeight <= clamp.clientHeight + 2) {
        clamp.classList.remove('is-clamped');
        btn.classList.add('d-none');
      }
    }
  })();

  (function () {
    const modal = document.getElementById('ticketDescModal');
    const body = document.getElementById('ticketDescModalBody');
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

@endsection
