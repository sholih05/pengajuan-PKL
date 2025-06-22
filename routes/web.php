<?php

use App\Http\Controllers\Siswa\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DudiController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\InstrukturController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KendalaSaranController;
use App\Http\Controllers\Admin\KetersediaanController;
use App\Http\Controllers\Admin\NilaiQuisionerController;
use App\Http\Controllers\Admin\PenempatanController;
use App\Http\Controllers\Admin\PenilaianController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\QuesionerController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TahunAkademikController;
use App\Http\Controllers\Admin\TemplatePenilaianController;
use App\Http\Controllers\Dudi\ProfilDudiController;
use App\Http\Controllers\Guru\ProfilGuruController;
use App\Http\Controllers\Instruktur\ProfilInstrukturController;
use App\Http\Controllers\Siswa\LihatFileController;
use App\Http\Controllers\Siswa\ProfilSiswaController;
use App\Http\Controllers\Siswa\PengajuanSuratController;
use App\Http\Controllers\Siswa\SuratController;
use App\Http\Middleware\RoleMiddleware;
use Faker\Core\File;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\TextUI\Configuration\FileCollection;


// Rute untuk login dan autentikasi
Route::get('/', [AuthController::class, 'index']);
Route::post('/', [AuthController::class, 'authenticate'])->name('login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/refreshcaptcha', [AuthController::class, 'refreshCaptcha'])->name("captcha.refresh");

Route::get('/coba', [AuthController::class, 'coba']);

// Kelompok rute yang memerlukan middleware autentikasi
Route::group(['middleware' => ['auth']], function () {
    // Rute Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(RoleMiddleware::class . ':1,2');

    // PKL penempatan
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'penempatan', 'controller' => PenempatanController::class], function () {
        Route::get('/', 'index')->name('penempatan');
        Route::post('/data', 'data')->name('penempatan.data');
        Route::post('/upsert', 'upsert')->name('penempatan.upsert');
        Route::get('/delete/{id}', 'destroy')->name('penempatan.delete');
        Route::get('/get_instruktur', 'getInstruktur')->name('penempatan.get_instruktur');
        Route::post('/uploadExcel', 'uploadExcel')->name('penempatan.uploadExcel');
        Route::get('/downloadExcel', 'downloadExcel')->name('penempatan.downloadExcel');
    });

    // PKL Presensi
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'presensi', 'controller' => PresensiController::class], function () {
        Route::get('/', 'index')->name('presensi');
        Route::post('/data', 'data')->name('presensi.data');
        Route::post('/upsert', 'upsert')->name('presensi.upsert');
        Route::get('/delete/{id}', 'destroy')->name('presensi.delete');
        Route::get('/get_penempatan', 'get_penempatan')->name('presensi.get_penempatan');
        Route::get('/downloadExcel', 'downloadExcel')->name('presensi.downloadExcel');
    });

    // PKL Kendala Saran
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'kendala-saran', 'controller' => KendalaSaranController::class], function () {
        Route::get('/', 'index')->name('kendala-saran');
        Route::post('/data', 'data')->name('kendala-saran.data');
        Route::post('/upsert', 'upsert')->name('kendala-saran.upsert');
        Route::get('/delete/{id}', 'destroy')->name('kendala-saran.delete');
        Route::get('/get_instruktur', 'getInstruktur')->name('kendala-saran.get_instruktur');
        Route::get('/downloadExcel', 'downloadExcel')->name('kendala-saran.downloadExcel');
    });

    // PKL NilaiQuisioner
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'nilai-quesioner', 'controller' => NilaiQuisionerController::class], function () {
        Route::get('/', 'index')->name('nilai-quesioner');
        Route::post('/data', 'data')->name('nilai-quesioner.data');
        Route::get('/edit/{nis}/{id_ta}', 'edit')->name('nilai-quesioner.edit');
        Route::post('/upsert', 'upsert')->name('nilai-quesioner.upsert');
        Route::get('/delete/{nis}/{id_ta}', 'destroy')->name('nilai-quesioner.delete');
        Route::get('/downloadExcel', 'downloadExcel')->name('nilai-quesioner.downloadExcel');
    });

    //pengajuan surat
    Route::group(['middleware' => [RoleMiddleware::class . ':1,5,4'], 'prefix' => 'siswa', 'controller' => SiswaController::class], function () {
        Route::get('/search/siswa', 'search')->name('siswa.searchh');
        Route::get('pengajuan', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
        Route::get('pengajuan/create', [PengajuanSuratController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan-surat/store', [PengajuanSuratController::class, 'store'])->name('pengajuan.surat.store');
        Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'getData'])->name('pengajuan.surat.get');
        Route::get('pengajuan/{id}/edit', [PengajuanSuratController::class, 'edit'])->name('pengajuan.edit');
        Route::put('pengajuan/{id}', [PengajuanSuratController::class, 'update'])->name('pengajuan.update');
        Route::delete('pengajuan/{id}', [PengajuanSuratController::class, 'destroy'])->name('pengajuan.destroy');
       

    });

     // Penilaian
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2,4']], function () {
        Route::resource('template-penilaian', TemplatePenilaianController::class)->except(['show']);
        Route::get('template-penilaian/details/{id}', [TemplatePenilaianController::class, 'show'])->name('template-penilaian.details.show');
        Route::get('template-penilaian/data', [TemplatePenilaianController::class, 'getData'])->name('template-penilaian.data');
        Route::post('template-penilaian/{id}/apply', [TemplatePenilaianController::class, 'applyTemplate'])->name('template-penilaian.apply');
        Route::get('template-penilaian/{id}/guru', [TemplatePenilaianController::class, 'getGuruByTemplate'])->name('template-penilaian.getGuru');
    });

    Route::group(['middleware' => [RoleMiddleware::class . ':1,2,4']], function () {
        // Routes untuk penilaian
        Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
        Route::get('penilaian/data', [PenilaianController::class, 'getData'])->name('penilaian.data');
        Route::get('penilaian/create/{id_siswa}', [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
        Route::get('penilaian/{id_siswa}', [PenilaianController::class, 'show'])->name('penilaian.show');
        Route::get('penilaian/{id_siswa}/edit', [PenilaianController::class, 'edit'])->name('penilaian.edit');
        Route::put('penilaian/{id_siswa}', [PenilaianController::class, 'update'])->name('penilaian.update');
        Route::delete('penilaian/{id_siswa}', [PenilaianController::class, 'destroy'])->name('penilaian.destroy');
        Route::get('penilaian/{id_siswa}/print', [PenilaianController::class, 'print'])->name('penilaian.print');
        Route::get('penilaian/dashboard', [PenilaianController::class, 'dashboard'])->name('penilaian.dashboard');
    });

    
    // admin
    Route::middleware([RoleMiddleware::class . ':1,2'])->group(function () {
        Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('pengajuan.surat.index');
        Route::get('/pengajuan-surat/data', [PengajuanSuratController::class, 'getDataAll'])->name('pengajuan.surat.getAll');
        Route::post('/pengajuan-surat/post', [PengajuanSuratController::class, 'store'])->name('pengajuan.surat.post');
        Route::get('/pengajuan-surat/{id}/edit', [PengajuanSuratController::class, 'edit'])->name('pengajuan.surat.edit');
        Route::put('/pengajuan-surat/{id}', [PengajuanSuratController::class, 'update'])->name('pengajuan.surat.update');
        Route::delete('/pengajuan-surat/{id}', [PengajuanSuratController::class, 'delete'])->name('pengajuan.surat.delete');
        Route::put('/pengajuan-surat/reject/{id}', [PengajuanSuratController::class, 'reject'])->name('pengajuan.surat.reject');
        Route::put('/pengajuan/surat/approve/{id}', [PengajuanSuratController::class, 'approve'])->name('pengajuan.surat.approve');
        


    });
    

    // siswa
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'siswa', 'controller' => SiswaController::class], function () {
        Route::get('/', 'index')->name('siswa');
        Route::get('/data', 'data')->name('siswa.data');
        Route::post('/store', 'store')->name('siswa.store');
        Route::get('/create', 'create')->name('siswa.create');
        Route::put('/{nis}/{nisn}', 'update')->name('siswa.update');
        Route::get('/{siswa}/edit', 'edit')->name('siswa.edit');
        Route::delete('/{nis}', 'destroy')->name('siswa.destroy');

        Route::post('/change-status-bekerja', 'change_status_kerja')->name('siswa.change_status_kerja');
        Route::get('/search', 'search')->name('siswa.search');
        Route::post('/uploadExcel', 'uploadExcel')->name('siswa.uploadExcel');
        Route::get('/downloadExcel', 'downloadExcel')->name('siswa.downloadExcel');
    });
    // guru
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'guru', 'controller' => GuruController::class], function () {
        Route::get('/', 'index')->name('guru');
        Route::get('/data', 'data')->name('guru.data');
        Route::get('/create', 'create')->name('guru.create');
        Route::post('/store', 'store')->name('guru.store');
        Route::get('/{id_guru}/edit', 'edit')->name('guru.edit');
        Route::put('/{id_guru}', 'update')->name('guru.update');
        Route::delete('/{id_guru}', 'destroy')->name('guru.destroy');
        Route::post('/updateRole', 'updateRole')->name('guru.updateRole');

        Route::get('/search', 'search')->name('guru.search');
        Route::post('/uploadExcel', 'uploadExcel')->name('guru.uploadExcel');
        Route::get('/downloadExcel', 'downloadExcel')->name('guru.downloadExcel');
    });

    // instruktur
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'instruktur', 'controller' => InstrukturController::class], function () {
        Route::get('/', 'index')->name('instruktur');
        Route::get('/data', 'data')->name('instruktur.data');
        Route::get('/create', 'create')->name('instruktur.create');
        Route::post('/store', 'store')->name('instruktur.store');
        Route::get('/{id_instruktur}/edit', 'edit')->name('instruktur.edit');
        Route::put('/{id_instruktur}', 'update')->name('instruktur.update');
        Route::delete('/{id_instruktur}', 'destroy')->name('instruktur.destroy');

        Route::get('/search', 'search')->name('instruktur.search');

        Route::post('/uploadExcel', 'uploadExcel')->name('instruktur.uploadExcel');
        Route::get('/downloadExcel', 'downloadExcel')->name('instruktur.downloadExcel');
        Route::post('/update-keterangan', [ProfilInstrukturController::class, 'updateKeterangan'])->name('presensi.updateKeterangan');

    });

    // Dudi
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'dudi', 'controller' => DudiController::class], function () {
        Route::get('/', 'index')->name('dudi');
        Route::get('/data', 'data')->name('dudi.data');
        Route::get('/create', 'create')->name('dudi.create');
        Route::post('/store', 'store')->name('dudi.store');
        Route::get('/{id_dudi}/edit', 'edit')->name('dudi.edit');
        Route::put('/{id_dudi}', 'update')->name('dudi.update');
        Route::delete('/{id_dudi}', 'destroy')->name('dudi.destroy');

        Route::post('/uploadExcel', 'uploadExcel')->name('dudi.uploadExcel');
        Route::get('/downloadExcel', 'downloadExcel')->name('dudi.downloadExcel');
    });
    // Ketersediaan
    Route::group(['middleware' => [RoleMiddleware::class . ':1,2'], 'prefix' => 'ketersediaan', 'controller' => KetersediaanController::class], function () {
        Route::get('/', 'index')->name('ketersediaan');
        Route::get('/data', 'data')->name('ketersediaan.data');
        Route::post('/upsert', 'upsert')->name('ketersediaan.upsert');
        Route::get('/delete/{id}', 'destroy')->name('ketersediaan.delete');
    });

    // Rute Master
    Route::group(['middleware' => [RoleMiddleware::class . ':1'], 'prefix' => 'master'], function () {
        Route::group(['prefix' => 'jurusan', 'controller' => JurusanController::class], function () {
            Route::get('/', 'index')->name('master.jurusan');
            Route::get('/data', 'data')->name('master.jurusan.data');
            Route::post('/upsert', 'upsert')->name('master.jurusan.upsert');
            Route::get('/delete/{id}', 'destroy')->name('master.jurusan.delete');
            Route::post('/uploadExcel', 'uploadExcel')->name('master.jurusan.uploadExcel');
            Route::get('/downloadExcel', 'downloadExcel')->name('master.jurusan.downloadExcel');
        });

        Route::group(['prefix' => 'tahun_akademik', 'controller' => TahunAkademikController::class], function () {
            Route::get('/', 'index')->name('master.tahun_akademik');
            Route::get('/data', 'data')->name('master.tahun_akademik.data');
            Route::post('/upsert', 'upsert')->name('master.tahun_akademik.upsert');
            Route::get('/delete/{id}', 'destroy')->name('master.tahun_akademik.delete');
            Route::get('/downloadExcel', 'downloadExcel')->name('master.tahun_akademik.downloadExcel');
        });

        Route::group(['prefix' => 'quesioner', 'controller' => QuesionerController::class], function () {
            Route::get('/', 'index')->name('master.quesioner');
            Route::get('/data', 'data')->name('master.quesioner.data');
            Route::post('/upsert', 'upsert')->name('master.quesioner.upsert');
            Route::get('/delete/{id}', 'destroy')->name('master.quesioner.delete');
            Route::post('/uploadExcel', 'uploadExcel')->name('master.quesioner.uploadExcel');
            Route::get('/downloadExcel', 'downloadExcel')->name('master.quesioner.downloadExcel');
        });
    });

    // PROFIL
    // siswa
    Route::group(['prefix' => 'd/siswa', 'controller' => ProfilSiswaController::class], function () {
        Route::get('/', 'index')->name('d.siswa');
        Route::post('/absen', 'absen')->name('d.siswa.absen');
        Route::get('/cek-absen', 'cek_absen')->name('d.siswa.cek_absen');
        Route::get('/kegiatan/data', 'data_kegiatan')->name('d.siswa.kegiatan.data');
        Route::post('/kegiatan/update', 'update_kegiatan')->name('d.siswa.kegiatan.update');
        Route::post('/akun/update', 'update_akun')->name('d.siswa.akun.update');
        Route::post('/update-foto/{nis}', 'updateFoto')->name('d.siswa.updateFoto');
        Route::post('/change-status-bekerja', 'change_status_kerja')->name('d.siswa.change_status_kerja');
        Route::get('/get_penempatan', 'get_penempatan')->name('d.siswa.get_penempatan');
        Route::get('/get_penempatan_detail', 'get_penempatan_detail')->name('d.siswa.get_penempatan_detail');
        Route::get('/quesioner', 'get_quesioner')->name('d.siswa.quesioner');
        Route::post('/quesioner/upsert', 'upsert_quesioner')->name('d.siswa.quesioner.upsert');
        Route::get('/quesioner/edit/{nis}/{id_ta}', 'edit_quesioner')->name('d.siswa.quesioner.edit');

        Route::get('/presensiExcel/{nis}/{id_penempatan}/{id_ta}', 'presensiExcel')->name('d.siswa.presensiExcel');
        Route::get('/kegiatanExcel/{nis}/{id_penempatan}/{id_ta}', 'kegiatanExcel')->name('d.siswa.kegiatanExcel');
        Route::get('/catatanExcel/{nis}/{id_penempatan}/{id_ta}', 'catatanExcel')->name('d.siswa.catatanExcel');

        Route::get('/presensiPdf/{nis}/{id_penempatan}/{id_ta}', 'presensiPdf')->name('d.siswa.presensiPdf');
        Route::get('/kegiatanPdf/{nis}/{id_penempatan}/{id_ta}', 'kegiatanPdf')->name('d.siswa.kegiatanPdf');
        Route::get('/catatanPdf/{nis}/{id_penempatan}/{id_ta}', 'catatanPdf')->name('d.siswa.catatanPdf');
        Route::get('/resumePdf/{nis}', 'resumePdf')->name('d.siswa.resumePdf');

        Route::get('/penilaian/siswa', 'nilaiSiswa')->name('d.siswa.penilaian');

    });

    // guru
    Route::group(['prefix' => 'd/guru', 'controller' => ProfilGuruController::class], function () {
        Route::get('/', 'index')->name('d.guru');
        Route::post('/akun/update', 'update_akun')->name('d.guru.akun.update');
        Route::get('/siswa/data', 'data_siswa')->name('d.guru.siswa.data');
        Route::get('/siswa/kegiatan', 'kegiatan_siswa')->name('d.guru.siswa.kegiatan');
        Route::post('/siswa/presensi-approval', 'presensi_approval')->name('d.guru.siswa.presensi_approval');
    });

    // instruktur
    Route::group(['prefix' => 'd/instruktur', 'controller' => ProfilInstrukturController::class], function () {
        Route::get('/', 'index')->name('d.instruktur');
        Route::post('/akun/update', 'update_akun')->name('d.instruktur.akun.update');
        Route::get('/siswa/data', 'data_siswa')->name('d.instruktur.siswa.data');
        Route::get('/siswa/kegiatan', 'kegiatan_siswa')->name('d.instruktur.siswa.kegiatan');
        Route::get('/kendala-saran', 'kendala_saran')->name('d.instruktur.kendala_saran');
        Route::post('/kendala-saran', 'kendala_saran_upsert')->name('d.instruktur.kendala_saran.upsert');
        Route::post('/siswa/presensi-approval', 'presensi_approval')->name('d.instruktur.siswa.presensi_approval');
        Route::post('/siswa/catatan/update', 'catatan_siswa_update')->name('d.instruktur.siswa.catatan.update');

        Route::get('/siswaExcel/{nis}', 'siswaExcel')->name('d.instruktur.siswaExcel');
        Route::get('/penilaian', 'penilaian')->name('d.instruktur.penilaian');
    });

    // dudi
    Route::group(['prefix' => 'd/dudi', 'controller' => ProfilDudiController::class], function () {
        Route::get('/', 'index')->name('d.dudi');
        Route::post('/akun/update', 'update_akun')->name('d.dudi.akun.update');
        Route::get('/siswa/data', 'data_siswa')->name('d.dudi.siswa.data');
        Route::get('/guru/data', 'data_guru')->name('d.dudi.guru.data');
        Route::get('/instruktur/data', 'data_instruktur')->name('d.dudi.instruktur.data');
        Route::get('/ketersediaan/data', 'data_ketersediaan')->name('d.dudi.ketersediaan.data');
        Route::post('/ketersediaan/upsert', 'upsert_ketersediaan')->name('d.dudi.ketersediaan.upsert');
        Route::get('/ketersediaan/delete/{id}', 'destroy_ketersediaan')->name('d.dudi.ketersediaan.delete');
    });
});

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return "Cache is cleared";
});


Route::get('/tampilan', function () {
    return view('pkl.pengajuan-surat.surat');
});

Route::get('/surat/{id}', [SuratController::class, 'index'])->name('surat.index');
Route::get('/pengajuan/surat/details/{id}', [PengajuanSuratController::class, 'details'])->name('pengajuan.surat.details');

// upload file


// Route::prefix('d/uploaded_files')->group(function () {
//     Route::get('uploaded-files', [FileController::class, 'index'])->name('d.upload-surat');
//     Route::post('upload', [FileController::class, 'upload'])->name('d.upload');
//     Route::put('update/{id}', [FileController::class, 'update'])->name('d.update'); // Ubah menjadi PUT
//     Route::get('delete/{id}', [FileController::class, 'delete'])->name('d.delete');
//     Route::get('edit/{id}', [FileController::class, 'edit'])->name('d.edit');
// });

Route::prefix('d/uploaded_files')->group(function () {
    Route::get('uploaded-files', [FileController::class, 'index'])->name('d.upload-surat');
    Route::post('upload', [FileController::class, 'upload'])->name('d.upload');
    Route::put('update/{id}', [FileController::class, 'update'])->name('d.update');
    Route::get('delete/{id}', [FileController::class, 'delete'])->name('d.delete');
    Route::get('edit/{id}', [FileController::class, 'edit'])->name('d.edit');

    Route::get('guru/files', [LihatFileController::class, 'index'])->name('admin.files');
    Route::get('guru/files/{id}/download', [LihatFileController::class, 'download'])->name('admin.download');
    Route::get('guru/files/{id}/details', [LihatFileController::class, 'viewDetails'])->name('admin.viewDetails');
    
});



