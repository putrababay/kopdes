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



    public function printStruk($id)
    {
        // 1. Cari data angsuran
        $angsuran = DB::table('angsuran')->where('id', $id)->first();

        if (!$angsuran) {
            return "Data angsuran dengan ID $id tidak ditemukan.";
        }

        // 2. Ambil data nasabah dan pinjaman
        // Sesuai error Anda, $angsuran->id_pinjam bernilai 6462.
        // Kita akan mencari di tabel nasabah_pinjam. 
        // PASTIKAN: nama kolom ID di tabel nasabah_pinjam itu apa? (id? id_pinjam? no_pinjam?)

        $pinjam = DB::table('nasabah_pinjam')
            ->where('id', $angsuran->id_pinjam) // Jika kolomnya bukan 'id', ganti di sini
            ->first();

        if (!$pinjam) {
            // JIKA MASIH ERROR: Coba cari berdasarkan kolom id_pinjam (asumsi nama kolomnya sama)
            $pinjam = DB::table('nasabah_pinjam')->where('id_pinjam', $angsuran->id_pinjam)->first();
        }

        if (!$pinjam) {
            return "Data Pinjaman ID: " . $angsuran->id_pinjam . " tidak ditemukan di tabel nasabah_pinjam. 
                Cek apakah kolom Primary Key di tabel tersebut bernama 'id' atau 'id_pinjam'.";
        }

        // 3. Ambil data nasabah dari tabel master_nasabah berdasarkan NIK
        $nasabah = DB::table('master_nasabah')->where('nik', $pinjam->nik)->first();

        $tampil = [
            'id_pinjam' => $angsuran->id_pinjam,
            'nama'      => $nasabah ? $nasabah->nama : 'Nama Tidak Ditemukan',
            'no_tlp'    => $nasabah ? $nasabah->no_tlp : '',
            'nominal'   => $angsuran->nominal,
            'ke'        => $angsuran->angsuran,
            'tgl'       => $angsuran->tgl,
        ];

        return view('admin.angsuran.print_struk', compact('tampil'));
    }
    /**
     * Helper untuk merapikan nomor WhatsApp agar bisa digunakan di wa.me
     */
    private function formatWhatsApp($number)
    {
        // Hilangkan karakter non-digit
        $number = preg_replace('/[^0-9]/', '', $number);

        // Jika dimulai dengan 0, ubah ke 62
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }

    public function destroy($id)
    {
        try {
            // 1. Cari data angsuran berdasarkan ID
            $angsuran = DB::table('angsuran')->where('id', $id)->first();

            // 2. Jika data tidak ditemukan
            if (!$angsuran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data angsuran tidak ditemukan.'
                ], 404);
            }

            // 3. Proses Hapus
            DB::table('angsuran')->where('id', $id)->delete();

            // 4. Berikan respon sukses ke SweetAlert
            return response()->json([
                'success' => true,
                'message' => 'Data angsuran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani jika ada error database
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
