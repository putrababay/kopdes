<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::create('nasabahs', function (Blueprint $table) {
        $table->id();
        $table->string('nik')->unique();
        $table->string('nama');
        $table->text('alamat');
        $table->date('tgl_lahir')->nullable();
        $table->string('kota_lahir')->nullable();
        $table->string('no_tlp');
        $table->string('pekerjaan')->nullable();
        $table->string('foto')->nullable();
        $table->string('username');
        $table->string('password');
        $table->enum('level', ['ADMIN', 'NASABAH', 'NASABAH MEMBER']);
        $table->string('lat')->nullable();
        $table->string('lng')->nullable();
        $table->timestamps();
    });
}

};