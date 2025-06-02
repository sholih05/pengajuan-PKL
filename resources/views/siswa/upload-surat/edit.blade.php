<!DOCTYPE html>
@extends('layouts.main')
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Nama File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Edit Nama File</h1>

    <form action="{{ route('d.update', $file->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- Gunakan PUT untuk update -->

        <div class="mb-3">
            <label for="new_name" class="form-label">Nama Baru:</label>
            <input type="text" class="form-control" id="new_name" name="new_name" value="{{ $file->file_name }}" required />
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Unggah File Baru (Opsional):</label>
            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.png">
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('d.upload-surat') }}" class="btn btn-secondary ms-2">Batal</a>
    </form>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
