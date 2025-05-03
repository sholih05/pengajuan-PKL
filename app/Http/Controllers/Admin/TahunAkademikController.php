<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ThnAkademikExport;
use App\Http\Controllers\Controller;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $data = ThnAkademik::where('is_active', true)->get();
        return view('master.tahun-akademik.index');
    }

    public function data(Request $request)
    {
        $data = ThnAkademik::where('is_active', true);

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
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
            'id_ta' => $isUpdate ? ($request->id_ta_old == $request->id_ta ? 'required|exists:thn_akademik,id_ta' : '') : '',
            'tahun_akademik' => 'required|string|max:9',
            'mulai' => 'required|date',
            'selesai' => 'required|date',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $tahun = $isUpdate ? ThnAkademik::where('id_ta', $request->id_ta_old)->first() : new ThnAkademik();

        // Update or insert the data
        $tahun->tahun_akademik = $request->tahun_akademik;
        $tahun->mulai = $request->mulai;
        $tahun->selesai = $request->selesai;
        $by = Auth::id();
        $isUpdate ? $tahun->updated_by = $by: $tahun->created_by = $by;
        $tahun->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }
    // Delete a record
    public function destroy(Request $request)
    {
        // Cek apakah id thn_akademik tidak terpakai di relasi Siswa
        $thnAkademik = ThnAkademik::where('id_ta', $request->id)
            ->whereDoesntHave('penempatan')
            ->first();

        if ($thnAkademik) {
            // Update is_active menjadi false
            $thnAkademik->is_active = false;
            $thnAkademik->updated_by = Auth::id();
            $thnAkademik->save();
            return response()->json(['status' => true, 'message' => 'Data tahun akademik berhasil dihapus'], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'Data tahun akademik sedang digunakan, tidak dapat dihapus'], 400);
        }
    }

    public function downloadExcel()
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new ThnAkademikExport, 'master-tahun-akademik.xlsx');
    }

}
