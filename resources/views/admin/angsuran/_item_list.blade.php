@forelse($nasabahs as $n)
<div class="col-12">
    <div class="card nasabah-card shadow-sm rounded-4 p-3 mb-2 border-0"
        onclick="showProfil('{{ $n->id }}')"
        style="cursor: pointer; background: linear-gradient(to right, #ffffff, #fcfcfc);">

        <div class="d-flex align-items-center">
            <div class="position-relative">
                <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                    style="width: 55px; height: 55px; font-size: 1.2rem;">
                    {{ substr($n->nama, 0, 1) }}
                </div>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                    <span class="visually-hidden">Aktif</span>
                </span>
            </div>

            <div class="flex-grow-1 ms-3">
                <h6 class="mb-0 fw-bold text-dark">{{ $n->nama }}</h6>
                <div class="text-muted small text-truncate" style="max-width: 150px;">
                    <i class="bi bi-geo-alt-fill text-danger" style="font-size: 0.75rem;"></i>
                    {{ $n->lokasi_penarikan ?? 'Lokasi tidak set' }}
                </div>
                <div class="mt-1">
                    <span class="badge rounded-pill bg-light text-primary border-primary border" style="font-size: 0.65rem;">
                        Tenor: {{ $n->angsuran }}x
                    </span>
                </div>
            </div>

            <div class="text-end">
                <div class="fw-bold text-primary mb-1" style="font-size: 1.1rem;">
                    Rp{{ number_format($n->pembayaran, 0, ',', '.') }}
                </div>
                <div class="d-flex flex-column align-items-end gap-1">
                    <span class="badge bg-primary bg-opacity-75 text-white px-2 py-1" style="font-size: 0.7rem;">
                        {{ $n->tempo_hari }}
                    </span>
                    <span class="badge bg-light text-dark border small px-2 py-1" style="font-size: 0.65rem;">
                        ID: #{{ $n->id }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12 text-center py-5">
    <div class="text-muted">
        <i class="bi bi-folder-x display-1"></i>
        <p class="mt-2">Tidak ada data nasabah aktif.</p>
    </div>
</div>
@endforelse