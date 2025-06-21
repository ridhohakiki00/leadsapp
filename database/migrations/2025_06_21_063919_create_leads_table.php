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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique(); // ganti id dengan nomor custom
            $table->date('tanggal')->nullable();
            $table->string('nama')->nullable();
            $table->string('nohp')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('tipe')->nullable();
            $table->string('warna')->nullable();
            $table->string('leasing')->nullable();
            $table->string('tenor')->nullable();
            $table->date('tanggal_kredit')->nullable();
            $table->string('asuransi')->nullable();
            $table->decimal('hargajual', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->string('status')->nullable();
            $table->string('distribusi')->nullable();
            $table->string('salesman')->nullable();
            $table->string('followup')->nullable();
            $table->string('statusfollowup')->nullable();
            $table->date('tglfollowup')->nullable();
            $table->string('hasilfollowup')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
