<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Dudi;
use App\Models\Jurusan;
use App\Models\Ketersediaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KetersediaanController extends Controller
{
    public function index()
    {
        $dudi = Dudi::where('is_active', true)->get();
        $jurusan = Jurusan::where('is_active', true)->get();
        return view('dudi.ketersediaan.index', compact('dudi', 'jurusan'));
    }

    public function data(Request $request)
    {
        $data = Ketersediaan::with('jurusan', 'dudi')->where('is_active', true);

        return DataTables::of($data)
            ->addColumn('jurusan', function ($dt) {
                return $dt->jurusan->jurusan;
            })
            ->addColumn('dudi', function ($dt) {
                return '<a href="' . url("/d/dudi?id=" . $dt->dudi->id_dudi) . '">' . $dt->dudi->nama . '</a>';
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns(['action', 'dudi'])
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
            'tanggal' => 'required|date',
            'id_jurusan' => 'required|exists:jurusan,id_jurusan',
            'id_dudi' => 'required|exists:dudi,id_dudi',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $sedia = $isUpdate ? Ketersediaan::where('id_ketersediaan', $request->id_ketersediaan)->first() : new Ketersediaan();

        // Update or insert the data
        $sedia->tanggal = $request->tanggal;
        $sedia->id_jurusan = $request->id_jurusan;
        $sedia->id_dudi = $request->id_dudi;
        $by = Auth::id();
        $isUpdate ? $sedia->updated_by = $by : $sedia->created_by = $by;
        $sedia->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }
    // Delete a record
    public function destroy(Request $request)
    {
        $data = Ketersediaan::findOrFail($request->id);
        // Update is_active menjadi false
        $data->is_active = false;
        $data->updated_by = Auth::id();
        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Ketersediaan berhasil dihapus.'
        ]);
    }
}
