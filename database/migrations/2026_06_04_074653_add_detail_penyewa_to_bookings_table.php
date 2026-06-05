<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->string('nama_penyewa');
        $table->string('no_hp');
        $table->string('email')->nullable();
        $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
        $table->date('tanggal_masuk');
        $table->string('durasi_sewa');
        $table->integer('jumlah_penghuni')->default(1);
        $table->text('catatan')->nullable();

        
        $table->string('status_pembayaran')->default('belum_bayar');
    });
}

public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn([
            'nama_penyewa',
            'no_hp',
            'email',
            'jenis_kelamin',
            'tanggal_masuk',
            'durasi_sewa',
            'jumlah_penghuni',
            'catatan',
            'status_pembayaran',
        ]);
    });
}
};
