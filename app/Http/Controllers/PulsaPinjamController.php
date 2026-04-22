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

        $nasabahs = Nasabah::with(['pulsaPinjam' => function ($q) {
            $q->orderBy('jam_tgl', 'DESC');
        }])
            ->when($search, function ($q) use ($search) {
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
        try {
            // 1. Validasi (Nama field harus sesuai dengan atribut 'name' di HTML)
            $request->validate([
                'id_nasaba' => 'required',
                'nomer'     => 'required',
                'harga'     => 'required|numeric',
                'status'    => 'required',
            ]);

            // 2. Simpan ke database
            \App\Models\MasterPulsaPinjam::create([
                'id_pulsa' => $request->id_nasaba, // Map: input 'id_nasaba' ke kolom 'id_pulsa'
                'nomer'    => $request->nomer,
                'harga'    => $request->harga,
                'status'   => $request->status,
                'jam_tgl'  => now(),
            ]);

            return response()->json(['message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            // Kirim pesan error detail jika gagal
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $data = \App\Models\MasterPulsaPinjam::findOrFail($id);

            // Map data agar sesuai dengan kolom database
            $updateData = [
                'id_pulsa' => $request->id_nasaba, // Memetakan id_nasaba ke id_pulsa
                'nomer'    => $request->nomer,
                'harga'    => $request->harga,
                'status'   => $request->status,
            ];

            $data->update($updateData);

            return response()->json(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $data = \App\Models\MasterPulsaPinjam::findOrFail($id);
            $data->delete();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // app/Models/Nasabah.php


}
