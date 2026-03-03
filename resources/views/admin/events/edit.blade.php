@extends('layouts.admin')

@section('title', 'Edit Event - ' . $event->name)

@section('content')

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
  <div>
    <h1 class="h4 fw-semibold mb-1">Edit Event</h1>
    <div class="text-muted small">Update data event.</div>
  </div>

  <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-secondary btn-sm fw-semibold">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">

    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      {{-- BASIC INFO --}}
      <div class="row g-3">

        <div class="col-12">
          <label class="form-label fw-semibold">Event Name</label>
          <input
            type="text"
            name="name"
            value="{{ old('name', $event->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            autocomplete="off"
          >
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Location <span class="text-muted small">(optional)</span></label>
          <input
            type="text"
            name="location"
            value="{{ old('location', $event->location) }}"
            class="form-control @error('location') is-invalid @enderror"
            autocomplete="off"
            placeholder="Contoh: Gedung A, Lantai 3"
          >
          @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Description</label>
          <input id="event-description" type="hidden" name="description" value="{{ old('description', $event->description) }}">
          <trix-editor input="event-description" class="@error('description') is-invalid @enderror"></trix-editor>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Thumbnail Image</label>

          @if($event->thumbnail)
            <div class="mb-2">
              <img
                src="{{ asset('storage/'.$event->thumbnail) }}"
                class="img-fluid rounded"
                style="max-height:150px; object-fit:cover;"
                alt="Event thumbnail"
              >
            </div>
          @endif

          <input
            type="file"
            name="thumbnail"
            class="form-control @error('thumbnail') is-invalid @enderror"
            accept="image/png,image/jpeg,image/jpg,image/webp"
          >
          @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="form-text">Upload baru untuk mengganti thumbnail lama. Format JPG/PNG/WEBP, max 2MB. Thumbnail akan otomatis di-crop dan resize ke template 16:10.</div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Event Date</label>
          <input
            type="text"
            name="event_date"
            value="{{ old('event_date', is_string($event->event_date) ? $event->event_date : optional($event->event_date)->format('Y-m-d')) }}"
            class="form-control @error('event_date') is-invalid @enderror js-date"
            placeholder="YYYY-MM-DD"
            required
          >
          @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Start Time</label>
          <input
            type="time"
            name="start_time"
            value="{{ old('start_time', $event->start_time) }}"
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
            value="{{ old('end_time', $event->end_time) }}"
            class="form-control @error('end_time') is-invalid @enderror"
            required
          >
          @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{--
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Event Code</label>
          <input
            type="text"
            name="code"
            value="{{ old('code', $event->code) }}"
            class="form-control @error('code') is-invalid @enderror"
            required
            autocomplete="off"
          >
          @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        --}}

      </div>

      <hr class="my-4">

      {{-- BATCH SETTINGS --}}
      <div class="row g-3">

        <div class="col-12 col-md-3">
          <label class="form-label fw-semibold">
            Batch Duration <span class="text-muted small">(minutes)</span>
          </label>
          <input
            type="number"
            name="batch_duration_min"
            value="{{ old('batch_duration_min', $event->batch_duration_min) }}"
            class="form-control @error('batch_duration_min') is-invalid @enderror"
            min="1"
            required
          >
          @error('batch_duration_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label fw-semibold">
            Gap Between Batch <span class="text-muted small">(minutes)</span>
          </label>
          <input
            type="number"
            name="gap_min"
            value="{{ old('gap_min', $event->gap_min) }}"
            class="form-control @error('gap_min') is-invalid @enderror"
            min="0"
            required
          >
          @error('gap_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label fw-semibold">
            Break Start <span class="text-muted small">(optional)</span>
          </label>
          <input
            type="time"
            name="break_start"
            value="{{ old('break_start', $event->break_start) }}"
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
            value="{{ old('break_end', $event->break_end) }}"
            class="form-control @error('break_end') is-invalid @enderror"
          >
          @error('break_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label fw-semibold">Capacity / Batch</label>
          <input
            type="number"
            name="capacity_per_batch"
            value="{{ old('capacity_per_batch', $event->capacity_per_batch) }}"
            class="form-control @error('capacity_per_batch') is-invalid @enderror"
            min="1"
            required
          >
          @error('capacity_per_batch') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

      </div>

      <div class="text-muted small mt-4">
        Kalau break dipakai, isi <span class="fw-semibold">Break Start</span> & <span class="fw-semibold">Break End</span>.
        Kalau kosong, event dianggap tanpa break.
      </div>

      <hr class="my-4">

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-dark fw-semibold">
          <i class="bi bi-save"></i> Save Changes
        </button>

        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-secondary fw-semibold">
          Cancel
        </a>
      </div>

    </form>

  </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.1.8/dist/trix.min.css">
<style>
  .trix-button-group--file-tools {
    display: none !important;
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/trix@2.1.8/dist/trix.umd.min.js"></script>
<script>
  document.addEventListener('trix-file-accept', function (event) {
    event.preventDefault();
  });

  document.addEventListener('trix-attachment-add', function (event) {
    if (event.attachment && event.attachment.file) {
      event.attachment.remove();
    }
  });

  document.addEventListener('trix-initialize', function (event) {
    const editorElement = event.target;
    const INDENT = '\u00A0\u00A0\u00A0\u00A0';

    editorElement.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') return;
      e.preventDefault();

      const editor = editorElement.editor;
      if (!editor) return;

      if (!e.shiftKey) {
        editor.insertString(INDENT);
        return;
      }

      const range = editor.getSelectedRange();
      const start = range[0];
      const end = range[1];

      if (start !== end || start < INDENT.length) return;

      const plain = editor.getDocument().toString();
      const left = plain.slice(start - INDENT.length, start);

      if (left === INDENT) {
        editor.setSelectedRange([start - INDENT.length, start]);
        editor.deleteInDirection('backward');
      }
    });
  });

  flatpickr('.js-date', {
    dateFormat: 'Y-m-d',
    allowInput: true,
    minDate: "today"
  });
</script>
@endpush


