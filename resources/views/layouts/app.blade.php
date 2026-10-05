<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Attendance Management System')</title>
    
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Vite Styles & Scripts -->
    @vite([request()->routeIs('staff.*') ? 'resources/css/staff_css.css' : (request()->routeIs('admin.*') ? 'resources/css/admin_css.css' : 'resources/css/app.css'), 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50 selection:bg-indigo-500 selection:text-white">
    @yield('body')

    @stack('scripts')
</body>
</html>
