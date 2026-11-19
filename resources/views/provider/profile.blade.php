<x-layouts.employer title="Profil Penyedia">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Profil Penyedia Pekerjaan</h1>
                <p class="mt-1 text-sm text-gray-600">Informasi biodata yang tersimpan pada akun Anda.</p>
            </div>
            <a href="{{ route('provider.profile.edit') }}"
               class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                Edit Profil
            </a>
        </div>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-6 sm:flex-row">
                <div class="shrink-0">
                    @if ($user->foto_profil)
                        <img src="{{ asset('storage/'.$user->foto_profil) }}"
                             alt="Foto profil {{ $user->nama }}"
                             class="h-28 w-28 rounded-full object-cover">
                    @else
                        <div class="flex h-28 w-28 items-center justify-center rounded-full bg-gray-100 text-center text-xs text-gray-500">
                            Foto profil belum diunggah
                        </div>
                    @endif
                </div>

                <dl class="grid flex-1 grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nomor HP</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->nomor_telepon ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Lokasi pekerjaan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->alamat ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->jenis_kelamin ? ucfirst($user->jenis_kelamin) : 'Belum diisi' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Deskripsi diri</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $user->deskripsi_diri ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis penyedia</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $user->jenis_pemberi }}</dd>
                    </div>
                    @if ($user->jenis_pemberi === 'Usaha/UMKM')
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama usaha</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->nama_usaha ?: 'Belum diisi' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status verifikasi akun</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $statusVerifikasi }}</dd>
                    </div>
                </dl>
            </div>
        </section>
    </div>
</x-layouts.employer>
