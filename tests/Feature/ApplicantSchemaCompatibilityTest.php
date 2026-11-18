<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class ApplicantSchemaCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    private function migration(): Migration
    {
        return require database_path('migrations/2026_11_14_091113_create_missing_vacancy_monitoring_tables.php');
    }

    private function legacyTable(): void
    {
        Schema::table('lamaran', function (Blueprint $table): void {
            $table->dropUnique(['id_lowongan', 'id_pencari']);
        });
        Schema::rename('lamaran', 'lamaran_canonical_test_backup');
        Schema::create('lamaran', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
        });
    }

    public function test_existing_canonical_applications_and_status_values_are_preserved(): void
    {
        $application = Lamaran::factory()->create(['status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai', 'pesan_lamaran' => 'Pesan asli']);
        $this->migration()->up();
        $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->id_lamaran, 'status_lamaran' => 'diterima', 'status_pekerjaan' => 'selesai', 'pesan_lamaran' => 'Pesan asli']);
    }

    public function test_empty_legacy_table_is_archived_before_creating_the_canonical_table(): void
    {
        $this->legacyTable();
        $this->migration()->up();
        $this->assertTrue(Schema::hasColumn('lamaran_monitoring_legacy', 'status'));
        $this->assertTrue(Schema::hasColumn('lamaran', 'id_lamaran'));
        $this->assertDatabaseCount('lamaran', 0);
        $this->assertDatabaseCount('lamaran_monitoring_legacy', 0);
    }

    public function test_populated_legacy_table_stops_migration_without_changing_data(): void
    {
        $this->legacyTable();
        DB::table('lamaran')->insert(['id' => 42, 'status' => 'diterima']);
        $this->expectException(RuntimeException::class);
        try {
            $this->migration()->up();
        } finally {
            $this->assertDatabaseHas('lamaran', ['id' => 42, 'status' => 'diterima']);
            $this->assertFalse(Schema::hasTable('lamaran_monitoring_legacy'));
        }
    }

    public function test_automatic_rollback_cannot_drop_existing_application_data(): void
    {
        $application = Lamaran::factory()->create();
        $this->expectException(RuntimeException::class);
        try {
            $this->migration()->down();
        } finally {
            $this->assertDatabaseHas('lamaran', ['id_lamaran' => $application->id_lamaran]);
        }
    }
}
