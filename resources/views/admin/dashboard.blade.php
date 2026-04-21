@extends('layouts.admin')

@section('content')
<style>
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .battery-container {
        border: 3px solid #333;
        border-radius: 10px;
        padding: 4px;
        position: relative;
        width: 100%;
        height: 50px;
        background: #fff;
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .battery-container::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 12px;
        width: 7px;
        height: 20px;
        background: #333;
        border-radius: 0 4px 4px 0;
    }

    .battery-level {
        height: 100%;
        border-radius: 6px;
        transition: width 1s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .card-today {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border: none;
        transform: scale(1.02);
    }
</style>

<div id="loading-overlay">
    <div class="spinner-grow text-primary" role="status"></div>
    <span class="mt-3 fw-bold">Memuat Laporan...</span>
</div>
<div class="card border-0 shadow-sm mb-4 bg-white">
    <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div class="flex-grow-1">
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                Halo, {{ $data['nama_user'] }}! <span class="ms-2">👋</span>
            </h4>
            <p class="text-muted mb-0 fs-md-6" style="font-size: 0.9rem;">
                <i class="far fa-clock me-1"></i> <span id="liveClock"></span>
            </p>
        </div>

        <div class="w-30 w-md-auto">
            <form id="filterForm" method="GET" class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <select name="bulan" class="form-select auto-submit shadow-sm border-secondary-subtle py-md-2"
                        style="min-width: 160px; font-weight: 500;">
                        @foreach([
                        '01'=>'Januari', '02'=>'Februari', '03'=>'Maret',
                        '04'=>'April', '05'=>'Mei', '06'=>'Juni',
                        '07'=>'Juli', '08'=>'Agustus', '09'=>'September',
                        '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                        ] as $k => $v)
                        <option value="{{ $k }}" {{ $bulan == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="position-relative">
                    <select name="tahun" class="form-select auto-submit shadow-sm border-secondary-subtle py-md-2"
                        style="min-width: 110px; font-weight: 500;">
                        @for($i = date('Y'); $i >= date('Y')-3; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <a href="{{ route('dashboard') }}" class="btn btn-light border border-secondary-subtle shadow-sm py-md-2 px-md-3" title="Reset Filter">
                    <i class="fas fa-sync-alt text-secondary"></i>
                </a>
            </form>
        </div>

    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <small class="text-muted d-block">Nasabah</small>
            <span class="h5 fw-bold mb-0 text-primary">{{ number_format($data['jum_all']) }}</span>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <small class="text-muted d-block">Pinjaman</small>
            <span class="h5 fw-bold mb-0 text-success">{{ number_format($data['jum_prose_dos']) }}</span>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <small class="text-muted d-block">Angsuran</small>
            <span class="h5 fw-bold mb-0 text-warning">{{ number_format($data['angsuran']) }}</span>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card card-today shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-calendar-day me-2"></i>KAS HARI INI</h6>
                    <span class="badge bg-white text-primary">LIVE</span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="opacity-75">Pemasukan</small>
                        <h5 class="fw-bold text-white">Rp{{ number_format($data['hari_ini']['masuk'],0,',','.') }}</h5>
                    </div>
                    <div class="col-6">
                        <small class="opacity-75">Pengeluaran</small>
                        <h5 class="fw-bold text-white">Rp{{ number_format($data['hari_ini']['keluar'],0,',','.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100 p-3 bg-light">
            <h6 class="text-muted small fw-bold mb-2">H-1 (KEMARIN)</h6>
            <div class="d-flex justify-content-between">
                <span class="text-success small">+{{ number_format($data['kemarin']['masuk'],0,',','.') }}</span>
                <span class="text-danger small">-{{ number_format($data['kemarin']['keluar'],0,',','.') }}</span>
            </div>
            <hr class="my-2">
            <div class="fw-bold small {{ $data['kemarin']['sisa'] >= 0 ? 'text-success' : 'text-danger' }}">
                Margin: Rp{{ number_format($data['kemarin']['sisa'],0,',','.') }}
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-battery-three-quarters me-2 text-primary"></i>Status Kesehatan Kas (Margin)
        </h6>
        <span class="badge {{ $data['nominal_margin'] >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
            Sisa: Rp{{ number_format($data['nominal_margin'], 0, ',', '.') }}
        </span>
    </div>

    <div class="battery-container my-3" style="height: 55px;">
        @php
        $p = $data['persen_margin'];
        // Penentuan warna berdasarkan sisa uang
        $color = 'bg-danger';
        if($p > 20) $color = 'bg-warning';
        if($p > 70) $color = 'bg-success';
        @endphp

        <div class="battery-level {{ $color }} shadow-sm"
            style="width: {{ $p }}%; transition: width 1.5s ease-in-out; display: flex; align-items: center; justify-content: center;">
            <span class="text-white fw-bold">{{ $p }}%</span>
        </div>
    </div>

    <div class="row mt-4 text-center">
        <div class="col-6 border-end">
            <p class="text-muted small mb-1">Total Pemasukan</p>
            <h6 class="fw-bold text-success">Rp{{ number_format($data['masuk_bln'], 0, ',', '.') }}</h6>
        </div>
        <div class="col-6">
            <p class="text-muted small mb-1">Total Pengeluaran</p>
            <h6 class="fw-bold text-danger">Rp{{ number_format($data['keluar_bln'], 0, ',', '.') }}</h6>
        </div>
    </div>

    <div class="mt-3 bg-light p-2 rounded text-center">
        <small class="text-muted" style="font-size: 0.75rem;">
            @if($p == 100)
            Luar biasa! Belum ada pengeluaran bulan ini.
            @elseif($p > 0)
            Dana tersedia sebesar <b>{{ $p }}%</b> dari total pemasukan.
            @else
            <i class="fas fa-exclamation-triangle text-danger me-1"></i>
            Peringatan: Pengeluaran melebihi atau sama dengan pemasukan!
            @endif
        </small>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3 small"><i class="fas fa-chart-line me-2"></i>Growth Kas Tahunan ({{ $tahun }})</h6>
            <div id="chartGrowth" style="height: 300px;"></div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3 small"><i class="fas fa-chart-bar me-2"></i>Frekuensi Pinjaman (Monthly)</h6>
            <div id="chartPinjam" style="height: 300px;"></div>
        </div>
    </div>
</div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        document.getElementById('liveClock').innerText = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Auto Submit & Loading
    document.querySelectorAll('.auto-submit').forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('loading-overlay').style.display = 'flex';
            document.getElementById('filterForm').submit();
        });
    });

    // Grafik 1: Arus Kas & Sisa
    Highcharts.chart('chartGrowth', {
        chart: {
            type: 'line'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
        },
        yAxis: {
            title: {
                text: 'Nominal'
            }
        },
        tooltip: {
            shared: true,
            valuePrefix: 'Rp'
        },
        credits: {
            enabled: false
        },
        // Grafik 1
        series: [{
            name: 'Pemasukan',
            color: '#1cc88a',
            data: @json($data['chart_masuk']) // Jauh lebih aman dan bersih
        }, {
            name: 'Pengeluaran',
            color: '#e74a3b',
            data: @json($data['chart_keluar'])
        }, {
            name: 'Sisa/Margin',
            color: '#f6c23e',
            dashStyle: 'dot',
            data: @json($data['chart_sisa'])
        }]
    });

    // Grafik 2: Jumlah Pinjaman (Bar Horizontal)
    Highcharts.chart('chartPinjam', {
        chart: {
            type: 'bar'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
        },
        yAxis: {
            title: {
                text: 'Jumlah Transaksi'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        // Grafik 2
        series: [{
            name: 'Transaksi',
            color: '#4e73df',
            data: @json($data['chart_count_pinjam'])
        }]
    });
</script>
@endsection