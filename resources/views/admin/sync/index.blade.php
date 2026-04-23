@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4">
                <div class="card-body p-3">
                    <div class="small opacity-75">Total Data Lokal (Offline)</div>
                    <h3 class="fw-bold mb-0" id="total-offline">{{ $stats['total_offline'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white rounded-4">
                <div class="card-body p-3">
                    <div class="small opacity-75">Total Data Server (Online)</div>
                    <h3 class="fw-bold mb-0" id="total-online">{{ $stats['total_online'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

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