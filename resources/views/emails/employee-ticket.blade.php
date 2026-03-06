<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticket Staff Sale</title>
</head>
<body style="margin:0;padding:24px;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
    <tr>
      <td style="padding:20px 22px;background:#1d4ed8;color:#fff;">
        <div style="font-size:20px;font-weight:700;line-height:1.2;">Ticket Staff Sale</div>
      </td>
    </tr>
    <tr>
      <td style="padding:22px;">
        <p style="margin:0 0 12px 0;">Hi {{ $registration->employee_name }},</p>
        <p style="margin:0 0 16px 0;">Ticket kamu sudah berhasil dibuat.</p>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin-bottom:16px;">
          <tr>
            <td style="padding:8px 0;color:#6b7280;width:130px;">Event</td>
            <td style="padding:8px 0;font-weight:600;">{{ $registration->event->name }}</td>
          </tr>
          <tr>
            <td style="padding:8px 0;color:#6b7280;">Queue</td>
            <td style="padding:8px 0;font-weight:600;">{{ $registration->queue_number }}</td>
          </tr>
          <tr>
            <td style="padding:8px 0;color:#6b7280;">Batch</td>
            <td style="padding:8px 0;font-weight:600;">{{ $registration->batch->batch_number }}</td>
          </tr>
          <tr>
            <td style="padding:8px 0;color:#6b7280;">Time</td>
            <td style="padding:8px 0;font-weight:600;">{{ $registration->batch->start_time }} - {{ $registration->batch->end_time }}</td>
          </tr>
          <tr>
            <td style="padding:8px 0;color:#6b7280;">Tanggal</td>
            <td style="padding:8px 0;font-weight:600;">{{ $registration->event->event_date }}</td>
          </tr>
        </table>

        <div style="border:1px dashed #9ca3af;border-radius:10px;padding:14px;background:#f9fafb;text-align:center;margin-bottom:14px;">
          <div style="font-size:13px;color:#6b7280;margin-bottom:8px;">QR Check-in</div>
          @if(!empty($qrImageUrl))
            <img
              src="{{ $qrImageUrl }}"
              alt="QR Ticket"
              style="display:block;margin:0 auto;width:220px;max-width:100%;height:auto;"
            >
          @else
            <div style="color:#9ca3af;font-size:13px;">QR tidak tersedia.</div>
          @endif
        </div>

        <p style="margin:0;color:#6b7280;font-size:13px;">Tunjukkan QR ini saat check-in.</p>
      </td>
    </tr>
  </table>
</body>
</html>
