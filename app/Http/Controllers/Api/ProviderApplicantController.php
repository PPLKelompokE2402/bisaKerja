<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProviderApplicantController extends Controller
{
    public function page(): View
    {
        return view('provider.applicants');
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Lamaran::STATUS_LABELS))],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $owned = Lamaran::query()->forProvider($request->user());
        $counts = (clone $owned)->selectRaw('status_lamaran, COUNT(*) AS total')
            ->groupBy('status_lamaran')->pluck('total', 'status_lamaran');

        $query = (clone $owned)->select(['id_lamaran', 'id_lowongan', 'id_pencari', 'status_lamaran', 'tanggal_lamaran'])
            ->with(['pencari:id_pengguna,nama', 'lowongan:id_lowongan,judul']);
        $search = trim($validated['search'] ?? '');
        if ($search !== '') {
            $query->where(function (Builder $applications) use ($search): void {
                $applications->whereHas('pencari', fn (Builder $users) => $users->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('lowongan', fn (Builder $jobs) => $jobs->where('judul', 'like', "%{$search}%"));
            });
        }
        if (! empty($validated['status'])) {
            $query->where('status_lamaran', $validated['status']);
        }

        $applications = $query->orderByDesc('tanggal_lamaran')->orderByDesc('id_lamaran')->paginate(15);

        return response()->json([
            'summary' => [
                'total' => $counts->sum(),
                'menunggu' => (int) $counts->get('menunggu', 0),
                'diterima' => (int) $counts->get('diterima', 0),
                'ditolak' => (int) $counts->get('ditolak', 0),
            ],
            'data' => $applications->getCollection()->map(fn (Lamaran $application) => $this->applicationData($application)),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
            ],
        ]);
    }

    public function show(Request $request, int $lamaran): JsonResponse
    {
        $application = Lamaran::query()->forProvider($request->user())
            ->with(['lowongan:id_lowongan,judul', 'pencari:id_pengguna,nama,email,nomor_telepon,deskripsi_diri,keahlian,pengalaman_kerja,cv,foto_profil'])
            ->findOrFail($lamaran);
        $applicant = $application->pencari;

        return response()->json(['data' => [
            ...$this->applicationData($application),
            'pesan_lamaran' => $application->pesan_lamaran,
            'status_pekerjaan' => $application->status_pekerjaan,
            'pelamar' => [
                'nama' => $applicant->nama,
                'email' => $applicant->email,
                'nomor_telepon' => $applicant->nomor_telepon,
                'deskripsi_diri' => $applicant->deskripsi_diri,
                'keahlian' => $applicant->keahlian,
                'pengalaman_kerja' => $applicant->pengalaman_kerja,
                'has_cv' => $this->availableFile($applicant->cv, 'cv'),
                'has_photo' => $this->availableFile($applicant->foto_profil, 'foto'),
            ],
        ]]);
    }

    public function file(Request $request, int $lamaran, string $type): BinaryFileResponse
    {
        $application = Lamaran::query()->forProvider($request->user())
            ->with('pencari:id_pengguna,cv,foto_profil')->findOrFail($lamaran);
        $path = $type === 'cv' ? $application->pencari->cv : $application->pencari->foto_profil;
        abort_unless($this->availableFile($path, $type), 404);

        if ($type === 'cv') {
            return response()->download(Storage::disk('public')->path($path), 'CV-pelamar.pdf', [
                'Content-Type' => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return response()->file(Storage::disk('public')->path($path), ['X-Content-Type-Options' => 'nosniff']);
    }

    /** @return array<string, mixed> */
    private function applicationData(Lamaran $application): array
    {
        return [
            'id' => $application->id_lamaran,
            'nama_pelamar' => $application->pencari->nama,
            'pekerjaan' => $application->lowongan->judul,
            'tanggal_melamar' => $application->tanggal_lamaran?->toIso8601String(),
            'status' => $application->status_lamaran,
            'status_label' => Lamaran::STATUS_LABELS[$application->status_lamaran] ?? $application->status_lamaran,
        ];
    }

    private function availableFile(?string $path, string $type): bool
    {
        $pattern = $type === 'cv' ? '~\Acv/[a-zA-Z0-9_-]+\.pdf\z~i' : '~\Afoto-profil/[a-zA-Z0-9_-]+\.(?:jpe?g|png)\z~i';

        return $path !== null && preg_match($pattern, $path) === 1 && Storage::disk('public')->exists($path);
    }
}
