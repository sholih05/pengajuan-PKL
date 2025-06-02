@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1>Detail File</h1>
    <table class="table table-bordered">
        <tr>
            <th>Nama File</th>
            <td>{{ $file->file_name }}</td>
        </tr>
        <tr>
            <th>Pengunggah</th>
            <td>{{ $file->uploader_name ?? 'Tidak Diketahui' }}</td>
        </tr>
        <tr>
            <th>Tanggal Upload</th>
            <td>{{ $file->uploaded_at->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <th>Lokasi Penyimpanan</th>
            <td>{{ $file->file_path }}</td>
        </tr>
    </table>
    <a href="{{ route('guru.files') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
