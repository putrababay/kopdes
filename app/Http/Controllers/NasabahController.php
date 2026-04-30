<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class NasabahController extends Controller
{
    /**
     * Menampilkan daftar nasabah dengan fitur pencarian dan paginasi.
     */
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

    /**
     * Logika simpan foto khusus KSWEB (Anti-Crash)
     * Menggunakan konsep kompresi jika memungkinkan, atau move() jika GD error.
     */
    private function saveFotoWithFallback($file)
    {
        $nama_foto = time() . "_" . uniqid() . ".jpg";
        $destinationPath = public_path('foto');
        
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        $filePath = $destinationPath . '/' . $nama_foto;

        try {
            // 1. Baca data gambar
            $imageData = file_get_contents($file->getRealPath());
            $src = @imagecreatefromstring($imageData);

            if (!$src) throw new \Exception("Gagal load gambar");

            // 2. Tentukan ukuran baru (Resize ke 1200px lebarnya)
            // Ini cara paling ampuh ngecilin size file kalau fitur kompresi rusak
            $oldW = imagesx($src);
            $oldH = imagesy($src);
            
            $newW = 1200; // Ukuran standar yang cukup tajam tapi ringan
            $newH = floor($oldH * ($newW / $oldW));

            $canvas = imagecreatetruecolor($newW, $newH);
            
            // Background putih
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));

            // 3. Proses Resize
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $oldW, $oldH);

            // 4. Simpan. Jika imagejpeg error, kita pakai imagepng sebagai cadangan (biasanya lebih stabil)
            // Kualitas 60 untuk JPEG
            if (!@imagejpeg($canvas, $filePath, 60)) {
                // Jika JPEG masih protes library version, simpan sebagai PNG (biasanya library PNG sehat)
                $filePathPng = str_replace('.jpg', '.png', $filePath);
                if (@imagepng($canvas, $filePathPng, 6)) {
                    $nama_foto = str_replace('.jpg', '.png', $nama_foto);
                } else {
                    throw new \Exception("Semua library GD Rusak");
                }
            }

            imagedestroy($src);
            imagedestroy($canvas);

        } catch (\Throwable $e) {
            // Jika semua cara GD gagal, baru gunakan move (file asli besar)
            $file->move($destinationPath, $nama_foto);
        }
        
        return $nama_foto;
    }

    /**
     * Menyimpan data nasabah baru.
     */
    public function store(Request $request) 
    {
        $request->validate([
            'nik'    => 'required|numeric|unique:master_nasabah,nik',
            'nama'   => 'required|string|max:255',
            'no_tlp' => 'required',
            // Batasan 2MB agar aman di lingkungan Android
            'foto'   => 'nullable|image|max:2048' 
        ]);

        $data = $request->all();
        $data['tgl_daftar'] = now(); 
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->saveFotoWithFallback($request->file('foto'));
        }

        Nasabah::create($data);
        return redirect()->route('nasabah.index')->with('success', 'Nasabah berhasil ditambahkan!');
    }

    /**
     * Memperbarui data nasabah.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nik'    => 'required|numeric|unique:master_nasabah,nik,' . $id,
            'nama'   => 'required|string|max:255',
            'no_tlp' => 'required',
            'foto'   => 'nullable|image|max:2048', 
        ]);

        $nasabah = Nasabah::findOrFail($id);
        $data = $request->except(['foto']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($nasabah->foto && File::exists(public_path('foto/' . $nasabah->foto))) {
                File::delete(public_path('foto/' . $nasabah->foto));
            }
            $data['foto'] = $this->saveFotoWithFallback($request->file('foto'));
        } 
        elseif ($request->remove_foto == '1') { 
            if ($nasabah->foto && File::exists(public_path('foto/' . $nasabah->foto))) {
                File::delete(public_path('foto/' . $nasabah->foto));
            }
            $data['foto'] = null;
        }

        $nasabah->update($data);
        return redirect()->route('nasabah.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Menghapus data nasabah.
     */
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