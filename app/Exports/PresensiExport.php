<?php

namespace App\Exports;

use App\Models\Penempatan;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\ThnAkademik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PresensiExport implements FromCollection, WithHeadings
{
    protected $nis;
    protected $stt;
    protected $id_ta;
    protected $id_penempatan;

    public function __construct($nis, $id_penempatan, $stt=null,$id_ta=null)
    {
        $this->nis = $nis;
        $this->stt = $stt;
        $this->id_ta = $id_ta;
        $this->id_penempatan = $id_penempatan;
    }

    /**
     * Mengambil data presensi berdasarkan NIS tertentu.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $ta = ThnAkademik::find($this->id_ta);
        // Ambil data presensi berdasarkan NIS jika ada
        $query = Presensi::active()->with([
            'siswa',  // Relasi Siswa
            'instruktur', // Relasi Instruktur
        ])->whereBetween('tanggal', [$ta->mulai, $ta->selesai]);

        // Jika NIS diberikan, filter berdasarkan NIS
        if (!empty($this->id_penempatan)) {
            $query->where('id_penempatan', $this->id_penempatan);
        }

        // Ambil data presensi
        $data = $query->get()->map(function ($presensi) {
            if ($this->stt == 'presensi') {
                return [
                    'tanggal' => $presensi->tanggal,
                    'masuk' => $presensi->masuk,
                    'foto_masuk' => $presensi->foto_masuk ? asset('storage/uploads/foto/' . $presensi->foto_masuk):'',
                    'pulang' => $presensi->pulang,
                    'foto_pulang' => $presensi->foto_pulang? asset('storage/uploads/foto/' . $presensi->foto_pulang):'',
                    'nis' => $presensi->nis,
                    'nama_siswa' => $presensi->siswa->nama,
                    'id_instruktur' => $presensi->id_instruktur,
                    'nama_instruktur' => $presensi->instruktur->nama,
                ];
            } else if ($this->stt == 'kegiatan') {
                return [
                    'tanggal' => $presensi->tanggal,
                    'kegiatan' => $presensi->kegiatan,
                    'is_acc_instruktur' => $presensi->is_acc_instruktur == 1 ? 'Ya' : ($presensi->is_acc_instruktur == 0 ? 'Tidak' : ''),
                    'is_acc_guru' => $presensi->is_acc_guru == 1 ? 'Ya' : ($presensi->is_acc_guru == 0 ? 'Tidak' : ''),
                    'nis' => $presensi->nis,
                    'nama_siswa' => $presensi->siswa->nama,
                    'id_instruktur' => $presensi->id_instruktur,
                    'nama_instruktur' => $presensi->instruktur->nama,

                ];
            } else if ($this->stt == 'catatan') {
                return [
                    'tanggal' => $presensi->tanggal,
                    'catatan' => $presensi->catatan,
                    'nis' => $presensi->nis,
                    'nama_siswa' => $presensi->siswa->nama,
                    'id_instruktur' => $presensi->id_instruktur,
                    'nama_instruktur' => $presensi->instruktur->nama,
                ];
            } else {
                return [
                    'tanggal' => $presensi->tanggal,
                    'masuk' => $presensi->masuk,
                    'foto_masuk' => $presensi->foto_masuk ? asset('storage/uploads/foto/' . $presensi->foto_masuk):'',
                    'pulang' => $presensi->pulang,
                    'foto_pulang' => $presensi->foto_pulang? asset('storage/uploads/foto/' . $presensi->foto_pulang):'',
                    'kegiatan' => $presensi->kegiatan,
                    'is_acc_instruktur' => $presensi->is_acc_instruktur == 1 ? 'Ya' : ($presensi->is_acc_instruktur == 0 ? 'Tidak' : ''),
                    'is_acc_guru' => $presensi->is_acc_guru == 1 ? 'Ya' : ($presensi->is_acc_guru == 0 ? 'Tidak' : ''),
                    'catatan' => $presensi->catatan,
                    'nis' => $presensi->nis,
                    'nama_siswa' => $presensi->siswa->nama,
                    'id_instruktur' => $presensi->id_instruktur,
                    'nama_instruktur' => $presensi->instruktur->nama,
                ];
            }
        });

        return $data;
    }

    /**
     * Menambahkan heading pada file Excel yang diexport.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->stt == 'presensi') {
            return [
                'Tanggal',
                'Masuk',
                'Foto Masuk',
                'Pulang',
                'Foto Pulang',
                'NIS',
                'Nama Siswa',
                'ID Instruktur',
                'Nama Instruktur',
            ];
        } else if ($this->stt == 'kegiatan') {
            return [
                'Tanggal',
                'Kegiatan',
                'Status Acc Instruktur',
                'Status Acc Guru',
                'NIS',
                'Nama Siswa',
                'ID Instruktur',
                'Nama Instruktur',
            ];
        } else if ($this->stt == 'catatan') {
            return [
                'Tanggal',
                'Catatan',
                'NIS',
                'Nama Siswa',
                'ID Instruktur',
                'Nama Instruktur',
            ];
        } else {
            return [
                'Tanggal',
                'Masuk',
                'Foto Masuk',
                'Pulang',
                'Foto Pulang',
                'Kegiatan',
                'Status Acc Instruktur',
                'Status Acc Guru',
                'Catatan Instruktur',
                'NIS',
                'Nama Siswa',
                'ID Instruktur',
                'Nama Instruktur',
            ];
        }
    }
}
