<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class GeneralNotification extends Notification
{
    public function __construct(
        public string  $title,
        public string  $message,
        public ?string $url   = null,
        public string  $icon  = 'fa-bell',
        public string  $color = 'orange',
    ) {}

    public function via($notifiable): array
    {
        // Kirim ke database DAN web push sekaligus
        return ['database', WebPushChannel::class];
    }

    /**
     * Simpan ke tabel notifications (muncul di halaman /notifications)
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title'      => $this->title,
            'message'    => $this->message,
            'action_url' => $this->url,
        ];
    }

    /**
     * Kirim ke browser via Web Push (muncul meski tab tidak aktif)
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        // Strip HTML tag dari message agar terbaca di notifikasi OS
        $plainMessage = strip_tags($this->message);

        return WebPushMessage::create()
            ->title($this->title)
            ->body($plainMessage)
            ->icon('/icon-192.png')
            ->badge('/badge-72.png')
            ->action('Lihat Detail', 'open')
            ->data(['url' => $this->url ?? '/']);
    }
}