<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="turbo-cache-control" content="no-preview">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('asset/img/logoBumdes.png') }}" type="image/png">

<title>
    @yield('title',
        (auth()->check()
            ? ucwords(str_replace('-', ' ', auth()->user()->role)) . ' Dashboard - BUMDes Patimban'
            : 'BUMDes Patimban')
    )
</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@stack('css')