<?php

namespace App\Http\Controllers\Siswa;

use App\Exports\PresensiExport;
use App\Http\Controllers\Admin\NilaiQuisionerController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Controller;
use App\Models\FileModel;
use App\Models\Penempatan;
use App\Models\Presensi;
use App\Models\Quesioner;
use App\Models\Siswa;
use App\Models\ThnAkademik;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProfilSiswaController extends Controller
{
    public function index(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');

        // Ambil data siswa beserta relasi terkait dengan kondisi where pada nis siswa
        $siswa = Siswa::with(['jurusan', 'user', 'penempatan.instruktur.dudi', 'penempatan.tahunAkademik'])
            ->where('nis', $nis)
            ->firstOrFail();


        $activeAcademicYear = getActiveAcademicYear();
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();

        $files = FileModel::all(); 

        return view('siswa.profile', compact('siswa', 'activeAcademicYear', 'ta','files'));
    }



    public function absen(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk file gambar
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Ambil NIS dari request atau sesi
        $nis = $request->has('nis') ? $request->nis : session('nis');
        if (!$nis) {
            return response()->json(['status' => 'error', 'message' => 'NIS tidak ditemukan.'], 400);
        }

        $tanggal = Carbon::today(); // Menggunakan Carbon untuk mendapatkan tanggal hari ini

        // Cek apakah sudah ada data absensi masuk untuk hari ini
        $presensi = Presensi::where('is_active', true)
            ->where('id_penempatan', $request->id_penempatan)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($presensi) {
            if ($request->hasFile('foto')) {
                $fileName = handlePhotoUpload($request->file('foto'), $presensi->foto_pulang);
            }
            // Jika sudah ada presensi hari ini, update waktu pulang
            $presensi->update([
                'pulang' => Carbon::now(), // Menggunakan waktu sekarang untuk jam pulang
                'foto_pulang' => $fileName
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil absen pulang',
            ]);
        } else {
            if ($request->hasFile('foto')) {
                $fileName = handlePhotoUpload($request->file('foto'));
            }
            // Jika belum ada presensi hari ini, buat data absensi masuk baru
            $presensiBaru = Presensi::create([
                'tanggal' => $tanggal,
                'masuk' => Carbon::now(), // Menggunakan waktu sekarang untuk jam masuk
                'foto_masuk' => $fileName,
                'id_penempatan' => $request->id_penempatan,
                'created_by' => Auth::id(), // ID pengguna yang membuat presensi
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil absen masuk',
            ]);
        }
    }

    function cek_absen(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');
        $tanggal = Carbon::today(); // Menggunakan Carbon untuk mendapatkan tanggal hari ini
        // Cek apakah sudah ada data absensi masuk untuk hari ini
        $presensi = Presensi::where('is_active', true)->where('id_penempatan', $request->id)
            ->whereDate('tanggal', $tanggal)
            ->count();
        $siswa = Siswa::select('nis', 'nama', 'status_bekerja')->where('is_active', true)->where('nis', $nis)->first();

        if ($presensi) {
            return response()->json([
                'status' => 'success',
                'message' => 'Absen Pulang',
                'data' => $siswa
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Absen Masuk',
                'data' => $siswa
            ]);
        }
    }

    function get_quesioner(Request $request)
    {
        $questions = Quesioner::where(['is_active' => true, 'id_ta' => $request->id_ta])->get();
        if ($questions) {
            return response()->json([
                'status' => 'success',
                'data' => $questions
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Quesioner tidak ditemukan',
            ]);
        }
    }

    public function data_kegiatan(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');
        $id_penempatan = $request->id_penempatan;
        $penempatan = Penempatan::find($id_penempatan);
        // Jika $penempatan kosong, kembalikan data kosong
        if (!$penempatan) {
            return DataTables::of(collect())->make(true);
        }
        $ta = ThnAkademik::find($penempatan->id_ta);
        $data = Presensi::with('siswa', 'instruktur', 'penempatan');
        if ($request->stt == 1) {
            $data = $data->select('tanggal', 'masuk', 'pulang', 'foto_masuk', 'foto_pulang');
        } elseif ($request->stt == 2) {
            $data = $data->select('id_presensi', 'tanggal', 'kegiatan', 'is_acc_instruktur', 'is_acc_guru',);
        } elseif ($request->stt == 3) {
            $data = $data->select('tanggal', 'catatan');
        }

        // memiliki penempatan by siswa
        $data = $data->whereHas('penempatan', function ($query) use ($nis, $id_penempatan) {
            $query->where('nis', $nis)->where('id_penempatan', $id_penempatan);
        });

        $data = $data->where(['is_active' => true])->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);
        return DataTables::of($data)
            ->addColumn('presensi_masuk', function ($dt) {
                return $dt->foto_masuk ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_masuk) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->masuk : $dt->masuk;
            })
            ->addColumn('presensi_pulang', function ($dt) {
                return $dt->foto_pulang ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_pulang) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->pulang : $dt->pulang;
            })
            ->addColumn('action', function ($dt) {
                $currentDate = date('Y-m-d'); // Ambil tanggal saat ini
                if ($dt->tanggal >= $currentDate) {
                    return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> ';
                }
                return ''; // Kosongkan jika tanggal sudah berlalu
            })
            ->rawColumns(['action', 'presensi_masuk', 'presensi_pulang'])
            ->make(true);
    }

    // Upsert (Insert or Update) a record
    public function update_kegiatan(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'kegiatan' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $presensi = Presensi::where('id_presensi', $request->id_presensi)->first();

        // Mengisi data dari request ke dalam model
        $presensi->kegiatan = $request->kegiatan;

        // Menentukan created_by atau updated_by berdasarkan tindakan
        $presensi->updated_by = Auth::id();
        // Menyimpan data presensi
        $presensi->save();

        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }

    public function change_status_kerja(Request $request)
    {
        $siswaController = new SiswaController();
        return $siswaController->change_status_kerja($request);
    }


    function update_akun(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');

        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'renew_password' => 'required|same:new_password',
        ]);

        // Ambil user berdasarkan NIS
        $user = User::where(['is_active' => true, 'username' => $nis])->first();

        // Periksa apakah password saat ini benar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password saat ini salah.',
            ], 400);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return response success
        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.',
        ], 200);
    }

    public function updateFoto(Request $request, $nis)
    {
        // Validasi input file foto
        $request->validate([
            'foto_profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Mencari siswa berdasarkan NIS
        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        // Cek apakah ada file foto yang diupload
        if ($request->hasFile('foto_profile')) {
            // Simpan foto ke direktori storage/public
            $fotoPath = $request->file('foto_profile')->store('uploads/img_users', 'public');

            // Update foto siswa di database
            $siswa->foto = $fotoPath;
            $siswa->save();

            // Mengirimkan URL gambar yang baru
            return response()->json([
                'success' => true,
                'imageUrl' => asset('storage/' . $fotoPath),
            ]);
        }

        // Jika tidak ada foto yang di-upload, kirim respons gagal
        return response()->json(['success' => false]);
    }

    function get_penempatan(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');
        $data = Penempatan::with(['guru', 'instruktur', 'dudi', 'tahunAkademik'])->active()->where(['nis' => $nis, 'id_ta' => $request->id_ta])->orderBy('id_penempatan', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


    function get_penempatan_detail(Request $request)
    {
        $data = Penempatan::with(['guru', 'instruktur', 'dudi', 'tahunAkademik'])->active()->where('id_penempatan', $request->id)->first();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


    function upsert_quesioner(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');

        // Menambahkan parameter tambahan ke Request yang sudah ada
        $request->merge([
            'tanggal' => date('Y-m-d'),
            'nis' => $nis,
        ]);
        $nilaiQuisionerController = new NilaiQuisionerController();

        // Panggil metode upsert
        return $nilaiQuisionerController->upsert($request);
    }

    function edit_quesioner(Request $request)
    {
        $nilaiQuisionerController = new NilaiQuisionerController();
        // Panggil metode upsert
        return $nilaiQuisionerController->edit($request);
    }

    // excel
    public function presensiExcel(Request $request)
    {
        return Excel::download(new PresensiExport($request->nis, $request->id_penempatan, 'presensi', $request->id_ta), 'data-presensi-' . $request->nis . ' ' . date('Y-m-d') . '.xlsx');
    }
    public function kegiatanExcel(Request $request)
    {
        return Excel::download(new PresensiExport($request->nis,$request->id_penempatan, 'kegiatan', $request->id_ta), 'data-kegiatan-' . $request->nis . ' ' . date('Y-m-d') . '.xlsx');
    }
    public function catatanExcel(Request $request)
    {
        return Excel::download(new PresensiExport($request->nis, $request->id_penempatan,'catatan', $request->id_ta), 'data-kegiatan-' . $request->nis . ' ' . date('Y-m-d') . '.xlsx');
    }

    // pdf
    public function presensiPdf(Request $request)
    {
        $penempatan = Penempatan::active()
            ->with([
                'siswa',  // Relasi Siswa
                'guru',   // Relasi Guru
                'instruktur', // Relasi Instruktur
                'tahunAkademik', // Relasi Tahun Akademik
                'dudi' // Relasi DUDI
            ])->where('nis', $request->nis)->where('id_penempatan', $request->id_penempatan)->first();

        if ($penempatan) {

            // Ambil data presensi siswa
            $presensi = Presensi::where('id_penempatan', $request->id_penempatan)
                ->whereBetween('tanggal', [$penempatan->tahunAkademik->mulai, $penempatan->tahunAkademik->selesai])
                ->active()
                ->with(['instruktur'])
                ->get();

            // Siapkan data untuk PDF
            $data = [
                'penempatan' => $penempatan,
                'presensi' => $presensi,
                'date' => now()->format('d-m-Y'),
            ];
            // return view('siswa.pdf.presensi', $data);
            $pdf = Pdf::loadView('siswa.pdf.presensi', $data);
            return $pdf->stream(); // Untuk menampilkan PDF di browser
        } else {
            return 'NO DATA';
        }
    }

    public function kegiatanPdf(Request $request)
    {

        $penempatan = Penempatan::active()
            ->with([
                'siswa',  // Relasi Siswa
                'guru',   // Relasi Guru
                'instruktur', // Relasi Instruktur
                'tahunAkademik', // Relasi Tahun Akademik
                'dudi' // Relasi DUDI
            ])->where('nis', $request->nis)
            ->where('id_penempatan', $request->id_penempatan)->first();
        if ($penempatan) {

            // Ambil data presensi siswa
            $presensi = Presensi::where('id_penempatan', $request->id_penempatan)
                ->whereBetween('tanggal', [$penempatan->tahunAkademik->mulai, $penempatan->tahunAkademik->selesai])
                ->active()
                ->with(['instruktur'])
                ->get();

            // Siapkan data untuk PDF
            $data = [
                'penempatan' => $penempatan,
                'presensi' => $presensi,
                'date' => now()->format('d-m-Y'),
            ];
            // return view('siswa.pdf.kegiatan', $data);
            $pdf = Pdf::loadView('siswa.pdf.kegiatan', $data);
            return $pdf->stream(); // Untuk menampilkan PDF di browser
        } else {
            return 'NO DATA';
        }
    }

    public function catatanPdf(Request $request)
    {

        $penempatan = Penempatan::active()
            ->with([
                'siswa',  // Relasi Siswa
                'guru',   // Relasi Guru
                'instruktur', // Relasi Instruktur
                'tahunAkademik', // Relasi Tahun Akademik
                'dudi' // Relasi DUDI
            ])->where('nis', $request->nis)->where('id_penempatan', $request->id_penempatan)->first();
        if ($penempatan) {

            // Ambil data presensi siswa
            $presensi = Presensi::where('id_penempatan', $request->id_penempatan)->whereBetween('tanggal', [$penempatan->tahunAkademik->mulai, $penempatan->tahunAkademik->selesai])
                ->active()
                ->with(['instruktur'])
                ->get();

            // Siapkan data untuk PDF
            $data = [
                'penempatan' => $penempatan,
                'presensi' => $presensi,
                'date' => now()->format('d-m-Y'),
            ];
            // return view('siswa.pdf.catatan', $data);
            $pdf = Pdf::loadView('siswa.pdf.catatan', $data);
            return $pdf->stream(); // Untuk menampilkan PDF di browser
        } else {
            return 'NO DATA';
        }
    }

    public function resumePdf(Request $request)
    {
        $penempatan = Penempatan::active()
            ->with([
                'siswa',  // Relasi Siswa
                'guru',   // Relasi Guru
                'instruktur', // Relasi Instruktur
                'tahunAkademik', // Relasi Tahun Akademik
                'dudi' // Relasi DUDI
            ])->where('nis', $request->nis)->where('id_penempatan', $request->id_penempatan)->first();

        // Ambil data presensi siswa
        // $presensi = Presensi::where('id_penempatan', $penempatan->id_penempatan)
        //     ->active()
        //     ->with(['instruktur'])
        //     ->get();

        // Siapkan data untuk PDF
        $data = [
            'penempatan' => $penempatan,
            'date' => now()->format('d-m-Y'),
        ];
        // return view('siswa.pdf.resume', $data);
        $pdf = Pdf::loadView('siswa.pdf.resume', $data);
        return $pdf->stream(); // Untuk menampilkan PDF di browser
    }
}
