<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\PrgObsvr;
use App\Models\TemplatePenilaian;
use App\Models\TemplatePenilaianItem;
use App\Models\ThnAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TemplatePenilaianController extends Controller
{
    /**
     * Konstanta untuk pesan-pesan umum
     */
    const MESSAGE_SUCCESS_CREATE = 'Template penilaian berhasil dibuat!';
    const MESSAGE_SUCCESS_UPDATE = 'Template penilaian berhasil diperbarui!';
    const MESSAGE_SUCCESS_DELETE = 'Template penilaian berhasil dihapus!';
    const MESSAGE_SUCCESS_APPLY = 'Template penilaian berhasil diterapkan!';
    const MESSAGE_ERROR_GENERAL = 'Terjadi kesalahan: ';
    const MESSAGE_ERROR_VALIDATION = 'Validasi gagal';
    const MESSAGE_ERROR_LOAD_TEMPLATE = 'Gagal memuat data template.';
    const MESSAGE_ERROR_LOAD_GURU = 'Gagal memuat data guru.';

    /**
     * Tampilkan daftar template penilaian
     */
    public function index()
    {
        try {
            $data = $this->getIndexData();
            return view('pkl.template-penilaian.index', $data);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERAL . $e->getMessage());
        }
    }

    /**
     * Ambil data untuk halaman index
     */
    private function getIndexData()
    {
        $templates = TemplatePenilaian::with('jurusan')
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $jurusans = Jurusan::where('is_active', 1)->get();
        $thnAkademik = ThnAkademik::where('is_active', 1)->get();
        $gurus = Guru::where('is_active', 1)->get();
        $aktifAkademik = getActiveAcademicYear();
            
        return compact('templates', 'jurusans', 'thnAkademik', 'aktifAkademik', 'gurus');
    }
    
    /**
     * Simpan template baru
     */
    public function store(Request $request)
    {
        $validator = $this->validateTemplateRequest($request);
        
        if ($validator->fails()) {
            return $this->jsonValidationError($validator);
        }
        
        try {
            DB::beginTransaction();
            
            // Simpan template
            $template = $this->createTemplate($request);
            
            // Simpan indikator utama dan sub-indikator (3 level)
            $this->saveIndicators($request->main_indicators, $template->id);
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => self::MESSAGE_SUCCESS_CREATE,
                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in TemplatePenilaianController@store: ' . $e->getMessage());
            return $this->jsonError($e->getMessage());
        }
    }
    
    /**
     * Tampilkan detail template
     */
    public function show($id)
    {
        try {
            $template = $this->getTemplateWithRelations($id);
            return response()->json($template);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@show: ' . $e->getMessage());
            return $this->jsonError(self::MESSAGE_ERROR_LOAD_TEMPLATE);
        }
    }
    
    /**
     * Tampilkan form untuk edit template
     */
    public function edit($id)
    {
        try {
            $template = $this->getTemplateWithRelations($id);
            return response()->json($template);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@edit: ' . $e->getMessage());
            return $this->jsonError(self::MESSAGE_ERROR_LOAD_TEMPLATE);
        }
    }
    
    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateTemplateUpdateRequest($request);
        
        if ($validator->fails()) {
            return $this->jsonValidationError($validator);
        }
        
        try {
            DB::beginTransaction();
            
            // Update template
            $template = $this->updateTemplate($request, $id);
            
            // Hapus dan update indikator
            $this->updateIndicators($request, $template);
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => self::MESSAGE_SUCCESS_UPDATE,
                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in TemplatePenilaianController@update: ' . $e->getMessage());
            return $this->jsonError($e->getMessage());
        }
    }
    
    /**
     * Hapus template (soft delete)
     */
    public function destroy($id)
    {
        try {
            $template = TemplatePenilaian::findOrFail($id);
            $template->is_active = 0;
            $template->updated_by = Auth::id();
            $template->save();
            
            return response()->json([
                'status' => true,
                'message' => self::MESSAGE_SUCCESS_DELETE
            ]);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@destroy: ' . $e->getMessage());
            return $this->jsonError($e->getMessage());
        }
    }
    
    /**
     * Gunakan template untuk membuat prg_obsvr
     */
    public function applyTemplate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_ta' => 'required|exists:thn_akademik,id_ta',
            'id_guru' => 'required|exists:guru,id_guru',
        ]);
        
        if ($validator->fails()) {
            return $this->jsonValidationError($validator);
        }
        
        try {
            DB::beginTransaction();
            
            $template = TemplatePenilaian::with(['mainItems.children.children'])
                ->findOrFail($id);
            
            // Buat program observer dari template
            $this->createProgramObserver($template, $request);
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => self::MESSAGE_SUCCESS_APPLY
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in TemplatePenilaianController@applyTemplate: ' . $e->getMessage());
            return $this->jsonError($e->getMessage());
        }
    }

    /**
     * Mendapatkan data untuk DataTables
     */
    public function getData(Request $request)
    {
        try {
            $query = TemplatePenilaian::with(['jurusan', 'creator'])
                ->where('is_active', 1);
            
            // Filter berdasarkan jurusan jika ada
            if ($request->has('jurusan_id') && !empty($request->jurusan_id)) {
                $query->where('jurusan_id', $request->jurusan_id);
            }
            
            return DataTables::of($query)
                ->addColumn('action', function ($template) {
                    return ''; // Action buttons akan dirender di JavaScript
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@getData: ' . $e->getMessage());
            return $this->jsonError($e->getMessage());
        }
    }

    /**
     * Mendapatkan daftar guru berdasarkan jurusan template
     */
    public function getGuruByTemplate($id)
    {
        try {
            $template = TemplatePenilaian::findOrFail($id);
            $gurus = Guru::where('id_jurusan', $template->jurusan_id)
                ->where('is_active', 1)
                ->get();
            
            return response()->json($gurus);
        } catch (\Exception $e) {
            Log::error('Error in TemplatePenilaianController@getGuruByTemplate: ' . $e->getMessage());
            return $this->jsonError(self::MESSAGE_ERROR_LOAD_GURU);
        }
    }

    /**
     * Validasi request template
     */
    private function validateTemplateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'nama_template' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id_jurusan',
            'main_indicators' => 'required|array|min:1',
            'main_indicators.*.text' => 'required|string|max:255',
        ]);
    }


    /**
     * Buat template baru
     */
    private function createTemplate(Request $request)
    {
        $template = new TemplatePenilaian();
        $template->nama_template = $request->nama_template;
        $template->jurusan_id = $request->jurusan_id;
        $template->deskripsi = $request->deskripsi;
        $template->created_by = Auth::id();
        $template->is_active = 1;
        $template->save();
        
        return $template;
    }

    /**
     * Update template yang ada
     */
    private function updateTemplate(Request $request, $id)
    {
        $template = TemplatePenilaian::findOrFail($id);
        $template->nama_template = $request->nama_template;
        $template->jurusan_id = $request->jurusan_id;
        $template->deskripsi = $request->deskripsi;
        $template->updated_by = Auth::id();
        $template->save();
        
        return $template;
    }

    private function saveIndicators($indicators, $templateId)
    {
        foreach ($indicators as $key => $main) {
            // Simpan indikator utama (Level 1)
            $mainItem = new TemplatePenilaianItem();
            $mainItem->template_id = $templateId;
            $mainItem->indikator = $main['text'];
            $mainItem->is_main = true;
            $mainItem->level = TemplatePenilaianItem::LEVEL_MAIN;
            $mainItem->urutan = $key + 1;
            $mainItem->is_nilai = false; // Main indicator tidak dinilai langsung
            $mainItem->is_active = 1;
            $mainItem->save();
            
            // Simpan sub-indikator (Level 2) jika ada
            if (isset($main['sub_indicators']) && is_array($main['sub_indicators'])) {
                foreach ($main['sub_indicators'] as $subKey => $sub) {
                    if (!empty($sub['text'])) {
                        $subItem = new TemplatePenilaianItem();
                        $subItem->template_id = $templateId;
                        $subItem->indikator = $sub['text'];
                        $subItem->is_main = false;
                        $subItem->parent_id = $mainItem->id;
                        $subItem->level = TemplatePenilaianItem::LEVEL_SUB;
                        $subItem->urutan = $subKey + 1;
                        $subItem->is_active = 1;
                        
                        // Cek apakah ada level 3, jika tidak maka level 2 yang dinilai
                        $hasLevel3 = isset($sub['sub_sub_indicators']) && 
                                   is_array($sub['sub_sub_indicators']) && 
                                   !empty(array_filter($sub['sub_sub_indicators'], function($item) {
                                       return !empty($item['text']);
                                   }));
                        
                        $subItem->is_nilai = !$hasLevel3; // Dinilai jika tidak ada level 3
                        $subItem->save();
                        
                        // Simpan sub-sub-indikator (Level 3) jika ada
                        if ($hasLevel3) {
                            foreach ($sub['sub_sub_indicators'] as $subSubKey => $subSub) {
                                if (!empty($subSub['text'])) {
                                    $subSubItem = new TemplatePenilaianItem();
                                    $subSubItem->template_id = $templateId;
                                    $subSubItem->indikator = $subSub['text'];
                                    $subSubItem->is_main = false;
                                    $subSubItem->parent_id = $mainItem->id; // Tetap reference ke main
                                    $subSubItem->level3_parent_id = $subItem->id; // Reference ke level 2
                                    $subSubItem->level = TemplatePenilaianItem::LEVEL_SUB_SUB;
                                    $subSubItem->urutan = $subSubKey + 1;
                                    $subSubItem->is_nilai = true; // Level 3 yang dinilai
                                    $subSubItem->is_active = 1;
                                    $subSubItem->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Update indikator dengan struktur level baru
     */
    private function updateIndicators(Request $request, TemplatePenilaian $template)
    {
        try {
            // Collect IDs to keep dari request
            $keepMainIds = [];
            $keepSubIds = [];
            $keepSubSubIds = [];
            
            // Analisis struktur dari request untuk menentukan apa yang harus dipertahankan
            foreach ($request->main_indicators as $main) {
                if (isset($main['id']) && !empty($main['id'])) {
                    $keepMainIds[] = $main['id'];
                }
                
                if (isset($main['sub_indicators']) && is_array($main['sub_indicators'])) {
                    foreach ($main['sub_indicators'] as $sub) {
                        if (isset($sub['id']) && !empty($sub['id'])) {
                            $keepSubIds[] = $sub['id'];
                        }
                        
                        if (isset($sub['sub_sub_indicators']) && is_array($sub['sub_sub_indicators'])) {
                            foreach ($sub['sub_sub_indicators'] as $subSub) {
                                if (isset($subSub['id']) && !empty($subSub['id'])) {
                                    $keepSubSubIds[] = $subSub['id'];
                                }
                            }
                        }
                    }
                }
            }
            
            // Hapus items yang tidak ada dalam request (mulai dari level terdalam)
            // 1. Hapus level 3 yang tidak ada dalam request
            TemplatePenilaianItem::where('template_id', $template->id)
                ->where('level', TemplatePenilaianItem::LEVEL_SUB_SUB)
                ->when(!empty($keepSubSubIds), function($query) use ($keepSubSubIds) {
                    return $query->whereNotIn('id', $keepSubSubIds);
                })
                ->when(empty($keepSubSubIds), function($query) {
                    return $query; // Hapus semua level 3 jika tidak ada yang dipertahankan
                })
                ->delete();
            
            // 2. Hapus level 2 yang tidak ada dalam request
            TemplatePenilaianItem::where('template_id', $template->id)
                ->where('level', TemplatePenilaianItem::LEVEL_SUB)
                ->when(!empty($keepSubIds), function($query) use ($keepSubIds) {
                    return $query->whereNotIn('id', $keepSubIds);
                })
                ->when(empty($keepSubIds), function($query) {
                    return $query; // Hapus semua level 2 jika tidak ada yang dipertahankan
                })
                ->delete();
            
            // 3. Hapus level 1 yang tidak ada dalam request
            TemplatePenilaianItem::where('template_id', $template->id)
                ->where('level', TemplatePenilaianItem::LEVEL_MAIN)
                ->when(!empty($keepMainIds), function($query) use ($keepMainIds) {
                    return $query->whereNotIn('id', $keepMainIds);
                })
                ->when(empty($keepMainIds), function($query) {
                    return $query; // Hapus semua level 1 jika tidak ada yang dipertahankan
                })
                ->delete();
            
            // Process indicators dari request
            foreach ($request->main_indicators as $mainKey => $main) {
                // Process Main Indicator (Level 1)
                $mainItem = $this->processMainIndicator($template->id, $main, $mainKey);
                
                // Process Sub Indicators (Level 2)
                if (isset($main['sub_indicators']) && is_array($main['sub_indicators'])) {
                    foreach ($main['sub_indicators'] as $subKey => $sub) {
                        if (!empty($sub['text'])) {
                            $subItem = $this->processSubIndicator($template->id, $mainItem->id, $sub, $subKey, $main['sub_indicators']);
                            
                            // Process Sub-Sub Indicators (Level 3)
                            if (isset($sub['sub_sub_indicators']) && is_array($sub['sub_sub_indicators'])) {
                                $this->processSubSubIndicators($template->id, $mainItem->id, $subItem->id, $sub['sub_sub_indicators']);
                                
                                // Update sub indicator is_nilai berdasarkan keberadaan level 3
                                $hasValidLevel3 = $this->hasValidLevel3Items($sub['sub_sub_indicators']);
                                $subItem->is_nilai = !$hasValidLevel3;
                                $subItem->save();
                            } else {
                                // Tidak ada level 3, maka level 2 yang dinilai
                                $subItem->is_nilai = true;
                                $subItem->save();
                            }
                        }
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateIndicators: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processMainIndicator($templateId, $mainData, $order)
    {
        if (isset($mainData['id']) && !empty($mainData['id'])) {
            // Update existing main indicator
            $mainItem = TemplatePenilaianItem::find($mainData['id']);
            if ($mainItem) {
                $mainItem->indikator = $mainData['text'];
                $mainItem->urutan = $order + 1;
                $mainItem->save();
                return $mainItem;
            }
        }
        
        // Create new main indicator
        return $this->createMainIndicator($templateId, $mainData['text'], $order);
    }

    /**
     * Process sub indicator (Level 2)
     */
    private function processSubIndicator($templateId, $parentId, $subData, $order, $allSubIndicators)
    {
        if (isset($subData['id']) && !empty($subData['id'])) {
            // Update existing sub indicator
            $subItem = TemplatePenilaianItem::find($subData['id']);
            if ($subItem) {
                $subItem->indikator = $subData['text'];
                $subItem->parent_id = $parentId;
                $subItem->urutan = $order + 1;
                // is_nilai akan diupdate setelah memproses level 3
                $subItem->save();
                return $subItem;
            }
        }
        
        // Create new sub indicator
        $hasLevel3 = isset($subData['sub_sub_indicators']) && 
                    is_array($subData['sub_sub_indicators']) && 
                    $this->hasValidLevel3Items($subData['sub_sub_indicators']);
        
        return $this->createSubIndicator($templateId, $parentId, $subData['text'], $order, $subData);
    }

    /**
     * Process sub-sub indicators (Level 3)
     */
    private function processSubSubIndicators($templateId, $parentId, $level3ParentId, $subSubIndicators)
    {
        foreach ($subSubIndicators as $subSubKey => $subSub) {
            if (!empty($subSub['text'])) {
                if (isset($subSub['id']) && !empty($subSub['id'])) {
                    // Update existing sub-sub indicator
                    $subSubItem = TemplatePenilaianItem::find($subSub['id']);
                    if ($subSubItem) {
                        $subSubItem->indikator = $subSub['text'];
                        $subSubItem->parent_id = $parentId;
                        $subSubItem->level3_parent_id = $level3ParentId;
                        $subSubItem->urutan = $subSubKey + 1;
                        $subSubItem->save();
                    }
                } else {
                    // Create new sub-sub indicator
                    $this->createSubSubIndicator($templateId, $parentId, $level3ParentId, $subSub['text'], $subSubKey);
                }
            }
        }
    }

    /**
     * Check if sub_sub_indicators array has valid items
     */
    private function hasValidLevel3Items($subSubIndicators)
    {
        if (!is_array($subSubIndicators)) {
            return false;
        }
        
        foreach ($subSubIndicators as $subSub) {
            if (!empty($subSub['text'])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Create main indicator (updated)
     */
    private function createMainIndicator($templateId, $text, $order)
    {
        $mainItem = new TemplatePenilaianItem();
        $mainItem->template_id = $templateId;
        $mainItem->indikator = $text;
        $mainItem->is_main = true;
        $mainItem->level = TemplatePenilaianItem::LEVEL_MAIN;
        $mainItem->urutan = $order + 1;
        $mainItem->is_nilai = false; // Main indicator tidak pernah dinilai langsung
        $mainItem->is_active = 1;
        $mainItem->save();
        
        return $mainItem;
    }

    /**
     * Create sub indicator (updated)
     */
    private function createSubIndicator($templateId, $parentId, $text, $order, $subData = [])
    {
        // Check if has valid level 3
        $hasLevel3 = isset($subData['sub_sub_indicators']) && 
                    is_array($subData['sub_sub_indicators']) && 
                    $this->hasValidLevel3Items($subData['sub_sub_indicators']);

        $subItem = new TemplatePenilaianItem();
        $subItem->template_id = $templateId;
        $subItem->indikator = $text;
        $subItem->is_main = false;
        $subItem->parent_id = $parentId;
        $subItem->level = TemplatePenilaianItem::LEVEL_SUB;
        $subItem->urutan = $order + 1;
        $subItem->is_nilai = !$hasLevel3; // Dinilai jika tidak ada level 3 yang valid
        $subItem->is_active = 1;
        $subItem->save();
        
        return $subItem;
    }

    /**
     * Create sub-sub indicator (updated)
     */
    private function createSubSubIndicator($templateId, $parentId, $level3ParentId, $text, $order)
    {
        $subSubItem = new TemplatePenilaianItem();
        $subSubItem->template_id = $templateId;
        $subSubItem->indikator = $text;
        $subSubItem->is_main = false;
        $subSubItem->parent_id = $parentId;
        $subSubItem->level3_parent_id = $level3ParentId;
        $subSubItem->level = TemplatePenilaianItem::LEVEL_SUB_SUB;
        $subSubItem->urutan = $order + 1;
        $subSubItem->is_nilai = true; // Level 3 selalu dinilai
        $subSubItem->is_active = 1;
        $subSubItem->save();
        
        return $subSubItem;
    }

    /**
     * Get template with relations untuk edit (optimized)
     */
    private function getTemplateWithRelations($id)
    {
        return TemplatePenilaian::with([
            'jurusan', 
            'creator', 
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
        ])->findOrFail($id);
    }

    /**
     * Validasi request update template (updated)
     */
    private function validateTemplateUpdateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'nama_template' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id_jurusan',
            'main_indicators' => 'required|array|min:1',
            'main_indicators.*.id' => 'nullable|exists:template_penilaian_items,id',
            'main_indicators.*.text' => 'required|string|max:255',
            'main_indicators.*.sub_indicators' => 'nullable|array',
            'main_indicators.*.sub_indicators.*.id' => 'nullable|exists:template_penilaian_items,id',
            'main_indicators.*.sub_indicators.*.text' => 'required_with:main_indicators.*.sub_indicators.*|string|max:255',
            'main_indicators.*.sub_indicators.*.sub_sub_indicators' => 'nullable|array',
            'main_indicators.*.sub_indicators.*.sub_sub_indicators.*.id' => 'nullable|exists:template_penilaian_items,id',
            'main_indicators.*.sub_indicators.*.sub_sub_indicators.*.text' => 'required_with:main_indicators.*.sub_indicators.*.sub_sub_indicators.*|string|max:255',
        ], [
            'main_indicators.required' => 'Minimal harus ada satu indikator utama.',
            'main_indicators.*.text.required' => 'Teks indikator utama wajib diisi.',
            'main_indicators.*.sub_indicators.*.text.required_with' => 'Teks sub-indikator wajib diisi jika sub-indikator ditambahkan.',
            'main_indicators.*.sub_indicators.*.sub_sub_indicators.*.text.required_with' => 'Teks sub-sub-indikator wajib diisi jika sub-sub-indikator ditambahkan.',
        ]);
    }

    /**
     * Create program observer with new structure
     */
    private function createProgramObserver(TemplatePenilaian $template, Request $request)
    {
        foreach ($template->mainItems as $main) {
            // Create main indicator PrgObsvr
            $mainPrgObsvr = new PrgObsvr();
            $mainPrgObsvr->indikator = $main->indikator;
            $mainPrgObsvr->is_nilai = '0'; // Main tidak dinilai langsung
            $mainPrgObsvr->id_ta = $request->id_ta;
            $mainPrgObsvr->id_guru = $request->id_guru;
            $mainPrgObsvr->id_jurusan = $template->jurusan_id;
            $mainPrgObsvr->created_by = Auth::id();
            $mainPrgObsvr->is_active = 1;
            $mainPrgObsvr->save();
            
            // Create sub indicators
            foreach ($main->children as $sub) {
                $subPrgObsvr = new PrgObsvr();
                $subPrgObsvr->indikator = $sub->indikator;
                $subPrgObsvr->is_nilai = $sub->is_nilai ? '1' : '0';
                $subPrgObsvr->id_ta = $request->id_ta;
                $subPrgObsvr->id_guru = $request->id_guru;
                $subPrgObsvr->id_jurusan = $template->jurusan_id;
                $subPrgObsvr->id1 = $mainPrgObsvr->id;
                $subPrgObsvr->created_by = Auth::id();
                $subPrgObsvr->is_active = 1;
                $subPrgObsvr->save();
                
                // Create sub-sub indicators if exists
                foreach ($sub->level3Children as $subSub) {
                    $subSubPrgObsvr = new PrgObsvr();
                    $subSubPrgObsvr->indikator = $subSub->indikator;
                    $subSubPrgObsvr->is_nilai = '1'; // Level 3 selalu dinilai
                    $subSubPrgObsvr->id_ta = $request->id_ta;
                    $subSubPrgObsvr->id_guru = $request->id_guru;
                    $subSubPrgObsvr->id_jurusan = $template->jurusan_id;
                    $subSubPrgObsvr->id1 = $subPrgObsvr->id;
                    $subSubPrgObsvr->created_by = Auth::id();
                    $subSubPrgObsvr->is_active = 1;
                    $subSubPrgObsvr->save();
                }
            }
        }
    }

    /**
     * Response JSON untuk error validasi
     */
    private function jsonValidationError($validator)
    {
        return response()->json([
            'status' => false,
            'message' => self::MESSAGE_ERROR_VALIDATION,
            'errors' => $validator->errors()
        ], 422);
    }

    /**
     * Response JSON untuk error umum
     */
    private function jsonError($message)
    {
        return response()->json([
            'status' => false,
            'message' => self::MESSAGE_ERROR_GENERAL . $message
        ], 500);
    }
}