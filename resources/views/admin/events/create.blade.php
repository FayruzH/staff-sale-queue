@extends('layouts.admin')

@section('title', 'Create Event')

@section('content')

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
  <div>
    <h1 class="h4 fw-semibold mb-1">Create Event</h1>
    <div class="text-muted small">Buat event staff sale baru.</div>
  </div>

  <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">

    <form method="POST" action="{{ route('admin.events.store') }}">
      @csrf

      <div class="row g-3">

        {{-- Event Name --}}
        <div class="col-12">
          <label class="form-label fw-semibold">Event Name</label>
          <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            class="form-control @error('name') is-invalid @enderror"
            required
          >
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Event Code --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Event Code</label>
          <input
            type="text"
            name="code"
            value="{{ old('code') }}"
            class="form-control @error('code') is-invalid @enderror"
            required
          >
          @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Date --}}
        <div class="col-md-6">
        <label class="form-label fw-semibold">Event Date</label>

        <input
            type="text"
            name="event_date"
            value="{{ old('event_date') }}"
            class="form-control @error('event_date') is-invalid @enderror js-date"
            placeholder="YYYY-MM-DD"
            required
        >

        @error('event_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        </div>


        {{-- Time --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">Start Time</label>
          <input
            type="time"
            name="start_time"
            value="{{ old('start_time') }}"
            class="form-control @error('start_time') is-invalid @enderror"
            required
          >
          @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">End Time</label>
          <input
            type="time"
            name="end_time"
            value="{{ old('end_time') }}"
            class="form-control @error('end_time') is-invalid @enderror"
            required
          >
          @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <hr class="my-4">

        {{-- BATCH SETTINGS --}}
        <div class="row g-3">

             {{-- Batch Duration --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">
                Batch Duration
                <span class="text-muted small">(minutes)</span>
                </label>
                <input
                type="number"
                name="batch_duration_min"
                value="{{ old('batch_duration_min', $event->batch_duration_min ?? '') }}"
                class="form-control @error('batch_duration_min') is-invalid @enderror"
                required
                >
                @error('batch_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Gap Between Batch --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">
                Gap Between Batch
                <span class="text-muted small">(minutes)</span>
                </label>
                <input
                type="number"
                name="gap_min"
                value="{{ old('gap_min', $event->gap_min ?? 0) }}"
                class="form-control @error('gap_min') is-invalid @enderror"
                required
                >
                @error('gap_between_batch') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Break Time --}}
            <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">
                Break Start <span class="text-muted small">(optional)</span>
            </label>
            <input
                type="time"
                name="break_start"
                value="{{ old('break_start') }}"
                class="form-control @error('break_start') is-invalid @enderror"
            >
            @error('break_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-3">
            <label class="form-label fw-semibold">
                Break End <span class="text-muted small">(optional)</span>
            </label>
            <input
                type="time"
                name="break_end"
                value="{{ old('break_end') }}"
                class="form-control @error('break_end') is-invalid @enderror"
            >
            @error('break_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>


            {{-- Capacity --}}
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">
                Capacity / Batch
                </label>
                <input
                type="number"
                name="capacity_per_batch"
                value="{{ old('capacity_per_batch') }}"
                class="form-control @error('capacity_per_batch') is-invalid @enderror"
                min="1"
                required
                >
                @error('capacity_per_batch') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- <div class="col-md-4">
            <label class="form-label fw-semibold">Auto Mode</label>
            <select name="is_auto_mode" class="form-select">
                <option value="0">OFF</option>
                <option value="1">ON</option>
            </select>
            </div> --}}

        </div>

        <div class="text-muted small mt-4">
          Kalau break dipakai, isi <span class="fw-semibold">Break Start</span> & <span class="fw-semibold">Break End</span>.
          Kalau kosong, event dianggap tanpa break.
        </div>

        <hr class="my-4">

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark fw-semibold">
            <i class="bi bi-check-lg"></i> Create Event
            </button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary fw-semibold">
            Cancel
            </a>
        </div>

    </form>

  </div>
</div>

@endsection

@push('scripts')
<script>
  flatpickr('.js-date', {
    dateFormat: 'Y-m-d',
    allowInput: true,
    minDate: "today"
  });
</script>
@endpush
