<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PrgObsvr;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penempatan;
use App\Models\TemplatePenilaian;
use App\Models\TemplatePenilaianItem;
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

            // dd($siswa);
            
            // Ambil indikator penilaian dari template
            $indikatorUtama = TemplatePenilaian::whereHas('mainItems', function ($query) {
                $query->where('is_main', true);
            })
            ->where('jurusan_id', $siswa->id_jurusan)
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->get();

            // dd($indikatorUtama);
            
            if ($indikatorUtama->isEmpty()) {
                return redirect()->route('penilaian.index')
                    ->with('warning', 'Belum ada indikator penilaian untuk jurusan ini. Silakan buat indikator terlebih dahulu.');
            }
            
            return view('pkl.penilaian.create', compact('siswa', 'indikatorUtama'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@create: ' . $e->getMessage());
            dd($e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    public function store(Request $request)
{
    try {
        // Validate the request
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,nis',
            'catatan' => 'nullable|string',
            'nilai-sub' => 'required|array',
            'nilai-sub.*' => 'required|in:0,1',
        ]);

        // Get current user ID (assuming you have authentication)
        $userId = Auth::user()->id ?? 1; // Default to 1 if not authenticated
        
        // Get current active academic year
        $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
        if (!$tahunAkademik) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Tahun akademik aktif tidak ditemukan.');
        }
        
        // Get student data
        $siswa = Siswa::where('nis', $request->id_siswa)
            ->where('is_active', 1)
            ->first();
        
        if (!$siswa) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // Begin transaction
        DB::beginTransaction();
        
        // Create main penilaian record for overall assessment
        $mainPenilaian = new Penilaian();
        $mainPenilaian->nis = $request->id_siswa;
        $mainPenilaian->id_instruktur = Auth::user()->id_instruktur ?? null;
        $mainPenilaian->is_active = 1;
        $mainPenilaian->created_by = $userId;
        $mainPenilaian->created_at = now();
        $mainPenilaian->save();
        
        // Process sub-indicator values
        $subIndicatorValues = $request->input('nilai-sub', []);
        
        // Group main indicators and their calculated values
        $mainIndicatorValues = [];
        
        // First, process all sub-indicators
        foreach ($subIndicatorValues as $subIndicatorId => $value) {
            // Get the sub-indicator item
            $subIndicator = TemplatePenilaianItem::find($subIndicatorId);
            
            if (!$subIndicator) {
                continue;
            }
            
            // Get or create the corresponding PrgObsvr record
            $prgObsvr = PrgObsvr::firstOrNew([
                'indikator' => $subIndicator->indikator,
                'id_ta' => $tahunAkademik->id_ta,
                'id_jurusan' => $siswa->id_jurusan,
                'is_active' => 1
            ]);
            
            // Store the Yes/No value in is_nilai column
            $prgObsvr->is_nilai = (int)$value; // 1 for Yes, 0 for No
            
            if (!$prgObsvr->exists) {
                $prgObsvr->created_by = $userId;
                $prgObsvr->created_at = now();
            } else {
                $prgObsvr->updated_by = $userId;
                $prgObsvr->updated_at = now();
            }
            
            $prgObsvr->save();
            
            // Track which main indicator this belongs to
            $parentId = $subIndicator->parent_id;
            if (!isset($mainIndicatorValues[$parentId])) {
                $mainIndicatorValues[$parentId] = [
                    'total' => 0,
                    'count' => 0
                ];
            }
            
            $mainIndicatorValues[$parentId]['total'] += (int)$value;
            $mainIndicatorValues[$parentId]['count']++;
        }
        
        // Now process main indicators
        foreach ($mainIndicatorValues as $mainIndicatorId => $values) {
            // Calculate the value for the main indicator based on proportion of sub-indicators
            // Each sub-indicator contributes equally to the total 100 points
            $subIndicatorCount = $values['count'];
            $subIndicatorWeight = 100 / $subIndicatorCount; // Each sub-indicator's weight
            
            // Calculate the main indicator value (0-100)
            $mainValue = ($values['total'] * $subIndicatorWeight);
            
            // Get the main indicator
            $mainIndicator = TemplatePenilaianItem::find($mainIndicatorId);
            
            if (!$mainIndicator) {
                continue;
            }
            
            // Get or create the corresponding PrgObsvr record for the main indicator
            $mainPrgObsvr = PrgObsvr::firstOrNew([
                'indikator' => $mainIndicator->indikator,
                'id_ta' => $tahunAkademik->id_ta,
                'id_jurusan' => $siswa->id_jurusan,
                'is_active' => 1
            ]);
            
            if (!$mainPrgObsvr->exists) {
                $mainPrgObsvr->created_by = $userId;
                $mainPrgObsvr->created_at = now();
            } else {
                $mainPrgObsvr->updated_by = $userId;
                $mainPrgObsvr->updated_at = now();
            }
            
            $mainPrgObsvr->save();
            
            // Set parent-child relationship in PrgObsvr
            // Get all sub-indicators for this main indicator
            $subIndicators = TemplatePenilaianItem::where('parent_id', $mainIndicatorId)->get();
            foreach ($subIndicators as $subIndicator) {
                // Find the corresponding PrgObsvr record
                $subPrgObsvr = PrgObsvr::where('indikator', $subIndicator->indikator)
                    ->where('id_ta', $tahunAkademik->id_ta)
                    ->where('id_jurusan', $siswa->id_jurusan)
                    ->where('is_active', 1)
                    ->first();
                
                if ($subPrgObsvr) {
                    // Set the parent-child relationship
                    $subPrgObsvr->id1 = $mainPrgObsvr->id;
                    $subPrgObsvr->save();
                }
            }
            
            // Create a penilaian detail record for the main indicator only
            $mainPenilaianDetail = new Penilaian();
            $mainPenilaianDetail->nis = $request->id_siswa;
            $mainPenilaianDetail->id_prg_obsvr = $mainPrgObsvr->id; // Link to PrgObsvr using id_prg_obsvr
            $mainPenilaianDetail->id_instruktur = Auth::user()->id_instruktur ?? null;
            $mainPenilaianDetail->nilai_instruktur = $mainValue; // Store the calculated value (0-100)
            $mainPenilaianDetail->is_active = 1;
            $mainPenilaianDetail->created_by = $userId;
            $mainPenilaianDetail->created_at = now();
            $mainPenilaianDetail->save();
        }
        
        // Calculate and update the final score
        $nilaiAkhir = Penilaian::hitungNilaiAkhir($request->id_siswa);
        $mainPenilaian->nilai_instruktur = $nilaiAkhir;
        $mainPenilaian->waktu_instruktur = now(); // Record the time of assessment
        
        // Save catatan to the main penilaian record
        if ($request->has('catatan')) {
            // $mainPenilaian->catatan = $request->catatan;
        }
        
        $mainPenilaian->save();
        
        DB::commit();
        
        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in PenilaianController@store: ' . $e->getMessage());
        
        return redirect()->route('penilaian.index')
            ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
    }
}

    /**
 * Display the specified assessment.
 *
 * @param  string  $nis
 * @return \Illuminate\Http\Response
 */
public function show($nis)
{
    try {
        // Get student data
        $siswa = Siswa::with(['jurusan'])
            ->where('nis', $nis)
            ->where('is_active', 1)
            ->firstOrFail();
        
        // Get current active academic year
        $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
        if (!$tahunAkademik) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Tahun akademik aktif tidak ditemukan.');
        }
        
        // Get main penilaian record
        $mainPenilaian = Penilaian::where('nis', $nis)
            ->whereNull('id_prg_obsvr')  // Main record doesn't have id_prg_obsvr
            ->where('is_active', 1)
            ->first();
        
        if (!$mainPenilaian) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Data penilaian tidak ditemukan.');
        }
        
        // Get all penilaian details for main indicators
        $penilaianDetails = Penilaian::with(['prgObsvr'])
            ->where('nis', $nis)
            ->whereNotNull('id_prg_obsvr')
            ->where('is_active', 1)
            ->get();
        
        // Get all PrgObsvr records for this student's major
        $prgObsvrs = PrgObsvr::where('id_jurusan', $siswa->id_jurusan)
            ->where('id_ta', $tahunAkademik->id_ta)
            ->where('is_active', 1)
            ->get();
        
        // Organize indicators into a hierarchical structure
        $mainIndicators = $prgObsvrs->whereNull('id1');
        
        // For each main indicator, attach its children
        foreach ($mainIndicators as $mainIndicator) {
            $mainIndicator->children = $prgObsvrs->where('id1', $mainIndicator->id);
        }
        
        // Calculate final score
        $nilaiAkhir = $mainPenilaian->nilai_instruktur ?? 0;
        
        // Get catatan
        $catatanText = $mainPenilaian->catatan;
        
        return view('pkl.penilaian.show', compact(
            'siswa', 
            'mainIndicators', 
            'penilaianDetails', 
            'nilaiAkhir', 
            'catatanText'
        ));
        
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
            $siswa = Siswa::with(['jurusan'])
                ->where('nis', $id_siswa)
                ->where('is_active', 1)
                ->firstOrFail();
            
            // Ambil penilaian siswa
            $penilaian = Penilaian::with('prgObsvr')
                ->where('nis', $id_siswa)
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
            Penilaian::where('nis', $id_siswa)
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
                $indikatorPertama = PrgObsvr::where('nis', $id_siswa)
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
 * Generate a printable assessment report.
 *
 * @param  string  $nis
 * @return \Illuminate\Http\Response
 */
public function print($nis)
{
    try {
        // Get student data
        $siswa = Siswa::with(['jurusan'])
            ->where('nis', $nis)
            ->where('is_active', 1)
            ->firstOrFail();
        
        // Get current active academic year
        $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
        if (!$tahunAkademik) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Tahun akademik aktif tidak ditemukan.');
        }

        // Get Penempatan
        $penempatan = Penempatan::where('nis', $siswa->nis)
            ->first();
        
        // Get main penilaian record
        $mainPenilaian = Penilaian::where('nis', $nis)
            ->whereNull('id_prg_obsvr')  // Main record doesn't have id_prg_obsvr
            ->where('is_active', 1)
            ->first();
        
        if (!$mainPenilaian) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Data penilaian tidak ditemukan.');
        }
        
        // Get all penilaian details for main indicators
        $penilaianDetails = Penilaian::with(['prgObsvr'])
            ->where('nis', $nis)
            ->whereNotNull('id_prg_obsvr')
            ->where('is_active', 1)
            ->get();
        
        // Get all PrgObsvr records for this student's major
        $prgObsvrs = PrgObsvr::where('id_jurusan', $siswa->id_jurusan)
            ->where('id_ta', $tahunAkademik->id_ta)
            ->where('is_active', 1)
            ->get();
        
        // Get all template items for this student's major
        $templateItems = TemplatePenilaianItem::whereHas('template', function($query) use ($siswa) {
            $query->where('jurusan_id', $siswa->id_jurusan)
                  ->where('is_active', 1);
        })->get();
        
        // Organize indicators into a hierarchical structure
        $mainIndicators = $templateItems->where('is_main', true)->sortBy('urutan');
        
        // For each main indicator, attach its children
        foreach ($mainIndicators as $mainIndicator) {
            $mainIndicator->children = $templateItems->where('parent_id', $mainIndicator->id)->sortBy('urutan');
            
            // Get the corresponding PrgObsvr record for this main indicator
            $mainPrgObsvr = $prgObsvrs->where('indikator', $mainIndicator->indikator)->first();
            
            if ($mainPrgObsvr) {
                $mainIndicator->prgObsvr = $mainPrgObsvr;
                
                // Get the corresponding penilaian record
                $mainIndicator->nilai = $penilaianDetails->where('id_prg_obsvr', $mainPrgObsvr->id)->first();
                
                // For each child, get its PrgObsvr and penilaian
                foreach ($mainIndicator->children as $child) {
                    $childPrgObsvr = $prgObsvrs->where('indikator', $child->indikator)->first();
                    
                    if ($childPrgObsvr) {
                        $child->prgObsvr = $childPrgObsvr;
                    }
                }
            }
        }
        
        // Calculate final score
        $nilaiAkhir = $mainPenilaian->nilai_instruktur ?? 0;
        
        // Get catatan
        $catatanText = $mainPenilaian->catatan;
        
        return view('pkl.penilaian.print', compact(
            'siswa', 
            'mainIndicators', 
            'penilaianDetails', 
            'nilaiAkhir', 
            'catatanText',
            'tahunAkademik',
            'penempatan'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error in PenilaianController@print: ' . $e->getMessage());
        
        return redirect()->route('penilaian.index')
            ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
    }
}
}