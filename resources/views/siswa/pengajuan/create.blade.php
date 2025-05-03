@extends('layouts.app')

@section('content')
<h1>Tambah Pengajuan Surat PKL</h1>
<form action="{{ route('pengajuan.store') }}" method="POST">
    @csrf
    <label>Nama Siswa:</label>
    <input type="text" name="nama_siswa" required>
    <label>Kelas:</label>
    <input type="text" name="kelas" required>
    <label>Perusahaan Tujuan:</label>
    <input type="text" name="perusahaan_tujuan" required>
    <label>Tanggal Pengajuan:</label>
    <input type="date" name="tanggal_pengajuan" required>
    <button type="submit">Simpan</button>
</form>
@endsection
