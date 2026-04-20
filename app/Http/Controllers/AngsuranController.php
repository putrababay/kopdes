<?php
// METHOD 1: List utama (Optimasi Join untuk performa)
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        $hariIndo = [
            'Sun' => 'MINGGU',
            'Mon' => 'SENIN',
            'Tue' => 'SELASA',
            'Wed' => 'RABU',
            'Thu' => 'KAMIS',
            'Fri' => 'JUMAT',
            'Sat' => 'SABTU'
        ];
        $harifilter = $request->input('hari', $hariIndo[date('D')]);
        $search = $request->input('search');

        $nasabahs = DB::table('master_pinjam')
            ->join('master_nasabah', 'master_pinjam.id_nasaba', '=', 'master_nasabah.id')
            ->select(
                'master_pinjam.*',
                'master_nasabah.nama',
                'master_nasabah.alamat',
                'master_nasabah.foto'
            )
            // Menampilkan ID Pinjam sebagai identitas unik di list
            ->addSelect('master_pinjam.id as id_pinjam')
            ->where('master_pinjam.tempo_hari', $harifilter)
            ->where('master_pinjam.status', 'AKTIF')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('master_nasabah.nama', 'LIKE', "%{$search}%")
                        ->orWhere('master_pinjam.lokasi_penarikan', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('master_nasabah.nama', 'ASC')
            ->simplePaginate(10);

        if ($request->ajax()) {
            return view('admin.angsuran._item_list', compact('nasabahs'))->render();
        }

        return view('admin.angsuran.index', compact('nasabahs', 'harifilter'));
    }

    public function getDetailNasabah($id_pinjam)
    {
        // Gunakan JOIN di sini agar data nasabah dan pinjam langsung menyatu
        $dataLengkap = DB::table('master_pinjam')
            ->join('master_nasabah', 'master_pinjam.id_nasaba', '=', 'master_nasabah.id')
            ->select('master_pinjam.*', 'master_nasabah.*', 'master_pinjam.id as id_pinjam_asli')
            ->where('master_pinjam.id', $id_pinjam)
            ->first();

        if (!$dataLengkap) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $pinjam = DB::table('master_pinjam')
            ->select('id_nasaba', 't_pinjam', 'jaminan', 'angsuran', 'detail_tgl') // Pastikan detail_tgl ada
            ->where('id_nasaba', $id_pinjam)
            ->first();

        // Ambil riwayat angsuran dari tabel angsuran
        $angsuran = DB::table('angsuran')
            ->where('id_pinjam', $id_pinjam)
            ->orderByRaw('CAST(angsuran AS UNSIGNED) ASC')
            ->get();

        return response()->json([
            'nasabah' => $dataLengkap, // Frontend mengharapkan objek profil
            'pinjam'  => $dataLengkap, // Frontend mengharapkan detail pinjaman
            'angsuran' => $angsuran     // Frontend mengharapkan array riwayat
        ]);
    }
}
