@extends('layouts.admin')

@section('title', 'Admin - Events')

@push('styles')
  <style>
    .events-filter .form-label {
      font-size: .78rem;
    }

    @media (max-width: 767.98px) {
      .events-filter {
        row-gap: .5rem !important;
      }

      .events-filter .form-label {
        display: none;
      }

      .events-filter .form-control,
      .events-filter .form-select {
        font-size: .875rem;
        min-height: 36px;
      }

      .events-filter-card .card-body {
        padding: .65rem .75rem !important;
      }
    }
  </style>
@endpush

@section('content')

  <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Events</h1>
      <div class="text-muted small">Manage events, batches, tickets, dan attendance.</div>
    </div>

    <a href="{{ route('admin.events.create') }}" class="btn btn-dark fw-semibold">
      <span class="d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Create Event</span>
      </span>
    </a>
  </div>

  @if(($totalEvents ?? $events->count()) > 3)
    <div class="events-filter-card card border-0 shadow-sm mb-2">
      <div class="card-body py-2 px-3">
        <form id="eventsFilterForm" method="GET" action="{{ route('admin.events.index') }}" class="events-filter row g-2 align-items-center">
          <div class="col-12 col-md-7">
            <label class="form-label text-muted small mb-1">Filter</label>
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              class="form-control form-control-sm"
              placeholder="Search name / location / code / date"
            >
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label text-muted small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All</option>
              <option value="draft" @selected(request('status') === 'draft')>Draft</option>
              <option value="active" @selected(request('status') === 'active')>Active</option>
              <option value="ended" @selected(request('status') === 'ended')>Ended</option>
            </select>
          </div>

          <div class="col-6 col-md-3">
            <label class="form-label text-muted small mb-1">Sort</label>
            <select name="sort" class="form-select form-select-sm">
              <option value="nearest" @selected(request('sort', 'nearest') === 'nearest')>Nearest</option>
              <option value="farthest" @selected(request('sort') === 'farthest')>Farthest</option>
              <option value="name_az" @selected(request('sort') === 'name_az')>Name A-Z</option>
              <option value="name_za" @selected(request('sort') === 'name_za')>Name Z-A</option>
            </select>
          </div>
        </form>
      </div>
    </div>
  @endif

  {{-- Table Card --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="fw-semibold">Event List</div>
        <div class="text-muted small">Total: {{ $events->count() }} event(s)</div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:80px;">ID</th>
              <th style="width:90px;">Thumb</th>
              <th style="width:160px;">Code</th>
              <th>Name</th>
              <th style="width:180px;">Location</th>
              <th style="width:140px;">Date</th>
              <th style="width:130px;">Status</th>
              <th style="width:110px;">Auto</th>
              <th style="width:240px;">Actions</th>
            </tr>
          </thead>

          <tbody>
            @forelse($events as $event)
              @php
                $statusClass = match($event->status) {
                  'open' => 'text-bg-success',
                  'closed' => 'text-bg-secondary',
                  'draft' => 'text-bg-warning',
                  default => 'text-bg-primary',
                };
              @endphp

              <tr>
                <td class="text-muted">{{ $event->id }}</td>

                <td>
                  @if(!empty($event->thumbnail))
                    <img
                      src="{{ asset('storage/'.$event->thumbnail) }}"
                      class="rounded border bg-light"
                      style="width:64px;height:40px;object-fit:cover;"
                      onerror="this.outerHTML='<div class=&quot;bg-light border rounded d-flex align-items-center justify-content-center text-muted&quot; style=&quot;width:64px;height:40px;font-size:11px;&quot;>No</div>';"
                      alt="thumb"
                    >
                  @else
                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted"
                         style="width:64px;height:40px;font-size:11px;">
                      No
                    </div>
                  @endif
                </td>

                <td class="fw-semibold">
                  <span class="d-inline-block text-break">{{ $event->code }}</span>
                </td>

                <td>
                  <a class="fw-semibold text-decoration-none"
                     href="{{ route('admin.events.show', $event) }}">
                    {{ $event->name }}
                  </a>

                  {{-- @if(!empty($event->description))
                    <div class="text-muted small mt-1">
                      {{ \Illuminate\Support\Str::limit($event->description, 80) }}
                    </div>
                  @endif --}}
                </td>

                <td>
                  <span class="text-muted">{{ $event->location ?: '-' }}</span>
                </td>

                <td class="text-nowrap">{{ $event->event_date }}</td>

                <td>
                  <span class="badge rounded-pill {{ $statusClass }}">
                    {{ strtoupper($event->status) }}
                  </span>
                </td>

                <td>
                  @if($event->is_auto_mode)
                    <span class="badge rounded-pill text-bg-success">ON</span>
                  @else
                    <span class="badge rounded-pill text-bg-light border">OFF</span>
                  @endif
                </td>

                <td>
                  <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-secondary btn-sm fw-semibold"
                       href="{{ route('admin.events.show', $event) }}">
                      View
                    </a>

                    <a class="btn btn-outline-primary btn-sm fw-semibold"
                       href="{{ route('admin.events.edit', $event) }}">
                      Edit
                    </a>

                    <button
                      type="button"
                      class="btn btn-outline-danger btn-sm fw-semibold"
                      data-bs-toggle="modal"
                      data-bs-target="#deleteModal"
                      data-action="{{ route('admin.events.destroy', $event) }}"
                      data-name="{{ $event->name }}"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9">
                  <div class="text-center py-4">
                    <div class="fw-semibold mb-1">No events found</div>
                    <div class="text-muted small mb-3">Create your first event to get started.</div>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-dark btn-sm fw-semibold">
                      + Create Event
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
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

@endsection

@push('scripts')
<script>
  (function () {
    const filterForm = document.getElementById('eventsFilterForm');
    if (filterForm) {
      const qInput = filterForm.querySelector('input[name="q"]');
      const statusSelect = filterForm.querySelector('select[name="status"]');
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

      if (statusSelect) statusSelect.addEventListener('change', submitFilter);
      if (sortSelect) sortSelect.addEventListener('change', submitFilter);
    }
  })();

  (function () {
    const modal = document.getElementById('deleteModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      if (!btn) return;

      const action = btn.getAttribute('data-action');
      const name = btn.getAttribute('data-name');

      const form = document.getElementById('deleteForm');
      const elName = document.getElementById('delName');

      if (form && action) form.action = action;
      if (elName) elName.textContent = name || '-';
    });
  })();
</script>
@endpush
