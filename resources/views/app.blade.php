<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>ระบบบริหารจัดการงานธุรการด้านกำลังพล</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Sarabun:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>

<body class="antialiased bg-slate-50/50 text-slate-600 font-sans">
    @inertia

    <!-- Lucide Icons (Local) -->
    <script src="{{ asset('js/lucide.min.js') }}"></script>
</body>

</html>
