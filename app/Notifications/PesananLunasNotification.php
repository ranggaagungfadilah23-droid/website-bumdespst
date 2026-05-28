<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PesananLunasNotification extends Notification
{
    public function __construct(
        public string $invoiceNumber
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'judul'   => 'Pesanan Lunas!',
            'pesan'   => "Pesanan #{$this->invoiceNumber} telah dikonfirmasi lunas oleh mitra.",
            'invoice' => $this->invoiceNumber,
            'url'     => '/customer/pesanan',
        ];
    }
}
