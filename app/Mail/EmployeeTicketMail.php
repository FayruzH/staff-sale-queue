<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registration $registration,
        public string $ticketUrl
    ) {
    }

    public function build(): self
    {
        $payload = $this->buildQrPayload();
        $qrImageUrl = $this->buildQrImageUrl($payload);

        return $this
            ->subject('Ticket Staff Sale - ' . $this->registration->event->name)
            ->view('emails.employee-ticket')
            ->with([
                'qrImageUrl' => $qrImageUrl,
            ]);
    }

    private function buildQrPayload(): string
    {
        $qrQueue = str_replace('-', '', strtoupper((string) $this->registration->queue_number)); // B01001
        $qrNip = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->registration->employee_identifier);
        $qrName = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->registration->employee_name);

        return $this->registration->event_id
            . '-' . $qrQueue
            . '-' . $qrNip
            . '-' . $qrName;
    }

    private function buildQrImageUrl(string $payload): string
    {
        // Service image QR publik supaya tetap tampil di email client seperti Gmail.
        return 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=10&data=' . rawurlencode($payload);
    }
}
