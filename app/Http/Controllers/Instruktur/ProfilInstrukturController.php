<?php

namespace App\Http\Controllers\Instruktur;

use App\Http\Controllers\Admin\NilaiQuisionerController;
use App\Http\Controllers\Controller;
use App\Models\Catatan;
use App\Models\Instruktur;
use App\Models\Penilaian;
use App\Models\Penempatan;
use App\Models\Presensi;
use App\Models\Quesioner;
use App\Models\ThnAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProfilInstrukturController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan id_instruktur
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_instruktur'); // Pastikan sesi ini sudah diatur saat login instruktur
        }

        // Ambil tahun akademik yang aktif
        $activeAcademicYear = getActiveAcademicYear();

        // Ambil data guru beserta relasi terkait dengan kondisi where pada id_instruktur dan penempatan terbaru
        $instruktur = Instruktur::with([
            'dudi',
            'penempatan' => function ($query) use ($activeAcademicYear) {
                // Filter penempatan berdasarkan tahun akademik yang aktif dan group by id_instruktur
                $query->where('id_ta', $activeAcademicYear["id_ta"]);
            },
            'penempatan.guru',
            'penempatan.tahunAkademik',
        ])
            ->where('id_instruktur', $id_instruktur)
            ->firstOrFail();
        // Kelompokkan penempatan berdasarkan id_instruktur di level collection
        $penempatanGrouped = $instruktur->penempatan->groupBy('id_guru');
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();

        return view('dudi.instruktur.profile', compact('instruktur', 'activeAcademicYear', 'penempatanGrouped', 'ta'));
    }

    public function data_siswa(Request $request)
    {
        // Tentukan ID guru
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_instruktur'); // Pastikan sesi ini sudah diatur saat login guru
        }
        // Mengambil data siswa berdasarkan id_guru
        $data = Penempatan::where('id_instruktur', $id_instruktur)
            ->where('id_ta', $request->id_ta)
            ->where('is_active', true) // Filter untuk penempatan yang aktif, jika dibutuhkan
            ->with('siswa','guru') // Memuat relasi siswa
            ->get()
            // ->pluck('siswa') // Mengambil hanya data siswa dari hasil query
            ->filter();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('nama_guru', function ($dt) {
                return '<a href="' . url("/d/guru?id=" . $dt->guru->id_guru) . '">' . $dt->guru->nama . '</a>';
            })
            ->rawColumns(['nama_guru', 'nama_siswa'])
            ->make(true);
    }

    function update_akun(Request $request)
    {
        // Tentukan nis siswa
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_guru');
        }

        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'renew_password' => 'required|same:new_password',
        ]);

        // Ambil user berdasarkan NIS
        $user = User::where(['is_active' => true, 'username' => $id_instruktur])->first();

        // Periksa apakah password saat ini benar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password saat ini salah.',
            ], 400);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return response success
        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.',
        ], 200);
    }

    public function kegiatan_siswa(Request $request)
    {
        // Tentukan nis siswa
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_instruktur'); // Pastikan sesi ini sudah diatur saat login guru
        }

        $ta = ThnAkademik::find($request->id_ta);

        $data = Presensi::with('siswa', 'instruktur','penempatan');
        // if ($request->stt == 1) {
        //     $data = $data->select('id_presensi', 'tanggal', 'masuk', 'pulang','foto_masuk','foto_pulang', 'siswa.nis');
        // } elseif ($request->stt == 2) {
        //     $data = $data->select('id_presensi', 'tanggal', 'kegiatan', 'is_acc_instruktur','is_acc_guru', 'catatan');
        // }

         // memiliki penempatan by instruktur
         $data = $data->whereHas('penempatan', function ($query) use ($id_instruktur) {
            $query->where('id_instruktur', $id_instruktur);
        });
        $data = $data->active()->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);
        return DataTables::of($data)
            ->addColumn('nama_siswa', function ($dt) {
                return $dt->siswa->nis . '<br>' . '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('presensi_masuk', function ($dt) {
                return $dt->foto_masuk ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_masuk) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->masuk : $dt->masuk;
            })
            ->addColumn('presensi_pulang', function ($dt) {
                return $dt->foto_pulang ? ' <a href="' . url('storage/uploads/foto/' . $dt->foto_pulang) . '" target="_blank"><i class="bi bi-image"></i></a> <br> ' . $dt->pulang : $dt->pulang;
            })
            ->addColumn('disetujui_instruktur', function ($dt) {
                $setujuChecked = $dt->is_acc_instruktur ? 'checked' : '';
                $tidakSetujuChecked = isset($dt->is_acc_instruktur) && !$dt->is_acc_instruktur ? 'checked' : '';


                return '
                    <div class="form-check">
                        <input type="radio" name="approval_' . $dt->id_presensi . '" class="disetujui-instruktur-radio form-check-input" data-id="' . $dt->id_presensi . '" id="radio-instruktur1' . $dt->id_presensi . '" value="1" ' . $setujuChecked . '>
                        <label class="form-check-label" for="radio-instruktur1' . $dt->id_presensi . '"> Ya </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="approval_' . $dt->id_presensi . '" class="disetujui-instruktur-radio form-check-input" data-id="' . $dt->id_presensi . '" id="radio-instruktur0' . $dt->id_presensi . '" value="0" ' . $tidakSetujuChecked . '>
                        <label class="form-check-label" for="radio-instruktur0' . $dt->id_presensi . '"> Tidak </label>
                    </div>';
            })
            ->addColumn('id', function ($dt) {
                return $dt->id_presensi;
            })
            ->addColumn('disetujui_guru', function ($dt) {
                return $dt->is_acc_guru===1 ? 'Ya' : ($dt->is_acc_guru===0 ? 'Ya' : '');
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> ';
            })
            ->rawColumns(['action', 'nama_siswa', 'disetujui_instruktur','disetujui_guru', 'presensi_masuk','presensi_pulang'])
            ->make(true);
    }

    public function presensi_approval(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:presensi,id_presensi',
            'is_acc' => 'required|boolean'
        ]);

        $presensi = Presensi::find($request->id);
        if ($request->stt=='instruktur') {
            $presensi->is_acc_instruktur = $request->is_acc;
        }else {
            $presensi->is_acc_guru = $request->is_acc;
        }
        $presensi->save();

        return response()->json(['message' => 'Status persetujuan berhasil diperbarui']);
    }

    // Upsert (Insert or Update) a record
    public function catatan_siswa_update(Request $request)
    {
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:225'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // Menentukan apakah record akan diperbarui atau dibuat baru
        $presensi = Presensi::where('id_presensi', $request->id_presensi)->first();
        $presensi->catatan = $request->catatan;
        $by = Auth::id();
        $presensi->updated_by = $by;
        $presensi->save();
        return response()->json(['status' => 'success', 'message' => 'Data saved successfully']);
    }

    public function kendala_saran(Request $request)
    {
        // Tentukan nis siswa
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_instruktur'); // Pastikan sesi ini sudah diatur saat login guru
        }
        $ta = ThnAkademik::find($request->id_ta);
        $data = Catatan::with('instruktur');
        $data = $data->where(['is_active' => true, 'id_instruktur' => $id_instruktur])->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);;
        return DataTables::of($data)
            ->addColumn('kategori_name', function ($dt) {
                return $dt->kategori == 'K' ? '<span class="badge text-bg-danger">Kendala</span>' : ($dt->kategori == 'S' ? '<span class="badge text-bg-primary">Saran</span>' : '');
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> ';
            })
            ->rawColumns(['action', 'kategori_name'])
            ->make(true);
    }

    public function kendala_saran_upsert(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'idKendalaSaran' => 'nullable|exists:catatan,id_catatan',
            'tanggal' => 'required|date',
            'catatanKendalaSaran' => 'required|string|max:225',
            'kategori' => 'required|in:K,S',
            'id_instruktur' => 'required|string|max:15',
        ]);

        // Data untuk disimpan
        $data = [
            'tanggal' => $validatedData['tanggal'],
            'catatan' => $validatedData['catatanKendalaSaran'],
            'kategori' => $validatedData['kategori'],
            'id_instruktur' => $validatedData['id_instruktur'],
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];

        // Jika id_catatan ada, lakukan update, jika tidak lakukan insert
        if ($validatedData['idKendalaSaran']) {
            $catatan = Catatan::findOrFail($validatedData['idKendalaSaran']);
            $catatan->update($data); // Update catatan yang sudah ada
            return response()->json(['status' => true, 'message' => 'Catatan berhasil diupdate']);
        } else {
            // Insert baru jika id_catatan tidak ada
            $data['created_by'] = Auth::id();
            $data['created_at'] = now();
            Catatan::create($data); // Insert data baru
            return response()->json(['status' => true, 'message' => 'Catatan berhasil disimpan']);
        }
    }

    function upsert_quesioner(Request $request)
    {
        $id_instruktur = $request->has('id_instruktur') ? $request->id_instruktur : session('id_instruktur');

        // Menambahkan parameter tambahan ke Request yang sudah ada
        $request->merge([
            'tanggal' => date('Y-m-d'),
            'id_instruktur' => $id_instruktur,
        ]);
        $nilaiQuisionerController = new NilaiQuisionerController();

        // Panggil metode upsert
        return $nilaiQuisionerController->upsert($request);
    }

    function edit_quesioner(Request $request)
    {
        $nilaiQuisionerController = new NilaiQuisionerController();
        // Panggil metode upsert
        return $nilaiQuisionerController->edit($request);
    }

    function penilaian(Request $request)
    {
        // Tentukan ID guru
        $id_instruktur = $request->id;
        if (Auth::user()->role == 4) {
            $id_instruktur = session('id_instruktur'); // Pastikan sesi ini sudah diatur saat login guru
        }
        // Mengambil data siswa berdasarkan id_guru
        $data = Penempatan::where('id_instruktur', $id_instruktur)
            ->where('id_ta', $request->id_ta)
            ->where('is_active', true) // Filter untuk penempatan yang aktif, jika dibutuhkan
            ->with('siswa','guru') // Memuat relasi siswa
            ->get()
            // ->pluck('siswa') // Mengambil hanya data siswa dari hasil query
            ->filter();

        return DataTables::of($data)
            ->addIndexColumn() // Menambahkan nomor row
            ->addColumn('nis', function ($dt) {
                return $dt->siswa->nis;
            })
            ->addColumn('nama_siswa', function ($dt) {
                return '<a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama . '</a>';
            })
            ->addColumn('jurusan', function ($dt) {
                return $dt->siswa->jurusan->jurusan ?? '-';
            })
            ->addColumn('penilaian', function ($dt) {
                $penilaian = Penilaian::where('nis', $dt->siswa->nis)
                    ->where('is_active', 1)
                    ->first();
                if ($penilaian) {
                return '
                    <a href="' . route('penilaian.show', $dt->siswa->nis) . '" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="' . route('penilaian.edit', $dt->siswa->nis) . '" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="' . route('penilaian.print', $dt->siswa->nis) . '" class="btn btn-secondary btn-sm" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                ';
                } else {
                    return '
                        <a href="' . route('penilaian.create', $dt->siswa->nis) . '" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Nilai
                        </a>
                    ';
                }
            })

            ->rawColumns(['nama_siswa', 'penilaian'])
            ->make(true);

    }

            public function updateKeterangan(Request $request)
        {
            $request->validate([
                'id' => 'required|exists:presensi,id_presensi',
                'keterangan' => 'required|in:Hadir,Izin,Sakit,Alpha',
            ]);

            $presensi = Presensi::where('id_presensi', $request->id)->first();
            $presensi->keterangan = $request->keterangan;
            $presensi->save();

            return response()->json(['success' => true, 'message' => 'Keterangan berhasil diupdate']);
        }
}