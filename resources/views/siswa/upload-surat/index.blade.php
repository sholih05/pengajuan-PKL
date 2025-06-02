
<!DOCTYPE html>
@extends('layouts.main')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD File Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Upload File Balasan Dudi</h1>

        <!-- Form Upload -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('d.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File:</label>
                        <input type="file" class="form-control" name="file" id="file" accept=".pdf,.doc,.docx,.png,." required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>

        <!-- Daftar File -->
        <h2 class="mb-3">Daftar File</h2>
        @if ($files->isNotEmpty())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file->file_name }}</td>
                            <td>{{ $file->uploaded_at }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('d.edit', $file->id) }}" class="btn btn-warning">Edit</a>

                                    <a href="{{ route('d.delete', $file->id) }}" class="btn btn-danger">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">Tidak ada file yang diunggah.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
