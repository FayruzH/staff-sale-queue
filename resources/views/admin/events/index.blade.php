@extends('layouts.admin')

@section('title', 'Admin - Events')

@section('content')

  <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Events</h1>
      <div class="text-muted small">Manage events, batches, tickets, dan attendance.</div>
    </div>

    <a href="{{ route('admin.events.create') }}" class="btn btn-dark fw-semibold">
      <i class="bi bi-plus-lg"></i>
      Create Event
    </a>
  </div>

  {{-- Table Card --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div class="fw-semibold">Event List</div>
        <div class="text-muted small">Total: {{ $events->count() }} event(s)</div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:80px;">ID</th>
              <th style="width:140px;">Code</th>
              <th>Name</th>
              <th style="width:140px;">Date</th>
              <th style="width:130px;">Status</th>
              <th style="width:110px;">Auto</th>
              <th style="width:220px;">Actions</th>
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

                <td class="fw-semibold">{{ $event->code }}</td>

                <td>
                  <a class="fw-semibold text-decoration-none"
                     href="{{ route('admin.events.show', $event) }}">
                    {{ $event->name }}
                  </a>
                  <div class="text-muted small">Click to manage batches & attendance</div>
                </td>

                <td>{{ $event->event_date }}</td>

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
                <td colspan="7" class="text-muted">No events found.</td>
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
