<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PenempatanExport;
use App\Http\Controllers\Controller;
use App\Imports\PenempatanImport;
use App\Models\Instruktur;
use App\Models\Penempatan;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PenempatanController extends Controller
{
    public function index()
    {
        // Ambil tahun akademik yang aktif
        $aktifAkademik = getActiveAcademicYear();
        $thnAkademik = ThnAkademik::where('is_active', true)->orderBy('tahun_akademik', 'desc')->get();
        $tahunAkademikExcel = $thnAkademik;
        $masterExcel = asset('assets/excel/master-penempatan.xlsx');
        return view('pkl.penempatan.index', compact('thnAkademik', 'aktifAkademik','tahunAkademikExcel','masterExcel'));
    }

    public function data(Request $request)
    {
        $data = Penempatan::with(['siswa', 'guru', 'instruktur', 'tahunAkademik', 'dudi'])
            ->where('penempatan.is_active', true)->where('id_ta', $request->id_ta);

        if (Auth::user()->role == 2) {
            $data = $data->whereHas('siswa', function ($query) {
                $query->where('id_jurusan', session('id_jurusan'));
            });
        }

        return DataTables::of($data)
            ->addColumn('nis', function ($dt) {
                return $dt->siswa->nis;
            })
            ->addColumn('nisn', function ($dt) {
                return $dt->siswa->nisn;
            })
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('nama_guru', function ($dt) {
                return '<a href="' . url("/d/guru?id=" . $dt->guru->id_guru) . '">' . $dt->guru->nama . '</a>';
            })
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->instruktur->id_instruktur) . '">' . $dt->instruktur->nama . '</a>';
            })
            ->addColumn('nama_dudi', function ($dt) {
                return $dt->dudi->nama;
            })
            ->addColumn('tahun_akademik', function ($dt) {
                return $dt->tahunAkademik->tahun_akademik;
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns([
                'action',
                'nama_siswa',
                'nama_guru',
                'nama_instruktur'
            ])
            ->make(true);
    }

    // Upsert (Insert or Update) a record
    // Function to handle upsert operation
    public function upsert(Request $request)
    {
        // Determine if this is an update or a create action
        $isUpdate = $request->stt;

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'id_ta' => 'required|exists:thn_akademik,id_ta',
            'nis' => 'required|exists:siswa,nis',
            'id_guru' => 'required|exists:guru,id_guru',
            'id_instruktur' => 'required|exists:instruktur,id_instruktur',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $q = $isUpdate ? Penempatan::where('id_penempatan', $request->id_penempatan)->first() : new Penempatan();

        // Update or insert the data
        $q->id_ta = $request->id_ta;
        $q->nis = $request->nis;
        $q->id_guru = $request->id_guru;
        $q->id_instruktur = $request->id_instruktur;
        $by = Auth::id();
        $isUpdate ? $q->updated_by = $by : $q->created_by = $by;
        $q->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }
    // Delete a record
    public function destroy(Request $request)
    {
        $data = Penempatan::findOrFail($request->id);
        // Update is_active menjadi false
        $data->is_active = false;
        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Penempatan berhasil dihapus.'
        ]);
    }

    public function getInstruktur(Request $request)
    {
        $nis = $request->input('nis');
        // Cari siswa berdasarkan NIS
        $data = Penempatan::with('instruktur')->where('nis', $nis)->first();

        if ($data) {
            return response()->json([
                'status' => 'success',
                'instruktur' => [
                    'id_instruktur' => $data->instruktur->id_instruktur,
                    'nama' => $data->instruktur->nama
                ],
                'id_penempatan' =>$data->id_penempatan,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Instruktur tidak ditemukan untuk siswa ini.'
        ], 404);
    }

   // Fungsi untuk menangani upload Excel via AJAX
   public function uploadExcel(Request $request)
   {
       // Validasi file dan ID Tahun Akademik
       $request->validate([
           'excel_file' => 'required|file|mimes:xlsx,csv',
           'id_ta_excel' => 'required|exists:thn_akademik,id_ta', // Pastikan ID Tahun Akademik valid
       ]);

       try {
           // Mengimpor data dari file Excel dan memberikan ID Tahun Akademik sebagai parameter
           Excel::import(new PenempatanImport($request->id_ta_excel), $request->file('excel_file'));

           return response()->json([
               'status' => true,
               'message' => 'Data berhasil diupload dan diproses.',
           ]);
       } catch (\Exception $e) {
           Log::error('Error uploading Excel file: ' . $e->getMessage());
           return response()->json([
               'status' => false,
               'message' => 'Terjadi kesalahan saat mengupload file.',
           ]);
       }
   }

    public function downloadExcel(Request $request)
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new PenempatanExport($request->id_ta), 'data-penempatan ' . date('Y-m-d') . '.xlsx');
    }
}
