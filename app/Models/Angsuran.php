<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Angsuran extends Authenticatable
{
    use Notifiable;

    protected $table = 'angsuran';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_pinjam',
        'nominal',
        'tgl',
        'angsuran',
        'status'
    ];

    /**
     * Kolom yang harus disembunyikan saat serialisasi (JSON/Array).
     * Ini akan mencegah username dan password muncul di Inspect Element/JS.
     */



    // app/Models/Nasabah.php
    public function pinjamans()
    {
        return $this->hasMany(Pinjam::class, 'id_nasaba');
    }
}
