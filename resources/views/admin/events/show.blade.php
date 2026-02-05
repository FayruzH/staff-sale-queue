@extends('layouts.admin')

@section('title', 'Event Detail - ' . $event->name)

@section('content')

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Event Detail</h1>
      <div class="text-muted small">
        <span class="fw-semibold">{{ $event->name }}</span>
        • Code: {{ $event->code }}
        • Date: {{ $event->event_date }}
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

  {{-- Event Summary --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="text-muted small">Status</div>
          @php
            $statusClass = match($event->status) {
              'draft'  => 'text-bg-secondary',
              'active' => 'text-bg-success',
              'ended'  => 'text-bg-dark',
              default  => 'text-bg-light border',
            };
          @endphp
          <span class="badge rounded-pill {{ $statusClass }}">{{ strtoupper($event->status) }}</span>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Auto Mode</div>
          <span class="badge rounded-pill {{ $event->is_auto_mode ? 'text-bg-success' : 'text-bg-secondary' }}">
            {{ $event->is_auto_mode ? 'ON' : 'OFF' }}
          </span>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Time</div>
          <div class="fw-semibold">{{ $event->start_time }} - {{ $event->end_time }}</div>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Break Window</div>
          <div class="fw-semibold">
            {{ $event->break_start ?? '-' }} - {{ $event->break_end ?? '-' }}
          </div>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Batch Duration</div>
          <div class="fw-semibold">{{ $event->batch_duration_min }} min</div>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Gap Between Batch</div>
          <div class="fw-semibold">{{ $event->gap_min }} min</div>
        </div>

        <div class="col-md-3">
          <div class="text-muted small">Capacity / Batch</div>
          <div class="fw-semibold">{{ $event->capacity_per_batch }}</div>
        </div>

        <div class="col-md-3">
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

        {{-- Reset Demo --}}
        <button
          class="btn btn-warning fw-semibold"
          data-action="{{ route('admin.events.reset', $event) }}"
          data-method="POST"
          data-confirm="Yes, Reset"
          data-confirm-class="btn-warning"
        >
          <i class="bi bi-arrow-counterclockwise"></i> Reset Demo
        </button>

        {{-- Generate Batches --}}
        <form method="POST" action="{{ route('admin.events.generateBatches', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-outline-secondary fw-semibold" type="submit">
            <i class="bi bi-grid-3x3-gap"></i> Generate Batches
          </button>
        </form>

        {{-- Start Event --}}
        <form method="POST" action="{{ route('admin.events.start', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-success fw-semibold"
                  type="submit"
                  {{ $event->status !== 'draft' ? 'disabled' : '' }}>
            <i class="bi bi-play-fill"></i> Start Event
          </button>
        </form>

        {{-- End Event --}}
        <form method="POST" action="{{ route('admin.events.end', $event) }}" class="d-inline">
          @csrf
          <button class="btn btn-danger fw-semibold"
                  type="submit"
                  {{ $event->status !== 'active' ? 'disabled' : '' }}>
            <i class="bi bi-stop-fill "></i> End Event
          </button>
        </form>

         {{-- ✅ Auto Mode Toggle --}}
        <form method="POST" action="{{ route('admin.events.autoMode', $event) }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-secondary fw-semibold" type="submit">
            <i class="bi bi-grid-3x3-gap"></i>
            Auto Mode: {{ $event->is_auto_mode ? 'ON' : 'OFF' }}
            </button>
        </form>

        {{-- ✅ Delete  --}}
            <button type="button" class="btn btn-outline-danger fw-semibold "
                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                    data-action="{{ route('admin.events.destroy', $event) }}"
                    data-name="{{ $event->name }}"
                    >
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

  @php
    $duplicateColors = $event->batches
      ->groupBy('color_code')
      ->filter(fn($g) => $g->count() > 1);
  @endphp

  @if($duplicateColors->isNotEmpty())
    <div class="alert alert-warning border-0 shadow-sm">
      <div class="fw-semibold mb-1">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Duplicate batch colors detected
      </div>
      <div class="text-muted small">
        Beberapa batch punya warna yang sama. Kalau target kamu “1 batch = 1 warna unik”, regenerate batches setelah update palette / logic assign warna.
      </div>
      <ul class="small mb-0 mt-2">
        @foreach($duplicateColors as $hex => $group)
          <li>
            <span class="d-inline-block rounded-2 border align-middle"
                  style="width:14px;height:14px;background:{{ $hex }};"></span>
            <span class="ms-2">{{ $hex }}</span>
            <span class="text-muted">→ batch:
              {{ $group->pluck('batch_number')->implode(', ') }}
            </span>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      @if($event->batches->isEmpty())
        <div class="text-muted">No batches yet.</div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:80px;">#</th>
                <th style="width:170px;">Time</th>
                <th style="width:120px;">Capacity</th>
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

                  {{-- ✅ UPDATED COLOR CELL (lebih jelas + siap dipakai ke ticket) --}}
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
                        <form method="POST" action="{{ route('admin.batches.start', $b) }}" class="d-inline">
                          @csrf
                          <button type="submit"
                                  class="btn btn-outline-primary btn-sm fw-semibold"
                                  {{ $b->status !== 'upcoming' ? 'disabled' : '' }}>
                            Start Batch
                          </button>
                        </form>

                        <form method="POST" action="{{ route('admin.batches.complete', $b) }}" class="d-inline">
                          @csrf
                          <button type="submit"
                                  class="btn btn-outline-success btn-sm fw-semibold"
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

  {{-- Generic Confirm Modal (dipakai delete + reset) --}}
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body">
          <div class="fw-semibold mb-2" id="cmTitle">Confirm</div>
          <div class="text-muted small" id="cmBody">Are you sure?</div>

          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
              Cancel
            </button>
            <form id="cmForm" method="POST" action="">
              @csrf
              <input type="hidden" name="_method" id="cmMethod" value="POST">
              <button type="submit" id="cmConfirmBtn" class="btn btn-danger fw-semibold">Confirm</button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>


  {{-- Delete Confirm Modal --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body">
          <div class="fw-semibold mb-2">Confirm Delete</div>
          <div class="text-muted small">
            Hapus event:
            <div class="mt-2">
              <span class="fw-semibold text-dark" id="delName">-</span>
            </div>
          </div>

          <div class="alert alert-warning small mt-3 mb-0">
            Semua batch & ticket terkait akan ikut terhapus.
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
                <form id="deleteForm" method="POST" action="#">
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

  <script>
    // confirm modal helper
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-action][data-confirm]');
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const method = btn.getAttribute('data-method') || 'POST';
      const confirmText = btn.getAttribute('data-confirm') || 'Confirm';
      const confirmClass = btn.getAttribute('data-confirm-class') || 'btn-danger';

      const title = 'Confirm Action';
      const body = 'Are you sure?';

      document.getElementById('cmTitle').textContent = title;
      document.getElementById('cmBody').textContent = body;

      const form = document.getElementById('cmForm');
      const methodEl = document.getElementById('cmMethod');
      const confirmBtn = document.getElementById('cmConfirmBtn');

      form.action = action;
      methodEl.value = method;

      confirmBtn.className = 'btn fw-semibold ' + confirmClass;
      confirmBtn.textContent = confirmText;

      const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
      modal.show();
    });
  </script>

@endsection
