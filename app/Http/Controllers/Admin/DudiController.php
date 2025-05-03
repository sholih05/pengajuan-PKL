<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DudiExport;
use App\Http\Controllers\Controller;
use App\Imports\DudiImport;
use App\Models\Dudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DudiController extends Controller
{

    public function index()
    {
        $masterExcel = asset('assets/excel/master_quesioer.xlsx');
        return view('dudi.dudi.index',compact('masterExcel'));
    }

    public function create()
    {
        $gender = array('L', 'P');
        $dudi = Dudi::where('is_active', true)->get();
        return view('dudi.dudi.create', compact('gender', 'dudi'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:30',
            'nama_pimpinan' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:100',
            'no_kontak' => 'required|string|max:14',
            'longitude' => 'required|string|max:50',
            'latitude' => 'required|string|max:50',
            'radius' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Simpan data siswa dengan id_user dari user yang baru dibuat
            Dudi::create([
                'nama' => $request->nama,
                'nama_pimpinan' => $request->nama_pimpinan,
                'no_kontak' => $request->no_kontak,
                'alamat' => $request->alamat,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
                'create_by' => Auth::id(),
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('dudi')->with('success', 'Data dudi berhasil disimpan');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id_dudi)
    {
        $dudi = Dudi::findOrFail($id_dudi);
        return view('dudi.dudi.edit', compact('dudi'));
    }

    public function update(Request $request, $id_dudi)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:30',
            'nama_pimpinan' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:100',
            'no_kontak' => 'required|string|max:14',
            'longitude' => 'required|string|max:50',
            'latitude' => 'required|string|max:50',
            'radius' => 'required|string|max:5',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Cari data dudi berdasarkan ID
            $dudi = Dudi::findOrFail($id_dudi);

            // Update data dudi
            $dudi->update([
                'nama' => $request->nama,
                'nama_pimpinan' => $request->nama_pimpinan,
                'no_kontak' => $request->no_kontak,
                'alamat' => $request->alamat,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'radius' => $request->radius,
                'updated_by' => Auth::id(),
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('dudi')->with('success', 'Data dudi berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }


    public function destroy($id_dudi)
    {
        try {
            // Cari data
            $dudi = Dudi::where('id_dudi', $id_dudi)->firstOrFail();
            // Update is_active menjadi false
            $dudi->is_active = false;
            $dudi->updated_by = Auth::id();
            $dudi->save();
            // Redirect kembali dengan pesan sukses
            return redirect()->route('dudi')->with('success', 'Data dudi dan user berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika terjadi error, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->route('dudi')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $data = Dudi::all();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('action', function ($dt) {
                $editUrl = route('dudi.edit', $dt->id_dudi);
                $deleteUrl = route('dudi.destroy', $dt->id_dudi); // Route untuk delete

                return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i></a> |
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $dt->id_dudi . ')"><i class="bi bi-trash-fill"></i></button>
                    <form id="delete-form-' . $dt->id_dudi . '" action="' . $deleteUrl . '" method="POST" style="display:none;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                    </form>';
            })
            ->addColumn('nama_dudi', function ($dt) {
                return '<a href="' . url("/d/dudi?id=" . $dt->id_dudi) . '">' . $dt->nama . '</a>';
            })
            ->rawColumns(['action', 'nama_dudi'])
            ->make(true);
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
            Excel::import(new DudiImport, $request->file('excel_file'));

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupload dan diproses.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading Excel file: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengupload file.' . $e->getMessage(),
            ]);
        }
    }

    public function downloadExcel()
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new DudiExport, 'data-dudi '.date('Y-m-d').'.xlsx');
    }
}
