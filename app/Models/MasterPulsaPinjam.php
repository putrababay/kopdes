<?php
// app/Models/MasterPulsaPinjam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPulsaPinjam extends Model
{
    use HasFactory;

    protected $table = 'master_pulsa_pinjam';
    protected $primaryKey = 'id_pinjam';
    public $timestamps = false;

   // app/Models/MasterPulsaPinjam.php

protected $fillable = [
    'id_pulsa',
    'id_nasabah', // sesuaikan di sini
    'nomer',
    'harga',
    'jam_tgl',
    'status'
];

public function nasabah()
{
    // Sesuaikan parameter kedua dengan nama kolom di database
    return $this->belongsTo(Nasabah::class, 'id_nasabah', 'id');
}

  
}