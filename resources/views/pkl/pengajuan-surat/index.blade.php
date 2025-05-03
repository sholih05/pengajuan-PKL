@extends('layouts.main')
@section('title', 'Pengajuan Surat')
@section('css')
    <link href="{{ asset('assets') }}/vendor/dataTables/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2.min.css" rel="stylesheet">
    <link href="{{ asset('assets') }}/vendor/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection
@section('content')
<div class="container">
    <h4 class="my-4">Pengajuan Surat</h4>
    <div class="d-flex justify-content-end">
        <button class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#pengajuanModal" onclick="resetForm()">Tambah Pengajuan</button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm" id="pengajuan-surat" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Jurusan</th>
                    <th>Perusahaan Tujuan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data akan dimuat oleh DataTables -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="pengajuanModal" tabindex="-1" aria-labelledby="pengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pengajuanModalLabel">Pengajuan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPengajuan">
                    <input type="hidden" id="pengajuanId" name="id">
                    @for ($i = 1; $i <= 4; $i++)
                    <div class="mb-3">
                        <label for="namaSiswa{{ $i }}" class="form-label">Nama Siswa {{ $i }}</label>
                        <select type="text" id="nis{{ $i }}" class="form-select" id="namaSiswa{{ $i }}" name="namaSiswa[]" required></select>
                    </div>
                    @endfor
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan" name="jurusan" required>
                    </div>
                    <div class="mb-3">
                        <label for="perusahaan" class="form-label">Perusahaan Tujuan</label>
                        <input type="text" class="form-control" id="perusahaan" name="perusahaan_tujuan" required>
                    </div> 
                    <div class="mb-3">
                        <label for="tanggalPengajuan" class="form-label">Tanggal Pengajuan</label>
                        <input type="date" class="form-control" id="tanggalPengajuan" name="tanggal_pengajuan" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- buton modal ditolak/distujui -->
<section>
<!-- Modal Keterangan Penolakan -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Keterangan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReject">
                    <input type="hidden" id="rejectId" name="id">
                    <div class="mb-3">
                        <label for="keteranganPenolakan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keteranganPenolakan" name="keterangan" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
</section>
<!-- pembuatan surat -->
<section>
<div class="modal fade" id="suratModal" tabindex="-1" aria-labelledby="suratModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suratModalLabel">Surat Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="suratModalBody">
                <!-- Surat akan dimuat di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary">Cetak Surat</button>
            </div>
        </div>
    </div>
</div>
</section>

@section('js')
<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/vendor/select2/js/select2.min.js"></script>

<script>
                            $(document).ready(function() {
                                    for (let i = 1; i <= 4; i++) {
                                        $(`#nis${i}`).select2({
                                            theme: 'bootstrap-5',
                                            dropdownParent: $("#pengajuanModal"),
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
                                                            var j = 0;
                                                            item.penempatan.forEach(el => {
                                                                if (el.id_ta == $('#id_ta').val()) {
                                                                    j++;
                                                                    txt = ` (Sudah di Tempatkan ${j}x)`;
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
                                    }
                                });
                        </script>

                        <!-- butoon ditolak/distujui -->
                        <script>
                           $(document).on('click', '.reject-btn', function() {
                            var id = $(this).data('id');
                            $('#rejectId').val(id);
                            $('#rejectModal').modal('show');
                        });

                        $('#formReject').on('submit', function(e) {
                            e.preventDefault();
                            var id = $('#rejectId').val();
                            var keterangan = $('#keteranganPenolakan').val();

                            $.ajax({
                                url: "{{ route('pengajuan.surat.reject', '') }}/" + id,
                                method: "PUT",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    keterangan: keterangan
                                },
                                success: function(response) {
                                    $('#rejectModal').modal('hide');
                                    $('#formReject')[0].reset();
                                    $('#pengajuan-surat').DataTable().ajax.reload();
                                    Toast.fire({ icon: "success", title: response.message });
                                },
                                error: function() {
                                    Toast.fire({ icon: "error", title: "Terjadi kesalahan" });
                                }
                            });
                        });
                        </script>
<!-- pembuatan surat -->
<script>
    $(document).on('click', '.approve-btn', function() {
    var id = $(this).data('id');

    $.ajax({
        url: "{{ route('pengajuan.surat.approve', '') }}/" + id,
        method: "PUT",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            $('#pengajuan-surat').DataTable().ajax.reload();
            Toast.fire({ icon: "success", title: response.message });

            // Tampilkan surat dalam modal
            $('#suratModalBody').html(`
                <p><strong>ID Pengajuan:</strong> ${response.surat.id}</p>
                <p><strong>Nama Siswa:</strong> ${response.surat.nama_siswa}</p>
                <p><strong>Jurusan:</strong> ${response.surat.jurusan}</p>
                <p><strong>Perusahaan Tujuan:</strong> ${response.surat.perusahaan_tujuan}</p>
                <p><strong>Tanggal Pengajuan:</strong> ${response.surat.tanggal_pengajuan}</p>
            `);
            $('#suratModal').modal('show');
        },
        error: function() {
            Toast.fire({ icon: "error", title: "Terjadi kesalahan" });
        }
    });
});
</script>
<script>
    $(function() {
        // Inisialisasi DataTables
        var tablePengajuan = $('#pengajuan-surat').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pengajuan.surat.getAll') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'namasiswa', name: 'namasiswa' },
                { data: 'jurusan', name: 'jurusan' },
                { data: 'perusahaan_tujuan', name: 'perusahaan_tujuan' },
                { data: 'tanggal_pengajuan', name: 'tanggal_pengajuan' },
                { data: 'status', name: 'status' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
            ],
            order: [[4, 'desc']], // Urutkan berdasarkan tanggal_pengajuan descending
        });

        // Submit Form Tambah/Edit
        $('#formPengajuan').on('submit', function(e) {
    e.preventDefault();
    var id = $('#pengajuanId').val();
    var url = id ? "{{ route('pengajuan.surat.update', '') }}/" + id : "{{ route('pengajuan.surat.store') }}";
    var method = id ? "PUT" : "POST";

    $.ajax({
        url: url,
        method: method,
        data: $(this).serialize(), // Pastikan field "tanggal_pengajuan" ada dalam serialize
        success: function(response) {
            $('#pengajuanModal').modal('hide');
            $('#formPengajuan')[0].reset();
            $('#pengajuan-surat').DataTable().ajax.reload();
            Toast.fire({ icon: "success", title: response.message });
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Tampilkan error pada console untuk debug
                console.error(xhr.responseJSON.errors);
            }
            Toast.fire({ icon: "error", title: "Terjadi kesalahan" });
        }
    });
});



        $('#pengajuan-surat').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            if (confirm("Yakin ingin menghapus data ini?")) {
                $.ajax({
                    url: "{{ route('pengajuan.surat.delete', '') }}/" + id,
                    method: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        $('#pengajuan-surat').DataTable().ajax.reload();
                        Toast.fire({ icon: "success", title: response.message });
                    },
                    error: function() {
                        Toast.fire({ icon: "error", title: "Terjadi kesalahan" });
                    }
                });
            }
        });
    });

    // Fungsi Edit
    function editData(id) {
        $.get("{{ route('pengajuan.surat.edit', '') }}/" + id, function(data) {
            $('#pengajuanModalLabel').text("Edit Pengajuan");
            $('#pengajuanId').val(data.id);
            $('#jurusan').val(data.jurusan);
            $('#perusahaan').val(data.perusahaan_tujuan);
            $('#tanggalPengajuan').val(data.tanggal_pengajuan);

            // Set nama siswa
            data.nama_siswa.forEach((nama, index) => {
                $(`#namaSiswa${index + 1}`).val(nama);
            });

            $('#pengajuanModal').modal('show');
        });
    }

    // Reset Form Tambah/Edit
    function resetForm() {
        $('#pengajuanModalLabel').text("Tambah Pengajuan");
        $('#formPengajuan')[0].reset();
        $('#pengajuanId').val('');
    }
 

</script>
@endsection


