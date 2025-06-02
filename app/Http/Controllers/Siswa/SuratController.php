<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PengajuanDetail;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index($id)
    {
        $surat = Pengajuan::findOrFail($id);
        $siswa = PengajuanDetail::where('id_surat', $surat->id)->get();

        $surat->tanggal_mulai = Carbon::parse($surat->tanggal_mulai)->locale('id_ID')->translatedFormat('d F Y');
        $surat->tanggal_selesai = Carbon::parse($surat->tanggal_selesai)->locale('id_ID')->translatedFormat('d F Y');

        $siswa->map(function ($item) {
            $item->nama = Siswa::where('nis', $item->nim)->first()->nama;
        });

        

        // Load the view and pass data to it
        $pdf = Pdf::loadView('pkl.pengajuan-surat.surat', compact('surat', 'siswa'));

        // Optionally set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Return the generated PDF to the browser
        return $pdf->stream();
    }
}
