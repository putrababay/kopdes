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
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Pilih Nasabah</label>
                            <select name="id_nasaba" id="id_nasaba" class="form-control select2-nasabah" style="width: 100%" required>
                                <option value="">Cari Nama atau Alamat...</option>
                                @foreach($all_nasabahs as $nas)
                                    {{-- Data atribut 'data-alamat' digunakan untuk pencarian kustom --}}
                                    <option value="{{ $nas->id }}" data-alamat="{{ $nas->alamat }}">
                                        {{ $nas->nama }} - {{ $nas->alamat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
    <label class="form-label small fw-bold">Lokasi Penarikan</label>
    <input type="text" name="lokasi_penarikan" id="lokasi_penarikan" class="form-control bg-light border-0" placeholder="Contoh: Kantor, Rumah, atau Pasar">
</div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Pinjam</label>
                            <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempo Hari</label>
                            <select name="tempo_hari" id="tempo_hari" class="form-select bg-light border-0">
                                @foreach(['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nominal Cair (Terima)</label>
                            <input type="text" id="display_pinjam" class="form-control bg-light border-0 rupiah-input" placeholder="Rp 0">
                            <input type="hidden" name="pinjam" id="pinjam">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Total Pinjaman (T_Pinjam)</label>
                            <input type="text" id="display_t_pinjam" class="form-control bg-light border-0 rupiah-input" placeholder="Rp 0">
                            <input type="hidden" name="t_pinjam" id="t_pinjam">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Angsuran (Kali)</label>
                            <input type="number" name="angsuran" id="angsuran" class="form-control bg-light border-0" min="1" max="20" value="10">
                        </div>
<div class="col-md-6">
    <label class="form-label small fw-bold text-primary">Pembayaran / Angsuran</label>
    <input type="text" id="display_pembayaran" class="form-control bg-primary bg-opacity-10 border-0 fw-bold text-primary" readonly placeholder="Rp 0">
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
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Data Pinjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>