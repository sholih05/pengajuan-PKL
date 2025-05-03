@extends('layouts.main')
@section('title')
    Instruktur
@endsection
@section('pagetitle')
    <div class="pagetitle">
        <h1>Instruktur</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Instruktur</li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5>Tambah Data Instruktur</h5>
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

                <form action="{{ route('instruktur.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="id_instruktur" class="form-label">ID Instruktur</label>
                        <input type="text" class="form-control @error('id_instruktur') is-invalid @enderror" id="id_instruktur" name="id_instruktur" value="{{ old('id_instruktur') }}" required maxlength="15">
                        <div class="invalid-feedback">
                            ID Instruktur wajib diisi dan maksimal 15 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required maxlength="50">
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
                                        {{ old('gender') == $g ? 'checked' : '' }} required>
                                    <label class="form-check-label"
                                        for="gender_{{ $g }}">{{ $g }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="invalid-feedback">
                            Gender wajib dipilih.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="no_kontak" class="form-label">Nomor Kontak</label>
                        <input type="number" class="form-control @error('no_kontak') is-invalid @enderror" id="no_kontak" name="no_kontak" value="{{ old('no_kontak') }}" required maxlength="14">
                        <div class="invalid-feedback">
                            Nomor kontak wajib diisi dan maksimal 14 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="35">
                        <div class="invalid-feedback">
                            Email wajib diisi dan maksimal 35 karakter.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" maxlength="100">{{ old('alamat') }}</textarea>
                        <div class="invalid-feedback">
                            Alamat maksimal 100 karakter.
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="id_dudi" class="form-label">DUDI</label>
                        <select class="form-select @error('id_dudi') is-invalid @enderror" id="id_dudi" name="id_dudi" required>
                            <option value="">Pilih</option>
                            @foreach ($dudi as $item)
                                <option value="{{ $item->id_dudi }}" {{ old('id_dudi') == $item->id_dudi ? 'selected' : '' }}>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Dudi wajib dipilih.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
@endsection
