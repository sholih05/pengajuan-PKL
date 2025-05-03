@extends('layouts.main')
@section('title')
    Quesioner
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Quesioner</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Quesioner</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Quesioner</h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                    <button class="btn btn-success btn-sm" id="btnUploadExcel"><i class="bi bi-upload"></i> Upload</button>
                    <a class="btn btn-danger btn-sm" href="{{ route('master.quesioner.downloadExcel') }}"><i class="bi bi-download"></i> Download</a>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>Soal</th>
                            <th>Tahun Akademik</th>
                            <th>#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
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
                        <input type="hidden" id="id_quesioner" name="id_quesioner">

                        <div class="form-group">
                            <label for="soal">Soal</label>
                            <input type="text" class="form-control" id="soal" name="soal" required>
                            <div class="invalid-feedback">Isian tidak boleh kosong.</div>
                        </div>

                        <div class="mb-3">
                            <label for="id_ta" class="form-label">Tahun Akademik</label>
                            <select class="form-select" id="id_ta" name="id_ta" required>
                                <option value="">Pilih</option>
                                @foreach ($ta as $item)
                                    <option value="{{ $item->id_ta }}">{{ $item->tahun_akademik }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"> Tahun Akademik wajib dipilih. </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
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
                ajax: '{!! route('master.quesioner.data') !!}',
                columns: [{
                        data: 'soal',
                        name: 'soal'
                    },
                    {
                        data: 'tahun_akademik',
                        name: 'tahun_akademik'
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
                $('#myForm').removeClass('was-validated');
                $('#stt').val('0');
                $('#modalTitle').text('Tambah Baru');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol edit
            $('#myTable').on('click', '.btn-edit', function() {
                var data = table.row($(this).closest('tr')).data();
                $('#stt').val('1');
                $('#soal').val(data.soal);
                $('#id_ta').val(data.id_ta);
                $('#id_quesioner').val(data.id_quesioner);
                $('#modalTitle').text('Ubah');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol hapus
            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var Id = data.id_quesioner;
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
                            url: "{{ url('master/quesioner/delete') }}" + "/" + Id,
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
                // Check form validity
                var form = $(this)[0]; // Get the form element
                if (form.checkValidity() === false) {
                    e.stopPropagation();
                } else {
                    var url = "{{ route('master.quesioner.upsert') }}";
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


            // upload file Excel
            $('#btnUploadExcel').click(function() {
                $('#uploadExcelModal').modal('show');
            });
            // Fungsi untuk menangani form upload file Excel
            $('#uploadExcelForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form submit default

                // Menampilkan pesan "Uploading..."
                $('#uploadingMessage').show();
                $('#errorMessage').hide();

                // Mengambil data form
                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('master.quesioner.uploadExcel') }}', // Route untuk handle upload
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Menyembunyikan pesan "Uploading..."
                        $('#uploadingMessage').hide();

                        if (response.status) {
                            // Menutup modal dan menampilkan success message
                            $('#uploadExcelModal').modal('hide');
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
                        $('#uploadingMessage').hide();
                        $('#errorMessage').text('Terjadi kesalahan. Silakan coba lagi.').show();
                    }
                });
            });

        });
    </script>
@endsection
@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection
