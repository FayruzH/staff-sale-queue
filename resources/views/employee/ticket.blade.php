@extends('layouts.employee')

@section('title', 'My Ticket')

@section('content')

  @php
    // Payload QR : event_id|queue_number
    $qrPayload = $registration->event_id . '|' . $registration->queue_number;

    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
      ->size(220)
      ->margin(1)
      ->generate($qrPayload);

    $qrSvgBig = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
      ->size(320)
      ->margin(1)
      ->generate($qrPayload);

    //  warna batch
    $batchColor = $registration->batch->color_code ?? '#0d6efd';
  @endphp

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">My Ticket</h1>
      <div class="text-muted small">
        <span class="badge text-bg-primary rounded-pill">
          Event: {{ $registration->event->name }}
        </span>
        <span class="ms-2">Date: {{ $registration->event->event_date }}</span>
      </div>
    </div>

    <a class="btn btn-outline-secondary btn-sm fw-semibold" href="{{ route('employee.events.index') }}">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <div class="row g-3">
    {{-- LEFT: Details --}}
    <div class="col-12 col-lg-7">
      <div class="card border-0 shadow-sm h-100"
           style="border-left: 6px solid {{ $batchColor }};">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="text-muted small">Ticket Details</div>
              <div class="fw-semibold">{{ $registration->employee_name }}</div>
            </div>

            <span class="badge text-bg-dark rounded-pill">QUEUE</span>
          </div>

          <hr class="my-3">

          <div class="row g-2 small">
            <div class="col-4 text-muted">NIP / ID</div>
            <div class="col-8 fw-semibold">{{ $registration->employee_identifier }}</div>

            <div class="col-4 text-muted">Batch</div>
            <div class="col-8 fw-semibold">
              {{-- badge warna batch --}}
              <span class="badge rounded-pill"
                    style="background: {{ $batchColor }}; color: #fff;">
                Batch {{ $registration->batch->batch_number }}
              </span>
              <span class="text-muted fw-normal ms-1">
                ({{ $registration->batch->start_time }} - {{ $registration->batch->end_time }})
              </span>
            </div>

            <div class="col-4 text-muted">Event Time</div>
            <div class="col-8 fw-semibold">
              {{ $registration->event->start_time }} - {{ $registration->event->end_time }}
            </div>
          </div>

          <div class="card bg-light mt-3"
               style="border: 4px solid {{ $batchColor }};">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
              <div>
                <div class="text-muted small">Queue Number</div>
                <div class="display-5 fw-bold mb-0">{{ $registration->queue_number }}</div>
              </div>
              <div class="text-muted small">
                Datang sesuai batch ya. Minimal 5 menit sebelum mulai biar smooth.
              </div>
            </div>
          </div>

          <div class="border rounded-3 p-3 bg-light small">
            <div class="text-muted">Now Serving: <span id="lsNow" class="fw-semibold">-</span></div>
            <div class="text-muted">Your Batch: <span id="lsYour" class="fw-semibold">-</span></div>
            <div class="text-muted">Status: <span id="lsStatus" class="fw-semibold">-</span></div>
          </div>


        </div>
      </div>
    </div>

    {{-- QR --}}
    <div class="col-12 col-lg-5">
      <div class="card border-0 shadow-sm"
           style="border-left: 6px solid {{ $batchColor }};">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="text-muted small">QR Code</div>
              <div class="fw-semibold">Untuk check-in</div>
            </div>

            <span class="badge text-bg-primary rounded-pill">SCAN</span>
          </div>

          <hr class="my-3">

          <div class="small text-muted">
            <div><span class="fw-semibold text-dark">Event:</span> {{ $registration->event->name }}</div>
            <div><span class="fw-semibold text-dark">Date:</span> {{ $registration->event->event_date }}</div>
            <div>
              <span class="fw-semibold text-dark">Batch:</span>
              <span class="badge rounded-pill ms-1"
                    style="background: {{ $batchColor }}; color:#fff;">
                {{ $registration->batch->batch_number }}
              </span>
            </div>
          </div>

          <div class="d-flex gap-3 align-items-center flex-wrap mt-3 p-3 bg-white border rounded-3">
            {{-- QR frame warna batch --}}
            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                 style="width:220px;height:220px;border: 3px dashed {{ $batchColor }};">
              {!! $qrSvg !!}
            </div>

            <div class="flex-grow-1">
              <div class="text-muted small fw-semibold">QUEUE NUMBER</div>
              <div class="h2 fw-bold mb-2">{{ $registration->queue_number }}</div>

              <div class="small text-muted">
                NIP: <span class="fw-semibold text-dark">{{ $registration->employee_identifier }}</span><br>
                Nama: <span class="fw-semibold text-dark">{{ $registration->employee_name }}</span><br>
                Batch: <span class="fw-semibold" style="color: {{ $batchColor }};">
                  {{ $registration->batch->batch_number ?? '-' }}
                </span>
              </div>

              <div class="d-grid mt-3">
                <button type="button"
                        class="btn btn-dark fw-semibold"
                        data-bs-toggle="modal"
                        data-bs-target="#qrModal">
                  <i class="bi bi-qr-code-scan"></i> Show QR
                </button>
              </div>

              <div class="text-muted small mt-2">
                Tip: “Show QR” buat mode fokus pas lagi scan.
              </div>
            </div>
          </div>

          <div class="alert alert-warning small mt-3 mb-0">
            Simpan halaman ini sampai selesai check-in.
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Focus QR --}}
  <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="text-muted small fw-semibold">QUEUE NUMBER</div>
              <div class="display-6 fw-bold">{{ $registration->queue_number }}</div>
              <div class="small text-muted mt-1">
                NIP: <span class="fw-semibold text-dark">{{ $registration->employee_identifier }}</span>
                • Nama: <span class="fw-semibold text-dark">{{ $registration->employee_name }}</span>
                • Batch:
                <span class="badge rounded-pill"
                      style="background: {{ $batchColor }}; color:#fff;">
                  {{ $registration->batch->batch_number }}
                </span>
              </div>
            </div>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          {{-- modal frame warna batch --}}
          <div class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-center"
               style="border: 3px dashed {{ $batchColor }};">
            {!! $qrSvgBig !!}
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
            Close
          </button>
        </div>
    </div>
  </div>

  <script>
    const liveUrl = "{{ route('employee.ticket.live', $registration) }}";

    const els = {
        now: document.getElementById('lsNow'),
        your: document.getElementById('lsYour'),
        status: document.getElementById('lsStatus'),
    };

    function computeTicketStatus(nowServingText, yourBatchText){

        const ns = parseInt((nowServingText || '').replace(/\D/g,''), 10);
        const yb = parseInt((yourBatchText || '').replace(/\D/g,''), 10);

        if (!yb) return '-';         // ga punya batch
        if (!ns) return 'WAITING';   // belum ada yang running

        if (ns === yb) return 'RUNNING';
        if (ns < yb) return 'WAITING';
        return 'ENDED';
    }


    async function syncLive(){
        try{
        const res = await fetch(liveUrl, { cache: 'no-store' });
        const j = await res.json();
        const s = els.status.textContent;
        els.status.className = 'fw-semibold ' + (s === 'RUNNING' ? 'text-success' : s === 'ENDED' ? 'text-secondary' : 'text-warning');


        const nowServing = j.running_batch?.batch_number
            ? `Batch ${j.running_batch.batch_number}`
            : '-';

        const yourBatch = j.your_batch?.batch_number
            ? `Batch ${j.your_batch.batch_number}`
            : '-';

        els.now.textContent = nowServing;
        els.your.textContent = yourBatch;
        els.status.textContent = computeTicketStatus(els.now.textContent, els.your.textContent);

        }catch(e){

        }
    }

    syncLive();
    setInterval(syncLive, 4000);
  </script>




@endsection
