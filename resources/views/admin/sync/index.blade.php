@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <i class="bi bi-pc-display h2 h1-md mb-0 me-2 me-md-3 opacity-50"></i>
                    <div>
                        <div class="small opacity-75 d-none d-sm-block">Data Lokal (Offline)</div>
                        <div class="small opacity-75 d-block d-sm-none">Lokal</div>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['total_offline']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <i class="bi bi-cloud-check h2 h1-md mb-0 me-2 me-md-3 opacity-50"></i>
                    <div>
                        <div class="small opacity-75 d-none d-sm-block">Data Server (Online)</div>
                        <div class="small opacity-75 d-block d-sm-none">Online</div>
                        <h3 class="fw-bold mb-0">{{ is_numeric($stats['total_online']) ? number_format($stats['total_online']) : $stats['total_online'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Detail Perbandingan Tabel</h6>
        </div>

        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th class="ps-4">Nama Tabel</th>
                        <th class="text-center">Lokal</th>
                        <th class="text-center">Online</th>
                        <th class="text-center">Belum Backup</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['detail'] as $row)
                    <tr>
                        <td class="ps-4 fw-medium">{{ $row['nama_tabel'] }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ number_format($row['lokal']) }}</span></td>
                        <td class="text-center">
                            @if($row['online'] === 'ERR')
                            <span class="badge bg-danger-subtle text-danger border-0">Offline</span>
                            @else
                            <span class="badge bg-light text-dark border">{{ number_format($row['online']) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['selisih'] > 0)
                            <span class="badge bg-warning text-dark border-0">
                                <i class="bi bi-arrow-up-circle-fill me-1"></i>{{ number_format($row['selisih']) }} data
                            </span>
                            @else
                            <span class="badge bg-success-subtle text-success border-0"><i class="bi bi-check-circle-fill me-1"></i>Sinkron</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-md-none border-top">
            @foreach($stats['detail'] as $row)
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-primary">{{ $row['nama_tabel'] }}</span>
                    @if($row['selisih'] > 0)
                    <span class="badge bg-warning text-dark small">
                        {{ number_format($row['selisih']) }} baru
                    </span>
                    @else
                    <span class="text-success small"><i class="bi bi-check-circle-fill"></i></span>
                    @endif
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Lokal: <strong>{{ number_format($row['lokal']) }}</strong></span>
                    <span>Online: <strong>{{ $row['online'] === 'ERR' ? 'Error' : number_format($row['online']) }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <style>
        /* Animasi halus untuk badge peringatan */
        .badge.bg-warning {
            animation: pulse-subtle 2s infinite;
        }

        @keyframes pulse-subtle {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* Penyesuaian ukuran teks untuk mobile */
        @media (max-width: 576px) {
            h3 {
                font-size: 1.25rem;
            }

            .h2 {
                font-size: 1.5rem !important;
            }
        }
    </style>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear-fill me-2"></i>Konfigurasi Hosting</h6>
                    <hr>
                    <form action="{{ route('pengaturan.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold">IP Host Online</label>
                            <input type="text" name="db_host_online" class="form-control" value="{{ $settings['db_host_online'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Nama Database</label>
                            <input type="text" name="db_database_online" class="form-control" value="{{ $settings['db_database_online'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Username DB</label>
                            <input type="text" name="db_username_online" class="form-control" value="{{ $settings['db_username_online'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Password DB</label>
                            <input type="password" name="db_password_online" class="form-control" value="{{ $settings['db_password_online'] ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Status Backup Data</h6>

                    <div id="sync-status" class="alert alert-light border small">
                        Klik tombol di bawah untuk mulai mencadangkan data lokal ke server online.
                    </div>

                    <div class="progress mb-3" style="height: 12px;">
                        <div id="overall-bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>

                    <div id="table-logs" class="list-group list-group-flush border rounded-3 mb-4" style="max-height: 250px; overflow-y: auto;">
                    </div>

                    <button id="btn-sync" class="btn btn-success px-4 rounded-pill">
                        <i class="bi bi-play-circle me-2"></i>Mulai Sinkronisasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-sync').addEventListener('click', function() {
        const btn = this;
        const logContainer = document.getElementById('table-logs');
        const statusMsg = document.getElementById('sync-status');
        const bar = document.getElementById('overall-bar');

        btn.disabled = true;
        logContainer.innerHTML = '';
        bar.style.width = '0%';

        const source = new EventSource("{{ route('sync.start') }}");

        source.addEventListener('progress', (e) => {
            const data = JSON.parse(e.data);
            bar.style.width = data.overall + '%';
            statusMsg.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> ${data.msg}`;
        });

        source.addEventListener('table_complete', (e) => {
            const data = JSON.parse(e.data);
            logContainer.insertAdjacentHTML('afterbegin', `
                <div class="list-group-item d-flex justify-content-between align-items-center py-2 small">
                    <span><i class="bi bi-check-circle text-success me-2"></i>${data.table}</span>
                    <span class="badge bg-info rounded-pill">${data.count} data baru</span>
                </div>
            `);
        });

        source.addEventListener('complete', (e) => {
            const data = JSON.parse(e.data);
            statusMsg.className = "alert alert-success border small";
            statusMsg.innerHTML = `<i class="bi bi-cloud-check me-2"></i>${data.message}`;
            source.close();
            btn.disabled = false;
            // Refresh halaman setelah 2 detik untuk memperbarui angka statistik
            setTimeout(() => location.reload(), 2000);
        });

        source.addEventListener('error', (e) => {
            let errorMsg = "Terjadi kesalahan koneksi.";
            try {
                const data = JSON.parse(e.data);
                errorMsg = `Gagal di ${data.table}: ${data.message}`;
            } catch (err) {}

            logContainer.insertAdjacentHTML('afterbegin', `<div class="list-group-item text-danger py-2 small">${errorMsg}</div>`);
            btn.disabled = false;
            source.close();
        });
    });
</script>
@endsection