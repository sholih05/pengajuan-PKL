<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SiswaExport;
use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Jurusan;
use App\Models\Siswa;
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

class SiswaController extends Controller
{
    public function index()
    {
        $data = array(
            'gol_dar' => array('A', 'B', 'O', 'AB'),
            'gender' => array('L', 'P'),
            'jurusan' => Jurusan::where('is_active', true)->get(),
            'masterExcel' => asset('assets/excel/master-siswa.xlsx')
        );
        return view('siswa.index', $data);
    }

    public function create()
    {
        $data = array(
            'gol_dar' => array('A', 'B', 'O', 'AB'),
            'gender' => array('L', 'P'),
            'jurusan' => Jurusan::where('is_active', true)->get()
        );
        return view('siswa.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nis' => [
                'required',
                'string',
                'max:10',
                'min:10',
                Rule::unique('siswa')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nisn' => [
                'required',
                'string',
                'max:10',
                'min:10',
                Rule::unique('siswa')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'tempat_lahir' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|string|max:1',
            'golongan_darah' => 'nullable|string|max:2',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|email|max:35',
            'alamat' => 'required|string|max:225',
            'kelas' => 'required|string|max:2',
            'id_jurusan' => 'required|string|max:5',
            'nama_wali' => 'required|string|max:35',
            'alamat_wali' => 'required|string|max:225',
            'no_kontak_wali' => 'required|string|max:14',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi foto
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Proses upload foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoPath = $foto->store('uploads/img_users', 'public'); // Simpan di storage/public/uploads/foto_siswa
            }

            // Buat user baru di tabel users
            $user = User::create([
                'username' => $request->nis, // Gunakan NIS sebagai username
                'password' => Hash::make($request->nis), // Hash password menggunakan NIS
                'role' => '5', // Role '5' untuk siswa, bisa disesuaikan
            ]);

            // Pastikan user berhasil disimpan, dan ambil ID-nya
            if ($user) {
                // Simpan data siswa dengan id_user dari user yang baru dibuat
                Siswa::create([
                    'nis' => $request->nis,
                    'nisn' => $request->nisn,
                    'nama' => $request->nama,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'gender' => $request->gender,
                    'golongan_darah' => $request->golongan_darah,
                    'no_kontak' => $request->no_kontak,
                    'email' => $request->email,
                    'alamat' => $request->alamat,
                    'kelas' => $request->kelas,
                    'id_jurusan' => $request->id_jurusan,
                    'nama_wali' => $request->nama_wali,
                    'alamat_wali' => $request->alamat_wali,
                    'no_kontak_wali' => $request->no_kontak_wali,
                    'id_user' => $user->id, // Ambil id dari user yang baru dibuat
                    'foto' => $fotoPath, // Simpan path foto
                    'created_by' => Auth::id(),
                ]);

                // Commit transaksi
                DB::commit();

                // Redirect ke halaman index dengan pesan sukses
                return redirect()->route('siswa')->with('success', 'Data siswa berhasil disimpan');
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }


    public function edit(Siswa $siswa)
    {
        $gol_dar = array('A', 'B', 'O', 'AB');
        $gender = array('L', 'P');
        $jurusan = Jurusan::where('is_active', true)->get();
        return view('siswa.edit', compact('siswa', 'gol_dar', 'gender', 'jurusan'));
    }

    public function update(Request $request, $nis, $nisn)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nis' => [
                'required',
                'string',
                'max:10',
                'min:10',
                Rule::unique('siswa', 'nis')->ignore($nis, 'nis')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nisn' => [
                'required',
                'string',
                'max:10',
                'min:10',
                Rule::unique('siswa', 'nisn')->ignore($nisn, 'nisn')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'nama' => 'required|string|max:50',
            'tempat_lahir' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|string|max:1',
            'golongan_darah' => 'nullable|string|max:2',
            'no_kontak' => 'required|string|max:14',
            'email' => 'required|email|max:35',
            'alamat' => 'required|string|max:225',
            'kelas' => 'required|string|max:2',
            'id_jurusan' => 'required|string|max:5',
            'nama_wali' => 'required|string|max:35',
            'alamat_wali' => 'required|string|max:225',
            'no_kontak_wali' => 'required|string|max:14',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mulai transaksi database untuk memastikan atomicity
        DB::beginTransaction();

        try {
            // Ambil data siswa yang ingin diupdate
            $siswa = Siswa::where('nis', $nis)->firstOrFail();

            // Update data user
            $user = User::findOrFail($siswa->id_user);
            if ($user->username != $request->nis) {
                $user->update([
                    'username' => $request->nis, // Update NIS sebagai username
                    'password' => Hash::make($request->nis), // Hash password dengan NIS
                ]);
            }

            // Update data siswa
            $siswa->update([
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'gender' => $request->gender,
                'golongan_darah' => $request->golongan_darah,
                'no_kontak' => $request->no_kontak,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'kelas' => $request->kelas,
                'id_jurusan' => $request->id_jurusan,
                'nama_wali' => $request->nama_wali,
                'alamat_wali' => $request->alamat_wali,
                'no_kontak_wali' => $request->no_kontak_wali,
                'updated_by' => Auth::id(),
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('siswa')->with('success', 'Data siswa berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();

            return redirect()->back()->with('swal_error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($nis)
    {
        try {
            // Cari data siswa berdasarkan NIS
            $q = Siswa::where('nis', $nis)->firstOrFail();

            // Update is_active menjadi false
            $q->is_active = false;
            $q->updated_by = Auth::id();
            $q->save();

            // Hapus user yang terkait jika diperlukan
            $user = User::find($q->id_user);
            if ($user) {
                // Update is_active menjadi false
                $user->is_active = false;
                $user->save();
            }

            // Redirect kembali dengan pesan sukses
            return redirect()->route('siswa')->with('success', 'Data siswa dan user berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika terjadi error, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->route('siswa')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function data(Request $request)
    {
        $data = Siswa::where('is_active', true);
        if (Auth::user()->role == 2) {
            $data = $data->where('id_jurusan', session('id_jurusan'));
        }
        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->nis) . '">' . $dt->nama . '</a>';
            })
            ->addColumn('action', function ($dt) {
                $editUrl = route('siswa.edit', $dt->nis);
                $deleteUrl = route('siswa.destroy', $dt->nis); // Route untuk delete

                return '
                    <button class="btn btn-sm btn-success btn-change"><i class="bi bi-person-gear"></i></button> | <a href="' . $editUrl . '" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i></a> |
                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(' . $dt->nis . ')"><i class="bi bi-trash-fill"></i></button>
                    <form id="delete-form-' . $dt->nis . '" action="' . $deleteUrl . '" method="POST" style="display:none;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                    </form>';
            })
            ->rawColumns(['action', 'nama_siswa'])
            ->make(true);
    }

    public function change_status_kerja(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:10|min:10|exists:siswa,nis',
            'status_bekerja' => 'required|string',
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Update if the record exists, otherwise create a new one
        $siswa = Siswa::active()->where('nis', $request->nis)->first();

        // Update or insert the data
        $siswa->status_bekerja = $request->status_bekerja;
        $siswa->updated_by = Auth::id();
        $siswa->save();

        // Return success response
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }

    public function search(Request $request)
    {
        $search = $request->input('q'); // Nilai pencarian dari input Select2
        $key = $request->input('k');

        // Query dasar untuk siswa yang aktif
        $siswaQuery = Siswa::with('penempatan')->where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nis', 'LIKE', "%{$search}%");
            });

        // Jika $k == 'presensi', cari siswa yang ada di penempatan
        if ($key === 'presensi') {
            $siswaQuery = $siswaQuery->whereHas('penempatan', function ($query) {
                $query->where('is_active', true); // Contoh: hanya ambil penempatan dengan status "active"
            });
        }

        // Tambahkan kondisi berdasarkan role user
        if (Auth::user()->role == 2) {
            $siswaQuery = $siswaQuery->where('id_jurusan', session('id_jurusan'));
        }

        // Jika $k == 'presensi', ambil data siswa beserta instruktur dari relasi penempatan
        if ($key === 'presensi') {
            $siswa = $siswaQuery->with(['penempatan.instruktur' => function ($query) {
                $query->select('id_instruktur', 'nama'); // Ambil hanya kolom yang diperlukan dari instruktur
            }])->get(['nama', 'nis']); // Ambil hanya kolom yang diperlukan dari siswa
        } else {
            $siswa = $siswaQuery->get(['nama', 'nis']); // Ambil hanya kolom yang diperlukan
        }

        return response()->json($siswa);
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
            Excel::import(new SiswaImport, $request->file('excel_file'));

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
        return Excel::download(new SiswaExport, 'data-siswa ' . date('Y-m-d') . '.xlsx');
    }
}