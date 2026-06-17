// public/sw.js

self.addEventListener('push', function (event) {
    if (!event.data) return;

    const data = event.data.json();

    const options = {
        body:    data.body    ?? 'Ada notifikasi baru.',
        icon:    data.icon    ?? '/icon-192.png',
        badge:   data.badge   ?? '/badge-72.png',
        data:    data.data    ?? {},
        actions: [
            { action: 'open',    title: '👀 Lihat Detail' },
            { action: 'dismiss', title: '✕ Tutup'         },
        ],
        requireInteraction: true, // Notifikasi tidak hilang sampai diklik
        vibrate: [200, 100, 200],
    };

    event.waitUntil(
        self.registration.showNotification(data.title ?? 'Notifikasi', options)
    );
});

// Klik notifikasi → buka URL tujuan
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    if (event.action === 'dismiss') return;

    const url = event.notification.data?.url ?? '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                // Jika tab website sudah terbuka, fokus ke sana
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(url);
                        return;
                    }
                }
                // Jika tidak ada tab terbuka, buka tab baru
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});