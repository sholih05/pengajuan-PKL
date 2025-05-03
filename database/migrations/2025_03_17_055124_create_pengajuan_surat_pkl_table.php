<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanSuratTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_surat_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa', 255);
            $table->string('kelas', 100);
            $table->string('perusahaan_tujuan', 255);
            $table->date('tanggal_pengajuan');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('aksi', 255)->nullable();
            $table->timestamps(); // Automatically creates `created_at` and `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_surat_pkl');
    }
}
