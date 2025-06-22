<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\PrgObsvr;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penempatan;
use App\Models\Presensi;
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

            // Ambil template penilaian untuk jurusan siswa
            $templates = TemplatePenilaian::with([
                'mainItems' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_MAIN)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                },
                'mainItems.children' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_SUB)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                },
                'mainItems.children.level3Children' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_SUB_SUB)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                }
            ])
            ->where('jurusan_id', $siswa->id_jurusan)
            ->where('is_active', 1)
            ->get();

            if ($templates->isEmpty()) {
                if (Auth::user()->role == 4) {
                    return redirect()->route('d.instruktur')
                        ->with('warning', 'Belum ada template penilaian untuk jurusan ini. Silakan buat template terlebih dahulu.');
                } else {
                    return redirect()->route('penilaian.index')
                        ->with('warning', 'Belum ada template penilaian untuk jurusan ini. Silakan buat template terlebih dahulu.');
                }
            }

            return view('pkl.penilaian.create', compact('siswa', 'templates'));
        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@create: ' . $e->getMessage());
            if (Auth::user()->role == 4) {
                return redirect()->route('d.instruktur')
                    ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
            } else {
                return redirect()->route('penilaian.index')
                    ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
            }
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'id_siswa' => 'required|exists:siswa,nis',
                'catatan' => 'nullable|string',
                'projectpkl' => 'required|string',
                'nilai-sub' => 'required|array',
                'nilai-sub.*' => 'required|in:0,1',
            ]);

            $userId = Auth::id() ?? 1;

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

            // Step 1: Process and save all assessment data to PrgObsvr
            $assessmentData = $this->processAssessmentData(
                $request->input('nilai-sub', []),
                $tahunAkademik->id_ta,
                $siswa->id_jurusan,
                $userId
            );

            Penempatan::where('nis', $request->id_siswa)->update([
                'projectpkl' => $request->projectpkl
            ]);

            // Step 2: Calculate and save main indicator scores to Penilaian
            $this->saveMainIndicatorAssessments(
                $request->id_siswa,
                $assessmentData['mainIndicators'],
                $request->catatan,
                $userId
            );

            DB::commit();

            if (Auth::user()->role == 4) {
                return redirect()->route('d.instruktur')
                    ->with('success', self::MESSAGE_SUCCESS_STORE);
            } else {
                return redirect()->route('penilaian.index')
                    ->with('success', self::MESSAGE_SUCCESS_STORE);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PenilaianController@store: ' . $e->getMessage());

            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Process all assessment data and save to PrgObsvr
     * Returns organized data for main indicator calculations
     */
    private function processAssessmentData($assessmentValues, $tahunAkademikId, $jurusanId, $userId)
    {
        $mainIndicatorData = [];
        $level2Calculations = [];
        $savedPrgObsvrs = [];  // Track saved PrgObsvr records

        // Get all template items for specific jurusan
        $allTemplateItems = TemplatePenilaianItem::with(['parent', 'level3Parent', 'level3Parent.parent'])
            ->whereHas('template', function($query) use ($jurusanId) {
                $query->where('jurusan_id', $jurusanId)
                      ->where('is_active', 1);
            })
            ->where('is_active', 1)
            ->orderBy('level')
            ->orderBy('urutan')
            ->get();

        Log::info("Retrieved Template Items for Jurusan", [
            'jurusan_id' => $jurusanId,
            'count' => $allTemplateItems->count(),
            'items' => $allTemplateItems->pluck('indikator')->toArray()
        ]);

        // Step 1: Save main indicators
        foreach ($allTemplateItems as $templateItem) {
            if ($templateItem->level !== PrgObsvr::LEVEL_MAIN) continue;

            $mainPrgObsvr = new PrgObsvr();
            $mainPrgObsvr->indikator = $templateItem->indikator;
            $mainPrgObsvr->id_ta = $tahunAkademikId;
            $mainPrgObsvr->id_jurusan = $jurusanId;
            $mainPrgObsvr->level = PrgObsvr::LEVEL_MAIN;
            $mainPrgObsvr->is_active = 1;
            $mainPrgObsvr->created_by = $userId;
            $mainPrgObsvr->created_at = now();
            $mainPrgObsvr->save();

            // Track the saved main indicator
            $savedPrgObsvrs['main'][$templateItem->id] = $mainPrgObsvr->id;

            Log::info("Saved Main Indicator", [
                'template_id' => $templateItem->id,
                'prg_obsvr_id' => $mainPrgObsvr->id,
                'indikator' => $mainPrgObsvr->indikator,
                'jurusan_id' => $jurusanId
            ]);
        }

        // Step 2: Save level 2 (sub) indicators
        foreach ($allTemplateItems as $templateItem) {
            if ($templateItem->level !== PrgObsvr::LEVEL_SUB) continue;

            $subPrgObsvr = new PrgObsvr();
            $subPrgObsvr->indikator = $templateItem->indikator;
            $subPrgObsvr->id_ta = $tahunAkademikId;
            $subPrgObsvr->id_jurusan = $jurusanId;
            $subPrgObsvr->level = PrgObsvr::LEVEL_SUB;
            $subPrgObsvr->is_active = 1;

            // Set nilai if exists in assessment values
            if (isset($assessmentValues[$templateItem->id])) {
                $subPrgObsvr->is_nilai = (int)$assessmentValues[$templateItem->id];
            }

            // Set id1 (main parent) if exists
            if ($templateItem->parent_id && isset($savedPrgObsvrs['main'][$templateItem->parent_id])) {
                $subPrgObsvr->id1 = $savedPrgObsvrs['main'][$templateItem->parent_id];

                Log::info("Setting Level 2 Parent", [
                    'template_id' => $templateItem->id,
                    'parent_template_id' => $templateItem->parent_id,
                    'id1' => $subPrgObsvr->id1
                ]);
            }

            $subPrgObsvr->created_by = $userId;
            $subPrgObsvr->created_at = now();
            $subPrgObsvr->save();

            // Track the saved sub indicator
            $savedPrgObsvrs['sub'][$templateItem->id] = $subPrgObsvr->id;

            // Track for main indicator calculations if has value
            if (isset($assessmentValues[$templateItem->id]) && $templateItem->parent_id) {
                if (!isset($mainIndicatorData[$templateItem->parent_id])) {
                    $mainIndicatorData[$templateItem->parent_id] = [
                        'level2_values' => [],
                        'template_id' => $templateItem->parent_id
                    ];
                }
                $mainIndicatorData[$templateItem->parent_id]['level2_values'][] = (int)$assessmentValues[$templateItem->id];
            }
        }

        // Step 3: Save level 3 (sub-sub) indicators
        foreach ($allTemplateItems as $templateItem) {
            if ($templateItem->level !== PrgObsvr::LEVEL_SUB_SUB) continue;

            $level3PrgObsvr = new PrgObsvr();
            $level3PrgObsvr->indikator = $templateItem->indikator;
            $level3PrgObsvr->id_ta = $tahunAkademikId;
            $level3PrgObsvr->id_jurusan = $jurusanId;
            $level3PrgObsvr->level = PrgObsvr::LEVEL_SUB_SUB;
            $level3PrgObsvr->is_active = 1;

            // Set nilai if exists in assessment values
            if (isset($assessmentValues[$templateItem->id])) {
                $level3PrgObsvr->is_nilai = (int)$assessmentValues[$templateItem->id];
            }

            // Set id2 (sub parent) and id1 (main parent) if exists
            if ($templateItem->level3_parent_id && isset($savedPrgObsvrs['sub'][$templateItem->level3_parent_id])) {
                $level3PrgObsvr->id2 = $savedPrgObsvrs['sub'][$templateItem->level3_parent_id];

                // Get the parent sub indicator to get its id1
                $parentSub = PrgObsvr::find($level3PrgObsvr->id2);
                if ($parentSub && $parentSub->id1) {
                    $level3PrgObsvr->id1 = $parentSub->id1;
                }

                Log::info("Setting Level 3 Parents", [
                    'template_id' => $templateItem->id,
                    'parent_template_id' => $templateItem->level3_parent_id,
                    'id2' => $level3PrgObsvr->id2,
                    'id1' => $level3PrgObsvr->id1
                ]);
            }

            $level3PrgObsvr->created_by = $userId;
            $level3PrgObsvr->created_at = now();
            $level3PrgObsvr->save();

            // Track for level 2 calculations if has value
            if (isset($assessmentValues[$templateItem->id]) && $templateItem->level3_parent_id) {
                $level2ParentId = $templateItem->level3_parent_id;
                $mainParentId = $templateItem->level3Parent ? $templateItem->level3Parent->parent_id : null;

                if (!isset($level2Calculations[$level2ParentId])) {
                    $level2Calculations[$level2ParentId] = [
                        'values' => [],
                        'main_parent_id' => $mainParentId,
                        'template_id' => $level2ParentId
                    ];
                }
                $level2Calculations[$level2ParentId]['values'][] = (int)$assessmentValues[$templateItem->id];
            }
        }

        // Calculate Level 2 indicators from Level 3
        $this->calculateLevel2Indicators(
            $level2Calculations,
            $tahunAkademikId,
            $jurusanId,
            $userId,
            $mainIndicatorData
        );

        // Calculate Main indicators from Level 2
        $this->calculateMainIndicators($mainIndicatorData, $tahunAkademikId, $jurusanId, $userId);

        return [
            'mainIndicators' => $mainIndicatorData,
            'level2Calculations' => $level2Calculations
        ];
    }

    /**
     * Calculate Level 2 indicators from Level 3 assessments
     */
    private function calculateLevel2Indicators($level2Calculations, $tahunAkademikId, $jurusanId, $userId, &$mainIndicatorData)
    {
        foreach ($level2Calculations as $level2TemplateId => $data) {
            $level2Template = TemplatePenilaianItem::find($level2TemplateId);
            if (!$level2Template) continue;

            // Level 2 value = 1 only if ALL Level 3 items are 1
            $level2Value = (array_sum($data['values']) == count($data['values'])) ? 1 : 0;

            // Update existing Level 2 PrgObsvr with calculated value
            $level2PrgObsvr = PrgObsvr::where('indikator', $level2Template->indikator)
                ->where('id_ta', $tahunAkademikId)
                ->where('id_jurusan', $jurusanId)
                ->where('level', PrgObsvr::LEVEL_SUB)
                ->where('is_active', 1)
                ->first();

            if ($level2PrgObsvr) {
                $level2PrgObsvr->is_nilai = $level2Value;
                $level2PrgObsvr->updated_by = $userId;
                $level2PrgObsvr->updated_at = now();
                $level2PrgObsvr->save();
            }

            // Add to main indicator calculation
            $mainParentId = $data['main_parent_id'];
            if (!isset($mainIndicatorData[$mainParentId])) {
                $mainIndicatorData[$mainParentId] = [
                    'level2_values' => [],
                    'template_id' => $mainParentId
                ];
            }
            $mainIndicatorData[$mainParentId]['level2_values'][] = $level2Value;
        }
    }

    /**
     * Calculate Main indicators from Level 2 values
     */
    private function calculateMainIndicators(&$mainIndicatorData, $tahunAkademikId, $jurusanId, $userId)
    {
        foreach ($mainIndicatorData as $mainTemplateId => &$data) {
            if (!empty($data['level2_values'])) {
                // Calculate percentage
                $percentage = (array_sum($data['level2_values']) / count($data['level2_values'])) * 100;
                $data['percentage'] = $percentage;

                // Save main indicator to PrgObsvr (without is_nilai, as it's calculated)
                $mainTemplate = TemplatePenilaianItem::find($mainTemplateId);
                if ($mainTemplate) {
                    $mainPrgObsvr = $this->saveCalculatedValueToPrgObsvr(
                        $mainTemplate,
                        $tahunAkademikId,
                        $jurusanId,
                        $userId,
                        null // Main indicators don't have is_nilai
                    );
                    $data['prg_obsvr_id'] = $mainPrgObsvr->id;
                }
            }
        }
    }

    /**
     * Save calculated value to PrgObsvr
     */
    private function saveCalculatedValueToPrgObsvr($templateItem, $tahunAkademikId, $jurusanId, $userId, $calculatedValue)
    {
        $prgObsvr = PrgObsvr::firstOrNew([
            'indikator' => $templateItem->indikator,
            'id_ta' => $tahunAkademikId,
            'id_jurusan' => $jurusanId,
            'level' => $templateItem->level,
            'is_active' => 1
        ]);

        // Set calculated value (null for main indicators)
        if ($calculatedValue !== null) {
            $prgObsvr->is_nilai = $calculatedValue;
        }

        // Set parent relationships based on level
        if ($templateItem->level == PrgObsvr::LEVEL_SUB) {
            // Find parent main indicator
            $mainPrgObsvr = PrgObsvr::where('indikator', $templateItem->mainParent->indikator)
                ->where('id_ta', $tahunAkademikId)
                ->where('id_jurusan', $jurusanId)
                ->where('level', PrgObsvr::LEVEL_MAIN)
                ->where('is_active', 1)
                ->first();

            if ($mainPrgObsvr) {
                $prgObsvr->id1 = $mainPrgObsvr->id;
            }
        } elseif ($templateItem->level == PrgObsvr::LEVEL_SUB_SUB) {
            // Find parent sub indicator
            $subPrgObsvr = PrgObsvr::where('indikator', $templateItem->subParent->indikator)
                ->where('id_ta', $tahunAkademikId)
                ->where('id_jurusan', $jurusanId)
                ->where('level', PrgObsvr::LEVEL_SUB)
                ->where('is_active', 1)
                ->first();

            if ($subPrgObsvr) {
                $prgObsvr->id2 = $subPrgObsvr->id;
                $prgObsvr->id1 = $subPrgObsvr->id1; // Inherit main parent
            }
        }

        if (!$prgObsvr->exists) {
            $prgObsvr->created_by = $userId;
            $prgObsvr->created_at = now();
        } else {
            $prgObsvr->updated_by = $userId;
            $prgObsvr->updated_at = now();
        }

        $prgObsvr->save();

        return $prgObsvr;
    }

    /**
     * Save main indicator assessments to Penilaian model
     */
    private function saveMainIndicatorAssessments($nis, $mainIndicatorData, $catatan, $userId)
    {
        $totalPercentages = [];

        // Save individual main indicator records
        foreach ($mainIndicatorData as $mainTemplateId => $data) {
            if (isset($data['percentage']) && isset($data['prg_obsvr_id'])) {
                $penilaian = new Penilaian();
                $penilaian->nis = $nis;
                $penilaian->id_prg_obsvr = $data['prg_obsvr_id'];
                $penilaian->id_instruktur = Auth::user()->id_instruktur ?? null;
                $penilaian->nilai_instruktur = $data['percentage'];
                $penilaian->waktu_instruktur = now();
                $penilaian->is_active = 1;
                $penilaian->created_by = $userId;
                $penilaian->save();

                $totalPercentages[] = $data['percentage'];
            }
        }

        // Create main summary record
        if (!empty($totalPercentages)) {
            $overallAverage = array_sum($totalPercentages) / count($totalPercentages);

            $mainPenilaian = new Penilaian();
            $mainPenilaian->nis = $nis;
            $mainPenilaian->id_instruktur = Auth::user()->id_instruktur ?? null;
            $mainPenilaian->nilai_instruktur = $overallAverage;
            $mainPenilaian->waktu_instruktur = now();
            $mainPenilaian->catatan = $catatan;
            $mainPenilaian->is_active = 1;
            $mainPenilaian->created_by = $userId;
            $mainPenilaian->save();
        }
    }

    /**
     * Calculate final score from main indicators only
     */
    public static function hitungNilaiAkhir($nis)
    {
        // Get the main summary record
        $mainRecord = Penilaian::where('nis', $nis)
            ->whereNull('id_prg_obsvr')
            ->where('is_active', 1)
            ->first();

        return $mainRecord ? $mainRecord->nilai_instruktur : 0;
    }

    /**
     * Create or update PrgObsvr record
     */
    private function createOrUpdatePrgObsvr($templateItem, $tahunAkademikId, $jurusanId, $userId, $level)
    {
        $prgObsvr = PrgObsvr::firstOrNew([
            'indikator' => $templateItem->indikator,
            'id_ta' => $tahunAkademikId,
            'id_jurusan' => $jurusanId,
            'level' => $level,
            'is_active' => 1
        ]);

        if (!$prgObsvr->exists) {
            $prgObsvr->created_by = $userId;
            $prgObsvr->created_at = now();
        } else {
            $prgObsvr->updated_by = $userId;
            $prgObsvr->updated_at = now();
        }

        $prgObsvr->save();

        return $prgObsvr;
    }

    /**
     * Create penilaian detail record
     */
    private function createPenilaianDetail($nis, $prgObsvrId, $nilai, $userId)
    {
        $penilaianDetail = new Penilaian();
        $penilaianDetail->nis = $nis;
        $penilaianDetail->id_prg_obsvr = $prgObsvrId;
        $penilaianDetail->id_instruktur = Auth::user()->id_instruktur ?? null;
        $penilaianDetail->nilai_instruktur = $nilai;
        $penilaianDetail->is_active = 1;
        $penilaianDetail->created_by = $userId;
        $penilaianDetail->created_at = now();
        $penilaianDetail->save();

        return $penilaianDetail;
    }

    /**
     * Calculate final score from all main indicators
     */
    private function calculateFinalScore($nis)
    {
        $mainIndicatorScores = Penilaian::join('prg_obsvr', 'penilaian.id_prg_obsvr', '=', 'prg_obsvr.id')
            ->where('penilaian.nis', $nis)
            ->where('prg_obsvr.level', PrgObsvr::LEVEL_MAIN)
            ->where('penilaian.is_active', 1)
            ->where('prg_obsvr.is_active', 1)
            ->avg('penilaian.nilai_instruktur');

        return $mainIndicatorScores ?? 0;
    }

    /**
     * Show assessment details
     */
    public function show($nis)
    {
        try {
            $siswa = Siswa::with(['jurusan'])
                ->where('nis', $nis)
                ->where('is_active', 1)
                ->firstOrFail();

            $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
            if (!$tahunAkademik) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Tahun akademik aktif tidak ditemukan.');
            }

            // Get main penilaian record (total score)
            $mainPenilaian = Penilaian::where('nis', $nis)
                ->whereNull('id_prg_obsvr')
                ->where('is_active', 1)
                ->first();

            if (!$mainPenilaian) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Data penilaian tidak ditemukan.');
            }

            $projectpkl = Penempatan::where('nis', $nis)->first();

            // Get main indicator assessments
            $mainIndicatorPenilaian = Penilaian::with(['prgObsvr'])
                ->where('nis', $nis)
                ->whereNotNull('id_prg_obsvr')
                ->where('is_active', 1)
                ->get();

            // Get all PrgObsvr records with hierarchical structure
            $mainIndicators = PrgObsvr::with(['children.level3Children'])
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $tahunAkademik->id_ta)
                ->where('level', PrgObsvr::LEVEL_MAIN)
                ->where('is_active', 1)
                ->orderBy('id')
                ->get();

            $penempatan = Penempatan::where('nis', $nis)
                ->where('is_active', 1)
                ->first();

            // Attach penilaian data to main indicators
            foreach ($mainIndicators as $main) {
                $main->penilaian = $mainIndicatorPenilaian->where('id_prg_obsvr', $main->id)->first();
            }

            $nilaiAkhir = $mainPenilaian->nilai_instruktur ?? 0;
            $catatanText = $mainPenilaian->catatan;

            return view('pkl.penilaian.show', compact(
                'siswa',
                'mainIndicators',
                'mainIndicatorPenilaian',
                'nilaiAkhir',
                'catatanText',
                'penempatan',
                'projectpkl'
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
     * @param  string  $nis
     * @return \Illuminate\Http\Response
     */
    public function edit($nis)
    {
        try {
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

            // Check if student has been assessed
            $mainPenilaian = Penilaian::where('nis', $nis)
                ->whereNull('id_prg_obsvr')
                ->where('is_active', 1)
                ->first();

            if (!$mainPenilaian) {
                return redirect()->route('penilaian.create', $nis)
                    ->with('warning', 'Siswa ini belum dinilai. Silakan buat penilaian terlebih dahulu.');
            }

            // Get assessment templates for student's major
            $templates = TemplatePenilaian::with([
                'mainItems' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_MAIN)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                },
                'mainItems.children' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_SUB)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                },
                'mainItems.children.level3Children' => function($query) {
                    $query->where('level', TemplatePenilaianItem::LEVEL_SUB_SUB)
                          ->where('is_active', 1)
                          ->orderBy('urutan');
                }
            ])
            ->where('jurusan_id', $siswa->id_jurusan)
            ->where('is_active', 1)
            ->get();

            if ($templates->isEmpty()) {
                if (Auth::user()->role == 4) {
                    return redirect()->route('d.instruktur')
                        ->with('warning', 'Template penilaian untuk jurusan ini tidak ditemukan.');
                } else {
                    return redirect()->route('penilaian.index')
                        ->with('warning', 'Template penilaian untuk jurusan ini tidak ditemukan.');
                }
            }

            // Get existing assessment values from PrgObsvr
            $existingValues = $this->getExistingAssessmentValues($siswa->id_jurusan, $tahunAkademik->id_ta);

            // Get catatan from main penilaian record
            $catatanText = $mainPenilaian->catatan ?? '';
            $projectpkl = Penempatan::where('nis', $nis)->first();

            return view('pkl.penilaian.edit', compact(
                'siswa',
                'templates',
                'existingValues',
                'catatanText',
                'projectpkl'
            ));

        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@edit: ' . $e->getMessage());
            return redirect()->route('penilaian.index')
                ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Get existing assessment values from PrgObsvr
     */
    private function getExistingAssessmentValues($jurusanId, $tahunAkademikId)
    {
        $prgObsvrs = PrgObsvr::where('id_jurusan', $jurusanId)
            ->where('id_ta', $tahunAkademikId)
            ->where('is_active', 1)
            ->whereNotNull('is_nilai') // Only get records that have assessment values
            ->get();

        $values = [];

        foreach ($prgObsvrs as $prgObsvr) {
            // Find corresponding template item
            $templateItem = TemplatePenilaianItem::where('indikator', $prgObsvr->indikator)
                ->where('level', $prgObsvr->level)
                ->where('is_active', 1)
                ->first();

            if ($templateItem) {
                $values[$templateItem->id] = $prgObsvr->is_nilai;
            }
        }

        return $values;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $nis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nis)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'catatan' => 'nullable|string|max:1000',
                'nilai-sub' => 'required|array',
                'nilai-sub.*' => 'required|in:0,1',
            ]);

            $userId = Auth::id() ?? 1;

            // Get current active academic year
            $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
            if (!$tahunAkademik) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Tahun akademik aktif tidak ditemukan.');
            }

            // Get student data
            $siswa = Siswa::where('nis', $nis)
                ->where('is_active', 1)
                ->first();

            if (!$siswa) {
                return redirect()->route('penilaian.index')
                    ->with('error', 'Data siswa tidak ditemukan.');
            }

            // Begin transaction
            DB::beginTransaction();

            // Step 1: Deactivate existing records
            $this->deactivateExistingRecords($nis, $siswa->id_jurusan, $tahunAkademik->id_ta, $userId);

            if ($request->projectpkl) {
                Penempatan::where('nis', $nis)->update(['projectpkl' => $request->projectpkl]);
            }
            // Step 2: Process and save updated assessment data
            $assessmentData = $this->processAssessmentData(
                $request->input('nilai-sub', []),
                $tahunAkademik->id_ta,
                $siswa->id_jurusan,
                $userId
            );

            // Step 3: Save updated main indicator assessments
            $this->updateMainIndicatorAssessments(
                $nis,
                $assessmentData['mainIndicators'],
                $request->catatan,
                $userId
            );

            DB::commit();

            return redirect()->route('penilaian.show', $nis)
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
     * Deactivate existing assessment records
     */
    private function deactivateExistingRecords($nis, $jurusanId, $tahunAkademikId, $userId)
    {
        // Deactivate existing Penilaian records
        Penilaian::where('nis', $nis)
            ->where('is_active', 1)
            ->update([
                'is_active' => 0,
                'updated_by' => $userId,
                'updated_at' => now()
            ]);

        // Deactivate existing PrgObsvr records for this student's major and academic year
        PrgObsvr::where('id_jurusan', $jurusanId)
            ->where('id_ta', $tahunAkademikId)
            ->where('is_active', 1)
            ->update([
                'is_active' => 0,
                'updated_by' => $userId,
                'updated_at' => now()
            ]);
    }

    /**
     * Update main indicator assessments in Penilaian model
     */
    private function updateMainIndicatorAssessments($nis, $mainIndicatorData, $catatan, $userId)
    {
        $totalPercentages = [];

        // Save individual main indicator records
        foreach ($mainIndicatorData as $mainTemplateId => $data) {
            if (isset($data['percentage']) && isset($data['prg_obsvr_id'])) {
                $penilaian = new Penilaian();
                $penilaian->nis = $nis;
                $penilaian->id_prg_obsvr = $data['prg_obsvr_id'];
                $penilaian->id_instruktur = Auth::user()->id_instruktur ?? null;
                $penilaian->nilai_instruktur = $data['percentage'];
                $penilaian->waktu_instruktur = now();
                $penilaian->is_active = 1;
                $penilaian->created_by = $userId;
                $penilaian->save();

                $totalPercentages[] = $data['percentage'];
            }
        }

        // Create updated main summary record
        if (!empty($totalPercentages)) {
            $overallAverage = array_sum($totalPercentages) / count($totalPercentages);

            $mainPenilaian = new Penilaian();
            $mainPenilaian->nis = $nis;
            $mainPenilaian->id_instruktur = Auth::user()->id_instruktur ?? null;
            $mainPenilaian->nilai_instruktur = $overallAverage;
            $mainPenilaian->waktu_instruktur = now();
            $mainPenilaian->catatan = $catatan;
            $mainPenilaian->is_active = 1;
            $mainPenilaian->created_by = $userId;
            $mainPenilaian->save();
        }
    }

    /**
     * Get assessment data for display in edit form
     */
    public function getEditData($nis)
    {
        try {
            $siswa = Siswa::with(['jurusan'])
                ->where('nis', $nis)
                ->where('is_active', 1)
                ->firstOrFail();

            $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
            if (!$tahunAkademik) {
                return response()->json(['error' => 'Tahun akademik aktif tidak ditemukan.'], 404);
            }

            // Get main indicators with their current scores
            $mainIndicators = PrgObsvr::with(['children.level3Children'])
                ->where('id_jurusan', $siswa->id_jurusan)
                ->where('id_ta', $tahunAkademik->id_ta)
                ->where('level', PrgObsvr::LEVEL_MAIN)
                ->where('is_active', 1)
                ->orderBy('id')
                ->get();

            // Get current penilaian records
            $penilaianRecords = Penilaian::where('nis', $nis)
                ->where('is_active', 1)
                ->get();

            // Attach penilaian data to indicators
            foreach ($mainIndicators as $main) {
                $main->penilaian = $penilaianRecords->where('id_prg_obsvr', $main->id)->first();

                // Get sub indicators with their values
                foreach ($main->children as $sub) {
                    $sub->current_value = $sub->is_nilai;

                    // Get level 3 children with their values
                    foreach ($sub->level3Children as $level3) {
                        $level3->current_value = $level3->is_nilai;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'siswa' => $siswa,
                    'mainIndicators' => $mainIndicators,
                    'catatan' => $penilaianRecords->whereNotNull('catatan')->first()->catatan ?? ''
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@getEditData: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data.'], 500);
        }
    }

    /**
     * Validate assessment data before update
     */
    private function validateUpdateData($request, $nis)
    {
        $rules = [
            'catatan' => 'nullable|string|max:1000',
            'nilai-sub' => 'required|array',
            'nilai-sub.*' => 'required|in:0,1',
        ];

        $messages = [
            'nilai-sub.required' => 'Data penilaian harus diisi.',
            'nilai-sub.array' => 'Format data penilaian tidak valid.',
            'nilai-sub.*.required' => 'Semua indikator harus dinilai.',
            'nilai-sub.*.in' => 'Nilai penilaian harus 0 atau 1.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // Additional validation: check if student exists and has existing assessment
        $siswa = Siswa::where('nis', $nis)->where('is_active', 1)->first();
        if (!$siswa) {
            throw new \Exception('Data siswa tidak ditemukan.');
        }

        $existingAssessment = Penilaian::where('nis', $nis)->where('is_active', 1)->exists();
        if (!$existingAssessment) {
            throw new \Exception('Data penilaian tidak ditemukan. Silakan buat penilaian terlebih dahulu.');
        }

        return $siswa;
    }

    /**
     * Get comparison data between old and new assessment
     */
    public function getAssessmentComparison($nis, $newAssessmentData)
    {
        try {
            $tahunAkademik = ThnAkademik::where('is_active', 1)->first();
            $siswa = Siswa::where('nis', $nis)->first();

            if (!$tahunAkademik || !$siswa) {
                return null;
            }

            // Get current assessment values
            $currentValues = $this->getExistingAssessmentValues($siswa->id_jurusan, $tahunAkademik->id_ta);

            // Compare with new values
            $changes = [];
            foreach ($newAssessmentData as $templateId => $newValue) {
                $oldValue = $currentValues[$templateId] ?? null;
                if ($oldValue !== null && $oldValue != $newValue) {
                    $templateItem = TemplatePenilaianItem::find($templateId);
                    if ($templateItem) {
                        $changes[] = [
                            'indikator' => $templateItem->indikator,
                            'level' => $templateItem->level,
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'change_type' => $newValue > $oldValue ? 'improvement' : 'decline'
                        ];
                    }
                }
            }

            return $changes;

        } catch (\Exception $e) {
            Log::error('Error in getAssessmentComparison: ' . $e->getMessage());
            return null;
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

            if (Auth::user()->role == 4) {
                return redirect()->route('d.instruktur')
                    ->with('success', self::MESSAGE_SUCCESS_DELETE);
            } else {
                return redirect()->route('penilaian.index')
                    ->with('success', self::MESSAGE_SUCCESS_DELETE);
            }
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

            $presensi = Presensi::where('id_penempatan', $penempatan->id_penempatan)
                ->selectRaw('keterangan, count(*) as jumlah')
                ->groupBy('keterangan')->get();
            
            $projectpkl = Penempatan::where('nis', $siswa->nis)
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
            $mainIndicatorPenilaian = Penilaian::with(['prgObsvr'])
                ->where('nis', $nis)
                ->whereNotNull('id_prg_obsvr')
                ->where('is_active', 1)
                ->get();

            // Get main indicators with proper hierarchy
            $mainIndicators = PrgObsvr::with([
                'children' => function($query) {
                    $query->where('level', PrgObsvr::LEVEL_SUB)
                          ->where('is_active', 1)
                          ->orderBy('id');
                },
                'children.level3Children' => function($query) {
                    $query->where('level', PrgObsvr::LEVEL_SUB_SUB)
                          ->where('is_active', 1)
                          ->orderBy('id');
                }
            ])
            ->where('id_jurusan', $siswa->id_jurusan)
            ->where('id_ta', $tahunAkademik->id_ta)
            ->where('level', PrgObsvr::LEVEL_MAIN)
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

            // Calculate final score
            $nilaiAkhir = $mainPenilaian->nilai_instruktur ?? 0;

            // Get catatan
            $catatanText = $mainPenilaian->catatan;

            return view('pkl.penilaian.print', compact(
                'siswa',
                'mainIndicators',
                'mainIndicatorPenilaian',
                'nilaiAkhir',
                'catatanText',
                'tahunAkademik',
                'penempatan',
                'presensi',
                'projectpkl'
            ));

        } catch (\Exception $e) {
            Log::error('Error in PenilaianController@print: ' . $e->getMessage());

            if (Auth::user()->role == 4) {
                return redirect()->route('d.instruktur')
                    ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
            } else {
                return redirect()->route('penilaian.index')
                    ->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
            }
        }
    }
}