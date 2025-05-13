@extends('layouts.main')
@section('title')
    Daftar Penilaian PKL
@endsection
@section('pagetitle')
<div class="pagetitle">
    <h1>Daftar Penilaian PKL</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">PKL</li>
            <li class="breadcrumb-item active">Penilaian</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Daftar Siswa PKL Tahun Akademik {{ $thnAkademik->tahun_akademik ?? 'N/A' }}</h5>
            
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="jurusan_filter">Jurusan</label>
                    <select id="jurusan_filter" class="form-select">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->jurusan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="penilaianTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th>Guru Pembimbing</th>
                        <th>Status</th>
                        <th>Nilai Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets') }}/vendor/dataTables/dataTables.js"></script>
<script src="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/select2/js/select2.min.js"></script>
<script>
    $(function() {
        var table = $('#penilaianTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('penilaian.data') }}",
                data: function(d) {
                    d.jurusan_id = $('#jurusan_filter').val();
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'nis', name: 'nis' },
                { data: 'nama', name: 'nama' },
                { data: 'jurusan.singkatan', name: 'jurusan.singkatan', defaultContent: 'N/A' },
                { data: 'nama_guru', name: 'nama_guru', defaultContent: 'N/A' },
                { data: 'status_penilaian', name: 'status_penilaian', orderable: false, searchable: false },
                { data: 'nilai_akhir', name: 'nilai_akhir', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[2, 'asc']]
        });

        // Filter berdasarkan jurusan
        $('#jurusan_filter').change(function() {
            table.draw();
        });
    });
</script>
@endsection