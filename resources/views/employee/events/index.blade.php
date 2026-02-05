@extends('layouts.employee')

@section('title', 'Staff Sale Events')

@section('content')

  @php
    $modalMsg = null;

    if (session('ticket_expired')) {
        $modalMsg = session('ticket_expired_message') ?? 'Ticket kamu sudah hangus.';
    } elseif (session('ticket_checked_in')) {
        $modalMsg = session('ticket_checked_in_message') ?? 'Ticket kamu sudah digunakan (check-in).';
    }
  @endphp

  {{-- Header --}}
  <div class="mb-3">
    <h1 class="h4 fw-semibold mb-1">Staff Sale Events</h1>
    <div class="text-muted small">Pilih event → pilih batch → register.</div>
  </div>

  {{-- Empty state --}}
  @if($events->count() === 0)
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-semibold">Belum ada event aktif.</div>
        <div class="text-muted small mt-1">Coba cek lagi nanti ya.</div>
      </div>
    </div>
  @else
    <div class="row g-3">
      @foreach($events as $event)
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                  <div class="fw-semibold">{{ $event->name }}</div>
                  <div class="text-muted small mt-1">
                    <div><span class="fw-semibold">Date:</span> {{ $event->event_date }}</div>
                    <div><span class="fw-semibold">Time:</span> {{ $event->start_time }} - {{ $event->end_time }}</div>
                  </div>
                </div>

                <span class="badge text-bg-primary rounded-pill">ACTIVE</span>
              </div>

              <div class="d-flex gap-2 mt-3">
                <a href="{{ route('employee.events.show', $event) }}"
                   class="btn btn-dark flex-fill fw-semibold">
                  View batches
                </a>

                <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}"
                   class="btn btn-outline-secondary fw-semibold">
                  Ticket
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif

  {{-- Modal Ticket Info --}}
  @if($modalMsg)
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body">
            <div class="fw-semibold mb-2">{{ $modalMsg }}</div>
            <div class="text-muted small">Silakan kembali dan coba lagi.</div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-dark fw-semibold" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          var el = document.getElementById('ticketModal');
          if (el && window.bootstrap) new bootstrap.Modal(el).show();
        });
      </script>
    @endpush
  @endif

@endsection
