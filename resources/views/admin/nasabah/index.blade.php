@extends('layouts.admin')

@section('content')


<style>
    .nasabah-card {
        border: none;
        border-radius: 12px;
        transition: 0.3s;
    }

    .avatar-img,
    .avatar-initial {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        cursor: pointer;
    }

    .avatar-initial {
        background-color: #e9ecef;
        color: #0d6efd;
        border: 1px solid #dee2e6;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .desktop-table {
            display: none;
        }

        .mobile-card {
            display: block;
        }

        .search-box {
            width: 100% !important;
            order: 2;
        }

        .header-actions {
            width: 100%;
            order: 1;
            margin-bottom: 10px;
        }

        .btn-tambah-mobile {
            width: 100%;
            border-radius: 10px !important;
        }

        /* Pastikan dropdown mobile terlihat di atas card */
        .dropdown-menu {
            z-index: 1050;
        }
    }

    @media (min-width: 769px) {
        .mobile-card {
            display: none;
        }
    }

    .img-preview-zoom {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .img-preview-zoom:hover {
        transform: scale(1.05);
    }
</style>

<div class="container py-0">
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-4 col-12 text-center text-md-start">
            <h3 class="fw-bold text-primary mb-0">Master Nasabah</h3>
        </div>
        <div class="col-md-8 col-12">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                <div class="header-actions d-md-none">
                    <button class="btn btn-primary btn-tambah-mobile py-2 shadow-sm" onclick="openModal('tambah')">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Nasabah Baru
                    </button>
                </div>

                <div class="input-group shadow-sm search-box">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-0"
                        placeholder="Cari nama atau NIK..." value="{{ request('search') }}"
                        oninput="autoSearch(this.value)">
                    @if(request('search'))
                    <button class="btn btn-white border-0 text-danger" type="button" onclick="window.location.href='{{ route('nasabah.index') }}'">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    @endif
                </div>

                <button class="btn btn-primary px-4 d-none d-md-block rounded-pill shadow-sm" onclick="openModal('tambah')">
                    <i class="bi bi-plus-lg me-1"></i> Tambah
                </button>
            </div>
        </div>
    </div>

    <div class="card nasabah-card shadow-sm desktop-table">
        <div class="card-body p-0 text-nowrap" style="background-color: #91cce433 !important">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nasabah</th>
                        <th>NIK</th>
                        <th>Kontak</th>
                        <th>Pekerjaan</th>
                        <th>Alamat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nasabah as $n)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                {{-- Cek apakah kolom foto di DB tidak kosong DAN file fisiknya ada di folder public/foto --}}
                                @if($n->foto && file_exists(public_path('foto/'.$n->foto)))
                                <img src="{{ asset('foto/'.$n->foto) }}"
                                    class="avatar-img me-3"
                                    style="cursor: pointer;"
                                    onclick="previewFoto('{{ asset('foto/'.$n->foto) }}', '{{ $n->nama }}')">
                                @else
                                {{-- Tampilkan inisial jika teks foto kosong ATAU file fisik tidak ditemukan --}}
                                <div class="avatar-initial me-3">{{ strtoupper(substr($n->nama, 0, 1)) }}</div>
                                @endif

                                <div>
                                    <div class="fw-bold text-dark">{{ $n->nama }}</div>
                                    <small class="text-muted">{{ $n->tgl_daftar }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-primary border">{{ $n->nik }}</span></td>
                        <td>
                            <div class="small fw-bold">{{ $n->no_tlp }}</div>
                            @if($n->lat)
                            <a href="https://www.google.com/maps?q={{ $n->lat }},{{ $n->lng }}" target="_blank" class="small text-decoration-none text-danger">
                                <i class="bi bi-geo-alt-fill"></i> Map
                            </a>
                            @endif
                        </td>
                        <td>{{ $n->pekerjaan }}</td>
                        <td>{{ Str::limit($n->alamat, 30) }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-white btn-sm border" onclick='openModal("edit", @json($n))'>
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <button class="btn btn-white btn-sm border" onclick="confirmDelete('{{ $n->id }}')">
                                    <i class="bi bi-trash3-fill text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <form id="delete-form-{{ $n->id }}" action="{{ route('nasabah.destroy', $n->id) }}" method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mobile-card">
        <?php $no = 1; ?>
        @foreach($nasabah as $n)
        <div class="card border-0 shadow-sm mb-3 overflow-hidden" style="border-radius: 15px;">
            <div class="bg-primary" style="height: 4px;"></div>

            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        @if($n->foto && file_exists(public_path('foto/'.$n->foto)))
                        <img src="{{ asset('foto/'.$n->foto) }}"
                            class="rounded-circle object-fit-cover border"
                            style="width: 50px; height: 50px; cursor: pointer;"
                            onclick="previewFoto('{{ asset('foto/'.$n->foto) }}', '{{ $n->nama }}')"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="avatar-placeholder bg-light text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle border"
                            style="width: 50px; height: 50px; {{ ($n->foto && file_exists(public_path('foto/'.$n->foto))) ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($n->nama, 0, 1)) }}
                        </div>

                        <div class="ms-3">
                            <h6 class="mb-0 fw-bold text-dark">{{ $n->nama }}</h6>
                            <small class="text-muted"><i class="bi bi-card-text me-1"></i>{{ $n->nik }}</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-primary border rounded-pill">#{{ $no++ }}</span>
                </div>

                <div class="info-section bg-light p-3 rounded-3 mb-3">
                    <div class="row g-2">
                        <div class="col-12 d-flex align-items-center mb-1">
                            <i class="bi bi-briefcase text-secondary me-2"></i>
                            <span class="small text-muted me-2">Pekerjaan:</span>
                            <span class="small fw-semibold ms-auto">{{ $n->pekerjaan ?? '-' }}</span>
                        </div>
                        <div class="col-12 d-flex align-items-center mb-1">
                            <i class="bi bi-telephone text-secondary me-2"></i>
                            <span class="small text-muted me-2">No. Telp:</span>
                            <span class="small fw-semibold ms-auto">{{ $n->no_tlp }}</span>
                        </div>
                        <div class="col-12 mt-2 pt-2 border-top">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-geo-alt text-secondary me-2 mt-1"></i>
                                <span class="small text-muted text-break">{{ $n->alamat }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        @if($n->lat)
                        <a href="https://www.google.com/maps?q={{ $n->lat }},{{ $n->lng }}" target="_blank" class="btn btn-outline-danger btn-sm w-100 py-2 rounded-3">
                            <i class="bi bi-map-fill me-1"></i> Lokasi
                        </a>
                        @else
                        <button class="btn btn-light btn-sm w-100 py-2 rounded-3 disabled text-muted">
                            <i class="bi bi-pin-map me-1"></i> No Map
                        </button>
                        @endif
                    </div>
                    <div class="col-3">
                        <button class="btn btn-outline-warning btn-sm w-100 py-2 rounded-3" onclick='openModal("edit", @json($n))'>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-outline-danger btn-sm w-100 py-2 rounded-3" onclick="confirmDelete('{{ $n->id }}')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    <div class="mt-4">{{ $nasabah->links('pagination::bootstrap-5') }}</div>
</div>

<div class="modal fade" id="modalNasabah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formNasabah" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="methodField"></div>
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Form Nasabah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">NIK</label>
                            <input type="number" name="nik" id="nik" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Pekerjaan</label>
                            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">No. Telepon</label>
                            <input type="text" name="no_tlp" id="no_tlp" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control rounded-3" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Koordinat Lokasi</label>
                            <div class="input-group">
                                <input type="text" name="lat" id="lat" class="form-control" placeholder="Lat" readonly>
                                <input type="text" name="lng" id="lng" class="form-control" placeholder="Lng" readonly>
                                <button class="btn btn-danger" type="button" onclick="getLocation()"><i class="bi bi-geo-alt"></i> Ambil</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Foto Profil</label>
                            <input type="file" name="foto" id="fotoInput" class="form-control rounded-3" accept="image/*" onchange="previewImage(this)">

                            <input type="hidden" name="remove_foto" id="remove_foto" value="0">

                            <div class="mt-3 p-2 border rounded bg-light text-center" id="previewContainer" style="display: none;">
                                <p class="small text-muted mb-2" id="previewLabel">Preview:</p>
                                <img id="imgPreview" class="img-thumbnail shadow-sm" style="max-height: 200px;">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearPhoto()">Hapus Foto</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let modalNasabah;
    let formNasabah;
    let methodField;
    let timer;

    document.addEventListener("DOMContentLoaded", function() {
        modalNasabah = new bootstrap.Modal(document.getElementById('modalNasabah'));
        formNasabah = document.getElementById('formNasabah');
        methodField = document.getElementById('methodField');
    });

    /**
     * Fitur Pencarian Otomatis
     */
    function autoSearch(val) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            window.location.href = "{{ route('nasabah.index') }}?search=" + encodeURIComponent(val);
        }, 800);
    }

    /**
     * Preview Foto Besar (Lightbox)
     */
    function previewFoto(url, title) {
        Swal.fire({
            title: title,
            imageUrl: url,
            imageAlt: title,
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    /**
     * Logika Buka Modal (Tambah/Edit)
     */
    function openModal(mode, data = null) {
        if (!formNasabah) return;
        formNasabah.reset();

        // Reset state hapus foto
        if (document.getElementById('remove_foto')) {
            document.getElementById('remove_foto').value = "0";
        }

        document.getElementById('previewContainer').style.display = 'none';

        if (mode === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah Nasabah';
            formNasabah.action = "{{ route('nasabah.store') }}";
            methodField.innerHTML = ''; // Method default POST
        } else {
            document.getElementById('modalTitle').innerText = 'Edit Nasabah';
            formNasabah.action = "{{ url('nasabah') }}/" + data.id;

            // Gunakan PUT untuk update agar sesuai dengan standar Laravel
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Isi Form
            document.getElementById('nik').value = data.nik;
            document.getElementById('nama').value = data.nama;
            document.getElementById('pekerjaan').value = data.pekerjaan;
            document.getElementById('no_tlp').value = data.no_tlp;
            document.getElementById('alamat').value = data.alamat;
            document.getElementById('lat').value = data.lat || '';
            document.getElementById('lng').value = data.lng || '';

            // Handle Foto Profil yang sudah ada
            if (data.foto) {
                const preview = document.getElementById('imgPreview');
                const container = document.getElementById('previewContainer');
                preview.src = "{{ asset('foto') }}/" + data.foto;
                container.style.display = 'block';
                document.getElementById('previewLabel').innerText = "Foto Saat Ini:";
            }
        }
        modalNasabah.show();
    }

    /**
     * Fitur Ambil Lokasi & Alamat Otomatis
     */
    function getLocation() {
        if (navigator.geolocation) {
            Swal.fire({
                title: 'Mencari Lokasi...',
                text: 'Harap tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(async (pos) => {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;

                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lon;

                try {
                    // Reverse Geocoding menggunakan Nominatim (OpenStreetMap)
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`);
                    const data = await response.json();

                    if (data && data.display_name) {
                        document.getElementById('alamat').value = data.display_name;
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi & Alamat Didapat',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Didapat',
                            text: 'Alamat tidak ditemukan',
                            timer: 1500
                        });
                    }
                } catch (error) {
                    console.error("Geocoding Error: ", error);
                    Swal.fire({
                        icon: 'success',
                        title: 'Lokasi Didapat',
                        text: 'Gagal mengambil detail alamat otomatis',
                        timer: 1500
                    });
                }

            }, (err) => {
                Swal.fire('Gagal', 'Pastikan izin GPS diaktifkan di browser Anda', 'error');
            });
        } else {
            Swal.fire('Opps', 'Browser Anda tidak mendukung Geolocation', 'error');
        }
    }

    /**
     * Preview Foto saat Upload
     */
    function previewImage(input) {
        if (input.files && input.files[0]) {
            if (document.getElementById('remove_foto')) {
                document.getElementById('remove_foto').value = "0";
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('imgPreview');
                const container = document.getElementById('previewContainer');
                preview.src = e.target.result;
                container.style.display = 'block';
                document.getElementById('previewLabel').innerText = "Preview Foto Baru:";
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * Fungsi Hapus Preview/Reset Foto (Sesuai Controller sebelumnya)
     */
    function clearPhoto() {
        document.getElementById('fotoInput').value = "";
        document.getElementById('imgPreview').src = "";
        document.getElementById('previewContainer').style.display = 'none';
        if (document.getElementById('remove_foto')) {
            document.getElementById('remove_foto').value = "1";
        }
    }

    /**
     * Konfirmasi Hapus Data
     */
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

{{-- Handler SweetAlert Flash Session --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Simpan',
        text: "{{ $errors->first() }}", // Menampilkan pesan error pertama agar lebih jelas
    });
</script>
@endif
@endsection