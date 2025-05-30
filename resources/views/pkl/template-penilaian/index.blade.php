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
    <div class="modal-dialog modal-xl" role="document">
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
                                <input type="text" class="form-control" id="nama_template" name="nama_template" required>
                                <div class="invalid-feedback">Nama template wajib diisi.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jurusan_id" class="form-label">Jurusan</label>
                                <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->jurusan }} ({{ $jurusan->singkatan }})</option>
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

                    <h5>Indikator Penilaian (3 Tingkat)</h5>
                    <p class="text-muted">Tambahkan indikator utama, sub-indikator, dan sub-sub-indikator untuk template ini.</p>

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

<!-- Modal Detail Template -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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

                <h5>Indikator Penilaian (3 Tingkat)</h5>
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
                        return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID');
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
            $('#detail_jurusan').text(response.jurusan.jurusan + ' (' + response.jurusan.singkatan + ')');
            $('#detail_created_by').text(response.creator ? response.creator.username : 'N/A');

            var date = new Date(response.created_at);
            $('#detail_created_at').text(date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID'));

            // Tampilkan indikator dengan struktur level yang jelas
            var tbody = $('#detail_indicators_table tbody');
            tbody.empty();

            if (response.main_items && response.main_items.length > 0) {
                $.each(response.main_items, function (mainIndex, main) {
                    // Level 1 - Main Indicator
                    tbody.append(`
                        <tr class="bg-primary text-white">
                            <td><strong>${mainIndex + 1}</strong></td>
                            <td><strong>${main.indikator}</strong></td>
                        </tr>
                    `);

                    // Level 2 - Sub Indicators
                    if (main.children && main.children.length > 0) {
                        $.each(main.children, function (subIndex, sub) {
                            tbody.append(`
                                <tr class="bg-info text-white">
                                    <td><strong>${mainIndex + 1}.${subIndex + 1}</strong></td>
                                    <td class="ps-3"><strong>${sub.indikator}</strong> ${sub.is_nilai ? '<span class="badge bg-success">Dinilai</span>' : '<span class="badge bg-secondary">Kalkulasi</span>'}</td>
                                </tr>
                            `);

                            // Level 3 - Sub-Sub Indicators (jika ada)
                            if (sub.level3_children && sub.level3_children.length > 0) {
                                $.each(sub.level3_children, function (subSubIndex, subSub) {
                                    tbody.append(`
                                        <tr class="bg-warning">
                                            <td><strong>${mainIndex + 1}.${subIndex + 1}.${subSubIndex + 1}</strong></td>
                                            <td class="ps-5">${subSub.indikator} <span class="badge bg-success">Dinilai</span></td>
                                        </tr>
                                    `);
                                });
                            }
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


$('#myTable').on('click', '.btn-edit', function () {
    var id = $(this).data('id');

    $.ajax({
        url: "{{ url('template-penilaian') }}/" + id + "/edit",
        method: "GET",
        dataType: "json",
        success: function (response) {
            console.log('Edit response:', response); // Debug log

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
                    addMainIndicatorWithData(main, mainIndex);
                });
            } else {
                // Tambahkan indikator utama kosong jika tidak ada
                addMainIndicator();
            }

            $('#modalTitle').text('Edit Template Penilaian');
            $('#myModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error('Edit error:', xhr.responseText);
            Toast.fire({
                icon: "error",
                title: 'Gagal memuat data template: ' + error
            });
        }
    });
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


        // Update fungsi untuk menambahkan indikator dengan informasi level yang lebih jelas
function addMainIndicator() {
    const mainIndicatorCount = document.querySelectorAll('.main-indicator').length + 1;
    const mainIndicatorDiv = document.createElement('div');
    mainIndicatorDiv.className = 'main-indicator card mb-4';
    mainIndicatorDiv.innerHTML = `
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <label class="form-label text-white mb-1"><strong>Level 1 - Indikator Utama</strong></label>
                <input type="text" class="form-control"
                    name="main_indicators[${mainIndicatorCount}][text]"
                    placeholder="Masukkan indikator utama..." required>
                <div class="invalid-feedback">Indikator utama wajib diisi.</div>
            </div>
            <button type="button" class="btn btn-light btn-sm remove-main">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body">
            <div class="sub-indicators">
                <!-- Sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-info btn-sm add-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Level 2 (Sub-Indikator)
            </button>
        </div>
    `;

    document.getElementById('indicators-container').appendChild(mainIndicatorDiv);

    // Event listeners
    const addSubButton = mainIndicatorDiv.querySelector('.add-sub');
    addSubButton.addEventListener('click', function () {
        addSubIndicator(this.previousElementSibling, mainIndicatorCount);
    });

    const removeMainButton = mainIndicatorDiv.querySelector('.remove-main');
    removeMainButton.addEventListener('click', function () {
        mainIndicatorDiv.remove();
        updateIndicatorOrder();
    });

    // Tambahkan sub-indikator pertama secara otomatis
    addSubIndicator(mainIndicatorDiv.querySelector('.sub-indicators'), mainIndicatorCount);
}

        // Fungsi untuk menambahkan indikator utama dengan data (updated)
function addMainIndicatorWithData(main, mainIndex) {
    const mainIndicatorCount = mainIndex + 1;
    const mainIndicatorDiv = document.createElement('div');
    mainIndicatorDiv.className = 'main-indicator card mb-4';
    mainIndicatorDiv.innerHTML = `
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <label class="form-label text-white mb-1"><strong>Level 1 - Indikator Utama</strong></label>
                <input type="text" class="form-control"
                    name="main_indicators[${mainIndicatorCount}][text]"
                    value="${escapeHtml(main.indikator)}"
                    placeholder="Masukkan indikator utama..." required>
                <input type="hidden" name="main_indicators[${mainIndicatorCount}][id]" value="${main.id}">
                <div class="invalid-feedback">Indikator utama wajib diisi.</div>
            </div>
            <button type="button" class="btn btn-light btn-sm remove-main">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>
        <div class="card-body">
            <div class="sub-indicators">
                <!-- Sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-info btn-sm add-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Level 2 (Sub-Indikator)
            </button>
        </div>
    `;

    document.getElementById('indicators-container').appendChild(mainIndicatorDiv);

    // Event listeners
    const addSubButton = mainIndicatorDiv.querySelector('.add-sub');
    addSubButton.addEventListener('click', function () {
        addSubIndicator(this.previousElementSibling, mainIndicatorCount);
    });

    const removeMainButton = mainIndicatorDiv.querySelector('.remove-main');
    removeMainButton.addEventListener('click', function () {
        if (confirm('Apakah Anda yakin ingin menghapus indikator utama ini beserta semua sub-indikatornya?')) {
            mainIndicatorDiv.remove();
            updateIndicatorOrder();
        }
    });

    // Tambahkan sub-indikator dari data
    const subContainer = mainIndicatorDiv.querySelector('.sub-indicators');

    if (main.children && main.children.length > 0) {
        $.each(main.children, function (subIndex, sub) {
            addSubIndicatorWithData(subContainer, mainIndicatorCount, sub, subIndex);
        });
    } else {
        // Tambahkan sub-indikator kosong jika tidak ada
        addSubIndicator(subContainer, mainIndicatorCount);
    }
}

        function addSubIndicator(subContainer, mainIndex) {
    const subIndicatorCount = subContainer.querySelectorAll('.sub-indicator-group').length + 1;
    const subIndicatorDiv = document.createElement('div');
    subIndicatorDiv.className = 'sub-indicator-group card mb-3';
    subIndicatorDiv.innerHTML = `
        <div class="card-header  text-white d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <label class="form-label text-white mb-1"><strong>Level 2 - Sub Indikator</strong></label>
                <input type="text" class="form-control"
                    name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][text]"
                    placeholder="Masukkan sub-indikator..." required>
                <div class="invalid-feedback">Sub-indikator wajib diisi.</div>
                <small class="text-white-50">
                    <i class="bi bi-info-circle"></i>
                    Jika tidak ada Level 3, maka Level 2 ini yang akan dinilai
                </small>
            </div>
            <button type="button" class="btn btn-light btn-sm remove-sub">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="sub-sub-indicators">
                <!-- Sub-sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-warning btn-sm add-sub-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Level 3 (Opsional)
            </button>
            <small class="text-muted d-block mt-1">
                <i class="bi bi-lightbulb"></i>
                Level 3 bersifat opsional. Jika ditambahkan, maka Level 3 yang akan dinilai.
            </small>
        </div>
    `;

    subContainer.appendChild(subIndicatorDiv);

    // Event listeners
    const addSubSubButton = subIndicatorDiv.querySelector('.add-sub-sub');
    addSubSubButton.addEventListener('click', function () {
        addSubSubIndicator(this.previousElementSibling, mainIndex, subIndicatorCount);
    });

    const removeSubButton = subIndicatorDiv.querySelector('.remove-sub');
    removeSubButton.addEventListener('click', function () {
        subIndicatorDiv.remove();
        updateSubIndicatorOrder(subContainer, mainIndex);
    });
}

        // Fungsi untuk menambahkan sub-indikator dengan data (updated)
function addSubIndicatorWithData(subContainer, mainIndex, sub, subIndex) {
    const subIndicatorCount = subIndex + 1;
    const subIndicatorDiv = document.createElement('div');
    subIndicatorDiv.className = 'sub-indicator-group card mb-3';

    // Tentukan status penilaian
    const isNilaiStatus = sub.is_nilai ?
        '<span class="badge bg-success">Dinilai</span>' :
        '<span class="badge bg-secondary">Kalkulasi</span>';

    subIndicatorDiv.innerHTML = `
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="flex-grow-1 me-2">
                <label class="form-label text-white mb-1">
                    <strong>Level 2 - Sub Indikator</strong> ${isNilaiStatus}
                </label>
                <input type="text" class="form-control"
                    name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][text]"
                    value="${escapeHtml(sub.indikator)}"
                    placeholder="Masukkan sub-indikator..." required>
                <input type="hidden" name="main_indicators[${mainIndex}][sub_indicators][${subIndicatorCount}][id]" value="${sub.id}">
                <div class="invalid-feedback">Sub-indikator wajib diisi.</div>
                <small class="text-white-50">
                    <i class="bi bi-info-circle"></i>
                    ${sub.is_nilai ? 'Level ini akan dinilai (tidak ada Level 3)' : 'Level ini dikalkulasi dari Level 3'}
                </small>
            </div>
            <button type="button" class="btn btn-light btn-sm remove-sub">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="sub-sub-indicators">
                <!-- Sub-sub-indikator akan ditambahkan di sini -->
            </div>
            <button type="button" class="btn btn-warning btn-sm add-sub-sub mt-2">
                <i class="bi bi-plus-circle"></i> Tambah Level 3 (Opsional)
            </button>
            <small class="text-muted d-block mt-1">
                <i class="bi bi-lightbulb"></i>
                Level 3 bersifat opsional. Jika ditambahkan, maka Level 3 yang akan dinilai.
            </small>
        </div>
    `;

    subContainer.appendChild(subIndicatorDiv);

    // Event listeners
    const addSubSubButton = subIndicatorDiv.querySelector('.add-sub-sub');
    addSubSubButton.addEventListener('click', function () {
        addSubSubIndicator(this.previousElementSibling, mainIndex, subIndicatorCount);
    });

    const removeSubButton = subIndicatorDiv.querySelector('.remove-sub');
    removeSubButton.addEventListener('click', function () {
        if (confirm('Apakah Anda yakin ingin menghapus sub-indikator ini beserta semua Level 3-nya?')) {
            subIndicatorDiv.remove();
            updateSubIndicatorOrder(subContainer, mainIndex);
        }
    });

    // Tambahkan sub-sub-indikator dari data
    const subSubContainer = subIndicatorDiv.querySelector('.sub-sub-indicators');

    if (sub.level3_children && sub.level3_children.length > 0) {
        $.each(sub.level3_children, function (subSubIndex, subSub) {
            addSubSubIndicatorWithData(subSubContainer, mainIndex, subIndicatorCount, subSub, subSubIndex);
        });
    }
    // Tidak menambahkan sub-sub-indikator kosong secara otomatis untuk edit
}

        function addSubSubIndicator(subSubContainer, mainIndex, subIndex) {
    const subSubIndicatorCount = subSubContainer.querySelectorAll('.input-group').length + 1;
    const subSubIndicatorDiv = document.createElement('div');
    subSubIndicatorDiv.className = 'input-group mb-2';
    subSubIndicatorDiv.innerHTML = `
        <span class="input-group-text">
            <small><strong>Level 3</strong></small>
        </span>
        <input type="text" class="form-control"
            name="main_indicators[${mainIndex}][sub_indicators][${subIndex}][sub_sub_indicators][${subSubIndicatorCount}][text]"
            placeholder="Masukkan detail indikator yang akan dinilai..." required>
        <button type="button" class="btn btn-outline-danger remove-sub-sub">
            <i class="bi bi-trash"></i>
        </button>
        <div class="invalid-feedback">Sub-sub-indikator wajib diisi.</div>
    `;

    subSubContainer.appendChild(subSubIndicatorDiv);

    // Event listener untuk tombol hapus sub-sub-indikator
    const removeSubSubButton = subSubIndicatorDiv.querySelector('.remove-sub-sub');
    removeSubSubButton.addEventListener('click', function () {
        subSubIndicatorDiv.remove();
        updateSubSubIndicatorOrder(subSubContainer, mainIndex, subIndex);
    });
}

        // Fungsi untuk menambahkan sub-sub-indikator dengan data (updated)
function addSubSubIndicatorWithData(subSubContainer, mainIndex, subIndex, subSub, subSubIndex) {
    const subSubIndicatorCount = subSubIndex + 1;
    const subSubIndicatorDiv = document.createElement('div');
    subSubIndicatorDiv.className = 'input-group mb-2';
    subSubIndicatorDiv.innerHTML = `
        <span class="input-group-text">
            <small><strong>Level 3</strong></small>
        </span>
        <input type="text" class="form-control"
            name="main_indicators[${mainIndex}][sub_indicators][${subIndex}][sub_sub_indicators][${subSubIndicatorCount}][text]"
            value="${escapeHtml(subSub.indikator)}"
            placeholder="Masukkan detail indikator yang akan dinilai..." required>
        <input type="hidden" name="main_indicators[${mainIndex}][sub_indicators][${subIndex}][sub_sub_indicators][${subSubIndicatorCount}][id]" value="${subSub.id}">
        <button type="button" class="btn btn-outline-danger remove-sub-sub">
            <i class="bi bi-trash"></i>
        </button>
        <div class="invalid-feedback">Sub-sub-indikator wajib diisi.</div>
    `;

    subSubContainer.appendChild(subSubIndicatorDiv);

    // Event listener untuk tombol hapus sub-sub-indikator
    const removeSubSubButton = subSubIndicatorDiv.querySelector('.remove-sub-sub');
    removeSubSubButton.addEventListener('click', function () {
        if (confirm('Apakah Anda yakin ingin menghapus item Level 3 ini?')) {
            subSubIndicatorDiv.remove();
            updateSubSubIndicatorOrder(subSubContainer, mainIndex, subIndex);
        }
    });
}

        // Update fungsi untuk memperbarui urutan dengan handling yang lebih baik
function updateIndicatorOrder() {
    const mainIndicators = document.querySelectorAll('.main-indicator');
    mainIndicators.forEach((mainIndicator, index) => {
        const newIndex = index + 1;

        // Update main indicator inputs
        const mainInputs = mainIndicator.querySelectorAll('input[name^="main_indicators"]');
        mainInputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/main_indicators\[\d+\]/, `main_indicators[${newIndex}]`);
            input.setAttribute('name', newName);
        });

        // Update sub indicators
        const subContainer = mainIndicator.querySelector('.sub-indicators');
        if (subContainer) {
            updateSubIndicatorOrder(subContainer, newIndex);
        }

        // Update event listener untuk add-sub button
        const addSubButton = mainIndicator.querySelector('.add-sub');
        if (addSubButton) {
            // Remove old event listener dan tambah yang baru
            const newAddSubButton = addSubButton.cloneNode(true);
            addSubButton.parentNode.replaceChild(newAddSubButton, addSubButton);

            newAddSubButton.addEventListener('click', function () {
                addSubIndicator(this.previousElementSibling, newIndex);
            });
        }
    });
}

function updateSubIndicatorOrder(subContainer, mainIndex) {
    const subIndicators = subContainer.querySelectorAll('.sub-indicator-group');
    subIndicators.forEach((subIndicator, index) => {
        const newSubIndex = index + 1;

        // Update sub indicator inputs
        const subInputs = subIndicator.querySelectorAll('input[name*="sub_indicators"]');
        subInputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(
                /main_indicators\[\d+\]\[sub_indicators\]\[\d+\]/,
                `main_indicators[${mainIndex}][sub_indicators][${newSubIndex}]`
            );
            input.setAttribute('name', newName);
        });

        // Update sub-sub indicators
        const subSubContainer = subIndicator.querySelector('.sub-sub-indicators');
        if (subSubContainer) {
            updateSubSubIndicatorOrder(subSubContainer, mainIndex, newSubIndex);
        }

        // Update event listener untuk add-sub-sub button
        const addSubSubButton = subIndicator.querySelector('.add-sub-sub');
        if (addSubSubButton) {
            const newAddSubSubButton = addSubSubButton.cloneNode(true);
            addSubSubButton.parentNode.replaceChild(newAddSubSubButton, addSubSubButton);

            newAddSubSubButton.addEventListener('click', function () {
                addSubSubIndicator(this.previousElementSibling, mainIndex, newSubIndex);
            });
        }
    });
}

function updateSubSubIndicatorOrder(subSubContainer, mainIndex, subIndex) {
    const subSubIndicators = subSubContainer.querySelectorAll('.input-group');
    subSubIndicators.forEach((subSubIndicator, index) => {
        const newSubSubIndex = index + 1;

        const input = subSubIndicator.querySelector('input[name*="sub_sub_indicators"]:not([type="hidden"])');
        const hiddenInput = subSubIndicator.querySelector('input[type="hidden"]');

        if (input) {
            const name = input.getAttribute('name');
            const newName = name.replace(
                /main_indicators\[\d+\]\[sub_indicators\]\[\d+\]\[sub_sub_indicators\]\[\d+\]/,
                `main_indicators[${mainIndex}][sub_indicators][${subIndex}][sub_sub_indicators][${newSubSubIndex}]`
            );
            input.setAttribute('name', newName);
        }

        if (hiddenInput) {
            const name = hiddenInput.getAttribute('name');
            const newName = name.replace(
                /main_indicators\[\d+\]\[sub_indicators\]\[\d+\]\[sub_sub_indicators\]\[\d+\]/,
                `main_indicators[${mainIndex}][sub_indicators][${subIndex}][sub_sub_indicators][${newSubSubIndex}]`
            );
            hiddenInput.setAttribute('name', newName);
        }
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
                    select.empty();
                    select.append('<option value="">Pilih Guru</option>');

                    $.each(response, function (index, guru) {
                        select.append('<option value="' + guru.id_guru + '">' + guru.nama + '</option>');
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

        // Fungsi helper untuk escape HTML
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
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
<style>
    .main-indicator {
    }
    .sub-indicator-group {
        margin-left: 20px;
    }
    .sub-sub-indicators {
        margin-left: 20px;
    }
    .card-header {
        font-weight: 600;
    }
</style>
@endsection