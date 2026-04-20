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

    // Optimasi: Gunakan Select spesifik dan Eager Loading yang difilter
    $query = Nasabah::select('id', 'nama', 'pekerjaan', 'alamat')
        ->with(['pinjamans' => function($q) use ($search, $tahun) {
            // Jika tidak mencari, filter tahun agar collapse tidak berat
            if (!$search) {
                $q->whereYear('tgl_pinjam', $tahun);
            }
            $q->select('id', 'id_nasaba', 'pinjam', 'angsuran', 'tgl_pinjam', 'status', 'tempo_hari', 'lokasi_penarikan', 'pembayaran')
              ->orderBy('id', 'desc');
        }])
        ->withCount(['pinjamans as jumlah_transaksi' => function($q) use ($search, $tahun) {
            if (!$search) $q->whereYear('tgl_pinjam', $tahun);
            $q->where('status', '!=', 'LUNAS'); // Fokus pada transaksi berjalan
        }])
        ->withMax(['pinjamans as pinjaman_terakhir' => function($q) use ($search, $tahun) {
            if (!$search) $q->whereYear('tgl_pinjam', $tahun);
        }], 'tgl_pinjam');

    if ($search) {
        $query->where('nama', 'LIKE', "%$search%")
              ->orWhere('pekerjaan', 'LIKE', "%$search%");
    } else {
        // Hanya tampilkan nasabah yang punya tanggungan atau aktivitas di tahun tersebut
        $query->whereHas('pinjamans', function($q) use ($tahun) {
            $q->whereYear('tgl_pinjam', $tahun);
        });
    }

    $nasabahs = $query->orderByDesc('pinjaman_terakhir')->paginate(15)->appends($request->all());
    $all_nasabahs = Nasabah::select('id', 'nama')->get(); // Untuk dropdown modal tambah

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