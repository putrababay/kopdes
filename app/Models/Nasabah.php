<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Nasabah extends Authenticatable
{
    use Notifiable;

    protected $table = 'master_nasabah';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nik',
        'nama',
        'alamat',
        'tgl_lahir',
        'kota_lahir',
        'no_tlp',
        'pekerjaan',
        'foto',
        'username',
        'password',
        'level',
        'lat',
        'lng',
        'tgl_daftar'
    ];

    /**
     * Kolom yang harus disembunyikan saat serialisasi (JSON/Array).
     * Ini akan mencegah username dan password muncul di Inspect Element/JS.
     */
    protected $hidden = [
        'password',
        'username',
        'remember_token', // Tambahkan ini jika Anda menggunakan fitur login
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }



    // app/Models/Nasabah.php
    public function pinjamans()
    {
        return $this->hasMany(Pinjam::class, 'id_nasaba');
    }

    // app/Models/Nasabah.php
    // app/Models/Nasabah.php

    public function pulsaPinjam()
    {
        // Jika di database kolomnya bernama 'id_nasabah' (pakai h)
        return $this->hasMany(MasterPulsaPinjam::class, 'id_pulsa', 'id');

        // ATAU jika di database kolomnya bernama 'nasabah_id'
        // return $this->hasMany(MasterPulsaPinjam::class, 'nasabah_id', 'id');
    }
}
