<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sistem Pemantauan Produksi & Six Sigma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js for Six Sigma Pareto & SPC Control Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="h-full antialiased font-sans text-slate-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" 
             @click="sidebarOpen = false" 
             style="display: none;"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-200 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            <!-- App Brand / Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800/80">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-bold tracking-tight text-white block leading-tight">SIX SIGMA MFG</span>
                        <span class="text-[11px] text-emerald-400 font-medium tracking-wide">Bracket Seat Leg Mobil</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 mb-2">Utama & Analitik</div>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard Eksekutif
                </a>

                <a href="{{ route('admin.analytics.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.analytics.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.analytics.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Analitik Mutu (DMAIC)
                </a>

                <div class="pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3">Operasional Pabrik</div>

                <a href="{{ route('admin.inspections.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.inspections.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.inspections.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Inspeksi Kualitas (QC)
                </a>

                <a href="{{ route('admin.batches.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.batches.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.batches.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Batch / Lot Produksi
                </a>

                <a href="{{ route('admin.capa.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.capa.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.capa.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Tindakan Perbaikan (CAPA)
                </a>

                <div class="pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3">Standar & Master Data</div>

                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Produk & Standar CTQ
                </a>

                <a href="{{ route('admin.lines.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.lines.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.lines.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Lini Mesin Stamping Press
                </a>

                <a href="{{ route('admin.defects.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.defects.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.defects.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    5 Jenis Cacat CTQ
                </a>

                <div class="pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3">K3 & Keselamatan Kerja</div>

                <a href="{{ route('admin.risks.index') }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.risks.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.risks.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Analisa Resiko Mesin Press
                </a>

                @if(auth()->user()->isAdmin())
                    <div class="pt-5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3">Sistem & Otorisasi</div>

                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Manajemen Pengguna
                    </a>
                @endif
            </nav>

            <!-- Current User Bar -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-emerald-400">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <span class="inline-flex items-center text-[10px] font-medium px-2 py-0.5 rounded-full {{ auth()->user()->role?->badgeColor() }}">
                            {{ auth()->user()->role?->label() }}
                        </span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center text-sm text-slate-500">
                        <span class="font-medium text-slate-800">Manufaktur PT</span>
                        <span class="mx-2 text-slate-300">/</span>
                        <span class="text-slate-600">{{ $header ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Quick CTQ Status -->
                    <div class="hidden sm:flex items-center px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-full text-xs text-emerald-700 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        Mesin & QC Online
                    </div>

                    <!-- User Dropdown Menu -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2 text-sm text-slate-700 hover:text-slate-900 focus:outline-none">
                            <span class="font-medium hidden md:inline">{{ auth()->user()->name ?? 'User' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50">
                                Profil Pengguna
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-medium">
                                    Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content Body -->
            <main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
                <!-- Global Flash Notifications -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
