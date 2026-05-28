<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class StatusPesananUpdateNotification extends Notification
{
    public function __construct(
        public string $invoiceNumber,
        public string $statusBaru
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $pesanStatus = match($this->statusBaru) {
            'Diproses' => 'sedang diproses oleh mitra.',
            'Dikemas'  => 'sedang dikemas oleh mitra.',
            'Dikirim'  => 'sudah dikirim! Silakan cek pesanan Anda.',
            'Selesai'  => 'telah selesai. Terima kasih sudah berbelanja!',
            default    => "statusnya diperbarui menjadi: {$this->statusBaru}.",
        };

        return [
            'judul'   => 'Update Status Pesanan',
            'pesan'   => "Pesanan #{$this->invoiceNumber} {$pesanStatus}",
            'invoice' => $this->invoiceNumber,
            'url'     => '/customer/pesanan',
        ];
    }
}
