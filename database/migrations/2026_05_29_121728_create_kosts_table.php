<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kosts', function (Blueprint $table) {
            $table->id();

            // Pemilik kos
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Informasi kos
            $table->string('nama_kost');
            
            $table->text('alamat');
            $table->integer('harga_per_bulan');

            $table->enum('jenis_kost', [
                'putra',
                'putri',
                'campur'
            ]);
            $table->boolean('tersedia')->default(true);
            // Fasilitas
            $table->json('fasilitas')->nullable();

            //jumlah kamar
            $table->integer('jumlah_kamar')->default(0);
            $table->integer('kamar_tersedia')->default(0);

            // Foto
            $table->string('thumbnail')->nullable();
            $table->string('gambar_kamar_mandi')->nullable();
            $table->string('gambar_interior')->nullable();
            $table->string('gambar_depan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kosts');
    }
};
