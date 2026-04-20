<style>
    .avatar-initial-large {
        width: 100px;
        height: 100px;
        background-color: #0d6efd;
        color: white;
        font-size: 3rem;
        font-weight: bold;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Agar modal pas di HP */
    @media (max-width: 576px) {
        .modal-body {
            padding: 1rem;
        }

        .avatar-initial-large {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
    }
</style>

<div class="modal fade" id="modalPinjam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formPinjam" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header border-0">
                    <h5 class="fw-bold text-primary" id="modalTitle">Tambah Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 text-center mb-4 mb-lg-0">
                            <div class="card bg-light border-0 rounded-4 p-3 h-100 d-flex align-items-center justify-content-center">
                                <div id="photo-container">
                                    <div class="avatar-initial-large shadow-sm mb-3" id="preview-initial">?</div>

                                    <img src="" id="preview-foto"
                                        class="img-fluid rounded-4 shadow-sm d-none"
                                        style="max-height: 200px; width: 100%; object-fit: cover;"
                                        onerror="this.classList.add('d-none'); document.getElementById('preview-initial').classList.remove('d-none');">
                                </div>
                                <div class="mt-3">
                                    <span id="nasabah-name-display" class="fw-bold d-block text-dark">-</span>
                                    <small id="nasabah-alamat-display" class="text-muted text-truncate d-block" style="max-width: 150px;">Pilih Nasabah</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Pilih Nasabah</label>
                                    <select name="id_nasaba" id="id_nasaba" class="form-control select2-nasabah" required data-placeholder="Cari Nama atau Alamat...">
                                        <option value=""></option>
                                        @foreach($all_nasabahs as $nas)
                                        <option value="{{ $nas->id }}"
                                            data-foto="{{ $nas->foto ? asset('foto/'.$nas->foto) : '' }}"
                                            data-nama="{{ $nas->nama }}"
                                            data-alamat="{{ $nas->alamat }}">
                                            {{ $nas->nama }} - {{ $nas->alamat }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Lokasi Penarikan</label>
                                    <input type="text" name="lokasi_penarikan" id="lokasi_penarikan" class="form-control bg-light border-0" placeholder="Lokasi penarikan">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Jaminan</label>
                                    <input type="text" name="jaminan" id="jaminan" class="form-control bg-light border-0" placeholder="Jaminan (misal: BPKB, Sertifikat, dll.)" required>
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold">Tgl Pinjam</label>
                                    <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control bg-light border-0" required>
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold">Tempo Hari</label>
                                    <select name="tempo_hari" id="tempo_hari" class="form-select bg-light border-0">
                                        @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'] as $hari)
                                        <option value="{{ $hari }}">{{ $hari }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold">Nominal Cair</label>
                                    <input type="text" id="display_pinjam" class="form-control bg-light border-0 rupiah-input" placeholder="Rp 0">
                                    <input type="hidden" name="pinjam" id="pinjam">
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold">Total Pinjaman</label>
                                    <input type="text" id="display_t_pinjam" class="form-control bg-light border-0 rupiah-input" placeholder="Rp 0">
                                    <input type="hidden" name="t_pinjam" id="t_pinjam">
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold">Angsuran (Kali)</label>
                                    <input type="number" name="angsuran" id="angsuran" class="form-control bg-light border-0" value="10">
                                </div>

                                <div class="col-6">
                                    <label class="form-label small fw-bold text-primary">Pembayaran</label>
                                    <input type="text" id="display_pembayaran" class="form-control bg-primary bg-opacity-10 border-0 fw-bold text-primary" readonly>
                                    <input type="hidden" name="pembayaran" id="pembayaran">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Status</label>
                                    <select name="status" id="status" class="form-select bg-light border-0">
                                        <option value="AKTIF">AKTIF</option>
                                        <option value="LUNAS">LUNAS</option>
                                        <option value="MACET">MACET</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Rencana Tanggal Angsuran</label>
                                    <div id="jadwal-preview" class="border rounded-3 p-2 bg-white" style="max-height: 120px; overflow-y: auto;">
                                    </div>
                                </div>

                                <input type="hidden" name="detail_tgl" id="detail_tgl">
                                <input type="hidden" name="tgl_akhir" id="tgl_akhir">


                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>