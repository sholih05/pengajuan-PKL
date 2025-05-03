@extends('layouts.main')
@section('title')
    Guru
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Guru</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Guru</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Guru</h5>
                <div>
                    <a href="{{ route('guru.create') }}" class="btn btn-primary btn-sm" id="btnAdd"><i
                            class="bi bi-plus-square"></i> Tambah</a>
                    <button class="btn btn-success btn-sm" id="btnUploadExcel"><i class="bi bi-upload"></i> Upload</button>
                    <a class="btn btn-danger btn-sm" href="{{ route('guru.downloadExcel') }}"><i class="bi bi-download"></i>
                        Download</a>

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
                            <th>Jurusan</th>
                            <th>Role</th>
                            <th>#</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="ubahRoleModal" tabindex="-1" aria-labelledby="ubahRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('guru.updateRole') }}" method="POST" id="formRole">
                    @csrf
                    <input type="hidden" name="id_guru" id="id_guru">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ubahRoleModalLabel">Ubah Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="role" class="form-label">Pilih Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="1">Super Admin</option>
                                <option value="2">Admin</option>
                                <option value="3">Guru</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
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
                ajax: '{!! route('guru.data') !!}',
                columns: [{
                        data: 'nama_guru',
                        name: 'nama'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'jurusan',
                        name: 'jurusan'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // AJAX form submit
            // Fungsi untuk mengirim form via AJAX
            function submitForm(formData) {
                $.ajax({
                    url: $('#formRole').attr('action'),
                    type: $('#formRole').attr('method'),
                    data: formData,
                    success: function(response) {
                        // Tampilkan pesan sukses
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $('#ubahRoleModal').modal('hide'); // Tutup modal
                        table.ajax.reload(); // Refresh tabel atau bagian yang relevan
                    },
                    error: function(xhr) {
                        // Tangani error
                        if (xhr.status === 401) {
                            // Jika statusnya 401 Unauthorized, reload halaman
                            window.location.reload();
                        } else {
                            alert('Terjadi kesalahan, coba lagi.');
                        }
                    }
                });
            }


            // Menangani form submit
            $('#formRole').on('submit', function(e) {
                e.preventDefault(); // Mencegah form submit biasa

                var role = $('#role').val(); // Ambil nilai role dari form
                var formData = $(this).serialize(); // Ambil data form

                // Jika role adalah "Super Admin" (misalnya role == 1)
                if (role == 1) {
                    // Tampilkan SweetAlert konfirmasi
                    Swal.fire({
                        title: 'Peringatan!',
                        text: "Super Admin hanya boleh ada satu. Setelah submit anda akan otomatis logout. Apakah Anda yakin untuk melanjutkan?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, lanjutkan!',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika konfirmasi Ya, kirimkan form menggunakan AJAX
                            submitForm(formData);
                            window.location.reload();
                        } else {
                            // Jika batal, form tidak akan disubmit
                            Swal.fire({
                                icon: 'info',
                                title: 'Batal',
                                text: 'Perubahan role dibatalkan.',
                            });
                        }
                    });
                } else {
                    // Jika bukan Super Admin, langsung kirimkan form
                    submitForm(formData);
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
                    url: '{{ route('guru.uploadExcel') }}', // Route untuk handle upload
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

        function confirmDelete(id) {
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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function ubahRole(id, role) {
            $('#role').val(role);
            $('#id_guru').val(id);
            $('#ubahRoleModal').modal('show');
        }
    </script>
@endsection
@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
@endsection
