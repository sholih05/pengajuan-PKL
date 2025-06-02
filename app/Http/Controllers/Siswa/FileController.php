<?php

namespace App\Http\Controllers\Siswa;

use App\Models\FileModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\ThnAkademik;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $nis = $request->has('nis') ? $request->nis : session('nis');

        // Ambil data siswa beserta relasi terkait dengan kondisi where pada nis siswa
        $siswa = Siswa::with(['jurusan', 'user', 'penempatan.instruktur.dudi', 'penempatan.tahunAkademik'])
            ->where('nis', $nis)
            ->firstOrFail();


        $activeAcademicYear = getActiveAcademicYear();
        $ta = ThnAkademik::where('is_active', true)->orderBy('id_ta', 'desc')->get();

        $files = FileModel::all(); 

        $files = FileModel::all(); // Ambil semua file
        return view('siswa.profile', compact('siswa', 'activeAcademicYear', 'ta','files'));
    }
    public function edit($id)
    {
        $file = FileModel::findOrFail($id);
        return view('siswa.upload-surat.edit', compact('file'));
    }


    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,png|max:2048',
        ]);

        // Cek apakah file ada
        if ($request->hasFile('file')) {
            // Ambil file
            $file = $request->file('file');

            // Buat nama unik untuk file
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file ke folder storage/public/uploads
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            // Simpan metadata file ke database
            FileModel::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'uploaded_at' => now(),
            ]);

            return redirect()->route('d.upload-surat')->with('success', 'File berhasil diunggah!');
        }

        return redirect()->route('d.upload-surat')->with('error', 'Gagal mengunggah file.');
    }

    public function delete($id)
    {
        $file = FileModel::findOrFail($id);

        // Hapus file dari storage
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Hapus data dari database
        $file->delete();

        return redirect()->route('d.siswa')->with('success', 'File berhasil dihapus!');
    }


    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'new_name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg|max:2048',
        ]);

        $file = FileModel::findOrFail($id);
        $newName = $request->input('new_name');

        // Jika file baru diunggah
        if ($request->hasFile('file')) {
            // Simpan file baru
            $newFile = $request->file('file');
            $newFileName = time() . '_' . $newFile->getClientOriginalName();
            $newFilePath = $newFile->storeAs('uploads', $newFileName, 'public');

            // Hapus file lama
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Update metadata di database
            $file->update([
                'file_name' => $newName,
                'file_path' => $newFilePath,
            ]);
        } else {
            // Update hanya nama file jika file baru tidak diunggah
            $file->update(['file_name' => $newName]);
        }

        return redirect()->route('d.siswa')->with('success', 'File berhasil dihapus!');
    }


}
