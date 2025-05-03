@extends('layouts.main')
@section('title')
    Nilai Quesioner
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Nilai Quesioner</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">PKL</li>
                <li class="breadcrumb-item active">Nilai Quesioner</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Nilai Quesioner</h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                    <button class="btn btn-danger btn-sm" id="btnDownload"><i class="bi bi-download"></i> Download</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>Tahun Akademik</th>
                            <th>Siswa</th>
                            <th>Rata-rata Nilai</th>
                            <th>#</th>
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
                    <h5 class="modal-title" id="modalTitle">Tambah Nilai Quesioner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="myForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" id="stt" name="stt" value="0">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <div class="invalid-feedback"> Tanggal wajib diisi. </div>
                        </div>

                        <div class="mb-3">
                            <label for="nis" class="form-label">Siswa</label>
                            <select class="form-select" id="nis" name="nis" style="width: 100%;"
                                required>
                            </select>
                            <div class="invalid-feedback"> Siswa wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quesioner</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Quesioner</th>
                                        <th>Ya</th>
                                        <th>Tidak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questions as $question)
                                        <tr>
                                            <td>
                                                {{ $question->soal }}
                                                <input type="hidden" name="id_nilai[{{ $question->id_quesioner }}]" value="">
                                            </td>
                                            <td>
                                                <input type="radio" name="quesioner[{{ $question->id_quesioner }}]"
                                                    value="1" required>
                                            </td>
                                            <td>
                                                <input type="radio" name="quesioner[{{ $question->id_quesioner }}]"
                                                    value="0">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="invalid-feedback"> Harap jawab semua pertanyaan. </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
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
                                <input class="form-check-input" type="radio" name="download_option" id="siswaOption"
                                    value="1">
                                <label class="form-check-label" for="siswaOption">
                                    Satu Siswa
                                </label>
                            </div>
                        </div>

                        <!-- Select Siswa (Hidden by default) -->
                        <div class="mb-3" id="SiswaSelectContainer" style="display:none;">
                            <label for="nis_download" class="form-label">Siswa</label>
                            <select class="form-select" id="nis_download" name="nis_download"
                                style="width: 100%;">
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
                    $("#SiswaSelectContainer").show();
                } else {
                    // Jika memilih "All", sembunyikan select NIS
                    $("#SiswaSelectContainer").hide();
                }
            });

            // Set default state
            if ($("#allOption").is(":checked")) {
                $("#SiswaSelectContainer").hide();
            } else {
                $("#SiswaSelectContainer").show();
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
                var id = '';
                if (downloadOption == "1") {
                    // Memastikan siswa dipilih jika opsi "NIS"
                    var id = $('#nis_download').val();
                    if (!id) {
                        alert("Siswa wajib dipilih.");
                        return;
                    }
                }

                // Menyembunyikan modal setelah klik tombol download
                $('#downloadModal').modal('hide');

                window.location.href="{{ route('nilai-quesioner.downloadExcel') }}?id="+id;
            });
        });
    </script>
    <script>
        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('nilai-quesioner.data') }}",
                    type: 'POST',
                },
                columns: [{
                        data: 'tahun_akademik',
                        name: 'tahun_akademik'
                    },
                    {
                        data: 'nama_siswa',
                        name: 'nama_siswa'
                    },
                    {
                        data: 'rata_rata_nilai',
                        name: 'rata_rata_nilai'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btnAdd').click(function() {
                $('#myForm')[0].reset();
                $('#stt').val('0');
                $('#modalTitle').text('Tambah Nilai Quesioner');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol edit
            $('#myTable').on('click', '.btn-edit', function() {
                var data = table.row($(this).closest('tr')).data();
                var nis = data.nis;
                var id_ta = data.id_ta;

                $.ajax({
                    url: `/nilai-quesioner/edit/${nis}/${id_ta}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            var data = response.data;

                            $('#stt').val('1');
                            $('#tanggal').val(data.tanggal);

                            // Set siswa
                            var newOptionSiswa = new Option(data.nis + ' - ' +
                                data.nama_siswa, data.nis, true, true);
                            $('#nis').append(newOptionSiswa).trigger('change');

                            // Clear previous selections for radio buttons
                            $('input[name^="quesioner"]').prop('checked', false);

                            // Set quesioner values based on response
                            data.quesioner.forEach(function(question) {
                                $(`input[name="quesioner[${question.id_quesioner}]"][value="${question.nilai}"]`)
                                    .prop('checked', true);
                                $(`input[name="id_nilai[${question.id_quesioner}]"]`).val(question.id_nilai);
                            });

                            $('#myModal').modal('show');
                        } else {
                            alert('Data tidak ditemukan.');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan.');
                    }
                });
            });



            // Event click untuk tombol hapus
            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var nis = data.nis;
                var id_ta = data.id_ta;

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
                            url: `/nilai-quesioner/delete/${nis}/${id_ta}`,
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

            $('#myForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this)[0];
                if (form.checkValidity() === false) {
                    e.stopPropagation();
                } else {
                    var url = "{{ route('nilai-quesioner.upsert') }}";
                    var formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: formData,
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

        $(document).ready(function() {
            $('#nis').select2({
                dropdownParent: $("#myModal"),
                placeholder: 'Cari Siswa ID/Nama...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('siswa.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
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
                placeholder: 'Cari Siswa ID/Nama...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('siswa.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
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


        });
    </script>
@endsection

@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection
