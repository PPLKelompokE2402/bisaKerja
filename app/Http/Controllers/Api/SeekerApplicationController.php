<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeekerApplicationController extends Controller
{
    public function page(): View
    {
        return view('seeker.applications');
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'applications_page' => ['sometimes', 'integer', 'min:1'],
            'history_page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $owned = Lamaran::query()->where('id_pencari', $request->user()->getKey())
            ->select(['id_lamaran', 'id_lowongan', 'status_lamaran', 'status_pekerjaan', 'tanggal_lamaran'])
            ->with('lowongan:id_lowongan,judul,lokasi,upah');

        $applicationsQuery = (clone $owned)->where(function (Builder $query): void {
            $query->whereNull('status_pekerjaan')->orWhere('status_pekerjaan', '!=', 'selesai');
        })->orderByDesc('tanggal_lamaran')->orderByDesc('id_lamaran');

        $historyQuery = (clone $owned)->where('status_pekerjaan', 'selesai')
            ->orderByDesc('tanggal_lamaran')->orderByDesc('id_lamaran');

        return response()->json([
            'applications' => $this->tableData($this->paginate($applicationsQuery, 'applications_page', $validated['applications_page'] ?? 1)),
            'history' => $this->tableData($this->paginate($historyQuery, 'history_page', $validated['history_page'] ?? 1)),
        ]);
    }

    private function paginate(Builder $query, string $pageName, int $page): LengthAwarePaginator
    {
        $result = $query->paginate(15, ['*'], $pageName, $page);

        return $page > $result->lastPage()
            ? $query->paginate(15, ['*'], $pageName, $result->lastPage())
            : $result;
    }

    /** @return array{data: array, meta: array<string, int|null>} */
    private function tableData(LengthAwarePaginator $applications): array
    {
        return [
            'data' => $applications->getCollection()->map(function (Lamaran $application): array {
                $status = $application->status_pekerjaan === 'selesai' ? $application->status_pekerjaan : $application->status_lamaran;

                return [
                    'id' => $application->getKey(),
                    'pekerjaan' => $application->lowongan?->judul,
                    'lokasi' => $application->lowongan?->lokasi,
                    'upah' => $application->lowongan?->upah,
                    'tanggal_melamar' => $application->tanggal_lamaran?->toIso8601String(),
                    'status' => $status,
                    'status_label' => $status === 'selesai' ? 'Selesai' : (Lamaran::STATUS_LABELS[$status] ?? $status),
                    'status_lamaran' => $application->status_lamaran,
                    'status_pekerjaan' => $application->status_pekerjaan,
                ];
            })->all(),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'total' => $applications->total(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
            ],
        ];
    }
}
