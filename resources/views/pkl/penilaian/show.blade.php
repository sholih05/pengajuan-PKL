@extends('layouts.main')
@section('title')
    Detail Penilaian PKL
@endsection
@section('pagetitle')
<div class="pagetitle">
    <h1>Detail Penilaian PKL</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">PKL</li>
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active">Detail Penilaian</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Data Siswa</h5>
            <div>
                <a href="{{ route('penilaian.edit', $siswa->id_siswa) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Penilaian
                </a>
                <a href="{{ route('penilaian.print', $siswa->id_siswa) }}" class="btn btn-primary" target="_blank">
                    <i class="bi bi-printer"></i> Cetak Penilaian
                </a>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">NIS</th>
                        <td>{{ $siswa->nis }}</td>
                    </tr>
                    <tr>
                        <th>Nama Siswa</th>
                        <td>{{ $siswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Jurusan</th>
                        <td>{{ $siswa->jurusan->nama_jurusan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Guru Pembimbing</th>
                        <td>{{ $siswa->guru->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Tahun Akademik</th>
                        <td>{{ $siswa->tahunAkademik->tahun_akademik ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <h5 class="card-title">Hasil Penilaian</h5>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <h4 class="alert-heading">Nilai Akhir: {{ number_format($nilaiAkhir, 2) }}</h4>
                    <p>Keterangan: <strong>{{ getNilaiKeterangan($nilaiAkhir) }}</strong></p>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="65%">Indikator Penilaian</th>
                        <th width="15%">Nilai</th>
                        <th width="15%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($indikatorUtama as $indikator)
                        <tr class="table-primary">
                            <td>{{ $no++ }}</td>
                            <td><strong>{{ $indikator->indikator }}</strong></td>
                            <td>
                                @php
                                    $nilai = $penilaian->where('id_prg_obsvr', $indikator->id)->first();
                                @endphp
                                
                                @if($indikator->is_nilai == '1' && $nilai)
                                    {{ $nilai->nilai }}
                                @else
                                    <span class="badge bg-secondary">Tidak Dinilai</span>
                                @endif
                            </td>
                            <td>
                                @if($indikator->is_nilai == '1' && $nilai)
                                    {{ getNilaiKeterangan($nilai->nilai) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        
                        @if($indikator->children->isNotEmpty())
                            @foreach($indikator->children as $child)
                                <tr>
                                    <td></td>
                                    <td class="ps-4">{{ $child->indikator }}</td>
                                    <td>
                                        @php
                                            $nilai = $penilaian->where('id_prg_obsvr', $child->id)->first();
                                        @endphp
                                        
                                        @if($child->is_nilai == '1' && $nilai)
                                            {{ $nilai->nilai }}
                                        @else
                                            <span class="badge bg-secondary">Tidak Dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($child->is_nilai == '1' && $nilai)
                                            {{ getNilaiKeterangan($nilai->nilai) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Catatan Penilaian</h5>
                        <p>{{ $catatanText ?: 'Tidak ada catatan' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Penilaian</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Dinilai Oleh</th>
                                <td>{{ $penilaian->first()->creator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Penilaian</th>
                                <td>{{ $penilaian->first() ? date('d-m-Y H:i', strtotime($penilaian->first()->created_at)) : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getNilaiKeterangan($nilai) {
    if ($nilai >= 90) {
        return 'Sangat Baik';
    } elseif ($nilai >= 80) {
        return 'Baik';
    } elseif ($nilai >= 70) {
        return 'Cukup';
    } elseif ($nilai >= 60) {
        return 'Kurang';
    } else {
        return 'Sangat Kurang';
    }
}
@endphp