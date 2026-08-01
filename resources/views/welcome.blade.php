<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Keuangan - PT Cahaya Tiga Putri Mandiri</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        .glow-effect:hover {
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
            transition: all 0.4s ease;
        }
        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 41, 59, 0.5) 0, transparent 50%), 
                radial-gradient(at 50% 0%, rgba(99, 102, 241, 0.15) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 41, 59, 0.5) 0, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-slate-100 font-sans antialiased overflow-x-hidden">

    <!-- Header / Navbar -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center z-10">
        <div class="flex items-center gap-3">
            <!-- Accounting Book Logo -->
            <div class="p-2.5 bg-indigo-600/10 rounded-xl border border-indigo-500/20 text-indigo-400">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
            </div>
            <span class="font-outfit font-bold text-lg tracking-wide bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">
                ERP KEUANGAN
            </span>
        </div>
        <div>
            <a href="/admin" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-xl font-semibold text-sm transition-all duration-300">
                Masuk Dashboard
            </a>
        </div>
    </header>

    <!-- Main Hero -->
    <main class="flex-grow flex items-center justify-center px-6 py-12 z-10">
        <div class="max-w-4xl w-full text-center flex flex-col items-center">
            
            <!-- Animated Graphic & Logo -->
            <div class="relative mb-8 group">
                <div class="absolute inset-0 bg-indigo-500/20 blur-3xl rounded-full opacity-60 group-hover:opacity-80 transition-all duration-500"></div>
                <div class="relative p-8 bg-slate-950/60 backdrop-blur-xl border border-slate-800 rounded-3xl glow-effect flex flex-col items-center">
                    
                    <!-- SVG Accounting/Finance Ledger Logo -->
                    <svg class="w-24 h-24 text-indigo-400 mb-4 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <!-- Book Cover Outline -->
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                        <!-- Statistics Graph inside Book -->
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h3m-3 3h5m6-3h3m-6 3h5" stroke-dasharray="1 1"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15l2-2 3 3 4-4" stroke-width="2" stroke="url(#gradient)"></path>
                        <defs>
                            <linearGradient id="gradient" x1="0" y1="1" x2="1" y2="0">
                                <stop offset="0%" stop-color="#818cf8" />
                                <stop offset="100%" stop-color="#c084fc" />
                            </linearGradient>
                        </defs>
                    </svg>

                    <h1 class="font-outfit font-extrabold text-4xl sm:text-5xl tracking-tight leading-tight bg-gradient-to-b from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">
                        Sistem Akuntansi Terintegrasi
                    </h1>
                    
                    <p class="mt-2 font-outfit text-indigo-400 font-semibold text-base sm:text-lg tracking-widest uppercase">
                        PT Cahaya Tiga Putri Mandiri
                    </p>
                </div>
            </div>

            <!-- Description -->
            <p class="max-w-xl text-slate-400 text-base sm:text-lg mb-10 leading-relaxed">
                Kelola pembukuan, transaksi kas/bank, faktur pelanggan & pemasok, serta laporan laba rugi secara instan, aman, dan efisien.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center px-4">
                <a href="/admin" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 font-semibold rounded-2xl transition-all duration-300 shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 group">
                    Buka Dashboard Akuntansi
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </a>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 w-full max-w-3xl">
                <div class="p-4 bg-slate-950/40 border border-slate-800/60 rounded-2xl text-center">
                    <span class="block text-2xl mb-1">📊</span>
                    <span class="text-xs font-semibold text-slate-300 block uppercase tracking-wider">Laba Rugi</span>
                    <span class="text-[10px] text-slate-500 mt-1 block">Real-time</span>
                </div>
                <div class="p-4 bg-slate-950/40 border border-slate-800/60 rounded-2xl text-center">
                    <span class="block text-2xl mb-1">💼</span>
                    <span class="text-xs font-semibold text-slate-300 block uppercase tracking-wider">Kas & Bank</span>
                    <span class="text-[10px] text-slate-500 mt-1 block">Mutasi & Saldo</span>
                </div>
                <div class="p-4 bg-slate-950/40 border border-slate-800/60 rounded-2xl text-center">
                    <span class="block text-2xl mb-1">📝</span>
                    <span class="text-xs font-semibold text-slate-300 block uppercase tracking-wider">Jurnal Umum</span>
                    <span class="text-[10px] text-slate-500 mt-1 block">Double Entry</span>
                </div>
                <div class="p-4 bg-slate-950/40 border border-slate-800/60 rounded-2xl text-center">
                    <span class="block text-2xl mb-1">📄</span>
                    <span class="text-xs font-semibold text-slate-300 block uppercase tracking-wider">Invoicing</span>
                    <span class="text-[10px] text-slate-500 mt-1 block">Termin & PPN</span>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-slate-900 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 z-10">
        <p>&copy; 2026 PT Cahaya Tiga Putri Mandiri. All rights reserved.</p>
        <div class="flex gap-4">
            <span>Sistem Informasi Akuntansi</span>
            <span>v1.2.0</span>
        </div>
    </footer>

</body>
</html>