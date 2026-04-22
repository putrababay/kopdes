<div class="modal fade" id="modalProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-lg">
        <div class="modal-content rounded-4-desktop border-0">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="text-center mb-4">
                    <div id="prof-initial" class="avatar-initial-large mx-auto shadow"></div>
                    <img id="prof-foto" src="" class="avatar-initial-large mx-auto shadow d-none" style="object-fit: cover;">
                    <h4 id="prof-nama" class="fw-bold mt-3 mb-0"></h4>
                    <span id="prof-pekerjaan" class="badge bg-light text-primary rounded-pill mb-2"></span>
                    <p id="prof-alamat" class="text-muted small px-4"></p>
                    <div class="d-flex justify-content-center gap-2">
                        <a id="prof-tlp" href="" class="btn btn-success btn-sm rounded-pill px-3"><i class="bi bi-whatsapp"></i> Hubungi</a>
                    </div>
                </div>

                <div class="card bg-light border-0 rounded-4 mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3 small text-uppercase text-muted">Detail Pinjaman</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">ID Pinjaman</small>
                                <span id="prof-id" class="fw-bold"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Tanggal Pinjam</small>
                                <span id="prof-tgl" class="fw-bold"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Total Pinjam</small>
                                <span id="prof-t-pinjam" class="fw-bold"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Angsuran</small>
                                <span id="prof-pembayaran" class="fw-bold text-primary"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Jaminan</small>
                                <span id="prof-jaminan" class="small"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Progress</small>
                                <span id="prof-progress" class="fw-bold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="container-maps" class="mb-3 d-none">
                    <label class="small fw-bold mb-2"><i class="bi bi-geo-alt-fill"></i> Lokasi Nasabah</label>
                    <div id="map" style="height: 180px;" class="rounded-4 shadow-sm"></div>
                    <div class="mt-2">
                        <a id="link-gmaps" href="#" target="_blank" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="bi bi-cursor-fill"></i> Petunjuk Arah (Google Maps)
                        </a>
                    </div>
                </div>

                <h6 class="fw-bold mb-2 small text-uppercase text-muted">Riwayat Angsuran</h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm small table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Ke</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="list-riwayat-angsuran">
                        </tbody>
                    </table>
                </div>

                <div class="sticky-bottom bg-white pt-2">
                    <button id="btn-bayar-selanjutnya" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow">
                        Bayar Angsuran Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>