<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pinjam extends Model
{
    use HasFactory;

    protected $table = 'master_pinjam';

    protected $fillable = [
        'id_nasaba',
        'tabungan',
        'pinjam',
        'angsuran',
        't_pinjam',
        'tgl_pinjam',
        'status',
        'ket',
        'tempo_hari',
        'lokasi_penarikan',
        'pembayaran',
        'jaminan',
        'tgl_akhir',
        'detail_tgl'
    ];
    public $timestamps = false; // Jika tabel benar-benar tidak punya kolom tersebut

    // Relasi balik ke Nasabah
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'id_nasaba');
    }
}
