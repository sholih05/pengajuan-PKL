@extends('layouts.main')
@section('title')
    Edit Siswa
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Edit Siswa</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('siswa') }}">Siswa</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Edit Data Siswa</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="container mt-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="siswaForm" action="{{ route('siswa.update', ['nis' => $siswa->nis, 'nisn' => $siswa->nisn]) }}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Step 1: Data Siswa -->
                    <div class="step" id="step-1">
                        <h4>Data Siswa</h4>
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="text" class="form-control" id="nis" name="nis"
                                value="{{ old('nis', $siswa->nis) }}" required maxlength="10" minlength="10">
                            <div class="invalid-feedback">NIS wajib diisi dan harus 10 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" id="nisn" name="nisn"
                                value="{{ old('nisn', $siswa->nisn) }}" required maxlength="10" minlength="10">
                            <div class="invalid-feedback">NISN wajib diisi dan harus 10 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama', $siswa->nama) }}" required maxlength="50">
                            <div class="invalid-feedback">Nama wajib diisi dan maksimal 50 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" required maxlength="20">
                            <div class="invalid-feedback">Tempat Lahir wajib diisi dan maksimal 20 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}" required>
                            <div class="invalid-feedback">Tanggal Lahir wajib diisi.</div>
                        </div>

                        <!-- Gender Radio Buttons -->
                        <div class="mb-3">
                            <label for="gender">Gender</label>
                            <div>
                                @foreach ($gender as $g)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender"
                                            id="gender_{{ $g }}" value="{{ $g }}"
                                            {{ old('gender', $siswa->gender) == $g ? 'checked' : '' }} required>
                                        <label class="form-check-label"
                                            for="gender_{{ $g }}">{{ $g }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="invalid-feedback">Gender wajib dipilih.</div>
                        </div>

                        <!-- Golongan Darah Radio Buttons -->
                        <div class="mb-3">
                            <label for="golongan_darah">Golongan Darah</label>
                            <div>
                                @foreach ($gol_dar as $gd)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="golongan_darah"
                                            id="golongan_darah_{{ $gd }}" value="{{ $gd }}"
                                            {{ old('golongan_darah', $siswa->golongan_darah) == $gd ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="golongan_darah_{{ $gd }}">{{ $gd }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="invalid-feedback">Golongan darah wajib dipilih.</div>
                        </div>


                        <div class="mb-3">
                            <label for="id_jurusan">Jurusan</label>
                            <select class="form-control" id="id_jurusan" name="id_jurusan" required>
                                <option value="">Pilih</option>
                                @foreach ($jurusan as $item)
                                    <option value="{{ $item->id_jurusan }}"
                                        {{ old('id_jurusan', $siswa->id_jurusan) == $item->id_jurusan ? 'selected' : '' }}>
                                        {{ $item->jurusan }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Jurusan wajib dipilih.</div>
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <img id="preview" src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : '' }}" alt="Preview Foto"
                                 style="{{ $siswa->foto ? '' : 'display:none;' }} margin-top: 10px; max-width: 200px; height: auto;">

                        </div>

                        <!-- Step Navigation Buttons -->
                        <button type="button" class="btn btn-primary" onclick="validateStep(1)">Next</button>
                    </div>

                    <!-- Step 2: Kontak dan Alamat -->
                    <div class="step d-none" id="step-2">
                        <h4>Kontak dan Alamat</h4>
                        <div class="mb-3">
                            <label for="no_kontak" class="form-label">Nomor Kontak</label>
                            <input type="number" class="form-control" id="no_kontak" name="no_kontak"
                                value="{{ old('no_kontak', $siswa->no_kontak) }}" required maxlength="14">
                            <div class="invalid-feedback">Nomor Kontak wajib diisi dan maksimal 14 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $siswa->email) }}" required maxlength="35">
                            <div class="invalid-feedback">Email wajib diisi dan maksimal 35 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required maxlength="225">{{ old('alamat', $siswa->alamat) }}</textarea>
                            <div class="invalid-feedback">Alamat wajib diisi dan maksimal 225 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select class="form-control" id="kelas" name="kelas" required>
                                <option value="">Pilih</option>
                                <option value="X" {{ old('kelas', $siswa->kelas) == 'X' ? 'selected' : '' }}>X</option>
                                <option value="XI" {{ old('kelas', $siswa->kelas) == 'XI' ? 'selected' : '' }}>XI</option>
                                <option value="XII" {{ old('kelas', $siswa->kelas) == 'XII' ? 'selected' : '' }}>XII</option>
                            </select>
                            <div class="invalid-feedback">Kelas wajib dipilih.</div>
                        </div>

                        <!-- Step Navigation Buttons -->
                        <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Previous</button>
                        <button type="button" class="btn btn-primary" onclick="validateStep(2)">Next</button>
                    </div>

                    <!-- Step 3: Data Wali -->
                    <div class="step d-none" id="step-3">
                        <h4>Wali</h4>
                        <div class="mb-3">
                            <label for="nama_wali" class="form-label">Nama Wali</label>
                            <input type="text" class="form-control" id="nama_wali" name="nama_wali"
                                value="{{ old('nama_wali', $siswa->nama_wali) }}" required maxlength="35">
                            <div class="invalid-feedback">Nama Wali wajib diisi dan maksimal 35 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat_wali" class="form-label">Alamat Wali</label>
                            <textarea class="form-control" id="alamat_wali" name="alamat_wali" rows="3" required maxlength="225">{{ old('alamat_wali', $siswa->alamat_wali) }}</textarea>
                            <div class="invalid-feedback">Alamat Wali wajib diisi dan maksimal 225 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="no_kontak_wali" class="form-label">Nomor Kontak Wali</label>
                            <input type="number" class="form-control" id="no_kontak_wali" name="no_kontak_wali"
                                value="{{ old('no_kontak_wali', $siswa->no_kontak_wali) }}" required maxlength="14">
                            <div class="invalid-feedback">Nomor Kontak Wali wajib diisi dan maksimal 14 karakter.</div>
                        </div>

                        <!-- Step Navigation Buttons -->
                        <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Previous</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#foto').on('change', function() {
            var file = this.files[0];
            if (file) {
                // Validasi tipe file
                var fileType = file.type;
                var validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validImageTypes.includes(fileType)) {
                    alert('Hanya file gambar yang diizinkan (JPG, PNG, GIF, WEBP)');
                    $(this).val(''); // Kosongkan input file
                    $('#preview').hide(); // Sembunyikan preview
                    return;
                }

                // Pratinjau gambar
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#preview').hide();
            }
        });
    });
</script>
    <script>
        function validateStep(step) {
            let valid = true;
            const inputs = document.querySelectorAll(`#step-${step} .form-control, #step-${step} .form-check-input`);

            inputs.forEach(input => {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (valid) {
                nextStep(step + 1);
            }
        }

        function nextStep(step) {
            document.querySelectorAll('.step').forEach((el) => {
                el.classList.add('d-none');
            });
            document.getElementById('step-' + step).classList.remove('d-none');
        }

        function prevStep(step) {
            document.querySelectorAll('.step').forEach((el) => {
                el.classList.add('d-none');
            });
            document.getElementById('step-' + step).classList.remove('d-none');
        }
    </script>
@endsection