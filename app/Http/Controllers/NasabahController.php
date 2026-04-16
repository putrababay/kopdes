<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah; // Pastikan Model Nasabah sudah ada
use Yajra\DataTables\Facades\DataTables;

class NasabahController extends Controller
{
    public function index()
    {
        return view('admin.nasabah.index');
    }

 public function getData()
{
    $nasabah = Nasabah::query();

    return DataTables::of($nasabah)
        ->addIndexColumn()
        ->addColumn('foto_profile', function ($row) {
            // Gunakan foto dari storage, jika kosong gunakan inisial/placeholder
            $url = $row->foto ? asset('storage/' . $row->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($row->nama);
            return '<img src="' . $url . '" class="rounded-circle shadow-sm" width="50" height="50" style="object-fit:cover; border: 2px solid #fff;">';
        })
        ->addColumn('info_lengkap', function ($row) {
            return '<div class="text-start">
                        <div class="text-detail">1. (NIK/Pekerjaan) : <br><span class="text-primary">'.$row->nik.' / '.$row->pekerjaan.'</span></div>
                        <div class="text-detail mt-1">2. (Alamat/Kota) : <br><span class="text-dark">'.$row->alamat.', '.$row->kota_lahir.'</span></div>
                        <div class="text-detail mt-1">3. (Kontak) : <br><span class="text-success">'.$row->no_tlp.'</span></div>
                        <div class="text-detail mt-1 text-muted small">Loc: '.$row->lat.', '.$row->lng.'</div>
                    </div>';
        })
        ->addColumn('action', function ($row) {
            return '<div class="btn-group-custom">
                        <button class="btn-edit" onclick="editData('.$row->id.')"><i class="fas fa-edit text-white"></i></button>
                        <button class="btn-delete" onclick="deleteData('.$row->id.')"><i class="fas fa-trash text-white"></i></button>
                    </div>';
        })
        ->rawColumns(['foto_profile', 'info_lengkap', 'action'])
        ->make(true);
}
}