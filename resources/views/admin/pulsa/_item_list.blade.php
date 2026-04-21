@forelse($nasabahs as $n)
<div class="col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-2">
        <div class="card-body p-0">
            <div class="d-flex align-items-center p-3 bg-white cursor-pointer nasabah-row collapsed" 
                 data-bs-toggle="collapse" data-bs-target="#detail_{{ $n->id }}">
                
                <div class="avatar-wrapper me-3">
                    <div class="avatar-initial">{{ substr($n->nama, 0, 1) }}</div>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <h6 class="fw-bold mb-0 text-dark">{{ $n->nama }}</h6>
                        <span class="badge bg-soft-primary text-primary rounded-pill ms-2" style="background:#eef2ff; font-size: 0.7rem;">
                            {{ $n->pulsa_pinjam_count }} Transaksi
                        </span>
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($n->alamat, 50) }}
                    </div>
                </div>

                <div class="ms-3 text-end d-none d-md-block me-4">
                    <small class="text-muted d-block">Total Hutang</small>
                    <span class="fw-bold text-danger">Rp {{ number_format($n->pulsaPinjam->sum('harga')) }}</span>
                </div>

                <i class="bi bi-chevron-down text-muted"></i>
            </div>

            <div class="collapse" id="detail_{{ $n->id }}">
                <div class="bg-light p-3 border-top">
                    <div class="table-responsive bg-white rounded-4 shadow-sm">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3">TANGGAL</th>
                                    <th>NOMOR HP</th>
                                    <th>NOMINAL</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @foreach($n->pulsaPinjam as $p)
                                <tr>
                                    <td class="ps-3">{{ $p->jam_tgl }}</td>
                                    <td class="fw-bold">{{ $p->nomer }}</td>
                                    <td class="text-danger fw-bold">Rp {{ number_format($p->harga) }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-warning me-1" 
                                            onclick="event.stopPropagation(); editPulsa('{{ $p->id_pinjam }}', '{{ $n->id }}', '{{ $p->nomer }}', '{{ $p->harga }}')">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light text-danger" 
                                            onclick="event.stopPropagation(); deletePulsa('{{ $p->id_pinjam }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12 text-center py-5">
    <i class="bi bi-emoji-frown text-muted" style="font-size: 3rem;"></i>
    <p class="text-muted mt-2">Data tidak ditemukan</p>
</div>
@endforelse