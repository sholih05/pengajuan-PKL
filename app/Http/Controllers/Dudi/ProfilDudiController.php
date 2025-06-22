<?php

namespace App\Http\Controllers\Dudi;

use App\Http\Controllers\Admin\KetersediaanController;
use App\Http\Controllers\Controller;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\Instruktur;
use App\Models\Jurusan;
use App\Models\Ketersediaan;
use App\Models\Penempatan;
use App\Models\Siswa;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProfilDudiController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->id;
        if (!$request->has('id')) {
            //gagal mengambil data
        }
        $thnAkademikAktif = getActiveAcademicYear();
        $dudi = Dudi::where('id_dudi', $id)->firstOrFail();
        $instruktur = Instruktur::where('id_dudi', $id)->get();
        $thnAkademik = ThnAkademik::active()->orderBy('id_ta', 'desc')->get();
        $jurusan= Jurusan::where('is_active',true)->get();
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();
        // dd($instruktur);
        return view('dudi.dudi.profile', compact('instruktur','ta', 'thnAkademikAktif', 'dudi', 'thnAkademik','jurusan'));
    }


    public function data_siswa(Request $request)
    {
        $id_dudi = $request->id;
        $id_ta = $request->id_ta;

        $data = Siswa::where('is_active', true)
            ->whereHas('penempatan', function ($query) use ($id_dudi, $id_ta) {
                $query->active()
                    ->where('id_ta', $id_ta)
                    ->whereHas('instruktur', function ($query) use ($id_dudi) {
                        $query->active()
                            ->where('id_dudi', $id_dudi);
                    });
            })
            ->select('nis', 'nisn', 'nama')
            ->distinct();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->nis) . '">' . $dt->nama . '</a>';
            })
            ->rawColumns(['nama_siswa'])
            ->make(true);
    }


    public function data_guru(Request $request)
    {
        $id_dudi = $request->id;
        $id_ta = $request->id_ta;

        // Mengambil data guru yang terkait dengan Dudi dan tahun akademik tertentu melalui relasi Eloquent
        $data = Guru::active()
            ->whereHas('penempatan', function ($query) use ($id_dudi, $id_ta) {
                $query->active()
                    ->where('id_ta', $id_ta)
                    ->whereHas('instruktur', function ($query) use ($id_dudi) {
                        $query->active()
                            ->where('id_dudi', $id_dudi);
                    });
            })
            ->select('id_guru', 'nama', 'no_kontak', 'email')
            ->distinct();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_guru', function ($dt) {
                return '<a href="' . url("/d/guru?id=" . $dt->id_guru) . '">' . $dt->nama . '</a>';
            })
            ->addColumn('action', function ($dt) {
                return '<button class="btn btn-sm btn-primary btn-edit">Edit</button>';
            })
            ->rawColumns(['nama_guru', 'action'])
            ->make(true);
    }

    public function data_instruktur(Request $request)
    {
        // Ambil ID Dudi dan ID Tahun Akademik dari request
        $id_dudi = $request->id;
        $id_ta = $request->id_ta;

        // Ambil data instruktur yang terkait dengan Dudi dan tahun akademik tertentu melalui tabel penempatan
        $data = Instruktur::where('id_dudi', $id_dudi)
            ->active()
            ->whereHas('penempatan', function ($query) use ($id_ta) {
                $query->where('id_ta', $id_ta)
                    ->active();
            });

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->id_instruktur) . '">' . $dt->nama . '</a>';
            })
            ->addColumn('action', function ($dt) {
                return '<button class="btn btn-sm btn-primary btn-edit">Edit</button>';
            })
            ->rawColumns(['nama_instruktur', 'action'])
            ->make(true);
    }

    public function data_ketersediaan(Request $request)
    {
        $thnAkademik = ThnAkademik::find($request->id_ta);
        // Ambil rentang tahun akademik
        $startDate = $thnAkademik->mulai;
        $endDate = $thnAkademik->selesai;
        // Ambil data ketersediaan dalam rentang tahun akademik
        $data = Ketersediaan::with('jurusan', 'dudi')
            ->whereBetween('tanggal', [$startDate, $endDate])->where('id_dudi', $request->id) ->active();

        return DataTables::of($data)
            ->addColumn('jurusan', function ($dt) {
                return $dt->jurusan->jurusan;
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

     // Function to handle upsert operation
     public function upsert_ketersediaan(Request $request)
     {
         $ketersediaanController = new KetersediaanController();
         return $ketersediaanController->upsert($request);
     }
     // Delete a record
     public function destroy_ketersediaan(Request $request)
     {
        $ketersediaanController = new KetersediaanController();
         return $ketersediaanController->destroy($request);
     }
}
