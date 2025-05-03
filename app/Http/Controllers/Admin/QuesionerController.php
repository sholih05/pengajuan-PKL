<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuesionerExport;
use App\Http\Controllers\Controller;
use App\Imports\QuesionerImport;
use App\Models\Quesioner;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class QuesionerController extends Controller
{

    public function index()
    {
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();
        $tahunAkademikExcel = $ta;
        $masterExcel = asset('assets/excel/master_quesioer.xlsx');
        return view('master.quesioner.index', compact('ta', 'tahunAkademikExcel','masterExcel'));
    }

    public function data(Request $request)
    {
        $data = Quesioner::with('tahunAkademik')->where('is_active', true);

        return DataTables::of($data)
            ->addColumn('tahun_akademik', function ($dt) {
                return $dt->tahunAkademik->tahun_akademik;
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns(['action'])
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
            'soal' => 'required|string|max:255',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $q = $isUpdate ? Quesioner::where('id_quesioner', $request->id_quesioner)->first() : new Quesioner();

        // Update or insert the data
        $q->id_ta = $request->id_ta;
        $q->soal = $request->soal;
        $by = Auth::id();
        $isUpdate ? $q->updated_by = $by : $q->created_by = $by;
        $q->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }
    // Delete a record
    public function destroy(Request $request)
    {
        $q = Quesioner::findOrFail($request->id);
        // Update is_active menjadi false
        $q->is_active = false;
        $q->updated_by = Auth::id();
        $q->save();

        return response()->json([
            'status' => true,
            'message' => 'Ketersediaan berhasil dihapus.'
        ]);
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
            Excel::import(new QuesionerImport($request->id_ta_excel), $request->file('excel_file'));

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

    public function downloadExcel()
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new QuesionerExport, 'data-quesioner '.date('Y-m-d').'.xlsx');
    }
}
