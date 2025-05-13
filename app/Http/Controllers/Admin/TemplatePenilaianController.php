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
     *
     * @return \Illuminate\View\View
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
     *
     * @return array
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            
            // Simpan indikator utama dan sub-indikator
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
            
            $template = TemplatePenilaian::with(['mainItems.children'])
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Validation\Validator
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
     * Validasi request update template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Validation\Validator
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
            'main_indicators.*.sub_indicators.*.text' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Buat template baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\TemplatePenilaian
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \App\Models\TemplatePenilaian
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

    /**
     * Simpan indikator utama dan sub-indikator
     *
     * @param  array  $indicators
     * @param  int  $templateId
     * @return void
     */
    private function saveIndicators($indicators, $templateId)
    {
        foreach ($indicators as $key => $main) {
            // Simpan indikator utama
            $mainItem = new TemplatePenilaianItem();
            $mainItem->template_id = $templateId;
            $mainItem->indikator = $main['text'];
            $mainItem->is_main = true;
            $mainItem->urutan = $key + 1; // Gunakan key sebagai urutan (nilai kecil)
            $mainItem->is_nilai = true;
            $mainItem->save();
            
            // Simpan sub-indikator jika ada
            if (isset($main['sub_indicators']) && is_array($main['sub_indicators'])) {
                foreach ($main['sub_indicators'] as $subKey => $sub) {
                    if (!empty($sub['text'])) {
                        $subItem = new TemplatePenilaianItem();
                        $subItem->template_id = $templateId;
                        $subItem->indikator = $sub['text'];
                        $subItem->is_main = false;
                        $subItem->parent_id = $mainItem->id;
                        $subItem->urutan = $subKey + 1; // Gunakan subKey sebagai urutan (nilai kecil)
                        $subItem->is_nilai = true;
                        $subItem->save();
                    }
                }
            }
        }
    }

    /**
     * Update indikator utama dan sub-indikator
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TemplatePenilaian  $template
     * @return void
     */
    private function updateIndicators(Request $request, TemplatePenilaian $template)
    {
        // Hapus indikator yang tidak ada dalam request
        $keepMainIds = [];
        $keepSubIds = [];
        
        foreach ($request->main_indicators as $main) {
            if (isset($main['id'])) {
                $keepMainIds[] = $main['id'];
            }
            
            if (isset($main['sub_indicators'])) {
                foreach ($main['sub_indicators'] as $sub) {
                    if (isset($sub['id'])) {
                        $keepSubIds[] = $sub['id'];
                    }
                }
            }
        }
        
        // Hapus sub-indikator yang tidak ada dalam request
        TemplatePenilaianItem::where('template_id', $template->id)
            ->where('is_main', false)
            ->whereNotIn('id', $keepSubIds)
            ->delete();
            
        // Hapus indikator utama yang tidak ada dalam request
        TemplatePenilaianItem::where('template_id', $template->id)
            ->where('is_main', true)
            ->whereNotIn('id', $keepMainIds)
            ->delete();
        
        // Update atau buat indikator utama dan sub-indikator
        foreach ($request->main_indicators as $key => $main) {
            // Update atau buat indikator utama
            if (isset($main['id'])) {
                $mainItem = TemplatePenilaianItem::find($main['id']);
                if ($mainItem) {
                    $mainItem->indikator = $main['text'];
                    $mainItem->urutan = $key + 1;
                    $mainItem->save();
                } else {
                    // Jika ID tidak valid, buat baru
                    $mainItem = $this->createMainIndicator($template->id, $main['text'], $key);
                }
            } else {
                $mainItem = $this->createMainIndicator($template->id, $main['text'], $key);
            }
            
            // Update atau buat sub-indikator
            if (isset($main['sub_indicators']) && is_array($main['sub_indicators'])) {
                foreach ($main['sub_indicators'] as $subKey => $sub) {
                    if (isset($sub['id'])) {
                        $subItem = TemplatePenilaianItem::find($sub['id']);
                        if ($subItem) {
                            $subItem->indikator = $sub['text'];
                            $subItem->parent_id = $mainItem->id;
                            $subItem->urutan = $subKey + 1;
                            $subItem->save();
                        } else if (!empty($sub['text'])) {
                            // Jika ID tidak valid, buat baru
                            $this->createSubIndicator($template->id, $mainItem->id, $sub['text'], $subKey);
                        }
                    } else if (!empty($sub['text'])) {
                        $this->createSubIndicator($template->id, $mainItem->id, $sub['text'], $subKey);
                    }
                }
            }
        }
    }

    /**
     * Buat indikator utama baru
     *
     * @param  int  $templateId
     * @param  string  $text
     * @param  int  $order
     * @return \App\Models\TemplatePenilaianItem
     */
    private function createMainIndicator($templateId, $text, $order)
    {
        $mainItem = new TemplatePenilaianItem();
        $mainItem->template_id = $templateId;
        $mainItem->indikator = $text;
        $mainItem->is_main = true;
        $mainItem->urutan = $order + 1;
        $mainItem->is_nilai = true;
        $mainItem->save();
        
        return $mainItem;
    }

    /**
     * Buat sub-indikator baru
     *
     * @param  int  $templateId
     * @param  int  $parentId
     * @param  string  $text
     * @param  int  $order
     * @return \App\Models\TemplatePenilaianItem
     */
    private function createSubIndicator($templateId, $parentId, $text, $order)
    {
        $subItem = new TemplatePenilaianItem();
        $subItem->template_id = $templateId;
        $subItem->indikator = $text;
        $subItem->is_main = false;
        $subItem->parent_id = $parentId;
        $subItem->urutan = $order + 1;
        $subItem->is_nilai = true;
        $subItem->save();
        
        return $subItem;
    }

    /**
     * Buat program observer dari template
     *
     * @param  \App\Models\TemplatePenilaian  $template
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function createProgramObserver(TemplatePenilaian $template, Request $request)
    {
        // Mapping ID template item ke ID prg_obsvr untuk referensi parent-child
        $idMapping = [];
        
        // Buat prg_obsvr dari indikator utama
        foreach ($template->mainItems as $main) {
            $prgObsvr = new PrgObsvr();
            $prgObsvr->indikator = $main->indikator;
            $prgObsvr->is_nilai = $main->is_nilai ? '1' : '0';
            $prgObsvr->id_ta = $request->id_ta;
            $prgObsvr->id_guru = $request->id_guru;
            $prgObsvr->id_jurusan = $template->jurusan_id;
            $prgObsvr->created_by = Auth::id();
            $prgObsvr->is_active = 1;
            $prgObsvr->save();
            
            // Simpan mapping ID
            $idMapping[$main->id] = $prgObsvr->id;
            
            // Buat prg_obsvr dari sub-indikator
            foreach ($main->children as $sub) {
                $subPrgObsvr = new PrgObsvr();
                $subPrgObsvr->indikator = $sub->indikator;
                $subPrgObsvr->is_nilai = $sub->is_nilai ? '1' : '0';
                $subPrgObsvr->id_ta = $request->id_ta;
                $subPrgObsvr->id_guru = $request->id_guru;
                $subPrgObsvr->id_jurusan = $template->jurusan_id;
                $subPrgObsvr->id1 = $prgObsvr->id; // Set parent
                $subPrgObsvr->created_by = Auth::id();
                $subPrgObsvr->is_active = 1;
                $subPrgObsvr->save();
            }
        }
    }

    /**
     * Ambil template dengan relasi
     *
     * @param  int  $id
     * @return \App\Models\TemplatePenilaian
     */
    private function getTemplateWithRelations($id)
    {
        return TemplatePenilaian::with(['jurusan', 'creator', 'mainItems.children'])
            ->findOrFail($id);
    }

    /**
     * Response JSON untuk error validasi
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return \Illuminate\Http\JsonResponse
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
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonError($message)
    {
        return response()->json([
            'status' => false,
            'message' => self::MESSAGE_ERROR_GENERAL . $message
        ], 500);
    }
}