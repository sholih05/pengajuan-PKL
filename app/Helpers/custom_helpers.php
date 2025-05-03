<?php

use App\Models\ThnAkademik;
use Carbon\Carbon;

use function PHPUnit\Framework\returnSelf;

if (!function_exists('role_text')) {
    /**
     * Format angka menjadi text.
     *
     * @param float $id
     * @return string
     */
    function role_text($id)
    {
        switch ($id) {
            case 1:
                return 'Super Admin';
                break;
            case 2:
                return 'Admin';
                break;
            case 3:
                return 'Guru';
                break;
            case 4:
                return 'Instruktur';
                break;
            case 5:
                return 'Siswa';
                break;

            default:
                return '';
                break;
        }
    }
}
if (!function_exists('getActiveAcademicYear')) {
    /**
     * Format angka menjadi text.
     *
     * @param float $id
     * @return string
     */
    function getActiveAcademicYear()
    {
        $today = Carbon::today()->toDateString();

        return ThnAkademik::where('is_active', 1)
            ->where('mulai', '<=', $today)
            ->where('selesai', '>=', $today)
            ->first();
    }
}

if (!function_exists('handlePhotoUpload')) {
    /**
     * Handle photo upload and deletion of old photo if exists
     *
     * @param UploadedFile|null $file
     * @param string|null $oldFileName
     * @return string|null
     */
    function handlePhotoUpload($file, $oldFileName = null)
    {
        if (!$file) {
            return $oldFileName; // Return existing file name if no new file uploaded
        }

        // Delete old file if exists
        if ($oldFileName) {
            $oldFilePath = storage_path('app/public/uploads/foto/' . $oldFileName);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Save new file to storage
        $fileName = microtime(true) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('uploads/foto', $fileName, 'public');

        return $fileName; // Return the new file name
    }
}
