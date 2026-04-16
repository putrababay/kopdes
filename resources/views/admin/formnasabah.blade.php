@extends('layouts.admin')

@section('content')
<style>
    .table-responsive { padding: 15px; }
    #tableNasabah { border-collapse: separate; border-spacing: 0 10px; }
    #tableNasabah tbody tr { 
        box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        background: #fff;
    }
    #tableNasabah td { padding: 15px !important; vertical-align: middle; border: none; }
    #tableNasabah thead th { border: none; padding-left: 15px; }
    #my_camera { background: #000; overflow: hidden; border-radius: 10px; margin: auto; }
    .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; }
</style>

<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary">Master Data Nasabah</h5>
            <button type="button" class="btn btn-primary shadow-sm" onclick="addData()">
                <i class="fas fa-plus-circle me-1"></i> Tambah Nasabah
            </button>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tableNasabah" class="table table-hover align-middle w-100">
                    <thead class="bg-light">
                        <tr class="small text-uppercase">
                            <th>Profil</th>
                            <th>Informasi Pribadi</th>
                            <th>Kontak & Pekerjaan</th>
                            <th>Lokasi & Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nasabah as $row)
                        <tr>
                            <td>
                                @if($row->foto)
                                    <img src="{{ asset('storage/foto/'.$row->foto) }}" class="rounded-circle object-fit-cover shadow-sm" width="50" height="50">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width:50px; height:50px;">
                                        {{ strtoupper(substr($row->nama, 0, 2)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama }}</div>
                                <div class="small text-muted">NIK: {{ $row->nik }}</div>
                                <div class="small text-muted"><i class="fas fa-calendar-alt me-1"></i> {{ $row->kota_lahir }}, {{ $row->tgl_lahir }}</div>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-map-marker-alt me-1"></i> {{ $row->alamat }}</div>
                                <div class="small fw-bold text-success"><i class="fas fa-phone me-1"></i> {{ $row->no_tlp }}</div>
                                <div class="small badge bg-light text-dark border">{{ $row->pekerjaan }}</div>
                            </td>
                            <td>
                                <div class="small">User: <span class="fw-bold">{{ $row->username }}</span> ({{ $row->level }})</div>
                                <div class="small text-muted">Daftar: {{ $row->tgl_daftar }}</div>
                                @if($row->lat)
                                <a href="https://www.google.com/maps?q={{ $row->lat }},{{ $row->lng }}" target="_blank" class="badge bg-danger text-decoration-none">
                                    <i class="fas fa-location-arrow me-1"></i> Lihat GPS
                                </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <button type="button" class="btn btn-sm btn-white text-warning border btn-edit-nasabah" 
                                            data-nasabah="{{ json_encode($row) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('nasabah.delete', $row->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-white text-danger border btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNasabah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formNasabah" action="{{ route('nasabah.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">Form Nasabah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="form_id">
                    <input type="hidden" name="lat" id="form_lat">
                    <input type="hidden" name="lng" id="form_lng">
                    <input type="hidden" name="image_base64" id="image_base64">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIK</label>
                            <input type="text" name="nik" id="form_nik" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" id="form_nama" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Alamat</label>
                            <textarea name="alamat" id="form_alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kota Lahir</label>
                            <input type="text" name="kota_lahir" id="form_kota_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tgl Lahir</label>
                            <input type="date" name="tgl_lahir" id="form_tgl_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">No. Telp</label>
                            <input type="text" name="no_tlp" id="form_no_tlp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" id="form_pekerjaan" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Level</label>
                            <select name="level" id="form_level" class="form-select">
                                <option value="NASABAH">NASABAH</option>
                                <option value="NASABAH MEMBER">NASABAH MEMBER</option>
                                <option value="ADMIN">ADMIN</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <div id="my_camera" class="mx-auto border rounded bg-dark" style="width:100%; max-width:320px; height:240px;"></div>
                            <button type="button" class="btn btn-dark btn-sm mt-2 w-100" onclick="take_snapshot()">
                                <i class="fas fa-camera me-1"></i> Ambil Foto Kamera
                            </button>
                        </div>
                        <div class="col-md-6 text-center">
                            <div id="results" class="mx-auto border rounded bg-light d-flex align-items-center justify-content-center" style="width:100%; max-width:320px; height:240px;">
                                <span class="text-muted small">Preview</span>
                            </div>
                            <input type="file" name="berkas" class="form-control form-control-sm mt-2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tableNasabah').DataTable({
        responsive: true,
        language: { search: "_INPUT_", searchPlaceholder: "Cari nasabah..." }
    });

    // Delegasi Event Klik
    $('#tableNasabah tbody').on('click', '.btn-edit-nasabah', function() {
        var data = $(this).data('nasabah');
        editData(data);
    });

    // Handle Delete dengan SweetAlert
    $('.btn-delete').on('click', function() {
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus data?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });

    $('#modalNasabah').on('hidden.bs.modal', function () {
        Webcam.reset(); 
    });
});

function getGeo() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            $('#form_lat').val(position.coords.latitude);
            $('#form_lng').val(position.coords.longitude);
        });
    }
}

function addData() {
    $('#formNasabah')[0].reset();
    $('#form_id').val('');
    $('#modalTitle').text('Tambah Nasabah Baru');
    $('#results').html('<span class="text-muted small">Preview</span>');
    $('#modalNasabah').modal('show');
    setTimeout(() => { Webcam.attach('#my_camera'); getGeo(); }, 500);
}

function editData(data) {
    $('#formNasabah')[0].reset();
    $('#modalTitle').text('Edit: ' + data.nama);
    $('#form_id').val(data.id);
    $('#form_nik').val(data.nik);
    $('#form_nama').val(data.nama);
    $('#form_alamat').val(data.alamat);
    $('#form_no_tlp').val(data.no_tlp);
    $('#form_pekerjaan').val(data.pekerjaan);
    $('#form_level').val(data.level);
    $('#form_tgl_lahir').val(data.tgl_lahir);
    $('#form_kota_lahir').val(data.kota_lahir);
    
    if(data.foto) {
        $('#results').html(`<img src="{{ asset('storage/foto') }}/${data.foto}" class="img-fluid rounded" style="max-height:100%">`);
    }

    $('#modalNasabah').modal('show');
    setTimeout(() => { Webcam.attach('#my_camera'); }, 500);
}

function take_snapshot() {
    Webcam.snap(data_uri => {
        $('#image_base64').val(data_uri);
        $('#results').html(`<img src="${data_uri}" class="img-fluid rounded" style="max-height:100%"/>`);
    });
}
</script>
@endpush
@endsection