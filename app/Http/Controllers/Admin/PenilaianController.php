<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PrgObsvr;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penempatan;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenilaianController extends Controller
{
    // Pesan-pesan untuk feedback
    const MESSAGE_SUCCESS_STORE = 'Data penilaian berhasil disimpan.';
    const MESSAGE_SUCCESS_UPDATE = 'Data penilaian berhasil diperbarui.';
    const MESSAGE_SUCCESS_DELETE = 'Data penilaian berhasil dihapus.';
    const MESSAGE_ERROR_GENERAL = 'Terjadi kesalahan: ';
    const MESSAGE_ERROR_NOT_FOUND = 'Data penilaian tidak ditemukan.';
    const MESSAGE_ERROR_VALIDATION = 'Validasi gagal. Periksa kembali data yang diinput.';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $thnAkademik = getActiveAcademicYear();
            $jurusans = Jurusan::where('is_active', 1)->get();
            // $kelas = Kelas::where('is_active', 1)->get();
            
            return view('pkl.penilaian.index', compact('jurusans', 'thnAkademik'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Get data for DataTables
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            $thnAkademik = getActiveAcademicYear();
            
            $query = Siswa::with(['jurusan'])
                ->where('is_active', 1);
            
            // Filter berdasarkan jurusan jika ada
            if ($request->has('jurusan_id') && !empty($request->jurusan_id)) {
                $query->where('id_jurusan', $request->jurusan_id);
            }
        
            
            return DataTables::of($query)
                ->addColumn('status_penilaian', function ($siswa) {
                    $penilaian = Penilaian::where('nis', $siswa->nis)
                        ->where('is_active', 1)
                        ->first();
                    
                    if ($penilaian) {
                        return '<span class="badge bg-success">Sudah Dinilai</span>';
                    } else {
                        return '<span class="badge bg-warning">Belum Dinilai</span>';
                    }
                })
                ->addColumn('nama_guru', function ($siswa) {
                    $penempatan = Penempatan::where('nis', $siswa->nis)->first();
                    return $penempatan ? $penempatan->guru->nama : '-';
                })
                ->addColumn('nilai_akhir', function ($siswa) {
                    $nilaiAkhir = Penilaian::hitungNilaiAkhir($siswa->nis);
                    
                    if ($nilaiAkhir > 0) {
                        return number_format($nilaiAkhir, 2);
                    } else {
                        return '-';
                    }
                })
                ->addColumn('action', function ($siswa) {
                    $penilaian = Penilaian::where('nis', $siswa->nis)
                        ->where('is_active', 1)
                        ->first();
                    
                    if ($penilaian) {
                        return '
                            <a href="' . route('penilaian.show', $siswa->nis) . '" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="' . route('penilaian.edit', $siswa->nis) . '" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="' . route('penilaian.print', $siswa->nis) . '" class="btn btn-secondary btn-sm" target="_blank">
                                <i class="bi bi-printer"></i> Cetak
                            </a>
                        ';
                    } else {
                        return '
                            <a href="' . route('penilaian.create', $siswa->nis) . '" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Nilai
                            </a>
                        ';
                    }
                })
                ->rawColumns(['status_penilaian', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@getData: ' . $e->getMessage());
            return response()->json(['error' => self::MESSAGE_ERROR_GENERAL . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $nis
     * @return \Illuminate\Http\Response
     */
    public function create($nis)
    {
        try {
            $siswa = Siswa::with(['jurusan'])
                ->where('nis', $nis)
                ->where('is_active', 1)
                ->firstOrFail();
            
            // Cek apakah siswa sudah dinilai
            $penilaianExists = Penilaian::where('nis', $nis)
                ->where('is_active', 1)
                ->exists();
            
            if ($penilaianExists) {
                return redirect()->route('penilaian.edit', $nis)
                    ->with('warning', 'Siswa ini sudah dinilai. Silakan edit penilaian yang ada.');
            }
            
            // Ambil indikator penilaian dari prg_obsvr
            $indikatorUtama = PrgObsvr::with('children')
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $siswa->id_ta)
                ->whereNull('id1')
                ->where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get();
            
            if ($indikatorUtama->isEmpty()) {
                return redirect()->route('penilaian.index')
                    ->with('warning', 'Belum ada indikator penilaian untuk jurusan ini. Silakan buat indikator terlebih dahulu.');
            }
            
            return view('pkl.penilaian.create', compact('siswa', 'indikatorUtama'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@create: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'id_siswa' => 'required|exists:siswa,id_siswa',
                'nilai.*' => 'required|numeric|min:0|max:100',
                'catatan' => 'nullable|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', self::MESSAGE_ERROR_VALIDATION);
            }
            
            $id_siswa = $request->id_siswa;
            $nilai = $request->nilai;
            $catatan = $request->catatan;
            $user_id = Auth::id();
            
            // Hapus penilaian yang sudah ada (jika ada)
            Penilaian::where('nis', $id_siswa)
                ->where('is_active', 1)
                ->update(['is_active' => 0, 'updated_by' => $user_id]);
            
            // Simpan penilaian baru
            $catatanSaved = false;
            
            foreach ($nilai as $id_prg_obsvr => $nilaiValue) {
                $penilaian = new Penilaian();
                $penilaian->nis = $id_siswa;
                $penilaian->id_prg_obsvr = $id_prg_obsvr;
                $penilaian->nilai = $nilaiValue;
                
                // Simpan catatan hanya pada satu record penilaian
                if (!$catatanSaved && !empty($catatan)) {
                    $penilaian->catatan = $catatan;
                    $catatanSaved = true;
                }
                
                $penilaian->created_by = $user_id;
                $penilaian->is_active = 1;
                $penilaian->save();
            }
            
            // Jika catatan belum disimpan (karena tidak ada nilai), simpan catatan pada record baru
            if (!$catatanSaved && !empty($catatan)) {
                $indikatorPertama = PrgObsvr::where('id_siswa', $id_siswa)
                    ->where('is_active', 1)
                    ->first();
                
                if ($indikatorPertama) {
                    $penilaian = new Penilaian();
                    $penilaian->id_siswa = $id_siswa;
                    $penilaian->id_prg_obsvr = $indikatorPertama->id;
                    $penilaian->nilai = 0;
                    $penilaian->catatan = $catatan;
                    $penilaian->created_by = $user_id;
                    $penilaian->is_active = 1;
                    $penilaian->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('penilaian.show', $id_siswa)
                ->with('success', self::MESSAGE_SUCCESS_STORE);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PenilaianController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id_siswa
     * @return \Illuminate\Http\Response
     */
    public function show($id_siswa)
    {
        try {
            $siswa = Siswa::with(['kelas', 'jurusan', 'guru'])
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->firstOrFail();
            
            // Ambil penilaian siswa
            $penilaian = Penilaian::with(['prgObsvr', 'creator'])
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->get();
            
            if ($penilaian->isEmpty()) {
                return redirect()->route('penilaian.index')
                    ->with('warning', 'Siswa ini belum dinilai. Silakan buat penilaian terlebih dahulu.');
            }
            
            // Ambil catatan penilaian
            $catatan = $penilaian->where('catatan', '!=', null)->first();
            $catatanText = $catatan ? $catatan->catatan : '';
            
            // Ambil indikator penilaian dari prg_obsvr
            $indikatorUtama = PrgObsvr::with('children')
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $siswa->id_ta)
                ->whereNull('id1')
                ->where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get();
            
            // Hitung nilai akhir
            $nilaiAkhir = Penilaian::hitungNilaiAkhir($id_siswa);
            
            return view('pkl.penilaian.show', compact('siswa', 'indikatorUtama', 'penilaian', 'catatanText', 'nilaiAkhir'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@show: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id_siswa
     * @return \Illuminate\Http\Response
     */
    public function edit($id_siswa)
    {
        try {
            $siswa = Siswa::with(['kelas', 'jurusan', 'guru'])
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->firstOrFail();
            
            // Ambil penilaian siswa
            $penilaian = Penilaian::with('prgObsvr')
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->get();
            
            if ($penilaian->isEmpty()) {
                return redirect()->route('penilaian.create', $id_siswa)
                    ->with('warning', 'Siswa ini belum dinilai. Silakan buat penilaian terlebih dahulu.');
            }
            
            // Ambil catatan penilaian
            $catatan = $penilaian->where('catatan', '!=', null)->first();
            $catatanText = $catatan ? $catatan->catatan : '';
            
            // Ambil indikator penilaian dari prg_obsvr
            $indikatorUtama = PrgObsvr::with('children')
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $siswa->id_ta)
                ->whereNull('id1')
                ->where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get();
            
            // Buat array nilai untuk form
            $nilaiArray = [];
            foreach ($penilaian as $p) {
                $nilaiArray[$p->id_prg_obsvr] = $p->nilai;
            }
            
            return view('pkl.penilaian.edit', compact('siswa', 'indikatorUtama', 'nilaiArray', 'catatanText'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@edit: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_siswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_siswa)
    {
        try {
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'nilai.*' => 'required|numeric|min:0|max:100',
                'catatan' => 'nullable|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', self::MESSAGE_ERROR_VALIDATION);
            }
            
            $nilai = $request->nilai;
            $catatan = $request->catatan;
            $user_id = Auth::id();
            
            // Hapus penilaian yang sudah ada
            Penilaian::where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->update(['is_active' => 0, 'updated_by' => $user_id]);
            
            // Simpan penilaian baru
            $catatanSaved = false;
            
            foreach ($nilai as $id_prg_obsvr => $nilaiValue) {
                $penilaian = new Penilaian();
                $penilaian->id_siswa = $id_siswa;
                $penilaian->id_prg_obsvr = $id_prg_obsvr;
                $penilaian->nilai = $nilaiValue;
                
                // Simpan catatan hanya pada satu record penilaian
                if (!$catatanSaved && !empty($catatan)) {
                    $penilaian->catatan = $catatan;
                    $catatanSaved = true;
                }
                
                $penilaian->created_by = $user_id;
                $penilaian->is_active = 1;
                $penilaian->save();
            }
            
            // Jika catatan belum disimpan (karena tidak ada nilai), simpan catatan pada record baru
            if (!$catatanSaved && !empty($catatan)) {
                $indikatorPertama = PrgObsvr::where('id_siswa', $id_siswa)
                    ->where('is_active', 1)
                    ->first();
                
                if ($indikatorPertama) {
                    $penilaian = new Penilaian();
                    $penilaian->id_siswa = $id_siswa;
                    $penilaian->id_prg_obsvr = $indikatorPertama->id;
                    $penilaian->nilai = 0;
                    $penilaian->catatan = $catatan;
                    $penilaian->created_by = $user_id;
                    $penilaian->is_active = 1;
                    $penilaian->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('penilaian.show', $id_siswa)
                ->with('success', self::MESSAGE_SUCCESS_UPDATE);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PenilaianController@update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id_siswa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_siswa)
    {
        try {
            DB::beginTransaction();
            
            $user_id = Auth::id();
            
            // Soft delete penilaian
            $updated = Penilaian::where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->update(['is_active' => 0, 'updated_by' => $user_id]);
            
            if (!$updated) {
                return redirect()->route('penilaian.index')
                    ->with('error', self::MESSAGE_ERROR_NOT_FOUND);
            }
            
            DB::commit();
            
            return redirect()->route('penilaian.index')
                ->with('success', self::MESSAGE_SUCCESS_DELETE);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PenilaianController@destroy: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Cetak lembar penilaian
     *
     * @param  int  $id_siswa
     * @return \Illuminate\Http\Response
     */
    public function print($id_siswa)
    {
        try {
            $siswa = Siswa::with(['kelas', 'jurusan', 'guru'])
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->firstOrFail();
            
            // Ambil penilaian siswa
            $penilaian = Penilaian::with(['prgObsvr', 'creator'])
                ->where('id_siswa', $id_siswa)
                ->where('is_active', 1)
                ->get();
            
            if ($penilaian->isEmpty()) {
                return redirect()->route('penilaian.index')
                    ->with('warning', 'Siswa ini belum dinilai. Silakan buat penilaian terlebih dahulu.');
            }
            
            // Ambil catatan penilaian
            $catatan = $penilaian->where('catatan', '!=', null)->first();
            $catatanText = $catatan ? $catatan->catatan : '';
            
            // Ambil indikator penilaian dari prg_obsvr
            $indikatorUtama = PrgObsvr::with('children')
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $siswa->id_ta)
                ->whereNull('id1')
                ->where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get();
            
            // Hitung nilai akhir
            $nilaiAkhir = Penilaian::hitungNilaiAkhir($id_siswa);
            
            return view('pkl.penilaian.print', compact('siswa', 'indikatorUtama', 'penilaian', 'catatanText', 'nilaiAkhir'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@print: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }
}