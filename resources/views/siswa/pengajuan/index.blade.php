@extends('layouts.app')

@section('content')
<h1>Daftar Pengajuan Surat PKL</h1>
<a href="{{ route('pengajuan.create') }}">Tambah Pengajuan</a>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Perusahaan Tujuan</th>
            <th>Tanggal Pengajuan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pengajuan as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->nim }}</td>
            <td>{{ $item->kelas }}</td>
            <td>{{ $item->perusahaan_tujuan }}</td>
            <td>{{ $item->tanggal_pengajuan }}</td>
            <td>{{ $item->status }}</td>
            <td>
                <a href="{{ route('pengajuan.edit', $item->id) }}">Edit</a>
                <form action="{{ route('pengajuan.destroy', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
