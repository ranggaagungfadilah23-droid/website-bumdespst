<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

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
        return ['database', WebPushChannel::class]; // ← tambahkan WebPushChannel
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'          => 'Pesanan Baru Masuk!',
            'message'        => "Pesanan <b>{$this->metode}</b> dari <b>{$this->namaCustomer}</b>.<br>" .
                                "Item: {$this->namaItem}<br>" .
                                "Total: <b>Rp " . number_format($this->total, 0, ',', '.') . "</b>",
            'action_url'     => route('mitra.pesanan.index'),
            'invoice_number' => $this->invoiceNumber,
            'metode'         => $this->metode,
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('🛒 Pesanan Baru Masuk!')
            ->body("Dari {$this->namaCustomer} - {$this->namaItem} | Rp " . number_format($this->total, 0, ',', '.'))
            ->action('Lihat Pesanan', route('mitra.pesanan.index'))
            ->icon('/images/logo.png'); // sesuaikan path icon
    }
}