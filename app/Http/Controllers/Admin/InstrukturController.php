<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InstrukturExport;
use App\Http\Controllers\Controller;
use App\Imports\InstrukturImport;
use App\Models\Dudi;
use App\Models\Instruktur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InstrukturController extends Controller
{
    public function index()
    {
        $masterExcel = asset('assets/excel/master-instruktur.xlsx');
        return view('dudi.instruktur.index', compact('masterExcel'));
    }

    public function create()
    {
        $gender = array('L', 'P');
        $dudi = Dudi::where('is_active', true)->get();
        return view('dudi.instruktur.create', compact('gender', 'dudi'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_instruktur' => [
                'required',
                'string',
                'max:15',
                'min:15',
                Rule::unique('instruktur')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'gender' => 'required|string|max:1',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|string|max:35|email',
            'alamat' => 'nullable|string|max:100',
            'id_dudi' => 'required|exists:dudi,id_dudi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Buat user baru di tabel users
            $user = User::create([
                'username' => $request->id_instruktur, // Gunakan id_instruktur sebagai username
                'password' => Hash::make($request->id_instruktur), // Hash password menggunakan id_instruktur
                'role' => '4',
            ]);

            // Pastikan user berhasil disimpan, dan ambil ID-nya
            if ($user) {
                // Simpan data siswa dengan id_user dari user yang baru dibuat
                Instruktur::create([
                    'id_instruktur' => $request->id_instruktur,
                    'nama' => $request->nama,
                    'gender' => $request->gender,
                    'no_kontak' => $request->no_kontak,
                    'email' => $request->email,
                    'alamat' => $request->alamat,
                    'id_dudi' => $request->id_dudi,
                    'id_user' => $user->id, // Ambil id dari user yang baru dibuat
                    'create_by' => Auth::id(),
                ]);

                // Commit transaksi
                DB::commit();

                // Redirect ke halaman index dengan pesan sukses
                return redirect()->route('instruktur')->with('success', 'Data instruktur berhasil disimpan');
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id_instruktur)
    {
        $instruktur = Instruktur::findOrFail($id_instruktur);
        $gender = array('L', 'P');
        $dudi = Dudi::where('is_active', true)->get();
        return view('dudi.instruktur.edit', compact('instruktur',  'gender', 'dudi'));
    }

    public function update(Request $request, $id_instruktur)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_instruktur' => [
                'required',
                'string',
                'max:15',
                'min:15',
                Rule::unique('instruktur', 'id_instruktur')->ignore($id_instruktur, 'id_instruktur')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'gender' => 'required|string|max:1',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|string|max:35|email',
            'alamat' => 'nullable|string|max:100',
            'id_dudi' => 'required|exists:dudi,id_dudi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Cari data instruktur berdasarkan ID
            $instruktur = Instruktur::findOrFail($id_instruktur);

            // Cari user yang terkait dengan instruktur
            $user = User::findOrFail($instruktur->id_user);
            if ($user->username != $instruktur->id_user) {
                // Update data user
                $user->update([
                    'username' => $request->id_instruktur, // Update username jika ID instruktur berubah
                    'password' => Hash::make($request->id_instruktur), // Opsional: update password jika diperlukan
                ]);
            }

            // Update data instruktur
            $instruktur->update([
                'id_instruktur' => $request->id_instruktur,
                'nama' => $request->nama,
                'gender' => $request->gender,
                'no_kontak' => $request->no_kontak,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'id_dudi' => $request->id_dudi,
                'updated_by' => Auth::id(),
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('instruktur')->with('success', 'Data instruktur berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }


    public function destroy($id_instruktur)
    {
        try {
            // Cari data
            $instruktur = Instruktur::where('id_instruktur', $id_instruktur)->firstOrFail();

            // Update is_active menjadi false
            $instruktur->is_active = false;
            $instruktur->updated_by = Auth::id();
            $instruktur->save();

            // Hapus user yang terkait jika diperlukan
            $user = User::find($instruktur->id_user);
            if ($user) {
                // Update is_active menjadi false
                $user->is_active = false;
                $user->updated_by = Auth::id();
                $user->save();
            }

            // Redirect kembali dengan pesan sukses
            return redirect()->route('instruktur')->with('success', 'Data instruktur dan user berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika terjadi error, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->route('instruktur')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $data = Instruktur::with('dudi')->where('is_active', true)->get();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('dudi', function ($dt) {
                return $dt->dudi->nama;
            })
            ->addColumn('nama_instruktur', function ($dt) {
                return '<a href="' . url("/d/instruktur?id=" . $dt->id_instruktur) . '">' . $dt->nama . '</a>';
            })
            ->addColumn('action', function ($dt) {
                $editUrl = route('instruktur.edit', $dt->id_instruktur);
                $deleteUrl = route('instruktur.destroy', $dt->id_instruktur); // Route untuk delete

                return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i></a> |
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $dt->id_instruktur . ')"><i class="bi bi-trash-fill"></i></button>
                    <form id="delete-form-' . $dt->id_instruktur . '" action="' . $deleteUrl . '" method="POST" style="display:none;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                    </form>';
            })
            ->rawColumns(['action', 'nama_instruktur'])
            ->make(true);
    }


    public function search(Request $request)
    {
        $search = $request->input('q'); // Nilai pencarian dari input Select2

        $instruktur = Instruktur::with('dudi') // Mengambil relasi dudi
            ->where('is_active', true) // Kondisi is_active = true
            ->where(function ($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('id_instruktur', 'LIKE', "%{$search}%");
            })
            ->get();

        // Mengubah data menjadi format yang diinginkan termasuk nama dudi
        $result = $instruktur->map(function ($item) {
            return [
                'id_instruktur' => $item->id_instruktur,
                'nama' => $item->nama,
                'nama_dudi' => $item->dudi ? $item->dudi->nama : null, // Ambil nama dudi jika ada
            ];
        });

        return response()->json($result);
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
            Excel::import(new InstrukturImport, $request->file('excel_file'));

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
        return Excel::download(new InstrukturExport, 'data-instruktur ' . date('Y-m-d') . '.xlsx');
    }
}
