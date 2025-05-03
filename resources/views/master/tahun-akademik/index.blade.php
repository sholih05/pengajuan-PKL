@extends('layouts.main')
@section('title')
Tahun Akademik
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Tahun Akademik</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Master</li>
                <li class="breadcrumb-item active">Tahun Akademik</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Tahun Akademik</h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                    <a class="btn btn-danger btn-sm" href="{{ route('master.tahun_akademik.downloadExcel') }}"><i class="bi bi-download"></i> Download</a>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Akademik</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
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
                        <input type="hidden" id="id_ta_old" name="id_ta_old">
                        <div class="form-group">
                            <label for="tahun_akademik">Tahun Akademik</label>
                            <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" required
                                maxlength="9">
                            <div class="invalid-feedback">Isian tidak boleh kosong.</div>
                        </div>

                        <div class="form-group">
                            <label for="mulai">Mulai</label>
                            <input type="date" class="form-control" id="mulai" name="mulai" required>
                            <div class="invalid-feedback">Isian tidak boleh kosong.</div>
                        </div>

                        <div class="form-group">
                            <label for="selesai">Selesai</label>
                            <input type="date" class="form-control" id="selesai" name="selesai" required>
                            <div class="invalid-feedback">Isian tidak boleh kosong.</div>
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
                ajax: "{!! route('master.tahun_akademik.data') !!}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tahun_akademik',
                        name: 'tahun_akademik'
                    },
                    {
                        data: 'mulai',
                        name: 'mulai'
                    },
                    {
                        data: 'selesai',
                        name: 'selesai'
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
                $('#tahun_akademik').val(data.tahun_akademik);
                $('#mulai').val(data.mulai);
                $('#selesai').val(data.selesai);
                $('#id_ta_old').val(data.id_ta);
                $('#modalTitle').text('Ubah');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol hapus
            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var Id = data.id_ta;
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
                            url: "{{ url('master/tahun_akademik/delete') }}" + "/" + Id,
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
                    var url = "{{ route('master.tahun_akademik.upsert') }}";

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
    </script>
@endsection
@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection
