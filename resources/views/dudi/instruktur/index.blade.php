@extends('layouts.main')
@section('title')
Instruktur
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Instruktur</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Instruktur</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Instruktur</h5>
                <div>
                    <a href="{{route('instruktur.create')}}" class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</a>
                    <button class="btn btn-success btn-sm" id="btnUploadExcel"><i class="bi bi-upload"></i> Upload</button>
                    <a class="btn btn-danger btn-sm" href="{{ route('instruktur.downloadExcel') }}"><i class="bi bi-download"></i> Download</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Dudi</th>
                            <th>#</th>
                        </tr>
                    </thead>
                </table>
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
                ajax: '{!! route('instruktur.data') !!}',
                columns: [{
                        data: 'nama_instruktur',
                        name: 'nama'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'dudi',
                        name: 'dudi'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
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
                    url: '{{ route('instruktur.uploadExcel') }}', // Route untuk handle upload
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
