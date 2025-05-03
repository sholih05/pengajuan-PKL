@extends('layouts.main')
@section('title')
    Guru
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Guru</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Guru</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Edit Data Guru</h5>
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

                <form action="{{ route('guru.update', $guru->id_guru) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="id_guru" class="form-label">ID Guru</label>
                        <input type="text" class="form-control @error('id_guru') is-invalid @enderror" id="id_guru" name="id_guru" value="{{ old('id_guru', $guru->id_guru) }}" required maxlength="15">
                        <div class="invalid-feedback">
                            ID Guru wajib diisi dan maksimal 15 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}" required maxlength="50">
                        <div class="invalid-feedback">
                            Nama wajib diisi dan maksimal 50 karakter.
                        </div>
                    </div>

                    <!-- Gender Radio Buttons -->
                    <div class="mb-3">
                        <label for="gender">Gender</label>
                        <div>
                            @foreach ($gender as $g)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender"
                                        id="gender_{{ $g }}" value="{{ $g }}"
                                        {{ old('gender', $guru->gender) == $g ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="gender_{{ $g }}">{{ $g }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="invalid-feedback">
                            Gender wajib dipilih.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="no_kontak" class="form-label">Nomor Kontak</label>
                        <input type="number" class="form-control @error('no_kontak') is-invalid @enderror" id="no_kontak" name="no_kontak" value="{{ old('no_kontak', $guru->no_kontak) }}" required maxlength="14">
                        <div class="invalid-feedback">
                            Nomor kontak wajib diisi dan maksimal 14 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $guru->email) }}" required maxlength="35">
                        <div class="invalid-feedback">
                            Email wajib diisi dan maksimal 35 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" maxlength="100">{{ old('alamat', $guru->alamat) }}</textarea>
                        <div class="invalid-feedback">
                            Alamat maksimal 100 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_jurusan" class="form-label">Jurusan</label>
                        <option value="">Pilih</option>
                        <select class="form-select @error('id_jurusan') is-invalid @enderror" id="id_jurusan" name="id_jurusan" required>
                            @foreach ($jurusan as $item)
                                <option value="{{ $item->id_jurusan }}" {{ old('id_jurusan', $guru->id_jurusan) == $item->id_jurusan ? 'selected' : '' }}>{{ $item->jurusan }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Jurusan wajib dipilih.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

@endsection
