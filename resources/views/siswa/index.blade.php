@extends('layouts.main')
@section('title')
    siswa
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Siswa</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Siswa</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Siswa</h5>
                <div>
                    <a href="{{ route('siswa.create') }}" class="btn btn-primary btn-sm" id="btnAdd"><i
                            class="bi bi-plus-square"></i> Tambah</a>
                    <button class="btn btn-success btn-sm" id="btnUploadExcel"><i class="bi bi-upload"></i> Upload</button>
                    <a class="btn btn-danger btn-sm" href="{{ route('siswa.downloadExcel') }}"><i
                            class="bi bi-download"></i>
                        Download</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Status Bekerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="myForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" id="nis" name="nis">
                        <div class="form-group">
                            <select class="form-select" name="status_bekerja" id="status_bekerja">
                                <option value="WFO">Work From Office (WFO)</option>
                                <option value="WFA">Work From Any Where (WFA)</option>
                            </select>
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
                ajax: "{{ route('siswa.data') }}",
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
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            // Event click untuk tombol change
            $('#myTable').on('click', '.btn-change', function() {
                $('#myForm')[0].reset();
                $('#myForm').removeClass('was-validated');
                var data = table.row($(this).closest('tr')).data();
                $('#nis').val(data.nis);
                $('#status_bekerja').val(data.status_bekerja);
                $('#myModal').modal('show');
            });

            $('#myForm').on('submit', function(e) {
                e.preventDefault();
                // Check form validity
                var form = $(this)[0]; // Get the form element
                if (form.checkValidity() === false) {
                    e.stopPropagation();
                } else {
                    var url = "{{ route('siswa.change_status_kerja') }}";
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
                    url: "{{ route('siswa.uploadExcel') }}", // Route untuk handle upload
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

        function confirmDelete(nis) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak dapat mengembalikan data ini setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika konfirmasi OK, submit form delete
                    document.getElementById('delete-form-' + nis).submit();
                }
            });
        }
    </script>
@endsection
@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection
