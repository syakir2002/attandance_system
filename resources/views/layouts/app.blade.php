<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff Attendance Management System')</title>
    @PwaHead
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Toastr Notification CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        /* Modern aesthetic styling for Toastr */
        #toast-container > div {
            opacity: 1 !important;
            border-radius: 0.75rem !important;
            padding: 14px 18px 14px 50px !important;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            backdrop-filter: blur(8px) !important;
            transition: all 0.2s ease-in-out !important;
        }
        #toast-container > div:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }
        #toast-container > .toast-success {
            background-color: #059669 !important; /* Emerald 600 */
            color: #ffffff !important;
        }
        #toast-container > .toast-error {
            background-color: #e11d48 !important; /* Rose 600 */
            color: #ffffff !important;
        }
        #toast-container > .toast-info {
            background-color: #4f46e5 !important; /* Indigo 600 */
            color: #ffffff !important;
        }
        #toast-container > .toast-warning {
            background-color: #d97706 !important; /* Amber 600 */
            color: #ffffff !important;
        }
        #toast-container .toast-title {
            font-weight: 700 !important;
            margin-bottom: 3px !important;
            font-size: 0.925rem !important;
        }
        #toast-container .toast-message {
            font-weight: 400 !important;
            font-size: 0.85rem !important;
            opacity: 0.95;
        }
        #toast-container .toast-close-button {
            top: -2px !important;
            right: -2px !important;
            color: #ffffff !important;
            opacity: 0.75 !important;
            text-shadow: none !important;
            font-size: 18px !important;
            transition: opacity 0.15s ease !important;
        }
        #toast-container .toast-close-button:hover {
            opacity: 1 !important;
        }
        #toast-container .toast-progress {
            background-color: rgba(255, 255, 255, 0.45) !important;
            height: 3px !important;
        }
    </style>

    <!-- Vite Styles & Scripts -->
    @vite([request()->routeIs('staff.*') ? 'resources/css/staff_css.css' : (request()->routeIs('admin.*') ? 'resources/css/admin_css.css' : 'resources/css/app.css'), 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50 selection:bg-indigo-500 selection:text-white">
    @yield('body')
    
    <!-- jQuery & Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-center",
            "preventDuplicates": false,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "1000",
            "extendedTimeOut": "1500",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if(session('success'))
            toastr.success(@json(session('success')), 'Berjaya');
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')), 'Ralat');
        @endif

        @if(session('warning'))
            toastr.warning(@json(session('warning')), 'Amaran');
        @endif

        @if(session('info'))
            toastr.info(@json(session('info')), 'Maklumat');
        @endif

        @if(session('status'))
            toastr.info(@json(session('status')), 'Status');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error(@json($error), 'Ralat Borang');
            @endforeach
        @endif
    </script>

    @stack('scripts')
    @RegisterServiceWorkerScript
</body>
</html>
