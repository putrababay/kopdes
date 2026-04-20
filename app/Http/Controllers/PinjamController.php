<?php

namespace App\Http\Controllers;

use App\Models\Pinjam;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PinjamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $tahun = $request->get('tahun', date('Y'));

        // 1. Definisikan closure untuk filter tahun agar konsisten
        $filterTahun = function ($q) use ($tahun) {
            $q->whereBetween('tgl_pinjam', ["$tahun-01-01", "$tahun-12-31"]);
        };


        $query = Nasabah::select('id', 'nama', 'pekerjaan', 'alamat');

        // 2. Eager Loading & Agregasi dalam satu langkah
        $query->with(['pinjamans' => function ($q) use ($search, $filterTahun) {
            if (!$search) $filterTahun($q);
            $q->select('id', 'id_nasaba', 'pinjam', 'angsuran', 'tgl_pinjam', 'status', 'tempo_hari', 'lokasi_penarikan', 'pembayaran', 't_pinjam', 'jaminan')
                ->orderBy('tgl_pinjam', 'desc');
        }])
            ->withCount(['pinjamans as jumlah_transaksi' => function ($q) use ($search, $filterTahun) {
                if (!$search) $filterTahun($q);
                $q->where('status', '!=', 'LUNAS');
            }])
            ->withMax(['pinjamans as pinjaman_terakhir' => function ($q) use ($search, $filterTahun) {
                if (!$search) $filterTahun($q);
            }], 'tgl_pinjam');

        // 3. Filter Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                    ->orWhere('pekerjaan', 'LIKE', "%$search%");
            });
        } else {
            $query->whereHas('pinjamans', $filterTahun);
        }

        $nasabahs = $query->orderByDesc('pinjaman_terakhir')->paginate(15)->appends($request->all());

        // 4. OPTIMASI UTAMA: Jangan ambil semua data nasabah jika tidak perlu
        // Ambil hanya ID dan Nama saja untuk dropdown
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
