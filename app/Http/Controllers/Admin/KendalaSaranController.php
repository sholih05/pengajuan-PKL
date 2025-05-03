<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CatatanExport;
use App\Http\Controllers\Controller;
use App\Models\Catatan;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class KendalaSaranController extends Controller
{
    public function index()
    {
        $activeAcademicYear = getActiveAcademicYear();
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();
        return view('pkl.kendala-saran.index', compact('activeAcademicYear','ta'));
    }

    public function data(Request $request)
    {
         $ta = ThnAkademik::find($request->id_ta);
        $data = Catatan::with(['instruktur'])->whereBetween('tanggal', [$ta->mulai, $ta->selesai])
            ->where('is_active', true);
        return DataTables::of($data)
            ->addColumn('kategori_name', function ($dt) {
                return $dt->kategori == 'K' ? '<span class="badge text-bg-danger">Kendala</span>' : ($dt->kategori == 'S' ? '<span class="badge text-bg-primary">Saran</span>' : '');
            })
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->instruktur->id_instruktur) . '">' . $dt->instruktur->nama . '</a>';
            })
            ->rawColumns(['nama_instruktur', 'kategori_name'])
            ->make(true);
    }

    public function downloadExcel(Request $request)
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new CatatanExport($request->id,$request->id_ta), 'data-kendala-saran-'.$request->id.' '.date('Y-m-d').'.xlsx');
    }
}
