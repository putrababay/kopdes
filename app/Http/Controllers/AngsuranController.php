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
        $today = now()->format('Y-m-d');

        // 1. Ambil data nasabah (Query Pertama)
        $nasabahs = DB::table('master_pinjam')
            ->join('master_nasabah', 'master_pinjam.id_nasaba', '=', 'master_nasabah.id')
            ->select(
                'master_pinjam.*',
                'master_nasabah.nama',
                'master_nasabah.alamat',
                'master_nasabah.foto'
            )
            ->addSelect('master_pinjam.id as id_pinjam')
            ->where('master_pinjam.tempo_hari', $harifilter)
            ->where('master_pinjam.status', '!=', 'LUNAS')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('master_nasabah.nama', 'LIKE', "%{$search}%")
                        ->orWhere('master_pinjam.lokasi_penarikan', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('master_nasabah.nama', 'ASC')
            ->simplePaginate(10);

        // 2. Ambil semua ID Pinjam yang muncul di halaman ini saja
        $ids = collect($nasabahs->items())->pluck('id_pinjam')->toArray();

        // 3. Ambil data angsuran hari ini untuk ID tersebut (Query Kedua - Efisien)
        $sudahBayar = DB::table('angsuran')
            ->whereIn('id_pinjam', $ids)
            ->where('tgl', 'LIKE', "{$today}%")
            ->pluck('nominal', 'id_pinjam'); // Menghasilkan array [id_pinjam => nominal]

        // 4. Tempelkan data angsuran ke dalam objek nasabahs
        $nasabahs->getCollection()->transform(function ($item) use ($sudahBayar) {
            $item->sudah_bayar = $sudahBayar[$item->id_pinjam] ?? null;
            return $item;
        });

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
            'id'        => $angsuran->id,
            'id_pinjam' => $angsuran->id_pinjam,
            'nama'      => $nasabah ? $nasabah->nama : 'Nama Tidak Ditemukan',
            'angsuran'  => $pinjam->angsuran,
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

    // public function destroy($id)
    // {
    //     try {
    //         // 1. Cari data angsuran berdasarkan ID
    //         $angsuran = DB::table('angsuran')->where('id', $id)->first();

    //         // 2. Jika data tidak ditemukan
    //         if (!$angsuran) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Data angsuran tidak ditemukan.'
    //             ], 404);
    //         }

    //         // 3. Proses Hapus
    //         DB::table('angsuran')->where('id', $id)->delete();

    //         // 4. Berikan respon sukses ke SweetAlert
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data angsuran berhasil dihapus.'
    //         ]);
    //     } catch (\Exception $e) {
    //         // Tangani jika ada error database
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menghapus data: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function destroy($id)
    {
        // Menggunakan Transaction agar jika salah satu gagal, semua dibatalkan
        DB::beginTransaction();

        try {
            // 1. Cari data angsuran berdasarkan ID sebelum dihapus untuk mendapatkan id_pinjam
            $angsuran = DB::table('angsuran')->where('id', $id)->first();

            // 2. Jika data tidak ditemukan
            if (!$angsuran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data angsuran tidak ditemukan.'
                ], 404);
            }

            $id_pinjam = $angsuran->id_pinjam;

            // 3. Proses Hapus data angsuran
            DB::table('angsuran')->where('id', $id)->delete();

            /**
             * LOGIKA UPDATE STATUS PINJAMAN
             */

            // 4. Ambil target total angsuran dari tabel nasabah_pinjam
            $pinjam = DB::table('master_pinjam')->where('id', $id_pinjam)->first();

            if ($pinjam) {
                // 5. Hitung jumlah angsuran yang tersisa di database untuk pinjaman ini
                $sisaAngsuran = DB::table('angsuran')->where('id_pinjam', $id_pinjam)->count();

                // 6. Jika jumlah angsuran sekarang lebih kecil dari target seharusnya, 
                //    maka status pinjaman harus AKTIF (bukan LUNAS lagi)
                if ($sisaAngsuran < $pinjam->angsuran) {
                    DB::table('master_pinjam')
                        ->where('id', $id_pinjam)
                        ->update(['status' => 'AKTIF']);
                }
            }

            // Simpan semua perubahan
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data angsuran dihapus dan status pinjaman diperbarui.'
            ]);
        } catch (\Exception $e) {
            // Batalkan perubahan jika terjadi error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan data angsuran dengan status default LUNAS
            DB::table('angsuran')->insert([
                'id_pinjam' => $request->id_pinjam,
                'nominal'   => $request->nominal,
                'tgl'       => $request->tgl, // Menerima format YYYY-MM-DD HH:mm:ss
                'angsuran'  => $request->angsuran,
                'status'    => $request->status ?? 'LUNAS'
            ]);

            // 2. Ambil info pinjaman
            $pinjam = DB::table('master_pinjam')->where('id', $request->id_pinjam)->first();

            if ($pinjam) {
                // 3. Hitung jumlah angsuran yang sudah dibayar
                $jumlahAngsuranSekarang = DB::table('angsuran')
                    ->where('id_pinjam', $request->id_pinjam)
                    ->count();

                // 4. Jika jumlah pembayaran sudah sesuai/melebihi target angsuran di master
                if ($jumlahAngsuranSekarang >= $pinjam->angsuran) {
                    DB::table('master_pinjam')
                        ->where('id', $request->id_pinjam)
                        ->update(['status' => 'LUNAS']);
                } else {
                    DB::table('master_pinjam')
                        ->where('id', $request->id_pinjam)
                        ->update(['status' => 'AKTIF']);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembayaran angsuran berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
