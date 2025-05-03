<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GuruExport;
use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\Jurusan;
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

class GuruController extends Controller
{
    public function index()
    {
        $masterExcel = asset('assets/excel/master-guru.xlsx');
        return view('guru.index',compact('masterExcel'));
    }

    public function create()
    {
        $gender = array('L', 'P');
        $jurusan = Jurusan::where('is_active', true)->get();
        return view('guru.create', compact('jurusan', 'gender'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_guru' => [
                'required',
                'string',
                'max:15',
                'min:15',
                Rule::unique('guru')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'gender' => 'required|string|max:1',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|string|max:35|email',
            'alamat' => 'nullable|string|max:100',
            'id_jurusan' => 'required|exists:jurusan,id_jurusan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Buat user baru di tabel users
            $user = User::create([
                'username' => $request->id_guru, // Gunakan id_guru sebagai username
                'password' => Hash::make($request->id_guru), // Hash password menggunakan id_guru
                'role' => '3',
            ]);

            // Pastikan user berhasil disimpan, dan ambil ID-nya
            if ($user) {
                // Simpan data siswa dengan id_user dari user yang baru dibuat
                Guru::create([
                    'id_guru' => $request->id_guru,
                    'nama' => $request->nama,
                    'gender' => $request->gender,
                    'no_kontak' => $request->no_kontak,
                    'email' => $request->email,
                    'alamat' => $request->alamat,
                    'id_jurusan' => $request->id_jurusan,
                    'id_user' => $user->id, // Ambil id dari user yang baru dibuat
                    'create_by' => Auth::id(),
                ]);

                // Commit transaksi
                DB::commit();

                // Redirect ke halaman index dengan pesan sukses
                return redirect()->route('guru')->with('success', 'Data guru berhasil disimpan');
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id_guru)
    {
        $guru = Guru::findOrFail($id_guru);
        $jurusan = Jurusan::where('is_active', true)->get();
        $gender = array('L', 'P');
        return view('guru.edit', compact('guru', 'jurusan', 'gender'));
    }

    public function update(Request $request, $id_guru)
    {
        // Validasi input 
        $validator = Validator::make($request->all(), [
            'id_guru' => [
                'required',
                'string',
                'max:15',
                'min:15',
                Rule::unique('guru', 'id_guru')->ignore($id_guru, 'id_guru')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'gender' => 'required|string|max:1',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|string|max:35|email',
            'alamat' => 'nullable|string|max:100',
            'id_jurusan' => 'required|exists:jurusan,id_jurusan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Cari data guru berdasarkan ID
            $guru = Guru::findOrFail($id_guru);

            // Cari user yang terkait dengan guru
            $user = User::findOrFail($guru->id_user);
            if ($user->username != $guru->id_user) {
                // Update data user
                $user->update([
                    'username' => $request->id_guru, // Update username jika ID guru berubah
                    'password' => Hash::make($request->id_guru), // Opsional: update password jika diperlukan
                ]);
            }

            // Update data guru
            $guru->update([
                'id_guru' => $request->id_guru,
                'nama' => $request->nama,
                'gender' => $request->gender,
                'no_kontak' => $request->no_kontak,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'id_jurusan' => $request->id_jurusan,
                'updated_by' => Auth::id(),
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('guru')->with('success', 'Data guru berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }


    public function destroy($id_guru)
    {
        try {
            // Cari data
            $guru = Guru::where('id_guru', $id_guru)->firstOrFail();

            // Update is_active menjadi false
            $guru->is_active = false;
            $guru->updated_by = Auth::id();
            $guru->save();

            // Hapus user yang terkait jika diperlukan
            $user = User::find($guru->id_user);
            if ($user) {
                // Update is_active menjadi false
                $user->is_active = false;
                $user->updated_by = Auth::id();
                $user->save();
            }

            // Redirect kembali dengan pesan sukses
            return redirect()->route('guru')->with('success', 'Data guru dan user berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika terjadi error, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->route('guru')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {

        $data = Guru::with('jurusan', 'user')->where('is_active', true);
        if (Auth::user()->role==2) {
            $data = $data->where('id_jurusan', session('id_jurusan'));
        }

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_guru', function ($dt) {
                return '<a href="'.url("/d/guru?id=".$dt->id_guru).'">'.$dt->nama.'</a>';
            })
            ->addColumn('jurusan', function ($dt) {
                return $dt->jurusan->jurusan;
            })
            ->addColumn('role', function ($dt) {
                return role_text($dt->user->role);
            })
            ->addColumn('action', function ($dt) {
                $editUrl = route('guru.edit', $dt->id_guru);
                $deleteUrl = route('guru.destroy', $dt->id_guru); // Route untuk delete
                $id = "'$dt->id_guru'";

                return '
                    <button class="btn btn-sm btn-success" onclick="ubahRole(' . $id . ',' . $dt->user->role . ')"><i class="bi bi-person-fill-gear"></i></button> |
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i></a> |
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $dt->id_guru . ')"><i class="bi bi-trash-fill"></i></button>
                    <form id="delete-form-' . $dt->id_guru . '" action="' . $deleteUrl . '" method="POST" style="display:none;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                    </form>';
            })
            ->rawColumns(['action','nama_guru'])
            ->make(true);
    }

    public function updateRole(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru', // Pastikan id_guru valid
            'role' => 'required|in:1,2,3' // Hanya boleh nilai 2 (Admin) atau 3 (Guru)
        ]);

        // Cari data guru berdasarkan ID
        $guru = Guru::findOrFail($request->id_guru);

        // Jika role yang diinginkan adalah Super Admin (1)
        if ($request->role == 1) {
            // Ubah super admin lama menjadi guru
            User::where('role', 1)->update(['role' => 3]);
        }
        // Jika role yang diinginkan adalah Admin (2)
        if ($request->role == 2) {
            // Ubah semua admin yang ada di jurusan ini menjadi guru
            User::where('role', 2)->whereHas('guru', function ($query) use ($guru) {
                $query->where('id_jurusan', $guru->id_jurusan); // Pastikan jurusan sama
            })->update(['role' => 3]);
        }

        // Cari user yang terkait dengan guru
        $user = User::findOrFail($guru->id_user); // Asumsikan ada relasi dengan tabel users
        $user->role = $request->role;
        // Simpan perubahan pada user
        $user->save();

        if ($request->role == 1) {
            Auth::logout(); // Logout user setelah menjadi Super Admin
            return redirect()->route('login'); // Arahkan ke halaman login
        }

        // Redirect kembali dengan pesan sukses
        return response()->json(['message' => 'Role berhasil diubah']);
    }


    public function search(Request $request)
    {
        $search = $request->input('q'); // Nilai pencarian dari input Select2

        $guru = Guru::where('is_active', true) // Tambahkan kondisi is_active = true
                    ->where(function($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%")
                              ->orWhere('id_guru', 'LIKE', "%{$search}%");
                    })
                    ->get(['nama', 'id_guru']); // Ambil hanya data yang dibutuhkan

        return response()->json($guru);
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
             Excel::import(new GuruImport, $request->file('excel_file'));

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
         return Excel::download(new GuruExport, 'data-guru ' . date('Y-m-d') . '.xlsx');
     }
}
