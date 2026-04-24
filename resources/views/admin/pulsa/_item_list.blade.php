@forelse($nasabahs as $n)
<div class="col-12">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-2">
        <div class="card-body p-0">
            <div class="d-flex align-items-center p-3 bg-white cursor-pointer nasabah-row collapsed border-bottom"
                data-bs-toggle="collapse"
                data-bs-target="#detail_{{ $n->id }}"
                style="transition: all 0.2s ease;">

                <div class="avatar-wrapper me-3">
                    <div class="position-relative">
                        @if($n->foto && file_exists(public_path('foto/'.$n->foto)))
                        <img src="{{ asset('foto/'.$n->foto) }}"
                            class="rounded-4 shadow-sm border border-2 border-white"
                            style="width: 55px; height: 55px; object-fit: cover;">
                        @else
                        <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width: 55px; height: 55px; font-size: 1.2rem; border: 1px solid rgba(13, 110, 253, 0.1);">
                            {{ strtoupper(substr($n->nama, 0, 1)) }}
                        </div>
                        @endif

                        {{-- Indikator Status Aktif (Hanya muncul jika ada pinjaman belum lunas) --}}
                        @if($n->pulsaPinjam->where('status', '0')->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle shadow-sm"
                            style="width: 14px; height: 14px;">
                        </span>
                        @endif
                    </div>
                </div>

                <div class="flex-grow-1 min-width-0">
                    <h6 class="fw-bold mb-1 text-dark text-truncate" style="letter-spacing: -0.3px;">{{ $n->nama }}</h6>

                    <div class="d-flex flex-column gap-1">
                        <div class="small text-muted text-truncate" style="font-size: 0.75rem;">
                            <i class="bi bi-briefcase me-1 text-primary opacity-75"></i> {{ $n->pekerjaan }}
                        </div>
                        <div class="small text-muted text-truncate" style="font-size: 0.75rem;">
                            <i class="bi bi-geo-alt me-1 text-primary opacity-75"></i> {{ Str::limit($n->alamat, 40) }}
                        </div>
                    </div>
                </div>

                <div class="ms-3 text-end me-2">
                    <small class="text-muted d-block" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 600;">Total Tagihan</small>
                    <span class="fw-bold text-danger d-block" style="font-size: 0.9rem;">
                        Rp {{ number_format($n->pulsaPinjam->sum('harga'), 0, ',', '.') }}
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mt-1" style="font-size: 0.6rem; padding: 0.35em 0.65em;">
                        {{ $n->pulsaPinjam->count() }} Transaksi
                    </span>
                </div>

                <div class="text-muted opacity-50">
                    <i class="bi bi-chevron-down shadow-icon"></i>
                </div>
            </div>

            <style>
                .nasabah-row:hover {
                    background-color: #f8f9fa !important;
                }

                .nasabah-row:not(.collapsed) {
                    background-color: #f1f5ff !important;
                    /* Warna sedikit biru saat terbuka */
                }

                .nasabah-row:not(.collapsed) i.bi-chevron-down {
                    transform: rotate(180deg);
                    transition: transform 0.3s ease;
                }

                .min-width-0 {
                    min-width: 0;
                }

                /* Penting agar text-truncate bekerja dalam flex */
            </style>

            <div class="collapse" id="detail_{{ $n->id }}">
                <div class="bg-light p-3 border-top">
                    <div class="table-responsive bg-white rounded-4 shadow-sm">
                        <div class="d-none d-md-block table-responsive bg-white rounded-3 shadow-sm">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th>No</th>
                                        <th class="ps-3">TANGGAL</th>
                                        <th>NOMOR HP</th>
                                        <th>NOMINAL</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @php
                                    $no = 1;
                                    @endphp
                                    @foreach($n->pulsaPinjam as $p)
                                    <tr>
                                        <td class="ps-3">{{ $no++ }}</td>
                                        <td class="ps-3">{{date('d-m-Y', strtotime($p->jam_tgl)) . ' ' . date('H:i:s', strtotime($p->jam_tgl)) }}</td>
                                        <td class="fw-bold">{{ $p->nomer }}</td>
                                        <td class="text-danger fw-bold">Rp {{ number_format($p->harga) }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill {{ $p->status == '0' ? 'bg-danger' : 'bg-success' }}">
                                                {{ $p->status == '0' ? 'BELUM LUNAS' : 'LUNAS' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light text-warning me-1" onclick="event.stopPropagation(); editPulsa('{{ $p->id_pinjam }}', '{{ $n->id }}', '{{ $p->nomer }}', '{{ $p->harga }}', '{{ $p->status }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light text-danger" onclick="event.stopPropagation(); deletePulsa('{{ $p->id_pinjam }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none">
                            @php
                            $noo = 1;
                            @endphp
                            @foreach($n->pulsaPinjam as $p)
                            <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <small class="text-muted d-block mb-1">{{ $noo++ }}. {{date('d-m-Y', strtotime($p->jam_tgl)) . ' ' . date('H:i:s', strtotime($p->jam_tgl)) }}</small>
                                            <h6 class="fw-bold mb-0">{{ $p->nomer }}</h6>
                                        </div>
                                        <span class="badge {{ $p->status == '0' ? 'bg-danger' : 'bg-success' }} rounded-pill">
                                            {{ $p->status == '0' ? 'BELUM LUNAS' : 'LUNAS' }}
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                        <div class="text-danger fw-bold">
                                            Rp {{ number_format($p->harga) }}
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-warning border-0 me-2" onclick="event.stopPropagation(); editPulsa('{{ $p->id_pinjam }}', '{{ $n->id }}', '{{ $p->nomer }}', '{{ $p->harga }}', '{{ $p->status }}')">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger border-0" onclick="event.stopPropagation(); deletePulsa('{{ $p->id_pinjam }}')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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