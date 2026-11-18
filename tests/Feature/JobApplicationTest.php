<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pemetaan otomatis fitur pengajuan lamaran (route, tabel, kolom, input form)
 * dari source code project yang sedang diuji. Berisi fungsi murni tanpa dependensi Laravel.
 */
final class ApplicationFeatureMap
{
    /** Urutan = prioritas pemetaan. Pola pertama paling ketat, pola berikutnya lebih longgar. */
    public const PATTERNS = [
        'cv'      => ['/^(cv|resume|cv_?file|file_?cv|cv_?path|resume_?path)$/i', '/cv|resume|berkas|dokumen|document|^file$/i'],
        'skills'  => ['/skill|keahlian|kemampuan|expertise|kompetensi/i'],
        'age'     => ['/^(age|umur|usia)$/i', '/umur|usia|^age_|_age$/i'],
        'birth'   => ['/(tanggal|tgl).*lahir|birth_?date|date_?of_?birth|^dob$|^birthday$/i', '/lahir|birth/i'],
        'address' => ['/tempat_?tinggal|alamat|address|domisili|residence/i', '/tempat|kota|city|location|lokasi/i'],
        'name'    => ['/^(name|nama|nama_?lengkap|full_?name|nama_?pelamar|applicant_?name)$/i', '/nama|name/i'],
    ];

    private const ROUTE_PATTERN = '#(^|/)(apply|lamar|lamaran|application|applications|job-applications|job_applications|lamar-pekerjaan)(/|$)#i';
    private const NAME_PATTERN  = '/(apply|lamar|application)/i';

    /** @param string[] $names @return array<string,?string> key logis => nama input/kolom */
    public static function mapNames(array $names): array
    {
        $map  = [];
        $used = [];

        foreach (self::PATTERNS as $key => $patterns) {
            $map[$key] = null;
            foreach ($patterns as $pattern) {
                foreach ($names as $name) {
                    if (in_array($name, $used, true)) {
                        continue;
                    }
                    if (preg_match($pattern, $name)) {
                        $map[$key] = $name;
                        $used[]    = $name;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /** @param string[] $names @param string[] $patterns */
    public static function firstMatch(array $names, array $patterns): ?string
    {
        foreach ($patterns as $pattern) {
            foreach ($names as $name) {
                if (preg_match($pattern, $name)) {
                    return $name;
                }
            }
        }

        return null;
    }

    /** Ganti semua {parameter} pada URI dengan id lowongan. */
    public static function fillUri(string $uri, int|string $id): string
    {
        return '/' . ltrim((string) preg_replace('/\{[^}]+\}/', (string) $id, $uri), '/');
    }

    /**
     * @param array<int,array{uri:string,methods:string[],name:?string}> $routes
     * @return array{form:?array,store:?array}
     */
    public static function pickRoutes(array $routes): array
    {
        $form   = null;
        $stores = [];

        foreach ($routes as $r) {
            $uri  = $r['uri'];
            $name = (string) ($r['name'] ?? '');

            if (preg_match('#^(_|up$|api/)#', $uri)) {
                continue;
            }
            if (! preg_match(self::ROUTE_PATTERN, $uri) && ! preg_match(self::NAME_PATTERN, $name)) {
                continue;
            }

            if (in_array('GET', $r['methods'], true) && $form === null
                && (preg_match('#(apply|lamar|lamaran|create|form)$#i', $uri) || preg_match('/(create|apply|form)$/i', $name))) {
                $form = $r;
            }

            if (in_array('POST', $r['methods'], true)
                && ! preg_match('#(update|status|delete|destroy|cancel|approve|reject)#i', $uri)) {
                $stores[] = $r;
            }
        }

        $store = $stores[0] ?? null;
        if ($form !== null) {
            foreach ($stores as $candidate) {
                if ($candidate['uri'] === $form['uri']) {
                    $store = $candidate;
                    break;
                }
            }
        }

        return ['form' => $form, 'store' => $store];
    }

    /** @param string[] $tables */
    public static function pickTable(array $tables): ?string
    {
        foreach (['job_applications', 'applications', 'lamarans', 'lamaran', 'job_applicants', 'applicants', 'pelamar'] as $exact) {
            if (in_array($exact, $tables, true)) {
                return $exact;
            }
        }

        foreach ($tables as $table) {
            if (preg_match('/applic|lamar|apply|pelamar/i', $table)) {
                return $table;
            }
        }

        return null;
    }
}

/**
 * WHITEBOX TESTING - Fitur Pengajuan Lamaran Pekerjaan (bisaKerja)
 * TC-01 s.d. TC-13
 *
 * Jalankan : php artisan test tests/Feature/JobApplicationTest.php
 *
 * Script ini membaca route, tabel, kolom, dan nama input form langsung dari project,
 * jadi tidak bergantung pada nama tertentu. Kalau fitur belum lengkap, setiap test
 * gagal dengan pesan yang menjelaskan bagian mana yang belum ada.
 */
class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    private const STATUS_AWAL = 'PENDING';

    /** Judul dan keterangan hasil yang diharapkan untuk laporan per TC. */
    private const TC = [
        '01' => ['Buka form lamaran pekerjaan', 'Form terbuka (status 200) dan semua input tersedia: nama, tanggal lahir, umur, tempat tinggal, keahlian, CV.'],
        '02' => ['Validasi seluruh field kosong', 'Pengajuan ditolak, muncul error pada kelima field wajib, tidak ada data tersimpan.'],
        '03' => ['Validasi tanpa nama', 'Pengajuan ditolak, error muncul pada nama saja, tidak ada data tersimpan.'],
        '04' => ['Validasi tanpa tanggal lahir', 'Pengajuan ditolak, error muncul pada tanggal lahir saja, tidak ada data tersimpan.'],
        '05' => ['Validasi tanpa umur', 'Pengajuan ditolak, error muncul pada umur saja, tidak ada data tersimpan.'],
        '06' => ['Validasi tanpa tempat tinggal', 'Pengajuan ditolak, error muncul pada tempat tinggal saja, tidak ada data tersimpan.'],
        '07' => ['Validasi tanpa keahlian', 'Pengajuan ditolak, error muncul pada keahlian saja, tidak ada data tersimpan.'],
        '08' => ['Pengajuan lamaran tanpa CV', 'Lamaran tersimpan dan kolom CV bernilai NULL (CV opsional).'],
        '09' => ['Pengajuan lamaran dengan CV PDF', 'Lamaran tersimpan, file PDF ada di storage, dan path CV tercatat di database.'],
        '10' => ['Pencegahan pengajuan ganda', 'Pengajuan kedua untuk lowongan yang sama ditolak, data tetap 1 dan tidak tertimpa.'],
        '11' => ['Biodata akun user tidak berubah', 'Nama, email, dan password akun user sama seperti sebelum pengajuan.'],
        '12' => ['Data lamaran tersimpan sesuai input', 'Semua field lamaran di database sama dengan yang diisi pada form.'],
        '13' => ['Status awal lamaran PENDING', 'Status awal lamaran tersimpan sebagai PENDING.'],
    ];

    /** @var array<string,array{label:string,ket:string}> hasil per TC, diisi saat tearDown */
    private static array $laporan = [];

    private User $user;
    private Job $job;

    private string $formUrl  = '';
    private string $storeUrl = '';
    private string $table    = '';

    /** @var array<string,?string> key logis => nama input pada form */
    private array $formMap = [];
    /** @var array<string,?string> key logis => nama kolom di tabel lamaran */
    private array $colMap = [];
    /** @var string[] */
    private array $formInputs = [];
    /** @var string[] */
    private array $columns = [];

    private ?string $jobIdInput = null;
    private ?string $userCol = null;
    private ?string $jobCol = null;
    private ?string $statusCol = null;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (array_unique(['public', 'local', (string) config('filesystems.default')]) as $disk) {
            Storage::fake($disk);
        }

        $this->user = User::factory()->create([
            'name'  => 'Akun Cay',
            'email' => 'cay@example.com',
        ]);

        $this->job = Job::create([
            'title'        => 'Backend Engineer (Laravel)',
            'company'      => 'CV Karya Bekasi',
            'location'     => 'Bekasi',
            'type'         => 'Full-time',
            'salary_min'   => 8000000,
            'salary_max'   => 12000000,
            'description'  => 'Mengembangkan REST API.',
            'requirements' => "1. Menguasai Laravel.\n2. Paham SQL.",
        ]);

        $this->discoverFeature();
    }

    protected function tearDown(): void
    {
        $this->catatHasil();

        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        self::cetakLaporan();

        parent::tearDownAfterClass();
    }

    // ------------------------------------------------------------------
    // LAPORAN HASIL PER TC
    // ------------------------------------------------------------------

    private function catatHasil(): void
    {
        if (! preg_match('/^test_tc(\d{2})_/', $this->name(), $m) || ! isset(self::TC[$m[1]])) {
            return;
        }

        $status = $this->status();

        if ($status->isSuccess()) {
            $label = 'PASS';
            $ket   = self::TC[$m[1]][1];
        } else {
            $label = $status->isFailure() ? 'FAIL' : ($status->isError() ? 'ERROR' : 'SKIP');
            $ket   = trim((string) preg_replace('/\s+/', ' ', $status->message()));
            $ket   = mb_strlen($ket) > 260 ? mb_substr($ket, 0, 257) . '...' : $ket;
        }

        self::$laporan[$m[1]] = ['label' => $label, 'ket' => $ket];
    }

    private static function cetakLaporan(): void
    {
        if (self::$laporan === []) {
            return;
        }

        ksort(self::$laporan);

        $garis  = str_repeat('=', 78);
        $tipis  = str_repeat('-', 78);
        $baris  = ["", $garis, ' HASIL WHITEBOX TESTING - PENGAJUAN LAMARAN PEKERJAAN', ' Waktu: ' . date('d/m/Y H:i:s'), $garis];
        $lulus  = 0;

        foreach (self::$laporan as $no => $hasil) {
            $lulus += $hasil['label'] === 'PASS' ? 1 : 0;

            $baris[] = sprintf(' [%s] TC-%s  %s', $hasil['label'], $no, self::TC[$no][0]);
            foreach (explode("\n", wordwrap('Hasil : ' . $hasil['ket'], 66, "\n", true)) as $i => $teks) {
                $baris[] = '         ' . ($i === 0 ? '' : '        ') . $teks;
            }
        }

        $total   = count(self::$laporan);
        $baris[] = $tipis;
        $baris[] = sprintf(' TOTAL: %d | LULUS: %d | GAGAL: %d', $total, $lulus, $total - $lulus);
        $baris[] = $garis;
        $baris[] = '';

        $teks = implode(PHP_EOL, $baris);

        fwrite(STDERR, $teks);
        @file_put_contents(dirname(__DIR__, 2) . '/storage/logs/hasil-whitebox-lamaran.txt', $teks);
    }

    // ------------------------------------------------------------------
    // DETEKSI FITUR DARI SOURCE CODE
    // ------------------------------------------------------------------

    private function discoverFeature(): void
    {
        // 1. Route
        $all  = [];
        $list = [];
        foreach (Route::getRoutes() as $route) {
            $all[] = ['uri' => $route->uri(), 'methods' => $route->methods(), 'name' => $route->getName()];
            if (! preg_match('#^(_|up$|storage/)#', $route->uri())) {
                $list[] = implode('|', array_diff($route->methods(), ['HEAD'])) . ' /' . ltrim($route->uri(), '/');
            }
        }

        $picked = ApplicationFeatureMap::pickRoutes($all);

        if ($picked['store'] === null) {
            $this->fail(
                "FITUR BELUM ADA: tidak ditemukan route POST untuk pengajuan lamaran "
                . "(contoh: POST /jobs/{id}/apply).\nRoute yang terdaftar saat ini:\n  - "
                . implode("\n  - ", $list)
                . "\nLengkapi dulu route, controller, form, dan migration lamaran, lalu jalankan ulang."
            );
        }

        $this->storeUrl = ApplicationFeatureMap::fillUri($picked['store']['uri'], $this->job->id);
        $this->formUrl  = $picked['form'] !== null
            ? ApplicationFeatureMap::fillUri($picked['form']['uri'], $this->job->id)
            : route('jobs.show', $this->job->id);

        // 2. Input pada form
        $response = $this->actingAs($this->user)->get($this->formUrl);
        if ($response->getStatusCode() !== 200) {
            $this->fail("Form lamaran tidak bisa dibuka: GET {$this->formUrl} mengembalikan status "
                . $response->getStatusCode() . ' (cek storage/logs/laravel.log).');
        }

        preg_match_all('/<(?:input|textarea|select)\b[^>]*\bname\s*=\s*["\']([^"\']+)["\']/i', $response->getContent(), $m);
        $inputs = array_map(fn ($n) => rtrim($n, '[]'), $m[1]);
        $inputs = array_values(array_diff(array_unique($inputs), ['_token', '_method']));

        if (! str_contains($picked['store']['uri'], '{')) {
            $this->jobIdInput = ApplicationFeatureMap::firstMatch(
                $inputs,
                ['/^(job_id|job_listing_id|lowongan_id|id_lowongan|vacancy_id)$/i']
            );
            $inputs = array_values(array_diff($inputs, [$this->jobIdInput]));
        }

        $this->formInputs = $inputs;
        $this->formMap    = ApplicationFeatureMap::mapNames($inputs);

        // 3. Tabel dan kolom
        $tables      = array_column(Schema::getTables(), 'name');
        $this->table = (string) ApplicationFeatureMap::pickTable($tables);

        if ($this->table === '') {
            $this->fail('FITUR BELUM ADA: tabel lamaran tidak ditemukan setelah migrasi dijalankan. '
                . 'Tabel yang ada: ' . implode(', ', $tables) . '. Buat migration tabel lamaran.');
        }

        $this->columns   = Schema::getColumnListing($this->table);
        $this->userCol   = ApplicationFeatureMap::firstMatch($this->columns, ['/^(user_id|pelamar_id|applicant_id|id_user)$/i']);
        $this->jobCol    = ApplicationFeatureMap::firstMatch($this->columns, ['/^(job_id|job_listing_id|lowongan_id|id_lowongan|vacancy_id)$/i']);
        $this->statusCol = ApplicationFeatureMap::firstMatch($this->columns, ['/^(status|status_lamaran|application_status)$/i']);

        $dataColumns = array_filter($this->columns, fn ($c) => ! preg_match('/^id$|_id$|^status|created_at|updated_at|deleted_at/i', $c));
        $this->colMap = ApplicationFeatureMap::mapNames(array_values($dataColumns));
    }

    // ------------------------------------------------------------------
    // HELPER
    // ------------------------------------------------------------------

    /** Nama input form untuk key logis (name, birth, age, address, skills, cv). */
    private function f(string $key): string
    {
        $name = $this->formMap[$key] ?? null;
        if ($name === null) {
            $this->fail("Input form untuk '{$key}' tidak ditemukan. Input yang terdeteksi di {$this->formUrl}: "
                . (implode(', ', $this->formInputs) ?: '(tidak ada)'));
        }

        return $name;
    }

    /** Nama kolom tabel lamaran untuk key logis. */
    private function c(string $key): string
    {
        $name = $this->colMap[$key] ?? null;
        if ($name === null) {
            $this->fail("Kolom untuk '{$key}' tidak ditemukan di tabel '{$this->table}'. Kolom yang ada: "
                . implode(', ', $this->columns));
        }

        return $name;
    }

    private function validData(array $override = []): array
    {
        $data = [
            $this->f('name')    => 'Cay Pelamar',
            $this->f('birth')   => '2002-03-15',
            $this->f('age')     => Carbon::parse('2002-03-15')->age,
            $this->f('address') => 'Jakarta Selatan',
            $this->f('skills')  => 'Laravel, MySQL, Tailwind',
        ];

        if ($this->jobIdInput !== null) {
            $data[$this->jobIdInput] = $this->job->id;
        }

        return array_merge($data, $override);
    }

    /** Kirim form seperti browser: login, datang dari halaman form, lalu POST. */
    private function submit(array $data)
    {
        return $this->actingAs($this->user)
            ->from($this->formUrl)
            ->post($this->storeUrl, $data);
    }

    private function fakePdf(): UploadedFile
    {
        return UploadedFile::fake()->create('cv-cay.pdf', 200, 'application/pdf');
    }

    private function totalLamaran(): int
    {
        return DB::table($this->table)->count();
    }

    private function lamaranTerakhir(string $tc): object
    {
        $row = DB::table($this->table)->orderByDesc('id')->first();
        $this->assertNotNull($row, "[{$tc}] Data lamaran tidak ditemukan di tabel '{$this->table}'.");

        return $row;
    }

    private function cvTersimpan(string $path): bool
    {
        $variants = array_unique([$path, preg_replace('#^/?(storage|public)/#', '', $path)]);

        foreach (array_unique(['public', 'local', (string) config('filesystems.default')]) as $disk) {
            foreach ($variants as $variant) {
                if (Storage::disk($disk)->exists($variant)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** Validasi gagal: redirect, error pada field yang dimaksud, dan tidak ada data tersimpan. */
    private function assertValidasiGagal(string $tc, $response, array $fieldError, array $fieldBersih = []): void
    {
        $this->assertSame(302, $response->getStatusCode(),
            "[{$tc}] Validasi gagal seharusnya redirect (302), tetapi status " . $response->getStatusCode());
        $response->assertSessionHasErrors($fieldError);
        if ($fieldBersih !== []) {
            $response->assertSessionDoesntHaveErrors($fieldBersih);
        }
        $this->assertSame(0, $this->totalLamaran(),
            "[{$tc}] Data tidak boleh tersimpan saat validasi gagal.");
    }

    // ------------------------------------------------------------------
    // TC-01 Buka form lamaran pekerjaan
    // ------------------------------------------------------------------
    public function test_tc01_buka_form_lamaran_pekerjaan(): void
    {
        $response = $this->actingAs($this->user)->get($this->formUrl);

        $response->assertOk();

        foreach (['name', 'birth', 'age', 'address', 'skills', 'cv'] as $key) {
            $this->assertNotNull($this->formMap[$key] ?? null,
                "[TC-01] Form harus memiliki input '{$key}'. Input terdeteksi: " . implode(', ', $this->formInputs));
        }
    }

    // ------------------------------------------------------------------
    // TC-02 Validasi seluruh field kosong
    // ------------------------------------------------------------------
    public function test_tc02_validasi_seluruh_field_kosong(): void
    {
        $fields = [$this->f('name'), $this->f('birth'), $this->f('age'), $this->f('address'), $this->f('skills')];

        $data = array_fill_keys($fields, '');
        if ($this->jobIdInput !== null) {
            $data[$this->jobIdInput] = $this->job->id;
        }

        $this->assertValidasiGagal('TC-02', $this->submit($data), $fields);
    }

    // ------------------------------------------------------------------
    // TC-03 Validasi tanpa nama
    // ------------------------------------------------------------------
    public function test_tc03_validasi_tanpa_nama(): void
    {
        $response = $this->submit($this->validData([$this->f('name') => '']));

        $this->assertValidasiGagal('TC-03', $response,
            [$this->f('name')],
            [$this->f('birth'), $this->f('age'), $this->f('address'), $this->f('skills')]);
    }

    // ------------------------------------------------------------------
    // TC-04 Validasi tanpa tanggal lahir
    // ------------------------------------------------------------------
    public function test_tc04_validasi_tanpa_tanggal_lahir(): void
    {
        $response = $this->submit($this->validData([$this->f('birth') => '']));

        $this->assertValidasiGagal('TC-04', $response,
            [$this->f('birth')],
            [$this->f('name'), $this->f('age'), $this->f('address'), $this->f('skills')]);
    }

    // ------------------------------------------------------------------
    // TC-05 Validasi tanpa umur
    // ------------------------------------------------------------------
    public function test_tc05_validasi_tanpa_umur(): void
    {
        $response = $this->submit($this->validData([$this->f('age') => '']));

        $this->assertValidasiGagal('TC-05', $response,
            [$this->f('age')],
            [$this->f('name'), $this->f('birth'), $this->f('address'), $this->f('skills')]);
    }

    // ------------------------------------------------------------------
    // TC-06 Validasi tanpa tempat tinggal
    // ------------------------------------------------------------------
    public function test_tc06_validasi_tanpa_tempat_tinggal(): void
    {
        $response = $this->submit($this->validData([$this->f('address') => '']));

        $this->assertValidasiGagal('TC-06', $response,
            [$this->f('address')],
            [$this->f('name'), $this->f('birth'), $this->f('age'), $this->f('skills')]);
    }

    // ------------------------------------------------------------------
    // TC-07 Validasi tanpa keahlian
    // ------------------------------------------------------------------
    public function test_tc07_validasi_tanpa_keahlian(): void
    {
        $response = $this->submit($this->validData([$this->f('skills') => '']));

        $this->assertValidasiGagal('TC-07', $response,
            [$this->f('skills')],
            [$this->f('name'), $this->f('birth'), $this->f('age'), $this->f('address')]);
    }

    // ------------------------------------------------------------------
    // TC-08 Pengajuan lamaran tanpa CV
    // ------------------------------------------------------------------
    public function test_tc08_pengajuan_lamaran_tanpa_cv(): void
    {
        $response = $this->submit($this->validData());

        $this->assertContains($response->getStatusCode(), [200, 201, 302, 303],
            '[TC-08] Status respons tidak sesuai: ' . $response->getStatusCode());
        $response->assertSessionHasNoErrors();

        $this->assertSame(1, $this->totalLamaran(), '[TC-08] Lamaran tanpa CV harus tersimpan (CV opsional).');

        $row = $this->lamaranTerakhir('TC-08');
        $this->assertNull($row->{$this->c('cv')}, '[TC-08] Kolom CV harus NULL saat tidak ada file diunggah.');
    }

    // ------------------------------------------------------------------
    // TC-09 Pengajuan lamaran dengan CV PDF
    // ------------------------------------------------------------------
    public function test_tc09_pengajuan_lamaran_dengan_cv_pdf(): void
    {
        $response = $this->submit($this->validData([$this->f('cv') => $this->fakePdf()]));

        $response->assertSessionHasNoErrors();
        $this->assertSame(1, $this->totalLamaran(), '[TC-09] Lamaran dengan CV PDF harus tersimpan.');

        $path = (string) $this->lamaranTerakhir('TC-09')->{$this->c('cv')};

        $this->assertNotSame('', $path, '[TC-09] Kolom CV harus terisi path file.');
        $this->assertStringEndsWith('.pdf', strtolower($path), "[TC-09] Path CV bukan PDF: {$path}");
        $this->assertTrue($this->cvTersimpan($path), "[TC-09] File CV tidak ditemukan di storage: {$path}");
    }

    // ------------------------------------------------------------------
    // TC-10 Pencegahan pengajuan ganda
    // ------------------------------------------------------------------
    public function test_tc10_pencegahan_pengajuan_ganda(): void
    {
        $this->submit($this->validData())->assertSessionHasNoErrors();
        $this->assertSame(1, $this->totalLamaran(), '[TC-10] Pengajuan pertama harus tersimpan.');

        $kedua = $this->submit($this->validData([$this->f('name') => 'Cay Pelamar Dua']));

        $this->assertLessThan(500, $kedua->getStatusCode(),
            '[TC-10] Pengajuan ganda harus ditangani dengan pesan, bukan error server (status ' . $kedua->getStatusCode() . ').');
        $this->assertSame(1, $this->totalLamaran(), '[TC-10] Pengajuan ganda tidak boleh membuat data kedua.');

        $query = DB::table($this->table);
        if ($this->userCol !== null) {
            $query->where($this->userCol, $this->user->id);
        }
        if ($this->jobCol !== null) {
            $query->where($this->jobCol, $this->job->id);
        }
        $this->assertSame(1, $query->count(), '[TC-10] Kombinasi pelamar dan lowongan harus unik.');

        $this->assertSame('Cay Pelamar', $this->lamaranTerakhir('TC-10')->{$this->c('name')},
            '[TC-10] Data pengajuan pertama tidak boleh tertimpa.');
    }

    // ------------------------------------------------------------------
    // TC-11 Biodata akun user tidak berubah
    // ------------------------------------------------------------------
    public function test_tc11_biodata_akun_user_tidak_berubah(): void
    {
        $sebelum = DB::table('users')->where('id', $this->user->id)->first();

        $this->submit($this->validData([$this->f('name') => 'Nama Di Form Berbeda']))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $this->totalLamaran(), '[TC-11] Lamaran harus tersimpan sebagai prasyarat.');

        $sesudah = DB::table('users')->where('id', $this->user->id)->first();

        $this->assertSame($sebelum->name, $sesudah->name, '[TC-11] Nama akun user berubah.');
        $this->assertSame($sebelum->email, $sesudah->email, '[TC-11] Email akun user berubah.');
        $this->assertSame($sebelum->password, $sesudah->password, '[TC-11] Password akun user berubah.');
        $this->assertSame('Akun Cay', $sesudah->name);
    }

    // ------------------------------------------------------------------
    // TC-12 Data lamaran tersimpan sesuai input
    // ------------------------------------------------------------------
    public function test_tc12_data_lamaran_tersimpan_sesuai_input(): void
    {
        $input = $this->validData();

        $this->submit($input)->assertSessionHasNoErrors();
        $this->assertSame(1, $this->totalLamaran(), '[TC-12] Lamaran harus tersimpan.');

        $row = $this->lamaranTerakhir('TC-12');

        if ($this->userCol !== null) {
            $this->assertEquals($this->user->id, $row->{$this->userCol}, '[TC-12] user_id tidak sesuai.');
        }
        if ($this->jobCol !== null) {
            $this->assertEquals($this->job->id, $row->{$this->jobCol}, '[TC-12] id lowongan tidak sesuai.');
        }

        $this->assertSame($input[$this->f('name')], $row->{$this->c('name')}, '[TC-12] Nama tidak sesuai.');
        $this->assertSame(
            Carbon::parse($input[$this->f('birth')])->toDateString(),
            Carbon::parse($row->{$this->c('birth')})->toDateString(),
            '[TC-12] Tanggal lahir tidak sesuai.'
        );
        $this->assertEquals($input[$this->f('age')], $row->{$this->c('age')}, '[TC-12] Umur tidak sesuai.');
        $this->assertSame($input[$this->f('address')], $row->{$this->c('address')}, '[TC-12] Tempat tinggal tidak sesuai.');
        $this->assertSame($input[$this->f('skills')], $row->{$this->c('skills')}, '[TC-12] Keahlian tidak sesuai.');
    }

    // ------------------------------------------------------------------
    // TC-13 Status awal lamaran PENDING
    // ------------------------------------------------------------------
    public function test_tc13_status_awal_lamaran_pending(): void
    {
        $this->submit($this->validData())->assertSessionHasNoErrors();
        $this->assertSame(1, $this->totalLamaran(), '[TC-13] Lamaran harus tersimpan.');

        $this->assertNotNull($this->statusCol,
            "[TC-13] Kolom status tidak ditemukan di tabel '{$this->table}'. Kolom yang ada: " . implode(', ', $this->columns));

        $this->assertSame(self::STATUS_AWAL, $this->lamaranTerakhir('TC-13')->{$this->statusCol},
            '[TC-13] Status awal lamaran harus ' . self::STATUS_AWAL . '.');
    }
}