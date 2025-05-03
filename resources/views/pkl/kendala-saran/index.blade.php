@extends('layouts.main')
@section('title')
    Kendala & Saran
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Kendala & Saran</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">PKL</li>
                <li class="breadcrumb-item active">Kendala & Saran</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Data Kendala & Saran</h5>
                <div>
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
                            <th>Tanggal</th>
                            <th>Instruktur</th>
                            <th>Kategori</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                </table>
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
                                    Semua Instruktur
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="download_option" id="instrukturOption"
                                    value="1">
                                <label class="form-check-label" for="instrukturOption">
                                    Satu Instruktur
                                </label>
                            </div>
                        </div>

                        <!-- Select Instruktur (Hidden by default) -->
                        <div class="mb-3" id="InstrukturSelectContainer" style="display:none;">
                            <label for="id_instruktur" class="form-label">Instruktur</label>
                            <select class="form-select" id="id_instruktur" name="id_instruktur" style="width: 100%;">
                            </select>
                            <div class="invalid-feedback"> Instruktur wajib dipilih. </div>
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
                    $("#InstrukturSelectContainer").show();
                } else {
                    // Jika memilih "All", sembunyikan select NIS
                    $("#InstrukturSelectContainer").hide();
                }
            });

            // Set default state
            if ($("#allOption").is(":checked")) {
                $("#InstrukturSelectContainer").hide();
            } else {
                $("#InstrukturSelectContainer").show();
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
                    var id = $('#id_instruktur').val();
                    if (!id) {
                        alert("Instruktur wajib dipilih.");
                        return;
                    }
                }

                // Menyembunyikan modal setelah klik tombol download
                $('#downloadModal').modal('hide');
                var id_ta = $('#id_ta').val();
                window.open("{{ route('kendala-saran.downloadExcel') }}?id=" + id+"&id_ta="+id_ta, "_blank");
            });
        });
    </script>
    <script>
        $(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kendala-saran.data') }}",
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
                        data: 'nama_instruktur',
                        name: 'nama_instruktur'
                    },
                    {
                        data: 'kategori_name',
                        name: 'kategori'
                    },
                    {
                        data: 'catatan',
                        name: 'catatan'
                    },
                ]
            });
            $('#id_ta').on('change', function() {
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
                    var newOptionNis = new Option(data.nis + ' - ' + data.nama_siswa, data.nis, true, true);
                    $('#nis').append(newOptionNis).trigger('change');
                }

                if (data.id_guru) {
                    var newOptionGuru = new Option(data.id_guru + ' - ' + data.nama_guru, data.id_guru,
                        true, true);
                    $('#id_guru').append(newOptionGuru).trigger('change');
                }

                if (data.id_instruktur) {
                    var newOptionInstruktur = new Option(data.id_instruktur + ' - ' + data.nama_instruktur +
                        ' - ' + data.nama_dudi, data.id_instruktur, true, true);
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

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#id_instruktur').select2({
                dropdownParent: $("#downloadModal"),
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
