@extends('layouts.employee')

@section('title', 'Register - ' . $event->name)

@section('content')

  {{-- Top bar --}}
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h1 class="h4 fw-semibold mb-1">Register</h1>
      <div class="text-muted small">Isi data lalu konfirmasi.</div>
    </div>

    <a href="{{ route('employee.events.show', $event) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
      <i class="bi bi-arrow-left"></i>
      Back
    </a>
  </div>

  {{-- Summary card --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="fw-semibold">{{ $event->name }}</div>

      <div class="row g-2 mt-2 small text-muted">
        <div class="col-12 col-md-6">
          <div><span class="fw-semibold text-dark">Batch:</span> {{ $batch->batch_number }} ({{ $batch->start_time }} - {{ $batch->end_time }})</div>
        </div>
        <div class="col-12 col-md-6">
          <div><span class="fw-semibold text-dark">Location:</span> {{ $event->location ?: '-' }}</div>
        </div>
        <div class="col-12 col-md-6">
          <div>
            <span class="fw-semibold text-dark">Remaining slot:</span>
            <span class="badge rounded-pill {{ $remainingSlots <= 0 ? 'text-bg-danger' : 'text-bg-success' }}">
              {{ $remainingSlots }}
            </span>
            <span class="text-muted">/ {{ $batch->capacity }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Errors (optional; flash partial sudah cover, tapi ini lebih dekat ke form) --}}
  @if ($errors->any())
    <div class="alert alert-danger" role="alert">
      <div class="fw-semibold mb-1">Validasi gagal</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form card --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <form id="registerForm" method="POST"
            action="{{ route('employee.events.registerSubmit', ['event'=>$event->id,'batch'=>$batch->id]) }}">
        @csrf

        <div class="mb-3">
          <label class="form-label fw-semibold">Employee ID / NIP</label>
          <input
            name="employee_id"
            value="{{ old('employee_id') }}"
            required
            class="form-control @error('employee_id') is-invalid @enderror"
            placeholder="Masukkan NIP"
            autocomplete="off"
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
            class="form-control @error('employee_name') is-invalid @enderror"
            placeholder="Masukkan nama"
            autocomplete="off"
          >
          @error('employee_name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="button" id="openConfirm" class="btn btn-dark w-100 fw-semibold">
          Register
        </button>
      </form>

      <div class="text-muted small mt-3">
        Pastikan data benar sebelum konfirmasi.
      </div>
    </div>
  </div>

  {{-- Confirm Modal (Bootstrap) --}}
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-body">
          <div class="fw-semibold mb-2">Confirm Register</div>

          <div class="small text-muted">
            <div class="mb-1"><span class="fw-semibold text-dark">Event:</span> {{ $event->name }}</div>
            <div class="mb-1"><span class="fw-semibold text-dark">Location:</span> {{ $event->location ?: '-' }}</div>
            <div class="mb-1"><span class="fw-semibold text-dark">Batch:</span> {{ $batch->batch_number }} ({{ $batch->start_time }} - {{ $batch->end_time }})</div>
            <div class="mb-1"><span class="fw-semibold text-dark">Employee ID:</span> <span id="cEmpId">-</span></div>
            <div><span class="fw-semibold text-dark">Nama:</span> <span id="cEmpName">-</span></div>
          </div>

          <div class="alert alert-warning small mt-3 mb-0">
            Setelah confirm, kamu akan masuk antrian batch yang dipilih.
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button id="confirmBtn" type="button" class="btn btn-dark fw-semibold">
            Confirm
          </button>
          <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-dismiss="modal">
            Back
          </button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openConfirm');
    const confirmBtn = document.getElementById('confirmBtn');

    const form = document.getElementById('registerForm');
    const modalEl = document.getElementById('confirmModal');

    if (!openBtn || !confirmBtn || !form || !modalEl || !window.bootstrap) return;

    const modal = new bootstrap.Modal(modalEl);

   openBtn.addEventListener('click', () => {
    const empIdEl = document.querySelector('[name="employee_id"]');
    const empNameEl = document.querySelector('[name="employee_name"]');

    const empId = (empIdEl?.value || '').trim();
    const empName = (empNameEl?.value || '').trim();

    // reset invalid state
    empIdEl?.classList.remove('is-invalid');
    empNameEl?.classList.remove('is-invalid');

    // simple warning (Bootstrap alert injected)
    const existingAlert = document.getElementById('clientWarn');
    existingAlert?.remove();

    let firstInvalid = null;

    // if (!form.reportValidity()) return;

    if (!empId) {
        empIdEl?.classList.add('is-invalid');
        firstInvalid = firstInvalid || empIdEl;
    }
    if (!empName) {
        empNameEl?.classList.add('is-invalid');
        firstInvalid = firstInvalid || empNameEl;
    }

    if (firstInvalid) {
        // show alert
        const alert = document.createElement('div');
        alert.id = 'clientWarn';
        alert.className = 'alert alert-warning small mb-3';
        alert.innerHTML = '<span class="fw-semibold">Oops.</span> Isi dulu data sebelum lanjut ya.';

        // taruh alert di atas form (di dalam card body)
        const cardBody = form.closest('.card')?.querySelector('.card-body');
        cardBody?.insertBefore(alert, form);

        firstInvalid.focus();
        return;
    }

    document.getElementById('cEmpId').innerText = empId;
    document.getElementById('cEmpName').innerText = empName;

    modal.show();
    });


    confirmBtn.addEventListener('click', () => {
      form.submit();
    });
  });
</script>
@endpush
