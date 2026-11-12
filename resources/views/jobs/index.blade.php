<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencari Kerja - bisaKerja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex">

    <!-- Sidebar Navigasi (Fixed/Sticky di Kiri, Diam di Tempat) -->
    <aside class="w-64 bg-white border-r h-screen p-6 hidden md:block sticky top-0 self-start">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-8 h-8 bg-emerald-700 rounded-lg flex items-center justify-center text-white font-bold text-lg">b</div>
            <span class="text-xl font-bold text-emerald-800">bisaKerja</span>
        </div>

        <nav class="space-y-4 text-gray-600 font-medium">
            <a href="{{ route('jobs.index') }}" class="flex items-center space-x-3 text-emerald-700 font-bold bg-emerald-50 p-3 rounded-xl">
                <span>Lowongan Kerja</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 hover:bg-gray-100 rounded-xl transition">
                <span>Lamaran Saya</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 hover:bg-gray-100 rounded-xl transition">
                <span>Chat</span>
            </a>
            <a href="#" class="flex items-center space-x-3 p-3 hover:bg-gray-100 rounded-xl transition">
                <span>Profil</span>
            </a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1">
        <div class="p-8 max-w-6xl mx-auto space-y-8">
            
            <!-- Banner & Form Pencarian (overflow-visible biar dropdown rekomendasi gak kepotong) -->
            <div class="relative rounded-2xl overflow-visible bg-emerald-800 text-white p-8 md:p-12 shadow-md">
                <div class="max-w-2xl space-y-4 relative z-10">
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">Cari pekerjaan di sekitar rumah</h1>
                    <p class="text-emerald-100 text-sm md:text-base">Temukan pekerjaan harian atau freelance sesuai lokasi tempat tinggalmu</p>
                    
                    <form action="{{ route('jobs.index') }}" method="GET" class="pt-4">
                        <div class="bg-white p-2 rounded-2xl shadow-lg flex flex-col md:flex-row gap-2 relative">
                            <!-- Input Pekerjaan / Keahlian -->
                            <div class="flex-1">
                                <input 
                                    type="text" 
                                    name="keyword" 
                                    placeholder="Pekerjaan / Keahlian..." 
                                    value="{{ request('keyword') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-600 text-gray-700"
                                >
                            </div>

                            <!-- Input Lokasi dengan Custom Live Search Dropdown -->
                            <div class="flex-1 relative">
                                <input 
                                    type="text" 
                                    id="location-input"
                                    name="location" 
                                    placeholder="Masukkan kota atau lokasi..." 
                                    value="{{ request('location') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-600 text-gray-700"
                                    autocomplete="off"
                                >

                                <!-- Box Dropdown Rekomendasi Melayang (z-50) -->
                                <div id="location-dropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-60 overflow-y-auto z-50">
                                    <ul id="location-list" class="py-2 text-sm text-gray-700">
                                        <!-- Rekomendasi diisi lewat JS -->
                                    </ul>
                                </div>
                            </div>

                            <!-- Tombol Cari -->
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-8 py-3 rounded-xl transition duration-200">
                                Cari
                            </button>
                        </div>
                    </form>

                    @if(request('keyword') || request('location'))
                        <div class="pt-2">
                            <a href="{{ route('jobs.index') }}" class="text-xs text-emerald-200 hover:text-white underline">
                                ↻ Reset Filter Pencarian
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Job Cards Grid (3 Kolom Sesuai Mockup) -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6">Lowongan di sekitarmu</h2>

                @if($jobs->isEmpty())
                    <div class="bg-white p-12 text-center rounded-2xl border text-gray-500">
                        <p class="text-lg font-semibold">Lowongan tidak ditemukan</p>
                        <p class="text-sm mt-1">Coba gunakan kata kunci atau lokasi lain.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($jobs as $job)
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <h3 class="font-bold text-gray-800 text-lg leading-snug">{{ $job->title }}</h3>
                                    <p class="text-xs text-gray-500 font-medium">
                                        📍 {{ $job->location }} | {{ $job->company }}
                                    </p>
                                    <p class="text-sm text-gray-600 line-clamp-2 pt-2">
                                        {{ $job->description }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-gray-100 space-y-3">
                                    <div class="text-emerald-700 font-bold text-base">
                                        Rp {{ number_format($job->salary_min) }} @if($job->salary_max) - Rp {{ number_format($job->salary_max) }} @endif
                                    </div>
                                    <a href="{{ route('jobs.show', $job->id) }}" class="block w-full bg-emerald-700 hover:bg-emerald-800 text-white text-center text-sm font-semibold py-2.5 rounded-xl transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- JavaScript Autocomplete Lokasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locations = [
                "Jakarta Selatan", "Jakarta Timur", "Jakarta Barat", "Jakarta Utara", "Jakarta Pusat",
                "Bekasi", "Depok", "Tangerang", "Tangerang Selatan", "Bogor",
                "Bandung", "Cimahi", "Cirebon", "Sukabumi", "Tasikmalaya",
                "Semarang", "Surakarta (Solo)", "Yogyakarta", "Magelang", "Purwokerto",
                "Surabaya", "Malang", "Sidoarjo", "Gresik", "Kediri",
                "Medan", "Palembang", "Pekanbaru", "Bandar Lampung", "Denpasar (Bali)",
                "Makassar", "Manado", "Banjarmasin", "Balikpapan", "Pontianak"
            ];

            const input = document.getElementById('location-input');
            const dropdown = document.getElementById('location-dropdown');
            const list = document.getElementById('location-list');

            input.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                list.innerHTML = '';

                if (query.length === 0) {
                    dropdown.classList.add('hidden');
                    return;
                }

                const matches = locations.filter(loc => loc.toLowerCase().includes(query));

                if (matches.length > 0) {
                    matches.forEach(loc => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2.5 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer flex items-center space-x-2 transition font-medium';
                        li.innerHTML = `<span class="text-emerald-600">📍</span> <span>${loc}</span>`;
                        
                        li.addEventListener('click', function () {
                            input.value = loc;
                            dropdown.classList.add('hidden');
                        });

                        list.appendChild(li);
                    });
                    dropdown.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                }
            });

            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>