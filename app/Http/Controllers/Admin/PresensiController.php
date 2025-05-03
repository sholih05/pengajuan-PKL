<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PresensiExport;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Models\Presensi;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PresensiController extends Controller
{
    public function index()
    {
        $activeAcademicYear = getActiveAcademicYear();
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();
        return view('pkl.presensi.index',compact('activeAcademicYear','ta'));
    }

    public function data(Request $request)
    {
        $ta = ThnAkademik::find($request->id_ta);
        $data = Presensi::with('siswa', 'instruktur')->where('is_active', true)->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);
        if (Auth::user()->role == 2) {
            $data = $data->whereHas('siswa', function ($query) {
                $query->where('id_jurusan', session('id_jurusan'));
            });
        }
        return DataTables::of($data)
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->instruktur->id_instruktur) . '">' . $dt->instruktur->nama. '</a>';
            })
            ->addColumn('presensi_masuk', function ($dt) {
                return $dt->foto_masuk ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_masuk) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->masuk : $dt->masuk;
            })
            ->addColumn('presensi_pulang', function ($dt) {
                return $dt->foto_pulang ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_pulang) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->pulang : $dt->pulang;
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns(['action','nama_instruktur','nama_siswa','presensi_masuk', 'presensi_pulang'])
            ->make(true);
    }

    // Upsert (Insert or Update) a record
    public function upsert(Request $request)
    {
        $isUpdate = $request->stt;

        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'masuk' => 'nullable|date_format:H:i',
            'pulang' => 'nullable|date_format:H:i',
            'kegiatan' => 'nullable|string|max:100',
            'foto_masuk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pulang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_acc_instruktur' => 'required|in:1,0',
            'is_acc_guru' => 'required|in:1,0',
            'id_penempatan' => 'required|exists:penempatan,id_penempatan',
            'catatan' => 'nullable|string|max:225'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Menentukan apakah record akan diperbarui atau dibuat baru
        $presensi = $isUpdate ? Presensi::where('id_presensi', $request->id_presensi)->first() : new Presensi();

        // Mengisi data dari request ke dalam model
        $presensi->tanggal = $request->tanggal;
        $presensi->masuk = $request->masuk;
        $presensi->pulang = $request->pulang;
        $presensi->kegiatan = $request->kegiatan;
        $presensi->is_acc_instruktur = $request->is_acc_instruktur;
        $presensi->is_acc_guru = $request->is_acc_guru;
        $presensi->id_penempatan = $request->id_penempatan;
        $presensi->catatan = $request->catatan;
        $presensi->is_active = true;

        if ($request->hasFile('foto_masuk')) {
            $presensi->foto_masuk = handlePhotoUpload($request->file('foto_masuk'), $presensi->foto_masuk);
        }

        if ($request->hasFile('foto_pulang')) {
            $presensi->foto_pulang = handlePhotoUpload($request->file('foto_pulang'), $presensi->foto_pulang);
        }

        // Menentukan created_by atau updated_by berdasarkan tindakan
        $by = Auth::id();
        if ($isUpdate) {
            $presensi->updated_by = $by;
        } else {
            $presensi->created_by = $by;
        }

        // Menyimpan data presensi
        $presensi->save();

        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }

    function get_penempatan(Request $request) {
        $data = Penempatan::with(['guru','instruktur','dudi','tahunAkademik'])->active()->where('nis',$request->nis)->orderBy('id_penempatan','desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }



    // Delete a record
    public function destroy(Request $request)
    {
        $data = Presensi::findOrFail($request->id);
        $data->is_active = false; // Set as inactive
        $data->save();

        return response()->json([
            'status' => true,
            'message' => 'Presensi berhasil dihapus.'
        ]);
    }

    public function downloadExcel(Request $request)
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new PresensiExport($request->nis,null,$request->id_ta), 'data-presensi-'.$request->nis.' '.date('Y-m-d').'.xlsx');
    }
}
