@foreach($nasabahs as $n)
<div class="col-12 nasabah-item">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="card-body p-0">
            <div class="d-flex align-items-center p-3 bg-white cursor-pointer nasabah-row collapsed"
                data-bs-toggle="collapse" data-bs-target="#detail_{{ $n->id }}">

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <h6 class="fw-bold mb-0 text-dark">{{ $n->nama }}</h6>
                        <span class="badge bg-blue-soft text-primary rounded-pill ms-2 sm-font" style="background:#eef2ff; font-size: 0.7rem;">
                            {{ $n->jumlah_transaksi }}x Transaksi
                        </span>
                    </div>
                    <div class="desktop-info d-none d-md-flex gap-3 small text-muted">

                        <span><i class="bi bi-person me-1"></i> {{ $n->id }}</span>
                        <span><i class="bi bi-briefcase me-1"></i> {{ $n->pekerjaan }}</span>
                        <span><i class="bi bi-clock-history me-1"></i> Terakhir: <b>{{ $n->pinjaman_terakhir ?? '-' }}</b></span>
                        <span><i class="bi bi-geo-alt me-1"></i> {{ $n->alamat }}</span>
                    </div>
                    <div class="mobile-info d-md-none small text-muted">
                        <span><i class="bi bi-person me-1"></i> {{ $n->id }}</span>
                        <span><i class="bi bi-briefcase me-1"></i> {{ Str::limit($n->pekerjaan, 30) }}</span>
                        <div><i class="bi bi-clock-history me-1"></i> {{ $n->pinjaman_terakhir ?? '-' }}</div>
                    </div>
                </div>


                <div class="d-flex align-items-center ms-3">
                    <div class="avatar-wrapper me-3">
                        <div class="position-relative">
                            @if($n->foto && file_exists(public_path('foto/'.$n->foto)))
                            {{-- Tampilan Jika Ada Foto --}}
                            <img src="{{ asset('foto/'.$n->foto) }}"
                                class="rounded-4 shadow-sm border border-2 border-white"
                                style="width: 55px; height: 55px; object-fit: cover;">
                            @else
                            {{-- Tampilan Jika Tidak Ada Foto (Inisial) --}}
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                style="width: 55px; height: 55px; font-size: 1.2rem; border: 1px solid rgba(13, 110, 253, 0.1);">
                                {{ strtoupper(substr($n->nama, 0, 1)) }}
                            </div>
                            @endif

                            {{-- Indikator Status Aktif --}}
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle shadow-sm"
                                style="width: 12px; height: 12px;">
                                <span class="visually-hidden">Aktif</span>
                            </span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-down text-muted"></i>
                </div>

            </div>

            <div class="collapse" id="detail_{{ $n->id }}">
                <div class="bg-light p-3 border-top">
                    <div class="d-none d-md-block table-responsive bg-white rounded-4 shadow-sm">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3">KODE</th>
                                    <th>NOMINAL</th>
                                    <th>ANGSURAN</th>
                                    <th>TOTAL</th>
                                    <th>TGL PINJAM</th>
                                    <th>STATUS</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @foreach($n->pinjamans as $p)
                                <tr>
                                    <td class="ps-3 fw-bold">#{{ $p->id }}</td>
                                    <td class="fw-bold text-dark">Rp{{ number_format($p->pinjam, 0, ',', '.') }}</td>
                                    <td>{{ number_format($p->angsuran, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-primary">Rp{{ number_format($p->t_pinjam, 0, ',', '.') }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tgl_pinjam)) }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $p->status == 'LUNAS' ? 'success' : 'warning' }} px-3">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-primary me-1" onclick="event.stopPropagation(); showProfil('{{ $p->id }}')"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-light text-warning me-1" onclick="event.stopPropagation(); editPinjaman(...)"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btn-light text-danger" onclick="event.stopPropagation(); confirmDelete('{{ $p->id }}')"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-md-none">
                        @foreach($n->pinjamans as $p)
                        <div class="card border-0 shadow-sm rounded-3 mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-primary">#{{ $p->id }}</span>
                                    <span class="badge bg-{{ $p->status == 'LUNAS' ? 'success' : 'warning' }} rounded-pill">{{ $p->status }}</span>
                                </div>
                                <div class="row g-2 mb-2 small">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nominal</small>
                                        <span class="fw-bold">Rp{{ number_format($p->pinjam, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted d-block">Total</small>
                                        <span class="fw-bold text-primary">Rp{{ number_format($p->t_pinjam, 0, ',', '.') }}</span>
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
@endforeach