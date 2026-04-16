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
        ->addColumn('keterangan', function ($row) {
            return '<div class="text-start">
                        <div class="text-detail">1. (email) : <br><span class="text-primary">('.$row->username.')</span></div>
                        <div class="text-detail mt-1">2. (password) : <br><span class="text-dark">('.$row->password.')</span></div>
                    </div>';
        })
        ->addColumn('status', function ($row) {
            return '<span class="badge-status text-uppercase">AKTIF</span>';
        })
        ->addColumn('action', function ($row) {
            return '<div class="btn-group-custom">
                        <button class="btn-edit"><i class="fas fa-edit text-white"></i></button>
                        <button class="btn-delete"><i class="fas fa-trash text-white"></i></button>
                    </div>';
        })
        ->rawColumns(['keterangan', 'status', 'action'])
        ->make(true);
}
}