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
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
@endif


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Data Siswa</h5>
        <div class="row mb-4">
            <div class="col-md-12">
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
                        <th width="30%">Jurusan</th>
                        <td>{{ $siswa->jurusan->jurusan ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
           
        </div>

        <form action="{{ route('penilaian.store') }}" method="POST" id="penilaianForm">
            @csrf
            <input type="hidden" name="id_siswa" value="{{ $siswa->nis }}">
            
            <h5 class="card-title">Form Penilaian</h5>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Berikan nilai untuk setiap indikator penilaian dengan Ya / Tidak.
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="65%">Indikator Penilaian</th>
                            <th width="15%">Ketercapaian (Ya/Tidak)</th>
                            <th width="15%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($indikatorUtama as $template)
                            {{-- <tr class="table-primary">
                                <td colspan="4"><strong>{{ $template->nama_template }}</strong></td>
                            </tr> --}}

                            @foreach ($template->mainItems as $item)
                                <tr class="table-secondary">
                                    <td>{{ $no++ }}</td>
                                    <td><strong>{{ $item->indikator }}</strong></td>
                                    <td>
                                        <input type="number"
                                            name="nilai[{{ $item->id }}]"
                                            class="form-control"
                                            readonly
                                            data-indikator="{{ $item->id }}">
                                    </td>
                                    <td id="ket-{{ $item->id }}">-</td>
                                </tr>

                                {{-- Subindikator jika ada --}}
                                @if ($item->children->isNotEmpty())
                                    @foreach ($item->children as $child)
                                        <tr>
                                            <td> </td>
                                            <td class="ps-4">{{ $child->indikator }}</td>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input nilai-radio"
                                                           type="radio"
                                                           name="nilai-sub[{{ $child->id }}]"
                                                           value="1"
                                                           data-parent="{{ $item->id }}">
                                                    <label class="form-check-label">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input nilai-radio"
                                                           type="radio"
                                                           name="nilai-sub[{{ $child->id }}]"
                                                           value="0"
                                                           data-parent="{{ $item->id }}">
                                                    <label class="form-check-label">Tidak</label>
                                                </div>
                                            </td>
                                            <td><span class="keterangan" id="ket-{{ $child->id }}">-</span></td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
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