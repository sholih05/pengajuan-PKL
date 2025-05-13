@extends('layouts.main')
@section('title')
    Buat Penilaian PKL
@endsection
@section('pagetitle')
<div class="pagetitle">
    <h1>Buat Penilaian PKL</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">PKL</li>
            <li class="breadcrumb-item"><a href="{{ route('penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active">Buat Penilaian</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Data Siswa</h5>
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

        <form action="{{ route('penilaian.store') }}" method="POST" id="penilaianForm">
            @csrf
            <input type="hidden" name="id_siswa" value="{{ $siswa->id_siswa }}">
            
            <h5 class="card-title">Form Penilaian</h5>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Berikan nilai untuk setiap indikator penilaian dengan skala 0-100.
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="65%">Indikator Penilaian</th>
                            <th width="15%">Nilai (0-100)</th>
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
                                    @if($indikator->is_nilai == '1')
                                        <input type="number" class="form-control nilai-input" 
                                            name="nilai[{{ $indikator->id }}]" 
                                            min="0" max="100" required
                                            data-indikator="{{ $indikator->id }}">
                                    @else
                                        <span class="badge bg-secondary">Tidak Dinilai</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="keterangan" id="ket-{{ $indikator->id }}">-</span>
                                </td>
                            </tr>
                            
                            @if($indikator->children->isNotEmpty())
                                @foreach($indikator->children as $child)
                                    <tr>
                                        <td></td>
                                        <td class="ps-4">{{ $child->indikator }}</td>
                                        <td>
                                            @if($child->is_nilai == '1')
                                                <input type="number" class="form-control nilai-input" 
                                                    name="nilai[{{ $child->id }}]" 
                                                    min="0" max="100" required
                                                    data-indikator="{{ $child->id }}">
                                            @else
                                                <span class="badge bg-secondary">Tidak Dinilai</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="keterangan" id="ket-{{ $child->id }}">-</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <label for="catatan" class="form-label">Catatan Penilaian</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="4"></textarea>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 text-end">
                    <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/penilaian.js') }}"></script>
@endsection