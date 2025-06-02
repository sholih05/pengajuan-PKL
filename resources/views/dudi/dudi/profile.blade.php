@extends('layouts.main')
@section('title')
    Instruktur
@endsection
@section('pagetitle')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Instruktur</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Instruktur</li>
                    <li class="breadcrumb-item">{{$instruktur->nama}}</li>
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
                    <div class="mt-3">
                        <label for="id_ta" class="form-label">Tahun Akademik</label>
                        <select class="form-select" id="id_ta" name="id_ta">
                            <option value="">Pilih</option>
                            @foreach ($ta as $item)
                                @if ($item->id_ta == $activeAcademicYear->id_ta)
                                    <option value="{{ $item->id_ta }}" selected>{{ $item->tahun_akademik }}
                                        (aktif)
                                    </option>
                                @else
                                    <option value="{{ $item->id_ta }}">{{ $item->tahun_akademik }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <h5 class="card-title">Data Instruktur</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID: </strong>{{ $instruktur->id_instruktur }}</li>
                        <li class="list-group-item"><strong>Nama: </strong>{{ $instruktur->nama }}</li>
                        <li class="list-group-item"><strong>Email: </strong>{{ $instruktur->email }}</li>
                        <li class="list-group-item"><strong>No. Kontak: </strong>{{ $instruktur->no_kontak }}</li>
                        <li class="list-group-item"><strong>Gender:
                            </strong>{{ $instruktur->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</li>
                        <li class="list-group-item"><strong>Alamat: </strong>{{ $instruktur->alamat }}</li>
                        <li class="list-group-item"><strong>Dudi: </strong> <a
                                href="{{ url('/d/dudi?id=' . $instruktur->dudi->id_dudi) }}">{{ $instruktur->dudi->nama ?? '-' }}
                            </a></li>
                        <!-- Tambahkan info lain yang diperlukan -->
                    </ul>

                    <h5 class="card-title mt-4">Guru Pembimbing</h5>
                    @if ($penempatanGrouped->isNotEmpty())
                        @foreach ($penempatanGrouped as $penempatanByInstruktur)
                            @php
                                $penempatan = $penempatanByInstruktur->first(); // Ambil satu penempatan per id_instruktur
                            @endphp
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Guru: </strong> <a
                                        href="{{ url('/d/guru?id=' . $penempatan->guru->id_guru) }}">{{ $penempatan->guru->nama ?? '-' }}</a>
                                </li>
                                <li class="list-group-item"><strong>Periode:
                                    </strong>{{ $penempatan->tahunAkademik->tahun_akademik ?? '-' }}</li>
                            </ul>
                            <br>
                        @endforeach
                    @else
                        <ul class="list-group">
                            <li class="list-group-item">Belum ada penempatan</li>
                        </ul>
                    @endif

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
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-data-siswa">Siswa
                                Magang</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#profile-absensi">Kehadiran</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-catatan">Catatan
                                Kegiatan</button>
                        </li>
                        @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#profile-penilaian">Penilaian</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#profile-quesioner">Quesioner</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#profile-kendala-saran">Kendala & Saran</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#profile-change-password">Akun</button>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content pt-2">
                        <!-- Data Siswa Tab -->
                        <div class="tab-pane fade show active profile-data-siswa" id="profile-data-siswa">

                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-data-siswa">
                                    <thead>
                                        <tr>
                                            <th>NIS</th>
                                            <th>NISN</th>
                                            <th>Nama</th>
                                            <th>Guru</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Quest Tab -->
                        <div class="tab-pane fade profile-quesioner" id="profile-quesioner">
                            <form id="questionForm" class="row g-3 needs-validation" novalidate>
                                @csrf
                                <input type="hidden" id="stt" name="stt" value="0">
                                <input type="hidden" id="nis" name="nis" value="{{ $instruktur->id_instruktur }}">
                                <div class="mb-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Quesioner</th>
                                                <th>Ya</th>
                                                <th>Tidak</th>
                                            </tr>
                                        </thead>
                                        <tbody id="quesioner-table">
                                        </tbody>
                                    </table>
                                    <div id="spinner" style="display: none; text-align: center; margin-top: 20px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <div class="invalid-feedback"> Harap jawab semua pertanyaan. </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>

                        <!-- Data Absensi Tab -->
                        <div class="tab-pane fade profile-absensi" id="profile-absensi">

                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-presensi" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Siswa</th>
                                            <th>Masuk</th>
                                            <th>Pulang</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Data Catatan Tab -->
                        <div class="tab-pane fade profile-catatan" id="profile-catatan">

                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="table-catatan" width="100%">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Tanggal</th>
                                            <th rowspan="2">Siswa</th>
                                            <th rowspan="2">Kegiatan</th>
                                            <th colspan="2">Disetujui</th>
                                            <th rowspan="2">Catatan Instruktur</th>
                                            @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                                                <th rowspan="2">#</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th>Instruktur</th>
                                            <th>Guru</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>


                            <!-- Data Catatan Tab -->
                            <div class="tab-pane fade profile-kendala-saran" id="profile-kendala-saran">
                                @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                                    <div class="d-flex flex-row-reverse">
                                        <button class="btn btn-primary btn-sm" id="btnAddKendalaSaran"><i
                                                class="bi bi-plus-square"></i> Tambah</button>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped table-sm" id="table-kendala-saran" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Kategori</th>
                                                <th>Catatan</th>
                                                @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                                                    <th>#</th>
                                                @endif
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                            <!-- Penilaian Tab -->
                            <div class="tab-pane fade profile-penilaian" id="profile-penilaian">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm" id="table-penilaian" width="100%">
                                        <thead>
                                            <tr>
                                                <th>NIS</th>
                                                <th>Siswa</th>
                                                <th>Jurusan</th>
                                                <th>Penilaian</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form id="changePasswordForm">
                                    @csrf
                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control"
                                                id="currentPassword" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control"
                                                id="newPassword" minlength="8" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="renewpassword" type="password" class="form-control"
                                                id="renewPassword" minlength="8" required>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                                    </div>
                                </form><!-- End Change Password Form -->
                            </div>
                        @endif
                    </div><!-- End Bordered Tabs -->
                </div>
            </div>
        </div>
    </div>

    @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
        <!-- Modal -->
        <div class="modal fade" id="catatanModal" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Form Catatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="catatanForm" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" id="id_presensi" name="id_presensi">
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" required></textarea>
                                <div class="invalid-feedback"> Catatan wajib diisi. </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="KendalaSaranModal" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kendala & Saran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="kendalaSaranForm" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <input type="hidden" id="idKendalaSaran" name="idKendalaSaran">
                            <input type="hidden" id="id_instruktur" name="id_instruktur"
                                value="{{ $instruktur->id_instruktur }}">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">Pilih</option>
                                    <option value="K">Kendala</option>
                                    <option value="S">Saran</option>
                                </select>
                                <div class="invalid-feedback">
                                    Kategori wajib dipilih.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-select" name="tanggal" id="tanggal" required>
                                <div class="invalid-feedback">
                                    Tanggal wajib diisi.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="catatanKendalaSaran" class="form-label">Catatan</label>
                                <textarea class="form-control" id="catatanKendalaSaran" name="catatanKendalaSaran" rows="3" required></textarea>
                                <div class="invalid-feedback"> Catatan Kendala / Saran wajib diisi. </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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

    <script>
        $(function() {
            var table1 = $('#table-data-siswa').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.instruktur.siswa.data') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id = '{{ $instruktur->id_instruktur }}'; // Ambil nilai ID Instruktur
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'siswa.nis',
                        name: 'siswa.nis'
                    },
                    {
                        data: 'siswa.nisn',
                        name: 'siswa.nisn'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'siswa.nama'
                    },
                    {
                        data: 'nama_guru',
                        name: 'guru.nama'
                    }
                ]
            });
            var table2 = $('#table-presensi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.instruktur.siswa.kegiatan') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id =
                            '{{ $instruktur->id_instruktur }}'; // Ambil nilai NIS dari elemen input atau lainnya
                        d.stt = '1';
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'siswa.nama'
                    },
                    {
                        data: 'presensi_masuk',
                        name: 'masuk'
                    },
                    {
                        data: 'presensi_pulang',
                        name: 'pulang'
                    },
                ]
            });
            var table3 = $('#table-catatan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.instruktur.siswa.kegiatan') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id =
                            '{{ $instruktur->id_instruktur }}'; // Ambil nilai NIS dari elemen input atau lainnya
                        d.stt = '2';
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'siswa.nama'
                    },
                    {
                        data: 'kegiatan',
                        name: 'kegiatan'
                    },
                    {
                        data: 'disetujui_instruktur',
                        name: 'disetujui_instruktur'
                    },
                    {
                        data: 'disetujui_guru',
                        name: 'disetujui_guru'
                    },
                    {
                        data: 'catatan',
                        name: 'catatan'
                    },
                    @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });
            var table4 = $('#table-kendala-saran').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.instruktur.kendala_saran') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id ='{{ $instruktur->id_instruktur }}'; // Ambil nilai NIS dari elemen input atau lainnya
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'kategori_name',
                        name: 'kategori'
                    },
                    {
                        data: 'catatan',
                        name: 'catatan'
                    },
                    @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });
            var table5 = $('#table-penilaian').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.instruktur.penilaian') !!}',
                    type: 'GET',
                    data: function(d) {
                        d.id = '{{ $instruktur->id_instruktur }}';
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [
                    {
                        data: 'nis',
                        name: 'nis'
                    },
                {
                    data: 'nama_siswa',
                    name: 'nama_siswa'
                },
                {
                    data: 'jurusan',
                    name: 'jurusan'
                },
                {
                    data: 'penilaian',
                    name: 'penilaian'
                }
            ]
        });

            $('#id_ta').on('change', function() {
                table1.ajax.reload();
                table2.ajax.reload();
                table3.ajax.reload();
                table4.ajax.reload();
                table5.ajax.reload();
            });
            @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2]))

                $(document).on('change', '.disetujui-instruktur-radio', function() {
                    let id = $(this).data('id');
                    let isChecked = $(this).val(); // Mengambil nilai radio button yang dipilih (1 atau 0)

                    $.ajax({
                        url: "{{ route('d.instruktur.siswa.presensi_approval') }}", // Sesuaikan dengan endpoint yang ditentukan
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            is_acc: isChecked,
                            stt: 'instruktur',
                        },
                        success: function(response) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                        },
                        error: function() {
                            Toast.fire({
                                icon: "error",
                                title: "Gagal memperbarui status persetujuan."
                            });
                        }
                    });
                });
                // catatan
                $('#table-catatan').on('click', '.btn-edit', function() {
                    var data = table3.row($(this).closest('tr')).data();
                    $('#id_presensi').val(data.id_presensi);
                    $('#catatan').val(data.catatan);
                    $('#catatanModal').modal('show');
                });

                $('#catatanForm').on('submit', function(e) {
                    e.preventDefault();
                    // Reset semua kelas validasi sebelumnya
                    $(this).removeClass('was-validated');

                    // Validasi HTML5 sebelum submit
                    if (this.checkValidity() === false) {
                        // Menambahkan kelas was-validated agar feedback tampil
                        $(this).addClass('was-validated');
                        return; // Menghentikan pengiriman form jika tidak valid
                    }

                    var formData = new FormData(this);
                    var url = "{{ route('d.instruktur.siswa.catatan.update') }}";

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#catatanModal').modal('hide');
                            if (response.status) {
                                table3.ajax.reload();
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
                        error: function() {
                            Toast.fire({
                                icon: "error",
                                title: 'Woops! Fatal Error.'
                            });
                        }
                    });
                });

                // kendala saran
                $('#btnAddKendalaSaran').on('click', function() {
                    $('#KendalaSaranModal').modal('show');
                });
                $('#table-kendala-saran').on('click', '.btn-edit', function() {
                    var data = table4.row($(this).closest('tr')).data();
                    $('#idKendalaSaran').val(data.id_catatan);
                    $('#catatanKendalaSaran').val(data.catatan);
                    $('#kategori').val(data.kategori);
                    $('#tanggal').val(data.tanggal);
                    $('#KendalaSaranModal').modal('show');
                });

                $('#kendalaSaranForm').on('submit', function(e) {
                    e.preventDefault();
                    // Reset semua kelas validasi sebelumnya
                    $(this).removeClass('was-validated');
                    // Validasi HTML5 sebelum submit
                    if (this.checkValidity() === false) {
                        // Menambahkan kelas was-validated agar feedback tampil
                        $(this).addClass('was-validated');
                        return; // Menghentikan pengiriman form jika tidak valid
                    }
                    var formData = new FormData(this);
                    var url = "{{ route('d.instruktur.kendala_saran.upsert') }}";
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#KendalaSaranModal').modal('hide');
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
                        error: function() {
                            Toast.fire({
                                icon: "error",
                                title: 'Woops! Fatal Error.'
                            });
                        }
                    });
                });
            @endif
        });
    </script>

    @if (session('id_instruktur') == $instruktur->id_instruktur || in_array(auth()->user()->role, [1, 2, 4]))
        <script>
            $(document).ready(function() {
                getQuestion();

                // Handle password change submission
                $('#changePasswordForm').on('submit', function(e) {
                    e.preventDefault();

                    const currentPassword = $('#currentPassword').val();
                    const newPassword = $('#newPassword').val();
                    const renewPassword = $('#renewPassword').val();

                    if (newPassword !== renewPassword) {
                        Toast.fire({
                            icon: "error",
                            title: "Password baru tidak cocok. Silakan coba lagi."
                        });
                        return;
                    }

                    $.ajax({
                        url: "{{ route('d.instruktur.akun.update') }}", // Endpoint untuk instruktur
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            current_password: currentPassword,
                            new_password: newPassword,
                            renew_password: renewPassword,
                            id: '{{ $instruktur->id_instruktur }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Toast.fire({
                                    icon: "success",
                                    title: "Password berhasil diubah."
                                });
                                $('#changePasswordForm')[0].reset();
                            } else {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message ||
                                        "Terjadi kesalahan saat mengubah password."
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.message);
                            Toast.fire({
                                icon: "error",
                                title: xhr.message ||
                                    "Terjadi kesalahan. Silakan coba lagi."
                            });
                        }
                    });
                });


                $('#questionForm').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this)[0];
                    if (form.checkValidity() === false) {
                        e.stopPropagation();
                    } else {
                        var url = "{{ route('d.siswa.quesioner.upsert') }}";
                        var formData = $(this).serialize();

                        $.ajax({
                            url: url,
                            method: "POST",
                            data: formData,
                            success: function(response) {
                                getNilaiQuestion();
                                if (response.status) {
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
            });
        </script>


<script>
    function getQuestion() {
        // Tampilkan spinner sebelum memulai AJAX
        $('#spinner').show();

        // Load data dengan AJAX
        $.ajax({
            url: "{{ route('d.siswa.quesioner') }}", // Endpoint backend
            type: "GET",
            data: {
                id_ta: $('#id_ta').val(),
            },
            success: function(response) {
                // Sembunyikan spinner setelah data berhasil dimuat
                $('#spinner').hide();

                if (response.status === 'success') {
                    const questions = response.data;
                    const tableBody = $('#quesioner-table');
                    tableBody.empty();
                    // Render data ke tabel
                    questions.forEach(question => {
                        const row = `
                        <tr>
                            <td>
                                ${question.soal}
                                <input type="hidden" name="id_nilai[${question.id_quesioner}]" value="">
                            </td>
                            <td>
                                <input type="radio" name="quesioner[${question.id_quesioner}]" value="1" required>
                            </td>
                            <td>
                                <input type="radio" name="quesioner[${question.id_quesioner}]" value="0">
                            </td>
                        </tr>
                    `;
                        tableBody.append(row);
                    });
                    getNilaiQuestion();
                } else {
                    Toast.fire({
                        icon: "error",
                        title: response.message
                    });
                }
            },
            error: function() {
                // Sembunyikan spinner jika terjadi error
                $('#spinner').hide();
                alert('Gagal memuat data pertanyaan.');
            }
        });
    }


    function getNilaiQuestion() {
        var nis = '{{ $instruktur->id_instruktur }}';
        var id_ta = $('#id_ta').val();

        $.ajax({
            url: "{{ url('d/siswa/quesioner/edit') }}/" + nis + "/" + id_ta,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;

                    $('#stt').val('1');
                    $('#id_nilai').val(data.id_nilai);
                    // Clear previous selections for radio buttons
                    $('input[name^="quesioner"]').prop('checked', false);

                    // Set quesioner values based on response
                    data.quesioner.forEach(function(question) {
                        $(`input[name="quesioner[${question.id_quesioner}]"][value="${question.nilai}"]`)
                            .prop('checked', true);
                        $(`input[name="id_nilai[${question.id_quesioner}]"]`).val(question
                            .id_nilai);
                    });
                } else {
                    $('#stt').val('0');
                }
            },
            // error: function() {
            //     alert('Terjadi kesalahan.');
            // }
        });
    }
</script>

    @endif
@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection