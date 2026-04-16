<div class="modal fade" id="modalNasabah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <form id="formNasabah" action="{{ route('nasabah.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="lat" id="f_lat">
                <input type="hidden" name="lng" id="f_lng">
                <input type="hidden" name="image_base64" id="f_base64">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="m_title">Form Nasabah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">NIK</label>
                            <input type="text" name="nik" id="f_nik" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" id="f_nama" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea name="alamat" id="f_alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div id="cam_box" class="border rounded bg-dark" style="height:200px;"></div>
                            <button type="button" class="btn btn-dark btn-sm w-100 mt-2" onclick="do_snap()">Capture</button>
                        </div>
                        <div class="col-md-6">
                            <div id="preview_box" class="border rounded bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                <span class="text-muted">No Image</span>
                            </div>
                            <input type="file" name="berkas" class="form-control form-control-sm mt-2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addData() {
    $('#formNasabah')[0].reset();
    $('#f_id').val('');
    $('#m_title').text('Tambah Nasabah');
    $('#preview_box').html('<span class="text-muted">No Image</span>');
    $('#modalNasabah').modal('show');
    Webcam.attach('#cam_box');
    getGeo();
}

function editData(data) {
    $('#f_id').val(data.id);
    $('#f_nik').val(data.nik);
    $('#f_nama').val(data.nama);
    $('#f_alamat').val(data.alamat);
    // ... isi field lainnya ...
    
    if(data.foto) {
        $('#preview_box').html(`<img src="{{ asset('storage/foto') }}/${data.foto}" class="img-fluid h-100">`);
    }
    
    $('#m_title').text('Edit: ' + data.nama);
    $('#modalNasabah').modal('show');
    Webcam.attach('#cam_box');
}

function do_snap() {
    Webcam.snap(uri => {
        $('#f_base64').val(uri);
        $('#preview_box').html(`<img src="${uri}" class="img-fluid h-100">`);
    });
}

function deleteData(id) {
    Swal.fire({ title: 'Hapus?', icon: 'warning', showCancelButton: true }).then(res => {
        if(res.isConfirmed) {
            let f = $('<form>', {action: `/nasabah/delete/${id}`, method: 'POST'});
            f.append('@csrf', '@method("DELETE")').appendTo('body').submit();
        }
    });
}
</script>