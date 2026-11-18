<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Saya - bisaKerja</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#e6f4ea',
                            700: '#1b5e43',
                            800: '#1e5d48',
                            900: '#144332',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#f8faf8] min-h-screen flex flex-col md:flex-row text-gray-800 antialiased">

    <!-- Sidebar Left -->
    <aside class="w-full md:w-64 bg-white border-r border-gray-100 md:min-h-screen p-6 md:p-8 flex flex-col justify-between shrink-0">
        <div>
            <!-- Logo Brand -->
            <div class="mb-10 flex items-center">
                <a href="{{ route('jobs.index') }}" class="text-2xl font-bold text-[#1b5e43] tracking-tight hover:opacity-90 transition">
                    bisaKerja
                </a>
            </div>

            <!-- Navigasi Sidebar -->
            <nav class="space-y-2">
                <a href="{{ route('jobs.index') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:text-[#1b5e43] hover:bg-[#f0fdf4] rounded-2xl text-sm font-medium transition">
                    <span>Beranda</span>
                </a>
                <a href="{{ route('applications.index') }}" class="flex items-center px-4 py-2.5 bg-[#e6f4ea] text-[#1b5e43] rounded-2xl text-sm font-semibold transition shadow-xs">
                    <span>Lamaran Saya</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:text-[#1b5e43] hover:bg-[#f0fdf4] rounded-2xl text-sm font-medium transition">
                    <span>Chat</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 hover:text-[#1b5e43] hover:bg-[#f0fdf4] rounded-2xl text-sm font-medium transition">
                    <span>Profil</span>
                </a>
            </nav>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-100 hidden md:block">
            <span class="text-xs text-gray-400">&copy; {{ date('Y') }} bisaKerja</span>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- Header Atas -->
        <header class="bg-transparent px-8 md:px-12 pt-6 pb-2 flex justify-between items-center">
            <div class="md:hidden">
                <span class="text-xl font-bold text-[#1b5e43]">bisaKerja</span>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <span class="text-sm font-bold text-[#1b5e43]">Pencari Kerja</span>
            </div>
        </header>

        <div class="p-8 md:px-12 md:py-8 max-w-6xl w-full">

            <!-- Judul Halaman -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#1b5e43] tracking-tight">Lamaran</h1>
                <p class="text-gray-500 text-sm mt-1">Pantau perkembangan lamaran pekerjaanmu.</p>
            </div>

            <!-- Notifikasi Pesan Sukses -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-sm font-bold ml-4">&times;</button>
                </div>
            @endif

            <!-- Notifikasi Pesan Error -->
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 text-sm font-bold ml-4">&times;</button>
                </div>
            @endif

            <!-- Tabel Daftar Lamaran -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden mb-12">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1e5d48] text-white text-xs md:text-sm font-semibold">
                                <th class="py-4 px-6 font-semibold">Pekerjaan</th>
                                <th class="py-4 px-6 font-semibold">Upah</th>
                                <th class="py-4 px-6 font-semibold">Tanggal melamar</th>
                                <th class="py-4 px-6 font-semibold text-center">Status</th>
                                <th class="py-4 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($activeApplications as $application)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900 text-sm md:text-base">
                                            {{ $application->job->title ?? 'Pekerjaan' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $application->job->location ?? ($application->job->company ?? 'Lokasi tidak tersedia') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-800 font-medium">
                                        {{ $application->formatted_salary }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        {{ $application->formatted_applied_date }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[96px] px-3.5 py-1 rounded-xl text-xs font-semibold {{ $application->status_badge_class }}">
                                            {{ $application->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($application->isCancellable())
                                            <button 
                                                type="button" 
                                                onclick="openCancelModal({{ $application->id }}, '{{ addslashes($application->job->title ?? 'Pekerjaan') }}')" 
                                                class="inline-flex items-center text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 border border-red-200 rounded-lg px-3 py-1 transition"
                                            >
                                                Batalkan
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="font-medium text-sm text-gray-600">Belum ada lamaran pekerjaan yang sedang berjalan.</p>
                                            <a href="{{ route('jobs.index') }}" class="text-xs font-semibold text-[#1b5e43] hover:underline mt-1">
                                                Cari dan Lamar Lowongan Pekerjaan &rarr;
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bagian Riwayat Pekerjaan -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-[#1b5e43] tracking-tight">Riwayat pekerjaan</h2>
                <p class="text-gray-500 text-sm mt-1">Daftar pekerjaan yang sudah kamu kerjakan</p>
            </div>

            <!-- Tabel Riwayat Pekerjaan -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1e5d48] text-white text-xs md:text-sm font-semibold">
                                <th class="py-4 px-6 font-semibold">Pekerjaan</th>
                                <th class="py-4 px-6 font-semibold">Upah</th>
                                <th class="py-4 px-6 font-semibold">Tanggal melamar</th>
                                <th class="py-4 px-6 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($completedApplications as $application)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900 text-sm md:text-base">
                                            {{ $application->job->title ?? 'Pekerjaan' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $application->job->location ?? ($application->job->company ?? 'Lokasi tidak tersedia') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-800 font-medium">
                                        {{ $application->formatted_salary }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        {{ $application->formatted_applied_date }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[96px] px-3.5 py-1 rounded-xl text-xs font-semibold bg-[#ecfeff] text-[#0e7490] border border-[#a5f3fc]">
                                            Selesai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="font-medium text-sm text-gray-600">Belum ada riwayat pekerjaan yang sudah selesai dikerjakan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Konfirmasi Pembatalan Lamaran -->
    <div id="cancelModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 transition-all transform animate-fadeIn">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Batalkan Lamaran?</h3>
                    <p class="text-xs text-gray-500">Konfirmasi pembatalan pengajuan pekerjaan</p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                Apakah Anda yakin ingin membatalkan lamaran untuk posisi <strong id="modalJobTitle" class="text-gray-900 font-semibold"></strong>? 
                Proses seleksi akan dihentikan dan perubahan status akan tersimpan di sistem.
            </p>

            <form id="cancelForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeCancelModal()" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-xs transition"
                    >
                        Kembali
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl text-xs transition shadow-xs"
                    >
                        Ya, Batalkan Lamaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Modal Interaktif -->
    <script>
        function openCancelModal(applicationId, jobTitle) {
            const modal = document.getElementById('cancelModal');
            const jobTitleEl = document.getElementById('modalJobTitle');
            const form = document.getElementById('cancelForm');

            jobTitleEl.textContent = jobTitle;
            form.action = "{{ url('/applications') }}/" + applicationId + "/cancel";

            modal.classList.remove('hidden');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
        }

        // Tutup modal jika user klik di luar area dialog
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('cancelModal');
            if (e.target === modal) {
                closeCancelModal();
            }
        });
    </script>

</body>
</html>

