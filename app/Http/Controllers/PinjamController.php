<?php

namespace App\Http\Controllers;

use App\Models\Pinjam;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PinjamController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Mengambil kata kunci pencarian dan filter tahun (default tahun saat ini)
    //     $search = $request->search;
    //     $tahun = $request->get('tahun', date('Y'));

    //     // 1. Definisikan closure untuk filter tahun agar konsisten digunakan di beberapa query relasi
    //     $filterTahun = function ($q) use ($tahun) {
    //         $q->whereBetween('tgl_pinjam', ["$tahun-01-01", "$tahun-12-31"]);
    //     };

    //     // Menyiapkan query dasar untuk mengambil kolom utama pada tabel nasabah
    //     $query = Nasabah::select('id', 'nama', 'pekerjaan', 'alamat');

    //     // 2. Eager Loading & Agregasi dalam satu langkah (Mencegah N+1 Query)
    //     $query->with(['pinjamans' => function ($q) use ($search, $filterTahun) {
    //         // Jika tidak sedang mencari, batasi detail pinjaman hanya pada tahun yang dipilih
    //         if (!$search) $filterTahun($q);
    //         $q->select('id', 'id_nasaba', 'pinjam', 'angsuran', 'tgl_pinjam', 'status', 'tempo_hari', 'lokasi_penarikan', 'pembayaran', 't_pinjam', 'jaminan')
    //             ->orderBy('tgl_pinjam', 'desc');
    //     }])
    //         // Menghitung jumlah transaksi yang statusnya belum lunas (sebagai kolom virtual jumlah_transaksi)
    //         ->withCount(['pinjamans as jumlah_transaksi' => function ($q) use ($search, $filterTahun) {
    //             if (!$search) $filterTahun($q);
    //             $q->where('status', '!=', 'LUNAS');
    //         }])
    //         // Mencari tanggal pinjaman terbaru dari nasabah (sebagai kolom virtual pinjaman_terakhir)
    //         ->withMax(['pinjamans as pinjaman_terakhir' => function ($q) use ($search, $filterTahun) {
    //             if (!$search) $filterTahun($q);
    //         }], 'tgl_pinjam');

    //     // 3. Logika Filter Pencarian vs Filter Tahun
    //     if ($search) {
    //         // Jika sedang mencari, cari di seluruh data nasabah berdasarkan nama atau pekerjaan
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama', 'LIKE', "%$search%")->orWhere('pekerjaan', 'LIKE', "%$search%");
    //         });
    //     } else {
    //         // Jika tidak mencari, hanya tampilkan nasabah yang memiliki transaksi di tahun tersebut
    //         $query->whereHas('pinjamans', $filterTahun);
    //     }

    //     // Eksekusi query dengan paginasi 15 data dan mengurutkan berdasarkan transaksi terbaru
    //     $nasabahs = $query->orderByDesc('pinjaman_terakhir')->paginate(10)->appends($request->all());

    //     // 4. OPTIMASI UTAMA: Mengambil data ringan untuk keperluan dropdown/list nasabah
    //     // Mengambil kolom spesifik agar penggunaan memory server tetap efisien
    //     $all_nasabahs = Nasabah::select('id', 'nama', 'alamat', 'pekerjaan', 'foto')->orderBy('nama')->get();

    //     // Mengirimkan data ke halaman view
    //     return view('admin.pinjam.index', compact('nasabahs', 'all_nasabahs', 'tahun'));
    // }

    public function index(Request $request)
    {
        $search = $request->search;
        $tahun = $request->get('tahun', date('Y'));

        $filterTahun = function ($q) use ($tahun) {
            $q->whereBetween('tgl_pinjam', ["$tahun-01-01", "$tahun-12-31"]);
        };

        $query = Nasabah::select('id', 'nama', 'pekerjaan', 'alamat', 'foto'); // Tambahkan foto di select

        $query->with(['pinjamans' => function ($q) use ($search, $filterTahun) {
            if (!$search) $filterTahun($q);
            $q->select('id', 'id_nasaba', 'pinjam', 'angsuran', 'tgl_pinjam', 'status', 'tempo_hari', 'lokasi_penarikan', 'pembayaran', 't_pinjam', 'jaminan')
                ->orderBy('tgl_pinjam', 'desc');
        }])
            ->withCount(['pinjamans as jumlah_transaksi' => function ($q) use ($search, $filterTahun) {
                if (!$search) $filterTahun($q);
                // Hapus filter LUNAS jika Anda ingin menghitung semua transaksi, 
                // atau biarkan jika ingin transaksi aktif saja
            }])
            ->withMax(['pinjamans as pinjaman_terakhir' => function ($q) use ($search, $filterTahun) {
                if (!$search) $filterTahun($q);
            }], 'tgl_pinjam');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")->orWhere('pekerjaan', 'LIKE', "%$search%");
            });
        } else {
            $query->whereHas('pinjamans', $filterTahun);
        }

        $nasabahs = $query->orderByDesc('pinjaman_terakhir')->paginate(10)->appends($request->all());

        // LOGIKA AJAX UNTUK AUTOSCROLL
        if ($request->ajax()) {
            // Render view partial saja
            return view('admin.pinjam._item_nasabah', compact('nasabahs'))->render();
        }

        $all_nasabahs = Nasabah::select('id', 'nama', 'alamat', 'pekerjaan', 'foto')->orderBy('nama')->get();

        return view('admin.pinjam.index', compact('nasabahs', 'all_nasabahs', 'tahun'));
    }



    // Fungsi Simpan (Store)
    public function store(Request $request)
    {
        $request->validate([
            'id_nasaba' => 'required',
            'pinjam' => 'required|numeric',
            'tgl_pinjam' => 'required|date',
        ]);

        Pinjam::create($request->all());
        return back()->with('success', 'Data pinjaman berhasil ditambahkan!');
    }

    // Fungsi Update
    public function update(Request $request, $id)
    {
        $pinjam = Pinjam::findOrFail($id);
        $pinjam->update($request->all());
        return back()->with('success', 'Data pinjaman berhasil diperbarui!');
    }

    // Fungsi Hapus
    public function destroy($id)
    {
        Pinjam::findOrFail($id)->delete();
        return back()->with('success', 'Data pinjaman berhasil dihapus!');
    }
}
