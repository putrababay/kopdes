<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $query = Nasabah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('nik', 'LIKE', "%$search%");
            });
        }

        $nasabah = $query->orderBy('id', 'desc')->paginate(10);
        return view('admin.nasabah.index', compact('nasabah'));
    }

    private function compressAndSaveFoto($file)
    {
        $nama_foto = time() . "_" . uniqid() . ".jpg"; // Simpan sebagai .jpg untuk efisiensi
        $destinationPath = public_path('foto');
        
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        $filePath = $destinationPath . '/' . $nama_foto;
        $imageInfo = getimagesize($file);
        $mime = $imageInfo['mime'];

        // Buat resource gambar berdasarkan tipe file asli
        switch ($mime) {
            case 'image/jpeg': $image = imagecreatefromjpeg($file); break;
            case 'image/png':  
                $image = imagecreatefrompng($file);
                // Menangani transparansi PNG agar tidak jadi hitam saat diconvert ke JPG
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $bg;
                break;
            default: return null;
        }

        // Kompresi: Quality 50-60 biasanya memotong ukuran sampai 80% tapi tetap tajam
        // Disimpan sebagai JPEG untuk memastikan sizenya kecil (di bawah 1MB)
        imagejpeg($image, $filePath, 60); 
        imagedestroy($image);
        
        return $nama_foto;
    }

    public function store(Request $request) 
    {
        $request->validate([
            'nik' => 'required|numeric|unique:master_nasabah,nik',
            'nama' => 'required|string|max:255',
            'no_tlp' => 'required',
            'foto' => 'nullable|image|max:10240' // Izinkan upload sampai 10MB untuk dikompres
        ]);

        $data = $request->all();
        $data['tgl_daftar'] = now(); 
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->compressAndSaveFoto($request->file('foto'));
        }

        Nasabah::create($data);
        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nik'       => 'required|numeric|unique:master_nasabah,nik,' . $id,
        'nama'      => 'required|string|max:255',
        'no_tlp'    => 'required',
        'pekerjaan' => 'required',
        'foto'      => 'nullable|image|max:10240',
    ]);

    $nasabah = Nasabah::findOrFail($id);
    
    // Ambil semua data kecuali foto
    $data = $request->except(['foto']);

    // LOGIKA 1: Jika ada file foto baru yang diunggah
    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada
        if ($nasabah->foto && File::exists(public_path('foto/' . $nasabah->foto))) {
            File::delete(public_path('foto/' . $nasabah->foto));
        }
        // Simpan & Kompres foto baru
        $data['foto'] = $this->compressAndSaveFoto($request->file('foto'));
    } 
    // LOGIKA 2: Jika user sengaja ingin mengosongkan foto 
    // (Kita asumsikan jika input file kosong dan ada flag khusus, atau sesuaikan kebutuhan)
    else if ($request->remove_foto == '1') { 
        if ($nasabah->foto && File::exists(public_path('foto/' . $nasabah->foto))) {
            File::delete(public_path('foto/' . $nasabah->foto));
        }
        $data['foto'] = null;
    }
    // Jika tidak ada file baru dan tidak disuruh hapus, 
    // kolom 'foto' tidak akan berubah karena sudah kita 'except' di awal.

    $nasabah->update($data);
    return redirect()->route('nasabah.index')->with('success', 'Data berhasil diperbarui!');
}

    public function destroy($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        if ($nasabah->foto && File::exists(public_path('foto/' . $nasabah->foto))) {
            File::delete(public_path('foto/' . $nasabah->foto));
        }
        $nasabah->delete();
        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil dihapus!');
    }
}