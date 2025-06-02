<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instruktur\ProfilInstrukturController;
use App\Models\FileModel;
use App\Models\Guru;
use App\Models\Penempatan;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\ThnAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ProfilGuruController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan ID guru
        $id_guru = $request->id;
        if (Auth::user()->role == 3) {
            $id_guru = session('id_guru'); // Pastikan sesi ini sudah diatur saat login guru
        }

        // Ambil tahun akademik yang aktif
        $activeAcademicYear = getActiveAcademicYear();

        // Ambil data guru beserta relasi terkait dengan kondisi where pada id_guru dan penempatan terbaru
        $guru = Guru::with([
            'jurusan',
            'user',
            'penempatan' => function ($query) use ($activeAcademicYear) {
                // Filter penempatan berdasarkan tahun akademik yang aktif dan group by id_instruktur
                $query->where('id_ta', $activeAcademicYear["id_ta"]);
            },
            'penempatan.instruktur',
            'penempatan.tahunAkademik',
            'penempatan.instruktur.dudi',
        ])
            ->where('id_guru', $id_guru)
            ->firstOrFail();
        // Kelompokkan penempatan berdasarkan id_instruktur di level collection
        $penempatanGrouped = $guru->penempatan->groupBy('id_instruktur');
        $files = FileModel::all();

        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();
        return view('guru.profile', compact('guru', 'activeAcademicYear', 'penempatanGrouped', 'ta','files'));
    }

    public function data_siswa(Request $request)
    {
        // Tentukan ID guru
        $id_guru = $request->id;
        if (Auth::user()->role == 3) {
            $id_guru = session('id_guru'); // Pastikan sesi ini sudah diatur saat login guru
        }
        // Mengambil data siswa berdasarkan id_guru
        $data = Penempatan::where('id_guru', $id_guru)
            ->where('id_ta', $request->id_ta)
            ->where('is_active', true) // Filter untuk penempatan yang aktif, jika dibutuhkan
            ->with('siswa','instruktur','dudi') // Memuat relasi siswa
            ->get()
            // ->pluck('siswa','instruktur') // Mengambil hanya data siswa dari hasil query
            ->filter();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->instruktur->id_instruktur) . '">' . $dt->instruktur->nama . '</a>';
            })
            ->addColumn('nama_dudi', function ($dt) {
                return '<a href="' . url("/d/dudi?id=" . $dt->dudi->id_dudi) . '">' . $dt->dudi->nama . '</a>';
            })
            ->rawColumns(['nama_siswa','nama_instruktur','nama_dudi'])
            ->make(true);
    }

    public function kegiatan_siswa(Request $request)
    {
        // Tentukan nis siswa
        $id_guru = $request->id;
        if (Auth::user()->role == 3) {
            $id_guru = session('id_guru'); // Pastikan sesi ini sudah diatur saat login guru
        }

        $ta = ThnAkademik::find($request->id_ta);

        $data = Presensi::with('siswa', 'guru', 'penempatan');
        // memiliki penempatan by guru
        $data = $data->whereHas('penempatan', function ($query) use ($id_guru) {
            $query->where('id_guru', $id_guru);
        });
        $data = $data->where(['is_active' => true,])->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);
        return DataTables::of($data)
            ->addColumn('nama_siswa', function ($dt) {
                return $dt->siswa->nis . '<br>' . '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('presensi_masuk', function ($dt) {
                return $dt->foto_masuk ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_masuk) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->masuk : $dt->masuk;
            })
            ->addColumn('presensi_pulang', function ($dt) {
                return $dt->foto_pulang ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_pulang) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->pulang : $dt->pulang;
            })
            ->addColumn('disetujui_guru', function ($dt) {
                $setujuChecked = $dt->is_acc_guru ? 'checked' : '';
                $tidakSetujuChecked = !$dt->is_acc_guru ? 'checked' : '';

                return '
                    <div class="form-check">
                        <input type="radio" name="approval_' . $dt->id_presensi . '" class="disetujui-guru-radio form-check-input" data-id="' . $dt->id_presensi . '" id="radio-guru1' . $dt->id_presensi . '" value="1" ' . $setujuChecked . '>
                        <label class="form-check-label" for="radio-guru1' . $dt->id_presensi . '"> Ya </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="approval_' . $dt->id_presensi . '" class="disetujui-guru-radio form-check-input" data-id="' . $dt->id_presensi . '" id="radio-guru0' . $dt->id_presensi . '" value="0" ' . $tidakSetujuChecked . '>
                        <label class="form-check-label" for="radio-guru0' . $dt->id_presensi . '"> Tidak </label>
                    </div>';
            })
            ->addColumn('disetujui_instruktur', function ($dt) {
                return $dt->is_acc_instruktur === 1 ? 'Ya' : ($dt->is_acc_instruktur === 0 ? 'Ya' : '');
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> ';
            })
            ->rawColumns(['action', 'nama_siswa', 'disetujui_instruktur', 'disetujui_guru', 'presensi_masuk', 'presensi_pulang'])
            ->make(true);
    }

    public function presensi_approval(Request $request)
    {
        $presensi_approval = new ProfilInstrukturController();

        // Panggil metode presensi_approval
        return $presensi_approval->presensi_approval($request);
    }

    function update_akun(Request $request)
    {
        // Tentukan nis siswa
        $id_guru = $request->id;
        if (Auth::user()->role == 3) {
            $id_guru = session('id_guru');
        }

        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'renew_password' => 'required|same:new_password',
        ]);

        // Ambil user berdasarkan NIS
        $user = User::where(['is_active' => true, 'username' => $id_guru])->first();

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
}
