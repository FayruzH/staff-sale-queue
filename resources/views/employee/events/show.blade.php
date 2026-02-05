@extends('layouts.employee')

@section('title', $event->name)

@section('content')

  {{-- Header Event --}}
  <div class="mb-3">
    <div class="d-flex justify-content-between align-items-start gap-2">
      <div>
        <h1 class="h4 fw-semibold mb-1">{{ $event->name }}</h1>
        <div class="text-muted small">Pilih batch yang tersedia untuk lanjut register.</div>
      </div>

      <a href="{{ route('employee.events.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
        <i class="bi bi-arrow-left"></i>
        Back
      </a>
    </div>
  </div>

  {{-- Card Info Event --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md">
          <div class="text-muted small">Date</div>
          <div class="fw-semibold">{{ $event->event_date }}</div>
        </div>

        <div class="col-12 col-md">
          <div class="text-muted small">Time</div>
          <div class="fw-semibold">{{ $event->start_time }} - {{ $event->end_time }}</div>
        </div>

        @if($event->break_start && $event->break_end)
          <div class="col-12 col-md">
            <div class="text-muted small">Break</div>
            <div class="fw-semibold">{{ $event->break_start }} - {{ $event->break_end }}</div>
          </div>
        @endif

        <div class="col-12 col-md-auto">
          <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}"
             class="btn btn-dark fw-semibold w-100">
            <i class="bi bi-qr-code-scan"></i>
            Check your ticket
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Title Section --}}
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h6 fw-semibold mb-0">Pilih Batch</h2>
    <span class="text-muted small">Available batches only</span>
  </div>

  <style>
    .batch-color-dot{
      width: 12px; height: 12px;
      border-radius: 999px;
      display:inline-block;
      border: 1px solid rgba(0,0,0,.12);
      flex: 0 0 auto;
    }
  </style>

  {{-- List Batch --}}
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

          // ✅ color code batch (fallback aman)
          $c = $b->color_code ?? '#CBD5E1';
        @endphp

        <div class="list-group-item py-3 {{ $disabled ? 'opacity-50' : '' }}"
             style="border-left: 6px solid {{ $c }};">
          <div class="d-flex justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
              <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- ✅ dot warna + title --}}
                <span class="batch-color-dot" style="background: {{ $c }};"></span>

                <div class="fw-semibold">
                  Batch {{ $b->batch_number }}
                  <span class="fw-normal text-muted">({{ $b->start_time }} - {{ $b->end_time }})</span>
                </div>

                <span class="badge rounded-pill {{ $statusBadgeClass }}">
                  {{ strtoupper($b->status) }}
                </span>

                <span class="badge rounded-pill {{ $slotBadgeClass }}">
                  Slot: {{ $b->remaining_slots }} / {{ $b->capacity }}
                </span>

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

            <div class="text-end">
              @if($b->status !== 'upcoming')
                <span class="badge text-bg-light border">Not available</span>
              @elseif($b->remaining_slots <= 0)
                <span class="badge text-bg-danger">Full</span>
              @else
                <a href="{{ route('employee.events.registerForm', ['event' => $event->id, 'batch' => $b->id]) }}"
                   class="btn btn-dark fw-semibold"
                   >
                  Pilih
                </a>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Already Registered Modal (Bootstrap) --}}
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
            <a href="{{ route('employee.ticket.loginForm', ['event_id' => $event->id]) }}"
               class="btn btn-dark fw-semibold">
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
