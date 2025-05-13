@extends('layouts.main')
@section('title')
Template Penilaian
@endsection
@section('pagetitle')
<div class="pagetitle">
    <h1>Template Penilaian</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">PKL</li>
            <li class="breadcrumb-item active">Template Penilaian</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h5>Data Template Penilaian</h5>
            <div>
                <button class="btn btn-primary btn-sm" id="btnAdd"><i class="bi bi-plus-square"></i> Tambah</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="id_jurusan_filter" class="form-label">Jurusan</label>
            <select class="form-select" id="id_jurusan_filter" name="id_jurusan_filter">
                <option value="">Semua Jurusan</option>
                @foreach ($jurusans as $jurusan)
                <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->jurusan }} ({{ $jurusan->singkatan }})</option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="myTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Template</th>
                        <th>Jurusan</th>
                    
                        <th>Dibuat Oleh</th>
                        <th>Tanggal Dibuat</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Template Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="myForm" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" id="stt" name="stt" value="0">
                    <input type="hidden" id="id_template" name="id_template" value="0">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_template" class="form-label">Nama Template</label>
                                <input type="text" class="form-control" id="nama_template" name="nama_template"
                                    required>
                                <div class="invalid-feedback">Nama template wajib diisi.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jurusan_id" class="form-label">Jurusan</label>
                                <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->jurusan }}
                                        ({{ $jurusan->singkatan }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Jurusan wajib dipilih.</div>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2"></textarea>
                    </div>

                    <hr class="my-4">

                    <h5>Indikator Penilaian</h5>
                    <p class="text-muted">Tambahkan indikator utama dan sub-indikator untuk template ini.</p>

                    <div id="indicators-container">
                        <!-- Template indikator utama akan ditambahkan di sini -->
                    </div>

                    <div class="mb-3">
                        <button type="button" id="add-main-indicator" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Indikator Utama
                        </button>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Terapkan Template -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Terapkan Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="applyForm" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" id="template_id" name="template_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_ta" class="form-label">Tahun Akademik</label>
                        <select class="form-select" id="id_ta" name="id_ta" required>
                            <option value="">Pilih Tahun Akademik</option>
                            @foreach ($thnAkademik as $ta)
                            @if ($ta->id_ta == $aktifAkademik->id_ta)
                            <option value="{{ $ta->id_ta }}" selected>{{ $ta->tahun_akademik }} (aktif)</option>
                            @else
                            <option value="{{ $ta->id_ta }}">{{ $ta->tahun_akademik }}</option>
                            @endif
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Tahun akademik wajib dipilih.</div>
                    </div>
                    <div class="mb-3">
                        <label for="id_guru" class="form-label">Guru Pembimbing</label>
                        <select class="form-select" id="id_guru" name="id_guru" required>
                            <option value="">Pilih Guru</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->id_guru }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Guru pembimbing wajib dipilih.</div>
                    </div>
                    <p class="text-muted">
                        Template ini akan diterapkan untuk membuat indikator penilaian (Program Observer) baru.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Template -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Template Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Nama Template</th>
                                <td id="detail_nama_template"></td>
                            </tr>
                            <tr>
                                <th>Jurusan</th>
                                <td id="detail_jurusan"></td>
                            </tr>
                           
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                           
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td id="detail_created_by"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td id="detail_created_at"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h5>Indikator Penilaian</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_indicators_table">
                        <thead>
                            <tr class="bg-light">
                                <th width="5%">No.</th>
                                <th>Indikator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Indikator akan ditampilkan di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnApplyFromDetail">Terapkan Template</button>
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
    $(function () {
        var table = $('#myTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! route('template-penilaian.data') !!}",
                type: 'GET',
                data: function (d) {
                    d.jurusan_id = $('#id_jurusan_filter').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nama_template',
                    name: 'nama_template'
                },
                {
                    data: 'jurusan.jurusan',
                    name: 'jurusan.jurusan',
                    render: function (data, type, row) {
                        return data + ' (' + row.jurusan.singkatan + ')';
                    }
                },
               
                {
                    data: 'creator.username',
                    name: 'creator.username',
                    defaultContent: 'N/A'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data) {
                        var date = new Date(data);
                        return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString(
                            'id-ID');
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm btn-detail" data-id="${row.id}"><i class="bi bi-eye"></i></button>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="${row.id}"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="btn btn-success btn-sm btn-apply" data-id="${row.id}"><i class="bi bi-check2-circle"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${row.id}"><i class="bi bi-trash"></i></button>
                                </div>
                            `;
                    }
                }
            ]
        });

        $('#id_jurusan_filter').on('change', function () {
            table.ajax.reload();
        });

        // Tambah Template
        $('#btnAdd').click(function () {
            $('#myForm')[0].reset();
            $('#stt').val('0');
            $('#id_template').val('0');
            $('#modalTitle').text('Tambah Template Penilaian');

            // Hapus semua indikator yang ada
            $('#indicators-container').empty();

            // Tambahkan indikator utama pertama secara otomatis
            addMainIndicator();

            $('#myModal').modal('show');
        });

        // Detail Template
        $('#myTable').on('click', '.btn-detail', function () {
            var id = $(this).data('id');

            $.ajax({
                url: "{{ url('template-penilaian/details') }}/" + id,
                method: "GET",
                dataType: "json",
                success: function (response) {
                    $('#detail_nama_template').text(response.nama_template);
                    $('#detail_jurusan').text(response.jurusan.jurusan + ' (' + response
                        .jurusan.singkatan + ')');
                    $('#detail_created_by').text(response.creator ? response.creator
                        .username : 'N/A');

                    var date = new Date(response.created_at);
                    $('#detail_created_at').text(date.toLocaleDateString('id-ID') + ' ' +
                        date.toLocaleTimeString('id-ID'));

                    // Tampilkan indikator
                    var tbody = $('#detail_indicators_table tbody');
                    tbody.empty();

                    if (response.main_items && response.main_items.length > 0) {
                        $.each(response.main_items, function (mainIndex, main) {
                            // Tambahkan indikator utama
                            tbody.append(`
                                    <tr class="bg-light">
                                        <td>${mainIndex + 1}</td>
                                        <td><strong>${main.indikator}</strong></td>
                                    </tr>
                                `);

                            // Tambahkan sub-indikator
                            if (main.children && main.children.length > 0) {
                                $.each(main.children, function (subIndex, sub) {
                                    tbody.append(`
                                            <tr>
                                                <td>${mainIndex + 1}.${subIndex + 1}</td>
                                                <td class="ps-4">${sub.indikator}</td>
                                            </tr>
                                        `);
                                });
                            }
                        });
                    } else {
                        tbody.append(`
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada indikator</td>
                                </tr>
                            `);
                    }

                    // Simpan ID template untuk tombol terapkan
                    $('#btnApplyFromDetail').data('id', id);

                    $('#detailModal').modal('show');
                },
                error: function () {
                    Toast.fire({
                        icon: "error",
                        title: 'Gagal memuat detail template.'
                    });
                }
            });
        });

        // Terapkan Template dari Detail
        $('#btnApplyFromDetail').click(function () {
            var id = $(this).data('id');
            $('#template_id').val(id);
            $('#detailModal').modal('hide');
            $('#applyModal').modal('show');
        });

        // Edit Template
        $('#myTable').on('click', '.btn-edit', function () {
            var id = $(this).data('id');

            $.ajax({
                url: "{{ url('template-penilaian') }}/" + id + "/edit",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    $('#stt').val('1');
                    $('#id_template').val(response.id);
                    $('#nama_template').val(response.nama_template);
                    $('#jurusan_id').val(response.jurusan_id);
                    $('#deskripsi').val(response.deskripsi);

                    // Hapus semua indikator yang ada
                    $('#indicators-container').empty();

                    // Tambahkan indikator dari response
                    if (response.main_items && response.main_items.length > 0) {
                        $.each(response.main_items, function (mainIndex, main) {
                            // Tambahkan indikator utama
                            addMainIndicatorWithData(main);
                        });
                    } else {
                        // Tambahkan indikator utama kosong jika tidak ada
                        addMainIndicator();
                    }

                    $('#modalTitle').text('Edit Template Penilaian');
                    $('#myModal').modal('show');
                },
                error: function () {
                    Toast.fire({
                        icon: "error",
                        title: 'Gagal memuat data template.'
                    });
                }
            });
        });

        // Terapkan Template
        $('#myTable').on('click', '.btn-apply', function () {
            var id = $(this).data('id');
            $('#template_id').val(id);

            // Reset form
            $('#applyForm')[0].reset();

            // Set default tahun akademik aktif
            $('#id_ta').val('{{ $aktifAkademik->id_ta }}');

            // Load guru berdasarkan jurusan template
            loadGuruByTemplate(id);

            $('#applyModal').modal('show');
        });

        // Hapus Template
        $('#myTable').on('click', '.btn-delete', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: "Apakah anda yakin?",
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('template-penilaian') }}/" + id,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
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
                        error: function () {
                            Toast.fire({
                                icon: "error",
                                title: 'Woops! Fatal Error.'
                            });
                        }
                    });
                }
            });
        });

        // Submit Form Template
        $('#myForm').on('submit', function (e) {
            e.preventDefault();
            console.log('Form submitted');

            var form = $(this)[0];

            if (form.checkValidity() === false) {
                e.stopPropagation();
                form.classList.add('was-validated');
            } else {
                var url = "{{ route('template-penilaian.store') }}";
                var method = "POST";

                if ($('#stt').val() == '1') {
                    url = "{{ url('template-penilaian') }}/" + $('#id_template').val();
                    method = "PUT";
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
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
                    error: function (xhr) {
                        console.log(xhr);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';

                            $.each(errors, function (key, value) {
                                errorMessage += value[0] + '<br>';
                            });

                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: 'Woops! Fatal Error.'
                            });
                        }
                    }
                });
            }
        });

        // Submit Form Apply Template
        $('#applyForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this)[0];

            if (form.checkValidity() === false) {
                e.stopPropagation();
                form.classList.add('was-validated');
            } else {
                var url = "{{ url('template-penilaian') }}/" + $('#template_id').val() + "/apply";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#applyModal').modal('hide');

                        if (response.status) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });

                            // Redirect ke halaman program observer
                            setTimeout(function () {
                                window.location.href =
                                    "{{ route('template-penilaian.index') }}";
                            }, 1500);
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    },
                    error: function () {
                        Toast.fire({
                            icon: "error",
                            title: 'Woops! Fatal Error.'
                        });
                    }
                });
            }
        });

        // Fungsi untuk menambahkan indikator utama
        function addMainIndicator() {
            // Gunakan counter sederhana, bukan timestamp
            const mainIndicatorCount = document.querySelectorAll('.main-indicator').length + 1;
            const mainIndicatorDiv = document.createElement('div');
            mainIndicatorDiv.className = 'main-indicator card mb-4';
            mainIndicatorDiv.innerHTML = `
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <input type="text" class="form-control" 
                    name="main_indicators[${mainIndicatorCount}][text]" 
                    placeholder="Indikator Utama" required>
                <div class="invalid-feedback">Indikator utama wajib diisi.</div>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-main">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body">
            <div class="sub-indicators">
                <!-- Sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-info btn-sm add-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Sub-Indikator
            </button>
        </div>
    `;

            document.getElementById('indicators-container').appendChild(mainIndicatorDiv);

            // Tambahkan event listener untuk tombol tambah sub-indikator
            const addSubButton = mainIndicatorDiv.querySelector('.add-sub');
            addSubButton.addEventListener('click', function () {
                addSubIndicator(this.previousElementSibling, mainIndicatorCount);
            });

            // Tambahkan event listener untuk tombol hapus indikator utama
            const removeMainButton = mainIndicatorDiv.querySelector('.remove-main');
            removeMainButton.addEventListener('click', function () {
                mainIndicatorDiv.remove();
                // Perbarui urutan setelah menghapus
                updateIndicatorOrder();
            });

            // Tambahkan sub-indikator pertama secara otomatis
            addSubIndicator(mainIndicatorDiv.querySelector('.sub-indicators'), mainIndicatorCount);
        }

        // Fungsi untuk menambahkan indikator utama dengan data
        function addMainIndicatorWithData(main) {
            const mainIndicatorCount = document.querySelectorAll('.main-indicator').length + 1;
            const mainIndicatorDiv = document.createElement('div');
            mainIndicatorDiv.className = 'main-indicator card mb-4';
            mainIndicatorDiv.innerHTML = `
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <input type="text" class="form-control" 
                    name="main_indicators[${mainIndicatorCount}][text]" 
                    value="${main.indikator}"
                    placeholder="Indikator Utama" required>
                <input type="hidden" name="main_indicators[${mainIndicatorCount}][id]" value="${main.id}">
                <div class="invalid-feedback">Indikator utama wajib diisi.</div>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-main">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body">
            <div class="sub-indicators">
                <!-- Sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-info btn-sm add-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Sub-Indikator
            </button>
        </div>
    `;

            document.getElementById('indicators-container').appendChild(mainIndicatorDiv);

            // Tambahkan event listener untuk tombol tambah sub-indikator
            const addSubButton = mainIndicatorDiv.querySelector('.add-sub');
            addSubButton.addEventListener('click', function () {
                addSubIndicator(this.previousElementSibling, mainIndicatorCount);
            });

            // Tambahkan event listener untuk tombol hapus indikator utama
            const removeMainButton = mainIndicatorDiv.querySelector('.remove-main');
            removeMainButton.addEventListener('click', function () {
                mainIndicatorDiv.remove();
                updateIndicatorOrder();
            });

            // Tambahkan sub-indikator dari data
            const subContainer = mainIndicatorDiv.querySelector('.sub-indicators');

            if (main.children && main.children.length > 0) {
                $.each(main.children, function (subIndex, sub) {
                    addSubIndicatorWithData(subContainer, mainIndicatorCount, sub);
                });
            } else {
                // Tambahkan sub-indikator kosong jika tidak ada
                addSubIndicator(subContainer, mainIndicatorCount);
            }
        }

        // Fungsi untuk menambahkan sub-indikator
        function addSubIndicator(subContainer, mainIndex) {
            // Gunakan counter sederhana untuk sub-indikator
            const subIndicatorCount = subContainer.querySelectorAll('.input-group').length + 1;
            const subIndicatorDiv = document.createElement('div');
            subIndicatorDiv.className = 'input-group mb-2';
            subIndicatorDiv.innerHTML = `
        <input type="text" class="form-control" 
            name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][text]" 
            placeholder="Sub-Indikator" required>
        <button type="button" class="btn btn-outline-danger remove-sub">
            <i class="bi bi-trash"></i>
        </button>
        <div class="invalid-feedback">Sub-indikator wajib diisi.</div>
    `;

            subContainer.appendChild(subIndicatorDiv);

            // Tambahkan event listener untuk tombol hapus sub-indikator
            const removeSubButton = subIndicatorDiv.querySelector('.remove-sub');
            removeSubButton.addEventListener('click', function () {
                subIndicatorDiv.remove();
                // Perbarui urutan sub-indikator setelah menghapus
                updateSubIndicatorOrder(subContainer, mainIndex);
            });
        }

        // Fungsi untuk menambahkan sub-indikator dengan data
        function addSubIndicatorWithData(subContainer, mainIndex, sub) {
            const subIndicatorCount = subContainer.querySelectorAll('.input-group').length + 1;
            const subIndicatorDiv = document.createElement('div');
            subIndicatorDiv.className = 'input-group mb-2';
            subIndicatorDiv.innerHTML = `
        <input type="text" class="form-control" 
            name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][text]" 
            value="${sub.indikator}"
            placeholder="Sub-Indikator" required>
        <input type="hidden" name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][id]" value="${sub.id}">
        <button type="button" class="btn btn-outline-danger remove-sub">
            <i class="bi bi-trash"></i>
        </button>
        <div class="invalid-feedback">Sub-indikator wajib diisi.</div>
    `;

            subContainer.appendChild(subIndicatorDiv);

            // Tambahkan event listener untuk tombol hapus sub-indikator
            const removeSubButton = subIndicatorDiv.querySelector('.remove-sub');
            removeSubButton.addEventListener('click', function () {
                subIndicatorDiv.remove();
                updateSubIndicatorOrder(subContainer, mainIndex);
            });
        }

        // Fungsi untuk memperbarui urutan indikator setelah penghapusan
        function updateIndicatorOrder() {
            const mainIndicators = document.querySelectorAll('.main-indicator');
            mainIndicators.forEach((mainIndicator, index) => {
                const inputs = mainIndicator.querySelectorAll('input[name^="main_indicators"]');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/main_indicators\[\d+\]/,
                        `main_indicators[${index + 1}]`);
                    input.setAttribute('name', newName);
                });

                // Perbarui juga event listener untuk sub-indikator
                const addSubButton = mainIndicator.querySelector('.add-sub');
                if (addSubButton) {
                    const oldHandler = addSubButton.onclick;
                    addSubButton.onclick = function () {
                        addSubIndicator(this.previousElementSibling, index + 1);
                    };
                }

                // Perbarui urutan sub-indikator
                updateSubIndicatorOrder(mainIndicator.querySelector('.sub-indicators'), index + 1);
            });
        }

        // Fungsi untuk memperbarui urutan sub-indikator
        function updateSubIndicatorOrder(subContainer, mainIndex) {
            const subIndicators = subContainer.querySelectorAll('.input-group');
            subIndicators.forEach((subIndicator, index) => {
                const input = subIndicator.querySelector('input');
                const name = input.getAttribute('name');
                const newName = name.replace(/main_indicators\[\d+\]\[sub_indicators\]\[\d+\]/,
                    `main_indicators[${mainIndex}][sub_indicators][${index + 1}]`);
                input.setAttribute('name', newName);
            });
        }

        // Fungsi untuk memuat guru berdasarkan template
        function loadGuruByTemplate(templateId) {
            $.ajax({
                url: "{{ url('template-penilaian') }}/" + templateId + "/guru",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    var select = $('#id_guru');
                    // select.empty();
                    select.append('<option value="">Pilih Guru</option>');

                    $.each(response, function (index, guru) {
                        select.append('<option value="' + guru.id_guru + '">' + guru.nama +
                            '</option>');
                    });
                },
                error: function () {
                    Toast.fire({
                        icon: "error",
                        title: 'Gagal memuat data guru.'
                    });
                }
            });
        }

        // Event listener untuk tombol tambah indikator utama
        $('#add-main-indicator').click(addMainIndicator);
    });

</script>
@endsection

@section('css')
<link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
<link href="{{ asset('assets') }}/vendor/select2/css/select2.min.css" rel="stylesheet">
<link href="{{ asset('assets') }}/vendor/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection
