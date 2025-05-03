<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NilaiQuesionerExport;
use App\Http\Controllers\Controller;

use App\Models\NilaiQuesioner;
use App\Models\Quesioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class NilaiQuisionerController extends Controller
{
    public function index()
    {
        $thnAkademik = getActiveAcademicYear();
        $questions = Quesioner::where(['is_active' => true, 'id_ta' => $thnAkademik['id_ta']])->get();
        return view('pkl.nilai-quesioner.index', compact('questions'));
    }

    public function data(Request $request)
    {
        $data = NilaiQuesioner::with(['siswa', 'thnAkademik'])
            ->selectRaw('siswa.nis, siswa.nama as nama_siswa, thn_akademik.id_ta, thn_akademik.tahun_akademik, (AVG(nilai)*100) as rata_rata_nilai')
            ->join('siswa', 'nilai_quesioner.nis', '=', 'siswa.nis')
            ->join('quesioner', 'nilai_quesioner.id_quesioner', '=', 'quesioner.id_quesioner')
            ->join('thn_akademik', 'quesioner.id_ta', '=', 'thn_akademik.id_ta')
            ->where('nilai_quesioner.is_active', true)
            ->groupBy('nis', 'thn_akademik.id_ta', 'siswa.nama','thn_akademik.tahun_akademik');
        return DataTables::of($data)
            ->addColumn('nama_siswa', function ($dt) {
                return $dt->siswa->nis.'<br><a href="' . url("/d/siswa?nis=" . $dt->siswa->nis) . '">' . $dt->siswa->nama. '</a>';
            })
            ->addColumn('action', function ($dt) {
                return ' <button class="btn btn-sm btn-primary btn-edit"><i class="bi bi-pencil-fill"></i></button> | <button class="btn btn-sm btn-danger btn-delete"><i class="bi bi-trash-fill"></i></button>';
            })
            ->rawColumns(['action','nama_siswa'])
            ->make(true);
    }

    // Upsert (Insert or Update) a record
    // Function to handle upsert operation
    public function upsert(Request $request)
    {
        $isUpdate = $request->stt;

        // Validasi data
        $request->validate([
            'tanggal' => 'required|date',
            'nis' => 'required|exists:siswa,nis',
            'quesioner' => 'required|array',
            'quesioner.*' => 'required|in:0,1'  // Pastikan hanya menerima nilai Ya (1) atau Tidak (0)
        ]);

        // Lakukan penyimpanan untuk setiap quesioner
        foreach ($request->quesioner as $idQuesioner => $nilai) {
            $data = [
                'tanggal' => $request->tanggal,
                'nis' => $request->nis,
                'id_quesioner' => $idQuesioner,
                'nilai' => $nilai,
                'created_by' => Auth::id(),
            ];

            if ($isUpdate) {
                // Jika update, cari data yang ada dan update
                NilaiQuesioner::updateOrCreate(
                    ['id_nilai' => $request->id_nilai[$idQuesioner], 'id_quesioner' => $idQuesioner],
                    $data
                );
            } else {
                // Insert baru
                NilaiQuesioner::create($data);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    }

    public function edit(Request $request)
    {
        // Mencari data NilaiQuesioner berdasarkan nis dan id_ta
        $nilaiRecords = NilaiQuesioner::with(['siswa', 'quesioner'])
            ->where('nis', $request->nis)
            ->whereHas('quesioner', function ($query) use ($request) {
                $query->where('id_ta', $request->id_ta);
            })
            ->where('nilai_quesioner.is_active', true)
            ->get();
            // dd($nilaiRecords);

        // Cek jika data tidak ditemukan
        if ($nilaiRecords->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        // Mengambil semua quesioner terkait dan nilainya dari NilaiQuesioner
        $quesionerData = $nilaiRecords->map(function ($nilai) {
            return [
                'id_nilai' => $nilai->id_nilai,
                'id_quesioner' => $nilai->quesioner->id_quesioner,
                'soal' => $nilai->quesioner->soal,
                'nilai' => $nilai->nilai,  // Mengambil nilai langsung dari tabel NilaiQuesioner
            ];
        });

        // Mengambil data siswa dan tanggal dari satu record (karena diasumsikan sama untuk semua quesioner terkait)
        $firstRecord = $nilaiRecords->first();

        // Mengembalikan response dalam format JSON
        return response()->json([
            'status' => 'success',
            'data' => [
                'tanggal' => $firstRecord->tanggal,
                'nis' => $firstRecord->nis,
                'nama_siswa' => $firstRecord->siswa->nama,
                'quesioner' => $quesionerData,
            ]
        ]);
    }

    // Delete a record
    public function destroy(Request $request)
    {
        // Mengupdate is_active menjadi false untuk semua record yang ditemukan
        NilaiQuesioner::where('nis', $request->nis)
            ->whereHas('quesioner', function ($query) use ($request) {
                $query->where('id_ta', $request->id_ta);
            })
            ->update(['nilai_quesioner.is_active' => false]);

        return response()->json([
            'status' => true,
            'message' => 'NilaiQuesioner berhasil dihapus.'
        ]);
    }

    public function downloadExcel(Request $request)
    {
        // Menggunakan export class untuk mendownload Excel
        return Excel::download(new NilaiQuesionerExport($request->id), 'data-nilai-quesioner-'.$request->id.' '.date('Y-m-d').'.xlsx');
    }
}
