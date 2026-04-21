<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterPulsaPinjam;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use DB;

class PulsaPinjamController extends Controller
{
  public function index(Request $request)
{
    $search = $request->input('search');

    $nasabahs = Nasabah::with(['pulsaPinjam' => function($q) {
            $q->orderBy('jam_tgl', 'DESC');
        }])
        ->when($search, function($q) use ($search) {
            $q->where('nama', 'LIKE', "%{$search}%")
              ->orWhere('alamat', 'LIKE', "%{$search}%");
        })
        ->whereHas('pulsaPinjam') 
        ->orderBy('nama', 'ASC')
        ->paginate(10);

    if ($request->ajax()) {
        // PENTING: Gunakan render() agar hasil view jadi string untuk append AJAX
        return view('admin.pulsa._item_list', compact('nasabahs'))->render();
    }

    return view('admin.pulsa.index', compact('nasabahs'));
}

    public function store(Request $request)
    {
        MasterPulsaPinjam::create([
            'id_pulsa' => $request->id_pulsa,
            'id_nasaba' => $request->id_nasaba,
            'nomer'    => $request->nomer,
            'harga'    => $request->harga,
            'jam_tgl'  => now(),
            'status'   => 'BELUM LUNAS'
        ]);

        return response()->json(['success' => 'Data berhasil disimpan']);
    }

    public function update(Request $request, $id)
    {
        $data = MasterPulsaPinjam::findOrFail($id);
        $data->update($request->all());
        return response()->json(['success' => 'Data berhasil diupdate']);
    }

    public function destroy($id)
    {
        MasterPulsaPinjam::destroy($id);
        return response()->json(['success' => 'Data dihapus']);
    }

    // app/Models/Nasabah.php


}