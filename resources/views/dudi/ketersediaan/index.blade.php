@extends('layouts.main')
@section('title')
    Ketersediaan
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Ketersediaan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Dudi</li>
                <li class="breadcrumb-item active">Ketersediaan</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Ketersediaan</h5>
                <div>
                    <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped table-sm" id="myTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jurusan</th>
                            <th>Dudi</th>
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
                        <input type="hidden" id="id_ketersediaan" name="id_ketersediaan">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <div class="invalid-feedback">Isian tidak boleh kosong.</div>
                        </div>

                        <div class="mb-3">
                            <label for="id_jurusan" class="form-label">Jurusan</label>
                            <select class="form-select" id="id_jurusan" name="id_jurusan" required>
                                <option value="">Pilih</option>
                                @foreach ($jurusan as $item)
                                    <option value="{{ $item->id_jurusan }}">{{ $item->jurusan }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"> Jurusan wajib dipilih. </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_dudi" class="form-label">DUDI</label>
                            <select class="form-select" id="id_dudi" name="id_dudi" required>
                                <option value="">Pilih</option>
                                @foreach ($dudi as $item)
                                    <option value="{{ $item->id_dudi }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"> Dudi wajib dipilih. </div>
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
                ajax: '{!! route('ketersediaan.data') !!}',
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jurusan',
                        name: 'jurusan'
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

            $('#btnAdd').click(function() {
                $('#myForm')[0].reset();
                $('#stt').val('0');
                $('#modalTitle').text('Tambah Baru');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol edit
            $('#myTable').on('click', '.btn-edit', function() {
                var data = table.row($(this).closest('tr')).data();
                $('#stt').val('1');
                $('#tanggal').val(data.tanggal);
                $('#id_jurusan').val(data.id_jurusan);
                $('#id_dudi').val(data.id_dudi);
                $('#id_ketersediaan').val(data.id_ketersediaan);
                $('#modalTitle').text('Ubah');
                $('#myModal').modal('show');
            });

            // Event click untuk tombol hapus
            $('#myTable').on('click', '.btn-delete', function() {
                var data = table.row($(this).closest('tr')).data();
                var Id = data.id_ketersediaan;
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
                            url: "{{ url('ketersediaan/delete') }}" + "/" + Id,
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
                    var url = "{{ route('ketersediaan.upsert') }}";
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
