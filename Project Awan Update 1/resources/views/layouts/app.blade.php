<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Expense Tracker') - Cloud Ready</title>
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-slate-100 min-h-screen flex flex-col justify-between">

    <!-- Header / Navigation -->
    <header class="glass sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo / Title -->
                <div class="flex items-center">
                    <a href="{{ Auth::check() ? route('home') : route('login') }}" class="flex items-center space-x-2">
                        <div class="bg-indigo-600 p-2 rounded-lg text-white shadow-md shadow-indigo-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-indigo-400 to-violet-300 bg-clip-text text-transparent">
                            Awan Expense
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-4">
                    @auth
                        <span class="hidden md:inline-block text-sm text-slate-400 mr-2">
                            Halo, <strong class="text-indigo-300 font-semibold">{{ Auth::user()->name }}</strong>
                        </span>
                        <a href="{{ route('home') }}" class="text-sm px-3 py-2 rounded-md hover:bg-slate-800 transition duration-150">
                            Home
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-rose-600/20 transition duration-150">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm px-4 py-2 rounded-lg hover:bg-slate-800 transition duration-150">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-indigo-600/20 transition duration-150">
                            Register
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer with Visual Server Name Indicator for Load Balancer -->
    <footer class="glass mt-auto py-6 border-t border-slate-850 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between text-slate-400 text-xs gap-4">
            <div>
                &copy; {{ date('Y') }} Awan Expense. Cloud Architecture Ready.
            </div>
            
            <!-- Load Balancer Visual Server Indicator -->
            <div class="flex items-center space-x-2 bg-slate-950/80 px-3 py-1.5 rounded-full border border-indigo-500/30 text-indigo-300 shadow-md">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-mono">Served by: <strong class="text-emerald-400 font-semibold">{{ gethostname() }}</strong></span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
