<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BUMDes Patimban')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png">
    <link rel="apple-touch-icon" href="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017.umd.js"></script>
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased">

    {{-- Panggil Navbar Biru secara otomatis di sini --}}
    {{-- Kita kirim variabel hideSearch=true jika route saat ini adalah pesanan --}}
    @include('theme.partials.customer-navbar', [
        'hideSearch' => request()->routeIs('customer.pesanan*')
    ])

    <main class="w-full">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
