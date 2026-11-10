<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencari Kerja - bisaKerja</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#16a34a',
                            600: '#15803d',
                            700: '#166534',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex">

    <!-- Sidebar Left -->
    <aside class="w-64 bg-white border-r min-h-screen p-6 hidden md:block">
        <div class="flex items-center gap-2 mb-8">
            <span class="text-2xl font-bold text-emerald-800">bisaKerja</span>
        </div>
        <nav class="space-y-2">
            <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 text-emerald-700 font-semibold rounded-lg">
                <span>Beranda</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Lamaran Saya</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Chat</span>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg">
                <span>Profil</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1">
        <!-- Top Navbar Mobile/Header -->
        <header class="bg-white border-b px-8 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800 md:hidden">bisaKerja</h1>
            <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full ml-auto">Pencari Kerja</span>
        </header>

        <div class="p-8 max-w-6xl mx-auto space-y-8">
            
            <!-- Hero Search Section (Sesuai Gambar 13 Mockup) -->
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-emerald-800 to-teal-900 p-8 md:p-12 text-white shadow-lg">
                <div class="relative z-10 text-center max-w-2xl mx-auto space-y-3">
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Cari pekerjaan di sekitar rumah</h2>
                    <p class="text-emerald-100 text-sm">Temukan pekerjaan harian atau freelance sesuai lokasi tempat tinggalmu</p>
                    
                    <!-- Form 2 Input (PBI-04 & PBI-05) -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="mt-6 flex flex-col md:flex-row gap-3 bg-white/10 p-3 rounded-xl backdrop-blur-md">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Pekerjaan / Keahlian..." 
                                class="w-full px-4 py-2.5 rounded-lg text-gray-800 bg-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                        </div>
                        <div class="flex-1">
                            <input 
                                type="text" 
                                name="location" 
                                value="{{ request('location') }}" 
                                placeholder="Masukkan kota atau lokasi..." 
                                class="w-full px-4 py-2.5 rounded-lg text-gray-800 bg-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                        </div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                            Cari
                        </button>
                        @if(request('search') || request('location'))
                            <a href="{{ route('jobs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2.5 rounded-lg text-sm text-center transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Job Cards Grid (3 Kolom Sesuai Mockup) -->
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Lowongan di sekitarmu</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($jobs as $job)
                        <div class="bg-white border rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900 text-base mb-1">{{ $job->title }}</h4>
                                <p class="text-gray-500 text-xs mb-3">📍 {{ $job->location }} | {{ $job->company }}</p>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $job->description }}</p>
                            </div>

                            <div>
                                <div class="text-emerald-700 font-bold text-base mb-4">
                                    Rp {{ number_format($job->salary_min) }}
                                </div>
                                <!-- TOMBOL LIHAT DETAIL SUDAH DISAMBUNGIN KE ROUTE SHOW -->
                                <a href="{{ route('jobs.show', $job->id) }}" class="block w-full bg-emerald-700 hover:bg-emerald-800 text-white text-center text-sm font-semibold py-2 rounded-lg transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 bg-white p-8 text-center rounded-xl border">
                            <p class="text-gray-500">Tidak ada lowongan kerja yang ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            </div>

        </div>
    </main>

</body>
</html>