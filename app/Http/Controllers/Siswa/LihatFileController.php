<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\FileModel;
use Illuminate\Http\Request;

class LihatFileController extends Controller
{
    //
    public function index()
    {
        $files = FileModel::all(); // Ambil semua file dari database
        return view('guru.profile', compact('files'));
    }

    public function download($id)
    {
        $file = FileModel::findOrFail($id);
        $filePath = storage_path('app/public/' . $file->file_path);

        if (file_exists($filePath)) {
            return response()->download($filePath, $file->file_name);
        }

        return redirect()->route('guru.files')->with('error', 'File tidak ditemukan!');
    }

    public function viewDetails($id)
    {
        $file = FileModel::findOrFail($id);
        return view('guru.files.details', compact('file'));
    }

}
