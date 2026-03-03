@extends('layouts.employee')

@section('title', 'Check Your Ticket')

@section('content')

  <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Check Your Ticket</h1>
      <div class="text-muted small">Masukin NIP + Nama untuk buka ticket kamu.</div>
    </div>

    <a href="{{ route('employee.events.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <form method="POST" action="{{ route('employee.ticket.loginSubmit') }}" class="needs-validation">
        @csrf

        <input type="hidden" name="event_id" value="{{ $eventId }}">

        <div class="mb-3">
          <label class="form-label fw-semibold">Employee ID / NIP</label>
          <input
            name="employee_id"
            value="{{ old('employee_id') }}"
            required
            autocomplete="off"
            class="form-control @error('employee_id') is-invalid @enderror"
            placeholder="Masukkan NIP"
          >
          @error('employee_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Nama</label>
          <input
            name="employee_name"
            value="{{ old('employee_name') }}"
            required
            autocomplete="off"
            class="form-control @error('employee_name') is-invalid @enderror"
            placeholder="Masukkan nama"
          >
          @error('employee_name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="d-grid gap-2 d-sm-flex">
          <button type="submit" class="btn btn-dark fw-semibold">
            <i class="bi bi-box-arrow-in-right"></i> Login
          </button>

          <a href="{{ route('employee.events.show', $eventId) }}" class="btn btn-outline-primary fw-semibold">
            Pilih batch & register
          </a>
        </div>

        <div class="text-muted small mt-3">
          Tip: pastiin NIP & nama sesuai data register ya.
        </div>
      </form>

    </div>
  </div>

  {{-- Modal: Ticket Not Found --}}
  @if(session('ticket_not_found'))
    <div class="modal fade" id="nfModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body">
            <div class="fw-semibold mb-2">
              {{ session('ticket_not_found_message') ?? 'Ticket tidak ditemukan.' }}
            </div>
            <div class="text-muted small">
              Coba cek lagi input kamu, atau register dulu kalau belum punya ticket.
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <a href="{{ route('employee.events.show', $eventId) }}" class="btn btn-dark fw-semibold">
              Pilih batch & register
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
          const el = document.getElementById('nfModal');
          if (el && window.bootstrap) new bootstrap.Modal(el).show();
        });
      </script>
    @endpush
  @endif

  @php
    $ticketBlockedMsg = null;
    if (session('ticket_checked_in')) {
      $ticketBlockedMsg = session('ticket_checked_in_message') ?? 'Ticket kamu sudah digunakan (check-in).';
    } elseif (session('ticket_expired')) {
      $ticketBlockedMsg = session('ticket_expired_message') ?? 'Ticket kamu sudah hangus karena batch/event sudah lewat.';
    }
  @endphp

  @if($ticketBlockedMsg)
    <div class="modal fade" id="ticketBlockedModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-body">
            <div class="fw-semibold mb-2">{{ $ticketBlockedMsg }}</div>
            <div class="text-muted small">Ticket sudah tidak bisa dipakai lagi. Silakan hubungi admin jika ada kesalahan.</div>
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
          const el = document.getElementById('ticketBlockedModal');
          if (el && window.bootstrap) new bootstrap.Modal(el).show();
        });
      </script>
    @endpush
  @endif

@endsection
