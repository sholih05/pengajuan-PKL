@extends('layouts.main')
@section('title')
    Presensi
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Presensi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">PKL</li>
                <li class="breadcrumb-item active">Presensi</li>
            </ol>
        </nav> 
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Presensi </h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                    <button class="btn btn-danger btn-sm" id="btnDownload"><i class="bi bi-download"></i> Download</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
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
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Masuk</th>
                            <th rowspan="2">Pulang</th>
                            <th rowspan="2">Siswa</th>
                            <th rowspan="2">Instruktur</th>
                            <th rowspan="2">Kegiatan</th>
                            <th colspan="2">Disetujui</th>
                            <th rowspan="2">#</th>
                        </tr>
                        <tr>
                            <th>Instruktur</th>
                            <th>Guru</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Presensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="myForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" id="stt" name="stt" value="0">
                        <input type="hidden" id="id_presensi" name="id_presensi">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <div class="invalid-feedback"> Tanggal wajib diisi. </div>
                        </div>

                        <div class="mb-3">
                            <label for="masuk" class="form-label">Masuk</label>
                            <input type="time" class="form-control" id="masuk" name="masuk" required>
                            <div class="invalid-feedback"> Waktu masuk wajib diisi. </div>
                        </div>

                        <div class="mb-3">
                            <label for="foto_masuk" class="form-label">Foto Masuk</label>
                            <input type="file" class="form-control" id="foto_masuk" name="foto_masuk" accept="image/*">
                            <div class="invalid-feedback"> Foto wajib diunggah. </div>
                        </div>

                        <div class="mb-3">
                            <label for="pulang" class="form-label">Pulang</label>
                            <input type="time" class="form-control" id="pulang" name="pulang">
                            <div class="invalid-feedback"> Waktu pulang wajib diisi. </div>
                        </div>

                        <div class="mb-3">
                            <label for="foto_pulang" class="form-label">Foto Pulang</label>
                            <input type="file" class="form-control" id="foto_pulang" name="foto_pulang" accept="image/*">
                            <div class="invalid-feedback"> Foto wajib diunggah. </div>
                        </div>

                        <div class="mb-3">
                            <label for="nis" class="form-label">Siswa</label>
                            <select class="form-select" id="nis" name="nis" style="width: 100%;" required>
                            </select>
                            <div class="invalid-feedback"> Siswa wajib dipilih. </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_penempatan" class="form-label">Penempatan</label>
                            <select class="form-select" id="id_penempatan" name="id_penempatan" required>
                            </select>
                            <div class="invalid-feedback"> Penempatan wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="kegiatan" class="form-label">Kegiatan</label>
                            <textarea class="form-control" id="kegiatan" name="kegiatan" rows="3"></textarea>
                            <div class="invalid-feedback"> Kegiatan wajib diisi. </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_acc_instruktur" class="form-label">Disetujui Instruktur</label>
                            <select class="form-select" id="is_acc_instruktur" name="is_acc_instruktur" required>
                                <option value="1" selected>Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                            <div class="invalid-feedback"> Status persetujuan wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_acc_guru" class="form-label">Disetujui Guru</label>
                            <select class="form-select" id="is_acc_guru" name="is_acc_guru" required>
                                <option value="1" selected>Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                            <div class="invalid-feedback"> Status persetujuan wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan Instruktur</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                            <div class="invalid-feedback"> Catatan wajib diisi. </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Download -->
    <div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadExcelModalLabel">Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="downloadForm">
                        @csrf

                        <!-- Radio Button for Selection -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Download Berdasarkan:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="download_option" id="allOption"
                                    value="0" checked>
                                <label class="form-check-label" for="allOption">
                                    Semua Siswa
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="download_option" id="nisOption"
                                    value="1">
                                <label class="form-check-label" for="nisOption">
                                    Satu Siswa
                                </label>
                            </div>
                        </div>

                        <!-- Select NIS (Hidden by default) -->
                        <div class="mb-3" id="nisSelectContainer" style="display:none;">
                            <label for="nis" class="form-label">Siswa</label>
                            <select class="form-select" id="nis_download" name="nis_download" style="width: 100%;">
                                <!-- Options will be dynamically loaded -->
                            </select>
                            <div class="invalid-feedback"> Siswa wajib dipilih. </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Download</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Download -->
@endsection

@section('js')
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.js"></script>
    <script src="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/select2/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Menambahkan event listener pada radio button
            $("input[name='download_option']").change(function() {
                if ($(this).val() == "1") {
                    // Jika memilih "NIS", tampilkan select NIS
                    $("#nisSelectContainer").show();
                } else {
                    // Jika memilih "All", sembunyikan select NIS
                    $("#nisSelectContainer").hide();
                }
            });

            // Set default state
            if ($("#allOption").is(":checked")) {
                $("#nisSelectContainer").hide();
            } else {
                $("#nisSelectContainer").show();
            }

            // Menampilkan modal download saat tombol download diklik
            $('#btnDownload').click(function() {
                $('#downloadModal').modal('show');
            });

            // Fungsi untuk menangani form download file Excel
            $('#downloadForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form submit default

                // Mengambil nilai dari form untuk mengecek apakah memilih "All" atau "NIS"
                var downloadOption = $('input[name="download_option"]:checked').val();
                var nis = '';
                if (downloadOption == "1") {
                    // Memastikan siswa dipilih jika opsi "NIS"
                    var nis = $('#nis_download').val();
                    if (!nis) {
                        alert("Siswa wajib dipilih.");
                        return;
                    }
                }

                // Menyembunyikan modal setelah klik tombol download
                $('#downloadModal').modal('hide');
                var id_ta = $('#id_ta').val();
                window.open("{{ route('presensi.downloadExcel') }}?nis=" + nis + "&id_ta=" + id_ta,
                    "_blank");
            });
        });
    </script>
    <script>
        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{   route('presensi.data') }}",
                    type: 'POST',
                    data: function(d) {
                        d.id_ta = $('#id_ta').val();
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'presensi_masuk',
                        name: 'masuk'
                    },
                    {
                        data: 'presensi_pulang',
                        name: 'pulang'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'nama_siswa'
                    },
                    {
                        data: 'nama_instruktur',
                        name: 'nama_instruktur'
                    },
                    {
                        data: 'kegiatan',
                        name: 'kegiatan'
                    },
                    {
                        data: 'is_acc_instruktur',
                        name: 'is_acc_instruktur',
                        render: function(data) {
                            return data === 1 ? 'Ya' : (data === 0 ? 'Tidak' : '');
                        }
                    },
                    {
                        data: 'is_acc_guru',
                        name: 'is_acc_guru',
                        render: function(data) {
                            return data === 1 ? 'Ya' : (data === 0 ? 'Tidak' : '');
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $('#id_ta').on('change', function() {
                table.ajax.reload();
            });

            $('#btnAdd').click(function() {
                $('#myForm')[0].reset();
                $('#myForm').removeClass('was-validated');
                $('#stt').val('0');
                $('#modalTitle').text('Tambah Presensi');
                $('#myModal').modal('show');
            });

            $('#myTable').on('click', '.btn-edit', function() {
                $('#myForm')[0].reset();
                $('#myForm').removeClass('was-validated');
                var data = table.row($(this).closest('tr')).data();
                $('#stt').val('1');
                $('#id_presensi').val(data.id_presensi);
                $('#tanggal').val(data.tanggal);
                $('#masuk').val(data.masuk ? data.masuk.slice(0, 5) : ''); // Memotong hanya HH:mm
                $('#pulang').val(data.pulang ? data.pulang.slice(0, 5) : '');
                $('#kegiatan').val(data.kegiatan);
                $('#is_acc_instruktur').val(data.is_acc_instruktur);
                $('#is_acc_guru').val(data.is_acc_guru);
                $('#catatan').val(data.catatan);
                getPenempatan(data.siswa.nis,data.id_penempatan);

                // Set default values for select2 fields
                if (data.siswa.nis) {
                    var newOptionNis = new Option(data.siswa.nis + ' - ' + data.siswa.nama, data.siswa.nis, true, true);
                    $('#nis').append(newOptionNis).trigger('change');
                }

                $('#myModal').modal('show');
            });

            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var Id = data.id_presensi;
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
                            url: "{{ url('presensi/delete') }}" + "/" + Id,
                            method: "GET",
                            success: function(response) {
                                if (response.status) {
                                    table.ajax.reload();
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
                    }
                });
            });

            $('#myForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var url = "{{ route('presensi.upsert') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#myModal').modal('hide');
                        if (response.status) {
                            table.ajax.reload();
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

            function getPenempatan(nis,old=null) {
                $('#id_penempatan').empty();
                // Lakukan AJAX untuk mendapatkan instruktur terkait
                $.ajax({
                    url: "{{ route('presensi.get_penempatan') }}", // Rute untuk mendapatkan instruktur berdasarkan siswa
                    type: "GET",
                    data: {
                        nis: nis
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.status === 'success') {
                            // Loop data untuk dimasukkan ke dalam select
                            response.data.forEach(item => {
                                var selected = (old==item.id_penempatan?'selected':'');
                                const optionText =
                                    `(${item.tahun_akademik.tahun_akademik}) DUDI: ${item.dudi.nama} - Instruktur: ${item.instruktur.nama} - Guru: ${item.guru.nama} `;
                                $('#id_penempatan').append(
                                    `<option value="${item.id_penempatan}" ${selected}>${optionText}</option>`
                                );
                            });
                        } else {
                            alert('Penempatan tidak ditemukan untuk siswa ini.');
                        }
                    },
                    error: function() {
                        alert('Gagal mendapatkan data Penempatan.');
                    }
                });
            }
            $('#nis').select2({
                dropdownParent: $("#myModal"),
                placeholder: 'Cari Siswa NIS/Nama...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('siswa.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            k: 'presensi'
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nis,
                                    text: item.nis + ' - ' + item.nama
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#nis_download').select2({
                dropdownParent: $("#downloadModal"),
                placeholder: 'Cari Siswa NIS/Nama...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('siswa.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            k: 'presensi'
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nis,
                                    text: item.nis + ' - ' + item.nama
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            // Event ketika siswa dipilih
            $('#nis').on('select2:select', function(e) {
                var nis = e.params.data.id; // Ambil NIS siswa yang dipilih
                getPenempatan(nis)

            });
        });
    </script>
@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection
