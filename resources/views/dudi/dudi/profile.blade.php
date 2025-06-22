@extends('layouts.main')
@section('title')
    Dudi
@endsection
@section('pagetitle')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Dudi</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Dudi</li>
                    <li class="breadcrumb-item">{{$dudi->nama}}</li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </nav>
        </div>
        <div>
            <!-- Button Kembali di atas kanan -->
            <a href="{{ url()->previous() }}" class="btn btn-warning"> <i class="bi bi-chevron-left"></i> Kembali</a>
        </div>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="row">
        <!-- Profile Section -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="row mt-3">
                        <label class="col-sm-5 col-form-label">Tahun Akademik</label>
                        <div class="col-sm-7">
                            <select class="form-select" id="id_ta" name="id_ta_siswa">
                                @foreach ($thnAkademik as $item)
                                    <option value="{{ $item->id_ta }}"
                                        @if ($item->id_ta == $thnAkademikAktif->id_ta) @selected(true) @endif>
                                        {{ $item->tahun_akademik }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="card-title">Data Dudi</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Nama: </strong>{{ $dudi->nama }}</li>
                        <li class="list-group-item"><strong>No. Kontak: </strong>{{ $dudi->no_kontak }}</li>
                        <li class="list-group-item"><strong>Nama Pimpinan: </strong>{{ $dudi->nama_pimpinan }}</li>
                        <li class="list-group-item"><strong>Alamat: </strong>{{ $dudi->alamat }}</li>
                        <li class="list-group-item" style="height: 200px">
                            <div id="map" style="height: 200px"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#profile-data-ketersediaan">Ketersediaan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-data-siswa">Siswa
                                Magang</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-data-guru">Guru
                                Pembimbing</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#profile-data-instruktur">Instruktur</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-2">
                        <!-- Data Siswa Tab -->
                        <div class="tab-pane fade show active profile-data-ketersediaan" id="profile-data-ketersediaan">
                            @if (in_array(auth()->user()->role, [1, 2, 4]) || session('id_dudi') == $dudi->id_dudi)
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i>
                                    Tambah</button>
                            </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-ketersediaan" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jurusan</th>
                                            @if (in_array(auth()->user()->role, [1, 2, 4]) || session('id_dudi') == $dudi->id_dudi)
                                                <th>#</th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Siswa Tab -->
                        <div class="tab-pane fade" id="profile-data-siswa">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-siswa" width="100%">
                                    <thead>
                                        <tr>
                                            <th>NIS</th>
                                            <th>NISN</th>
                                            <th>Nama</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Guru Tab -->
                        <div class="tab-pane fade profile-data-guru" id="profile-data-guru">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-guru" width="100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Kontak</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Instruktur Tab -->
                        <div class="tab-pane fade profile-data-instruktur" id="profile-data-instruktur">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-instruktur" width="100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Kontak</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div><!-- End Bordered Tabs -->
                </div>
            </div>
        </div>
    </div>

    @if (in_array(auth()->user()->role, [1, 2, 4]) || session('id_dudi') == $dudi->id_dudi)
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="myForm" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" id="stt" name="stt" value="0">
                            <input type="hidden" id="id_ketersediaan" name="id_ketersediaan">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                <div class="invalid-feedback">Isian tidak boleh kosong.</div>
                            </div>

                            <div class="mb-3">
                                <label for="id_jurusan" class="form-label">Jurusan</label>
                                <select class="form-select" id="id_jurusan" name="id_jurusan" required>
                                    <option value="">Pilih</option>
                                    @foreach ($jurusan as $item)
                                        <option value="{{ $item->id_jurusan }}">{{ $item->jurusan }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"> Jurusan wajib dipilih. </div>
                            </div>

                            <input type="hidden" id="id_dudi" name="id_dudi" value="{{ $dudi->id_dudi }}">

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('js')
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.js"></script>
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/leaflet/leaflet.js"></script>
    <script>
        // Langkah 2: Inisialisasi Peta
        var map = L.map('map').setView([{{ $dudi->latitude }}, {{ $dudi->longitude }}],
            13); // Koordinat Jakarta sebagai contoh

        // Tambahkan Tile Layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Langkah 3: Tambahkan Pin (Marker) pada peta
        var marker = L.marker([{{ $dudi->latitude }}, {{ $dudi->longitude }}]).addTo(map) // Koordinat Jakarta
            .bindPopup('{{ $dudi->nama }}')
            .openPopup();
    </script>
    <script>
        $(function() {
            var table1 = $('#table-siswa').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.dudi.siswa.data') !!}', // Route untuk fetch data siswa
                    type: 'GET',
                    data: function(d) {
                        d.id = '{{ $dudi->id_dudi }}'; // Kirim ID Dudi
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'nis',
                        name: 'nis'
                    },
                    {
                        data: 'nisn',
                        name: 'nisn'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'nama'
                    }
                ]
            });
            var table2 = $('#table-guru').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.dudi.guru.data') !!}', // Route untuk fetch data siswa
                    type: 'GET',
                    data: function(d) {
                        d.id = '{{ $dudi->id_dudi }}'; // Kirim ID Dudi
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'id_guru',
                        name: 'id_guru'
                    },
                    {
                        data: 'nama_guru',
                        name: 'nama'
                    },
                    {
                        data: 'no_kontak',
                        name: 'no_kontak'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    }
                ]
            });
            var table3 = $('#table-instruktur').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.dudi.instruktur.data') !!}', // Route untuk fetch data siswa
                    type: 'GET',
                    data: function(d) {
                        d.id = '{{ $dudi->id_dudi }}'; // Kirim ID Dudi
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'id_instruktur',
                        name: 'id_instruktur'
                    },
                    {
                        data: 'nama_instruktur',
                        name: 'nama'
                    },
                    {
                        data: 'no_kontak',
                        name: 'no_kontak'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    }
                ]
            });
            var table4 = $('#table-ketersediaan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.dudi.ketersediaan.data') !!}', // Route untuk fetch data siswa
                    type: 'GET',
                    data: function(d) {
                        d.id = '{{ $dudi->id_dudi }}'; // Kirim ID Dudi
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jurusan',
                        name: 'jurusan'
                    },
                    @if (in_array(auth()->user()->role, [1, 2, 4]) || session('id_dudi') == $dudi->id_dudi)
                        {
                            data: 'action',
                            name: 'action'
                        },
                    @endif
                ]
            });

            $('#id_ta').on('change', function() {
                table1.ajax.reload();
                table2.ajax.reload();
                table3.ajax.reload();
                table4.ajax.reload();
            });
            @if (in_array(auth()->user()->role, [1, 2, 4]) || session('id_dudi') == $dudi->id_dudi)
                $('#btnAdd').click(function() {
                    $('#myForm')[0].reset();
                    $('#stt').val('0');
                    $('#modalTitle').text('Tambah Baru');
                    $('#myModal').modal('show');
                });

                // Event click untuk tombol edit
                $('#table-ketersediaan').on('click', '.btn-edit', function() {
                    var data = table4.row($(this).closest('tr')).data();
                    $('#stt').val('1');
                    $('#tanggal').val(data.tanggal);
                    $('#id_jurusan').val(data.id_jurusan);
                    $('#id_dudi').val(data.id_dudi);
                    $('#id_ketersediaan').val(data.id_ketersediaan);
                    $('#modalTitle').text('Ubah');
                    $('#myModal').modal('show');
                });

                // Event click untuk tombol hapus
                $('#table-ketersediaan').on('click', '.btn-delete', function() {
                    var data = table4.row($(this).closest('tr')).data();
                    var Id = data.id_ketersediaan;
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        text: "Anda tidak akan dapat mengembalikan ini!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('d/dudi/ketersediaan/delete') }}" + "/" + Id,
                                method: "GET",
                                success: function(response) {
                                    if (response.status) {
                                        table4.ajax.reload();
                                        Toast.fire({
                                            icon: "success",
                                            title: response.message
                                        });
                                    } else {
                                        Toast.fire({
                                            icon: "error",
                                            title: response.message
                                        });
                                    }
                                },
                                error: function(response) {
                                    // Handle error
                                    Toast.fire({
                                        icon: "error",
                                        title: 'Woops! Fatal Error.'
                                    });
                                }
                            });
                        }
                    });
                });

                $('#myForm').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this)[0]; // Get the form element
                    if (form.checkValidity() === false) {
                        e.stopPropagation();
                    } else {
                        var url = "{{ route('d.dudi.ketersediaan.upsert') }}";
                        var formData = $(this).serialize();
                        $.ajax({
                            url: url,
                            method: "POST",
                            data: formData,
                            success: function(response) {
                                $('#myModal').modal('hide');
                                if (response.status) {
                                    table4.ajax.reload();
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                            },
                            error: function(response) {
                                Toast.fire({
                                    icon: "error",
                                    title: 'Woops! Fatal Error.'
                                });
                            }
                        });
                    }
                });
            @endif
        });
    </script>
@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/leaflet/leaflet.css" rel="stylesheet">
@endsection