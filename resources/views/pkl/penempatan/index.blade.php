@extends('layouts.main')
@section('title')
    Penempatan
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Penempatan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">PKL</li>
                <li class="breadcrumb-item active">Penempatan</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Penempatan</h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                    <button class="btn btn-success btn-sm" id="btnUploadExcel"><i class="bi bi-upload"></i> Upload</button>
                    <button class="btn btn-danger btn-sm me-2" onclick="getLaporan('/penempatan/downloadExcel')"><i class="bi bi-download"></i> Download</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="id_ta1" class="form-label">Tahun Akademik</label>
                <select class="form-select" id="id_ta1" name="id_ta1" required>
                    <option value="">Pilih</option>
                    @foreach ($thnAkademik as $item)
                        @if ($item->id_ta == $aktifAkademik->id_ta)
                            <option value="{{ $item->id_ta }}" selected>{{ $item->tahun_akademik }} (aktif)</option>
                        @else
                            <option value="{{ $item->id_ta }}">{{ $item->tahun_akademik }}</option>
                        @endif
                    @endforeach
                </select>
                <div class="invalid-feedback"> Tahun Akademik wajib dipilih. </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>Tahun Akademik</th>
                            <th>Siswa</th>
                            <th>Guru</th>
                            <th>Instruktur</th>
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
                    <h5 class="modal-title" id="modalTitle">Tambah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="myForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" id="stt" name="stt" value="0">
                        <input type="hidden" id="id_penempatan" name="id_penempatan">
                        <div class="mb-3">
                            <label for="id_ta" class="form-label">Tahun Akademik</label>
                            <select class="form-select" id="id_ta" name="id_ta" required>
                                <option value="">Pilih</option>
                                @foreach ($thnAkademik as $item)
                                    @if ($item->id_ta == $aktifAkademik->id_ta)
                                        <option value="{{ $item->id_ta }}" selected>{{ $item->tahun_akademik }} (aktif)</option>
                                    @else
                                        <option value="{{ $item->id_ta }}">{{ $item->tahun_akademik }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback"> Tahun Akademik wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="nis" class="form-label">Siswa</label>
                            <select class="form-select" id="nis" name="nis" style="width: 100%;" required>
                            </select>
                            <div class="invalid-feedback"> Siswa wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Guru</label>
                            <select class="form-select" id="id_guru" name="id_guru" style="width: 100%;" required>
                            </select>
                            <div class="invalid-feedback"> Guru wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_instruktur" class="form-label">Instruktur</label>
                            <select class="form-select" id="id_instruktur" name="id_instruktur" style="width: 100%;"
                                required>
                            </select>
                            <div class="invalid-feedback"> Instruktur wajib dipilih. </div>
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
    <script src="{{ asset('assets') }}/vendor/select2/js/select2.min.js"></script>
    <script>
        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penempatan.data')  }} ",
                    type: 'POST',
                    data: function(d) {
                        d.id_ta = $('#id_ta1').val();
                    }
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
                        data: 'nama_guru',
                        name: 'nama_guru'
                    },
                    {
                        data: 'nama_instruktur',
                        name: 'nama_instruktur'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $('#id_ta1').on('change', function() {
                table.ajax.reload();
            });

            $('#btnAdd').click(function() {
                $('#myForm')[0].reset();
                $('#stt').val('0');
                $('#modalTitle').text('Tambah Baru');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol edit
            $('#myTable').on('click', '.btn-edit', function() {
                var data = table.row($(this).closest('tr')).data();
                console.log(data);

                $('#stt').val('1');
                $('#id_penempatan').val(data.id_penempatan);
                $('#id_ta').val(data.id_ta);
                $('#modalTitle').text('Ubah');

                // Set nilai default untuk nis, id_guru, dan id_instruktur
                if (data.nis) {
                    var newOptionNis = new Option(data.nis + ' - ' + data.siswa.nama, data.nis, true, true);
                    $('#nis').append(newOptionNis).trigger('change');
                }

                if (data.id_guru) {
                    var newOptionGuru = new Option(data.id_guru + ' - ' + data.guru.nama, data.id_guru,
                        true, true);
                    $('#id_guru').append(newOptionGuru).trigger('change');
                }

                if (data.id_instruktur) {
                    var newOptionInstruktur = new Option(data.id_instruktur + ' - ' + data.instruktur.nama +
                        ' - ' + data.dudi.nama, data.id_instruktur, true, true);
                    $('#id_instruktur').append(newOptionInstruktur).trigger('change');
                }

                $('#myModal').modal('show');
            });

            // Event click untuk tombol hapus
            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var Id = data.id_penempatan;
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
                            url: "{{ url('penempatan/delete') }}" + "/" + Id,
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
                var form = $(this)[0]; // Get the form element
                if (form.checkValidity() === false) {
                    e.stopPropagation();
                } else {
                    var url = "{{ route('penempatan.upsert') }}";
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
                    url: "{{ route('penempatan.uploadExcel') }}", // Route untuk handle upload
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
        function getLaporan(link) {
            var id_ta = $('#id_ta1').val();
            window.open(link+'?id_ta='+id_ta, '_blank');
        }
    </script>
    <script>
        $(document).ready(function() {
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
                            k: 'penempatan',
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                var txt = '';
                                var i=0;
                                item.penempatan.forEach(el => {
                                    if (el.id_ta==$('#id_ta').val()) {
                                        i++;
                                        txt = ` (Sudah di Tempatkan ${i}x)`;
                                    }
                                });
                                return {
                                    id: item.nis,
                                    text: item.nis + ' - ' + item.nama + txt // Tampilkan data siswa
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#id_guru').select2({
                dropdownParent: $("#myModal"),
                placeholder: 'Cari Guru ID/Nama...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('guru.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // istilah pencarian
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id_guru,
                                    text: item.id_guru + ' - ' + item
                                        .nama // Tampilkan data siswa
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
            $('#id_instruktur').select2({
                dropdownParent: $("#myModal"),
                placeholder: 'Cari Instruktur ID/Nama/Dudi...',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('instruktur.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // istilah pencarian
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id_instruktur,
                                    text: item.id_instruktur + ' - ' + item.nama + ' - ' + item
                                        .nama_dudi // Tampilkan data siswa
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
