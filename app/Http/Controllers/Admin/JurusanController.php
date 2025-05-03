<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JurusanExport;
use App\Http\Controllers\Controller;
use App\Imports\JurusanImport;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class JurusanController extends Controller
{
    public function index()
    {
        return view('master.jurusan.index');
    }

    public function data(Request $request)
    {
        $data = Jurusan::where('is_active', true)->get();

        return DataTables::of($data)
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
            'id_jurusan' => $isUpdate ? ($request->id_jurusan_old == $request->id_jurusan ? 'required|string|max:5|exists:jurusan,id_jurusan' : 'required|string|max:5|unique:jurusan,id_jurusan') : 'required|string|max:5|unique:jurusan,id_jurusan',
            'jurusan' => 'required|string|max:35',
            'singkatan' => 'required|string|max:5',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $jurusan = $isUpdate ? Jurusan::where('id_jurusan', $request->id_jurusan_old)->first() : new Jurusan();

        // Update or insert the data
        $jurusan->id_jurusan = $request->id_jurusan;
        $jurusan->jurusan = $request->jurusan;
        $jurusan->singkatan = $request->singkatan;
        $by = Auth::id();
        $isUpdate ? $jurusan->updated_by = $by : $jurusan->created_by = $by;
        $jurusan->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }
    // Delete a record
    public function destroy(Request $request)
    {
        $jurusan = Jurusan::findOrFail($request->id);
        // Update is_active menjadi false
        $jurusan->is_active = false;
        $jurusan->updated_by = Auth::id();
        $jurusan->save();

        return response()->json([
            'status' => true,
            'message' => 'Kabupaten berhasil dihapus.'
        ]);
    }

    // Fungsi untuk menangani upload Excel via AJAX
    public function uploadExcel(Request $request)
    {
        // Validasi file
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv',
        ]);

        try {
            // Mengimpor data dari file Excel
            Excel::import(new JurusanImport, $request->file('excel_file'));

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
        return Excel::download(new JurusanExport, 'master-jurusan.xlsx');
    }
}
