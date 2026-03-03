@extends('layouts.admin')

@section('title', 'Attendance - ' . $event->name)

@push('styles')
  <style>
    .batch-dot {
      width: 12px;
      height: 12px;
      border-radius: 999px;
      display: inline-block;
      border: 1px solid rgba(0, 0, 0, .12);
      vertical-align: middle;
    }

    .attendance-filter .form-label {
      font-size: .78rem;
    }

    @media (max-width: 767.98px) {
      .attendance-filter {
        row-gap: .5rem !important;
      }

      .attendance-filter .form-label {
        display: none;
      }

      .attendance-filter .form-control,
      .attendance-filter .form-select {
        min-height: 36px;
        font-size: .875rem;
      }

      .attendance-filter-card .card-body,
      .attendance-scan-card .card-body {
        padding: .75rem !important;
      }

      .attendance-scan-group {
        display: block;
      }

      .attendance-scan-group .input-group-text {
        display: none;
      }

      .attendance-scan-group .form-control {
        border-radius: .375rem !important;
        width: 100%;
      }

      .attendance-scan-group .btn {
        width: 100%;
        margin-top: .5rem;
        border-radius: .375rem !important;
      }
    }
  </style>
@endpush

@section('content')

  <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Attendance</h1>
      <div class="text-muted small">
        <span class="fw-semibold">{{ $event->name }}</span>
        - Event ID: {{ $event->id }}
        - Date: {{ $event->event_date }}
      </div>
    </div>

    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted small">Total Registered</div>
          <div class="h3 fw-bold mb-0">{{ $total }}</div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted small">Checked-in</div>
          <div class="h3 fw-bold mb-0">{{ $checkedIn }}</div>
          <span class="badge text-bg-success rounded-pill mt-2">Present</span>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="text-muted small">Remaining</div>
          <div class="h3 fw-bold mb-0">{{ $remaining }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="attendance-scan-card card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="fw-semibold">Quick Scan</div>

      <form method="POST" action="{{ route('admin.attendance.scan', $event) }}" class="mt-3">
        @csrf
        <div class="attendance-scan-group input-group input-group-lg">
          <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
          <input id="scanInput"
                 name="queue_number"
                 value="{{ old('queue_number') }}"
                 class="form-control"
                 placeholder="Scan / Input Queue"
                 autofocus>
          <button class="btn btn-dark fw-semibold" type="submit">
            Check-in
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="attendance-filter-card card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="fw-semibold mb-2">Filter</div>

      <form id="attendanceFilterForm" method="GET" class="attendance-filter row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <label class="form-label text-muted small mb-1">Search</label>
          <input name="q"
                 value="{{ request('q') }}"
                 class="form-control form-control-sm"
                 placeholder="Search queue / NIP / name">
        </div>

        <div class="col-6 col-md-3">
          <label class="form-label text-muted small mb-1">Batch</label>
          <select name="batch_id" class="form-select form-select-sm">
            <option value="">All Batches</option>
            @foreach($batches as $b)
              <option value="{{ $b->id }}" @selected(request('batch_id') == $b->id)>
                Batch {{ $b->batch_number }} ({{ $b->start_time }} - {{ $b->end_time }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-3">
          <label class="form-label text-muted small mb-1">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="checked" @selected(request('status') === 'checked')>Checked-in</option>
          </select>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="fw-semibold">Registrations</div>
        <div class="text-muted small">Showing {{ $registrations->count() }} item(s) on this page</div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:72px;">#</th>
              <th>Queue</th>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Batch</th>
              <th>Status</th>
              <th style="width:190px;">Action</th>
            </tr>
          </thead>

          <tbody>
            @forelse($registrations as $i => $r)
              @php
                $hl = session('last_scanned_id') == $r->id;
                $batch = $r->batch;
                $c = $batch->color_code ?? '#CBD5E1';
              @endphp

              <tr id="reg-{{ $r->id }}" class="{{ $hl ? 'table-success' : '' }}">
                <td class="text-muted">
                  {{ $registrations->firstItem() + $i }}
                </td>

                <td class="fw-semibold">{{ $r->queue_number }}</td>
                <td>{{ $r->employee_identifier }}</td>
                <td>{{ $r->employee_name }}</td>

                <td>
                  @if($batch)
                    <div class="d-flex align-items-center gap-2">
                      <span class="batch-dot" style="background: {{ $c }};"></span>
                      <span class="badge rounded-pill" style="background: {{ $c }}; color:#fff;">
                        Batch {{ $batch->batch_number }}
                      </span>
                      <span class="text-muted small">
                        ({{ $batch->start_time }} - {{ $batch->end_time }})
                      </span>
                    </div>
                  @else
                    <span class="badge text-bg-secondary">-</span>
                  @endif
                </td>

                <td>
                  @if($r->checked_in_at)
                    <span class="badge text-bg-success">
                      PRESENT ({{ $r->checked_in_at->format('H:i:s') }})
                    </span>
                  @else
                    <span class="badge text-bg-warning">NOT YET</span>
                  @endif

                  @if($hl)
                    <span class="badge text-bg-light border ms-2">LAST SCANNED</span>
                  @endif
                </td>

                <td>
                  @if(!$r->checked_in_at)
                    <form method="POST"
                          action="{{ route('admin.attendance.checkin', ['event' => $event->id, 'registration' => $r->id]) }}"
                          class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-primary btn-sm fw-semibold">
                        Check-in
                      </button>
                    </form>
                  @else
                    <form method="POST"
                          action="{{ route('admin.attendance.undo', ['event' => $event->id, 'registration' => $r->id]) }}"
                          class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold" title="Undo check-in">
                        Undo
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-muted py-4 text-center">
                  No registrations found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $registrations->links() }}
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    (function () {
      const filterForm = document.getElementById('attendanceFilterForm');
      if (filterForm) {
        const qInput = filterForm.querySelector('input[name="q"]');
        const batchSelect = filterForm.querySelector('select[name="batch_id"]');
        const statusSelect = filterForm.querySelector('select[name="status"]');
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

        if (batchSelect) batchSelect.addEventListener('change', submitFilter);
        if (statusSelect) statusSelect.addEventListener('change', submitFilter);
      }
    })();

    (function () {
      const input = document.getElementById('scanInput');
      if (input) {
        @if(session('success') || session('warning'))
          input.value = '';
        @endif
        input.focus();
        input.select();
      }

      const lastId = @json(session('last_scanned_id'));
      if (lastId) {
        const row = document.getElementById('reg-' + lastId);
        if (row) row.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    })();
  </script>
@endpush
