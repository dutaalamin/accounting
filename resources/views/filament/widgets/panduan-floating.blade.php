<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
    <!-- Popup content -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-4 w-96 bg-white dark:bg-gray-900 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden"
         style="display: none;">
        
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center gap-x-2">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-6 h-6 text-primary-500" />
                <h2 class="font-bold text-gray-800 dark:text-gray-200">Panduan Langkah Pertama</h2>
            </div>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
            </button>
        </div>

        <div class="p-5 bg-primary-50/30 dark:bg-primary-900/10">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                Selamat datang! Jika bingung harus mengklik apa, silakan ikuti urutan mudah di bawah ini:
            </p>
            
            <ol class="list-decimal list-inside space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <li class="leading-relaxed">
                    <strong>Langkah 1 (Buat Dompet/Kategori):</strong><br/>
                    Klik menu <strong class="text-primary-600 dark:text-primary-400">Daftar Akun / Dompet</strong> di menu kiri. Klik tombol <strong>Buat Akun Baru</strong> di pojok kanan atas untuk memasukkan data kas Anda (Misal: Kas Kecil, Rekening BCA, Modal).
                </li>
                <li class="leading-relaxed">
                    <strong>Langkah 2 (Mulai Mencatat):</strong><br/>
                    Setelah akun selesai dibuat, klik menu <strong class="text-primary-600 dark:text-primary-400">Catat Transaksi Harian</strong>. Di sana Anda bisa mencatat setiap ada uang masuk atau keluar.
                </li>
            </ol>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button @click="open = !open" 
            class="flex items-center justify-center w-14 h-14 ml-auto bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-500 hover:scale-105 hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary-500/50">
        <x-filament::icon icon="heroicon-o-question-mark-circle" class="w-7 h-7" />
    </button>
</div>
