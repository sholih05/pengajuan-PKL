@extends('layouts.main')
@section('title')
    Guru
@endsection
@section('pagetitle')
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Guru</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Guru</li>
                    <li class="breadcrumb-item">{{$guru->nama}}</li>
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
                    <h5 class="card-title">Data Guru</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID: </strong>{{ $guru->id_guru }}</li>
                        <li class="list-group-item"><strong>Nama: </strong>{{ $guru->nama }}</li>
                        <li class="list-group-item"><strong>Jurusan: </strong>{{ $guru->jurusan->jurusan }}</li>
                        <li class="list-group-item"><strong>Email: </strong>{{ $guru->email }}</li>
                        <li class="list-group-item"><strong>No. Kontak: </strong>{{ $guru->no_kontak }}</li>
                        <li class="list-group-item"><strong>Gender:
                            </strong>{{ $guru->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</li>
                        <li class="list-group-item"><strong>Alamat: </strong>{{ $guru->alamat }}</li>
                        <!-- Tambahkan info lain yang diperlukan -->
                    </ul>

                    <h5 class="card-title mt-4">Instruktur Pembimbing</h5>
                    @if ($penempatanGrouped->isNotEmpty())
                        @foreach ($penempatanGrouped as $penempatanByInstruktur)
                            @php
                                $penempatan = $penempatanByInstruktur->first(); // Ambil satu penempatan per id_instruktur
                            @endphp
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Dudi:</strong>
                                    <a href="{{ url('/d/dudi?id=' . $penempatan->instruktur->dudi->id_dudi) }}">
                                        {{ $penempatan->instruktur->dudi->nama }}</a>
                                </li>
                                <li class="list-group-item"><strong>Instruktur:</strong>
                                    <a href="{{ url('/d/instruktur?id=' . $penempatan->instruktur->id_instruktur) }}">
                                        {{ $penempatan->instruktur->nama }}</a>
                                </li>
                                <li class="list-group-item"><strong>Periode:
                                    </strong>{{ $penempatan->tahunAkademik->tahun_akademik ?? '-' }}
                                </li>
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
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-data-guru">Siswa
                                Bimbingan</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#profile-absensi">Kehadiran</button>
                        </li>
                        
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-catatan">Catatan
                                Kegiatan</button>
                        </li>
                        @if (session('id_guru') == $guru->id_guru || in_array(auth()->user()->role, [1, 2]))
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab"
                                data-bs-target="#detail-surat">Detail Surat</button>
                        </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">
                                    Akun</button>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content pt-2">
                        <!-- Data Guru Tab -->
                        <div class="tab-pane fade show active profile-data-guru" id="profile-data-guru">

                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>NIS</th>
                                            <th>NISN</th>
                                            <th>Nama</th>
                                            <th>Instruktur</th>
                                            <th>DUDI</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- detail surat -->
                        <div class="tab-pane fade profile-absensi" id="detail-surat">

                            <div class="table-responsive">
                                <table class="table table-striped table-sm" id="detail-surat" width="100%">
                                    <thead>
                                        <th>number</th>
                                        <th>file_name </th>
                                        <th>Nis </th>
                                        <th>uploaded_at</th>                           
                                      </t>
                                    </thead>
                                    <tbody>
                                        @foreach ($files as $index => $file)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $file->file_name }}</td>
                                                <td>{{ $file->siswa_id ?? 'Tidak Diketahui' }}</td>
                                                <td>{{ $file->uploaded_at }}</td>
                                                <td>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i> Lihat File
                                                </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                                        </tr>
                                        <tr>
                                            <th>Instruktur</th>
                                            <th>Guru</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        @if (session('id_guru') == $guru->id_guru || in_array(auth()->user()->role, [1, 2]))
                            <!-- Change Password Tab -->
                            <div class="tab-pane fade pt-3" id="profile-change-password">
                                <!-- Change Password Form -->
                                <form id="changePasswordForm">
                                    @csrf
                                    <div class="row mb-3">
                                        <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control" id="currentPassword"
                                                required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New
                                            Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="newpassword" type="password" class="form-control" id="newPassword"
                                                minlength="8" required>
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


@endsection

@section('js')
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.js"></script>
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.js"></script>
    <script>
        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.guru.siswa.data') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id =
                        '{{ $guru->id_guru }}'; // Ambil nilai NIS dari elemen input atau lainnya
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
                        data: 'nama_instruktur',
                        name: 'instruktur.nama'
                    },
                    {
                        data: 'nama_dudi',
                        name: 'dudi.nama'
                    }
                ]
            });

            var table2 = $('#table-presensi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('d.guru.siswa.kegiatan') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id =
                            '{{ $guru->id_guru }}'; // Ambil nilai NIS dari elemen input atau lainnya
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
                    url: '{!! route('d.guru.siswa.kegiatan') !!}',
                    type: 'GET',
                    data: function(d) {
                        // Menambahkan parameter tambahan yang akan dikirim ke server
                        d.id =
                            '{{ $guru->id_guru }}'; // Ambil nilai NIS dari elemen input atau lainnya
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
                    }
                ]
            });
            $('#id_ta').on('change', function() {
                table.ajax.reload();
            });

        });
    </script>
    @if (session('id_guru') == $guru->id_guru || in_array(auth()->user()->role, [1, 2]))
        <script>
            $(document).ready(function() {
                $(document).on('change', '.disetujui-guru-radio', function() {
                    let id = $(this).data('id');
                    let isChecked = $(this).val(); // Mengambil nilai radio button yang dipilih (1 atau 0)

                    $.ajax({
                        url: "{{ route('d.guru.siswa.presensi_approval') }}", // Sesuaikan dengan endpoint yang ditentukan
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            is_acc: isChecked,
                            stt: 'guru',
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
                        url: "{{ route('d.guru.akun.update') }}", // Endpoint untuk guru
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            current_password: currentPassword,
                            new_password: newPassword,
                            renew_password: renewPassword,
                            id: '{{ $guru->id_guru }}'
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
            });
        </script>
    @endif
@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection
