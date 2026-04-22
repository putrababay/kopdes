<div class="modal fade" id="modalPulsa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formPulsa" class="w-100">
            @csrf
            <input type="hidden" name="id_pinjam" id="id_pinjam">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Pinjam Pulsa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nasabah</label>
                        <select name="id_nasaba" id="id_nasaba" class="form-select select2-nasabah" required>
                            <option value="">Cari Nama Nasabah...</option>
                            @foreach(\App\Models\Nasabah::orderBy('nama', 'ASC')->get() as $nasabah)
                            <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->alamat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor HP Tujuan</label>
                        <input type="text" name="nomer" id="nomer" class="form-control bg-light border-0" placeholder="08xx..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nominal Harga (Rp)</label>
                        <input type="text" id="harga_display" class="form-control bg-light border-0" placeholder="Contoh: 12.000" required>
                        <input type="hidden" name="harga" id="harga">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" id="status" class="form-select bg-light border-0" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="0">Belum Lunas</option>
                            <option value="1">Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
                </div>
            </div>
        </form>
    </div>
</div>