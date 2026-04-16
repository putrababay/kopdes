<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Nasabah extends Authenticatable
{
    use Notifiable;

    protected $table = 'master_nasabah';
    public $timestamps = false; // Matikan jika kolom created_at tidak ada

    protected $fillable = [
        'id', 'nik', 'nama', 'alamat', 'tgl_lahir', 'kota_lahir', 
        'no_tlp', 'pekerjaan', 'foto', 'username', 'password', 
        'level', 'lat', 'lng', 'tgl_daftar'
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
}