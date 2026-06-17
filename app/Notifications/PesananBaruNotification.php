<?php
// app/Notifications/PesananBaruNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PesananBaruNotification extends Notification
{
    public function __construct(
        public string $invoiceNumber,
        public string $namaCustomer,
        public string $namaItem,
        public int    $total,
        public string $metode
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            // ✅ Sesuai dengan yang dibaca view: data['title'] & data['message']
            'title'      => 'Pesanan Baru Masuk!',
            'message'    => "Pesanan <b>{$this->metode}</b> dari <b>{$this->namaCustomer}</b>.<br>" .
                            "Item: {$this->namaItem}<br>" .
                            "Total: <b>Rp " . number_format($this->total, 0, ',', '.') . "</b>",
            'action_url' => route('mitra.pesanan.index'),

            // Data tambahan (opsional, untuk kebutuhan lain)
            'invoice_number' => $this->invoiceNumber,
            'metode'         => $this->metode,
        ];
    }
}