@extends('layouts.app')

@section('content')
<h1>Edit Pengajuan Surat PKL</h1>
<form action="{{ route('pengajuan.update', $pengajuan->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Nama Siswa:</label>
    <input type="text" name="nama_siswa" value="{{ $pengajuan->nama_siswa }}" required>
    <label>Kelas:</label>
    <input type="text" name="kelas" value="{{ $pengajuan->kelas }}" required>
    <label>Perusahaan Tujuan:</label>
    <input type="text" name="perusahaan_tujuan" value="{{ $pengajuan->perusahaan_tujuan }}" required>
    <label>Tanggal Pengajuan:</label>
    <input type="date" name="tanggal_pengajuan" value="{{ $pengajuan->tanggal_pengajuan }}" required>
    <label>Status:</label>
    <select name="status">
        <option value="Pending" {{ $pengajuan->status == 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Disetujui" {{ $pengajuan->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
        <option value="Ditolak" {{ $pengajuan->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
    </select>
    <button type="submit">Simpan</button>
</form>
@endsection
