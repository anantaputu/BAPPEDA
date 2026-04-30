<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataValue;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class FunctionalModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_case_01_login_dengan_kredensial_benar_admin(): void
    {
        $response = $this->post('/login', [
            'email' => 'lepang@gmail.com',
            'password' => 'lepang123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertSame('lepang@gmail.com', auth()->user()->email);
    }

    public function test_case_02_login_dengan_kredensial_benar_inputer_dan_hak_akses_berbeda(): void
    {
        $response = $this->post('/login', [
            'email' => 'nanta@gmail.com',
            'password' => 'ananta',
        ]);

        $response->assertRedirect(route('inputer.dashboard'));
        $this->assertAuthenticated();

        $this->get('/admin/users')->assertForbidden();
        $this->get(route('inputer.dashboard'))->assertOk();
    }

    public function test_case_03_login_dengan_password_salah(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'lepang@gmail.com',
            'password' => 'salah-total',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_case_04_logout_dari_sistem(): void
    {
        $this->actingAs($this->adminUser());

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_case_05_tambah_data_baru_dengan_form_lengkap_sukses(): void
    {
        $inputer = $this->inputerUser();

        $response = $this->actingAs($inputer)->post(route('inputer.storeSingle'), [
            'nama_data' => 'Jumlah Penduduk Miskin Test',
            'id_tema' => 1,
            'id_urusan' => 1,
            'id_bidang' => 1,
            'id_frekuensi' => 1,
            'satuan' => 'Jiwa',
            'sumber' => 'BPS',
            'deskripsi' => 'Data uji tambah',
            'tahun_terbit' => 2025,
            'values' => [
                ['tahun' => '2023', 'nilai' => '120.5'],
                ['tahun' => '2024', 'nilai' => '130.25'],
            ],
        ]);

        $response->assertRedirect(route('inputer.dashboard'));
        $response->assertSessionHas('success');

        $data = Data::where('nama_data', 'Jumlah Penduduk Miskin Test')->first();

        $this->assertNotNull($data);
        $this->assertSame($inputer->id, $data->id_user);
        $this->assertDatabaseHas('data_values', [
            'id_data' => $data->id_data,
            'tahun' => '2023',
            'nilai' => 120.5,
        ]);
        $this->assertDatabaseHas('data_values', [
            'id_data' => $data->id_data,
            'tahun' => '2024',
            'nilai' => 130.25,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'id_data' => $data->id_data,
            'action' => 'UPLOAD',
        ]);
    }

    public function test_case_06_tambah_data_dengan_kolom_wajib_kosong_muncul_validasi_error(): void
    {
        $response = $this->actingAs($this->inputerUser())
            ->from(route('inputer.createSingle'))
            ->post(route('inputer.storeSingle'), [
                'nama_data' => '',
                'id_tema' => '',
                'id_urusan' => '',
                'id_bidang' => '',
                'id_frekuensi' => '',
                'satuan' => '',
                'sumber' => '',
                'values' => [],
            ]);

        $response->assertRedirect(route('inputer.createSingle'));
        $response->assertSessionHasErrors([
            'nama_data',
            'id_tema',
            'id_urusan',
            'id_bidang',
            'id_frekuensi',
            'satuan',
            'sumber',
            'values',
        ]);
    }

    public function test_case_07_edit_data_yang_sudah_ada_mengubah_nilai_time_series(): void
    {
        $inputer = $this->inputerUser();
        $data = $this->createDataset($inputer, 'Indikator Edit');

        $response = $this->actingAs($inputer)->put(route('inputer.data.update', $data->id_data), [
            'nama_data' => 'Indikator Edit',
            'id_tema' => 1,
            'id_urusan' => 1,
            'id_bidang' => 1,
            'id_frekuensi' => 1,
            'satuan' => 'Persen',
            'sumber' => 'BPS Update',
            'deskripsi' => 'Data setelah edit',
            'tahun_terbit' => 2026,
            'values' => [
                ['tahun' => '2024', 'nilai' => '55'],
                ['tahun' => '2025', 'nilai' => '70'],
            ],
        ]);

        $response->assertRedirect(route('inputer.dashboard'));
        $this->assertDatabaseHas('data_values', [
            'id_data' => $data->id_data,
            'tahun' => '2024',
            'nilai' => 55.0,
        ]);
        $this->assertDatabaseHas('data_values', [
            'id_data' => $data->id_data,
            'tahun' => '2025',
            'nilai' => 70.0,
        ]);
        $this->assertDatabaseMissing('data_values', [
            'id_data' => $data->id_data,
            'tahun' => '2023',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'id_data' => $data->id_data,
            'action' => 'EDIT',
        ]);
    }

    public function test_case_08_hapus_data_dengan_konfirmasi_popup_terpasang(): void
    {
        $inputer = $this->inputerUser();
        $data = $this->createDataset($inputer, 'Indikator Hapus');

        $response = $this->actingAs($inputer)->delete(route('inputer.data.destroy', $data->id_data));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('data', ['id_data' => $data->id_data]);
        $this->assertDatabaseHas('activity_logs', [
            'id_data' => $data->id_data,
            'action' => 'DELETE',
            'target_name' => 'Indikator Hapus',
        ]);

        $indexPage = File::get(resource_path('js/Pages/Inputer/Data/Index.vue'));
        $deleteModal = File::get(resource_path('js/Components/Layout/DeleteModal.vue'));

        $this->assertStringContainsString("import DeleteModal", $indexPage);
        $this->assertStringContainsString("showDeleteModal.value = true", $indexPage);
        $this->assertStringContainsString("router.delete(`/inputer/data/${dataToDelete.value.id_data}`", $indexPage);
        $this->assertStringContainsString('Hapus Data?', $deleteModal);
        $this->assertStringContainsString('Ya, Hapus Permanen', $deleteModal);
    }

    public function test_case_09_pencarian_data_berdasarkan_nama(): void
    {
        $this->createDataset($this->inputerUser(), 'Angka Partisipasi Sekolah');
        $this->createDataset($this->inputerUser(), 'Jumlah Guru');

        $response = $this->get('/search?search=Partisipasi');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->where('results.total', 1)
            ->where('results.data.0.nama_data', 'Angka Partisipasi Sekolah')
        );
    }

    public function test_case_10_filter_data_berdasarkan_tema_atau_bidang(): void
    {
        $inputer = $this->inputerUser();

        $matching = $this->createDataset($inputer, 'Filter Tema Bidang Match', [
            'id_tema' => 2,
            'id_bidang' => 2,
        ]);
        $this->createDataset($inputer, 'Filter Tema Bidang Lain', [
            'id_tema' => 1,
            'id_bidang' => 1,
        ]);

        $response = $this->get('/search?id_tema=2&id_bidang=2');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->where('results.total', 1)
            ->where('results.data.0.id_data', $matching->id_data)
            ->where('results.data.0.nama_data', 'Filter Tema Bidang Match')
        );
    }

    public function test_case_11_pencarian_data_yang_tidak_ada_menampilkan_kosong(): void
    {
        $this->createDataset($this->inputerUser(), 'Dataset Existing');

        $response = $this->get('/search?search=DataYangTidakAda123');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->where('results.total', 0)
            ->where('results.data', [])
        );

        $searchPage = File::get(resource_path('js/Pages/Public/Search.vue'));
        $this->assertStringContainsString('Data Tidak Ditemukan', $searchPage);
    }

    public function test_case_12_menekan_tombol_bookmark_pin_pada_data_tertentu(): void
    {
        $inputer = $this->inputerUser();
        $data = $this->createDataset($inputer, 'Indikator Bookmark');

        $this->actingAs($inputer)
            ->post(route('inputer.dataset.bookmark', $data->id_data))
            ->assertRedirect();

        $this->assertDatabaseHas('bookmark', [
            'user_id' => $inputer->id,
            'dataset_id' => $data->id_data,
        ]);

        $this->actingAs($inputer)
            ->post(route('inputer.dataset.bookmark', $data->id_data))
            ->assertRedirect();

        $this->assertDatabaseMissing('bookmark', [
            'user_id' => $inputer->id,
            'dataset_id' => $data->id_data,
        ]);
    }

    public function test_case_13_menampilkan_daftar_data_yang_sudah_di_bookmark(): void
    {
        $inputer = $this->inputerUser();
        $mine = $this->createDataset($inputer, 'Pinned Inputer');
        $other = $this->createDataset($this->adminUser(), 'Pinned Admin');

        Bookmark::create([
            'user_id' => $inputer->id,
            'dataset_id' => $mine->id_data,
        ]);
        Bookmark::create([
            'user_id' => $inputer->id,
            'dataset_id' => $other->id_data,
        ]);

        $response = $this->actingAs($inputer)->get(route('inputer.dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Inputer/Dashboard')
            ->where('pinnedData.0.id_data', $mine->id_data)
        );

        $pinned = $response->viewData('page')['props']['pinnedData'];
        $this->assertCount(1, $pinned);
        $this->assertSame('Pinned Inputer', $pinned[0]['nama_data']);
    }

    public function test_case_14_ekspor_data_ke_excel_bisa_dibuka_dan_isi_sesuai(): void
    {
        $data = $this->createDataset($this->inputerUser(), 'Ekspor Excel', [
            'satuan' => 'Persen',
            'sumber' => 'BPS',
        ]);

        DataUpload::create([
            'id_data' => $data->id_data,
            'id_user' => $this->inputerUser()->id,
            'periode' => '2025',
            'file_path' => 'manual_input',
            'value' => [
                'values' => [
                    ['tahun' => '2024', 'nilai' => '10'],
                    ['tahun' => '2025', 'nilai' => '20'],
                ],
            ],
        ]);

        $response = $this->get(route('public.export', $data->id_data));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('content-disposition');

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx-test-');
        file_put_contents($tempPath, $response->streamedContent());

        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('No', $sheet->getCell('A1')->getValue());
        $this->assertSame('Periode', $sheet->getCell('B1')->getValue());
        $this->assertSame('Nilai', $sheet->getCell('C1')->getValue());
        $this->assertSame('2024', $sheet->getCell('B2')->getValue());
        $this->assertSame(10, $sheet->getCell('C2')->getValue());
        $this->assertSame('2025', $sheet->getCell('B3')->getValue());
        $this->assertSame(20, $sheet->getCell('C3')->getValue());

        @unlink($tempPath);
    }

    public function test_case_15_admin_menambah_master_tema_baru(): void
    {
        $response = $this->actingAs($this->adminUser())->post('/admin/tema', [
            'nama_tema' => 'Tema Master Baru',
        ]);

        $response->assertRedirect('/admin/tema');
        $this->assertDatabaseHas('tema', [
            'nama_tema' => 'Tema Master Baru',
        ]);
    }

    public function test_case_16_admin_mengelola_akun_pengguna_tambah_dan_edit(): void
    {
        $admin = $this->adminUser();

        $storeResponse = $this->actingAs($admin)->post('/admin/users', [
            'nama_depan' => 'User',
            'nama_belakang' => 'Baru',
            'username' => 'userbaru',
            'email' => 'userbaru@example.com',
            'role_id' => Role::where('nama_role', 'Inputer')->value('id_role'),
            'password' => 'secret123',
        ]);

        $storeResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'username' => 'userbaru',
            'email' => 'userbaru@example.com',
        ]);

        $user = User::where('email', 'userbaru@example.com')->firstOrFail();

        $updateResponse = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'nama_depan' => 'User',
            'nama_belakang' => 'Update',
            'email' => 'userupdate@example.com',
            'role_id' => Role::where('nama_role', 'Inputer')->value('id_role'),
        ]);

        $updateResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nama_belakang' => 'Update',
            'email' => 'userupdate@example.com',
        ]);
    }

    private function adminUser(): User
    {
        return User::where('email', 'lepang@gmail.com')->firstOrFail();
    }

    private function inputerUser(): User
    {
        return User::where('email', 'nanta@gmail.com')->firstOrFail();
    }

    private function createDataset(User $user, string $name, array $overrides = []): Data
    {
        $data = Data::create(array_merge([
            'id_user' => $user->id,
            'nama_data' => $name,
            'deskripsi' => 'Dataset pengujian',
            'id_tema' => 1,
            'id_urusan' => 1,
            'id_bidang' => 1,
            'id_frekuensi' => 1,
            'satuan' => 'Persen',
            'sumber' => 'BPS',
            'tahun_terbit' => 2025,
        ], $overrides));

        DataValue::create([
            'id_data' => $data->id_data,
            'tahun' => '2023',
            'nilai' => 40,
        ]);
        DataValue::create([
            'id_data' => $data->id_data,
            'tahun' => '2024',
            'nilai' => 50,
        ]);

        return $data;
    }
}
