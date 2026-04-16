<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
public function index(Request $request)
{
    $bulan = $request->get('bulan', date('m'));
    $tahun = $request->get('tahun', date('Y'));
    $filter = "$tahun-$bulan";
    
    // Data Dasar
    $data['nama_user'] = "Bayu Anggara"; // Bisa diambil dari Auth::user()->name
    $data['jum_all'] = DB::table('master_nasabah')->count();
    $data['jum_prose_dos'] = DB::table('master_pinjam')->whereRaw("DATE_FORMAT(tgl_pinjam, '%Y-%m') = ?", [$filter])->count();
    $data['angsuran'] = DB::table('angsuran')->whereRaw("DATE_FORMAT(tgl, '%Y-%m') = ?", [$filter])->count();

    // Arus Kas Hari Ini vs Kemarin
    $fnGetKas = function($tgl) {
        $masuk = DB::table('angsuran')->where('tgl', 'like', $tgl . '%')->sum('nominal') ?? 0;
        $keluar = (DB::table('master_pinjam')->where('tgl_pinjam', 'like', $tgl . '%')->sum('pinjam') ?? 0) + 
                  (DB::table('tabungan')->where('tgl_ambil', 'like', $tgl . '%')->sum('ambil') ?? 0);
        return ['masuk' => $masuk, 'keluar' => $keluar, 'sisa' => $masuk - $keluar];
    };
    $data['hari_ini'] = $fnGetKas(date('Y-m-d'));
    $data['kemarin'] = $fnGetKas(date('Y-m-d', strtotime("-1 days")));

// Baterai Progress (Filtered Month)
$data['masuk_bln'] = DB::table('angsuran')->whereRaw("DATE_FORMAT(tgl, '%Y-%m') = ?", [$filter])->sum('nominal') ?? 0;

$data['keluar_bln'] = (DB::table('master_pinjam')->whereRaw("DATE_FORMAT(tgl_pinjam, '%Y-%m') = ?", [$filter])->sum('pinjam') ?? 0) + 
                     (DB::table('tabungan')->whereRaw("DATE_FORMAT(tgl_ambil, '%Y-%m') = ?", [$filter])->sum('ambil') ?? 0);

// Hitung Sisa (Margin)
$data['nominal_margin'] = $data['masuk_bln'] - $data['keluar_bln'];

// Rumus Persentase Margin:
// Jika Margin <= 0, maka 0% (Baterai Habis)
// Jika ada sisa, hitung berapa persen sisa tersebut dari total uang masuk
if ($data['masuk_bln'] > 0) {
    $persen = ($data['nominal_margin'] / $data['masuk_bln']) * 100;
    // Kita batasi minimal 0 dan maksimal 100
    $data['persen_margin'] = max(0, min(100, round($persen)));
} else {
    $data['persen_margin'] = 0;
}
    

    // DATA GRAFIK (Per Tahun)
    $grafik_raw = DB::select("
        SELECT 
            m.bulan,
            COALESCE(p.total_keluar, 0) as keluar,
            COALESCE(a.total_masuk, 0) as masuk,
            COALESCE(p.count_pinjam, 0) as jumlah_pinjam
        FROM (SELECT 1 AS bulan UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 
              UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) m
        LEFT JOIN (
            SELECT MONTH(tgl_pinjam) as bln, SUM(pinjam) as total_keluar, COUNT(*) as count_pinjam 
            FROM master_pinjam WHERE YEAR(tgl_pinjam) = ? GROUP BY MONTH(tgl_pinjam)
        ) p ON m.bulan = p.bln
        LEFT JOIN (
            SELECT MONTH(tgl) as bln, SUM(nominal) as total_masuk 
            FROM angsuran WHERE YEAR(tgl) = ? GROUP BY MONTH(tgl)
        ) a ON m.bulan = a.bln
    ", [$tahun, $tahun]);

    $data['chart_masuk'] = collect($grafik_raw)->pluck('masuk');
    $data['chart_keluar'] = collect($grafik_raw)->pluck('keluar');
    $data['chart_sisa'] = collect($grafik_raw)->map(fn($item) => $item->masuk - $item->keluar);
    $data['chart_count_pinjam'] = collect($grafik_raw)->pluck('jumlah_pinjam');

    return view('admin.dashboard', compact('data', 'bulan', 'tahun'));
}
}