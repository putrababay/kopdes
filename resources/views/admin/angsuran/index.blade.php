@extends('layouts.admin')

@section('content')

<style>
    /* Styling khusus mobile */
    .nasabah-card {
        transition: transform 0.2s;
        border: 1px solid #eee !important;
    }

    .nasabah-card:active {
        transform: scale(0.98);
    }

    .sticky-filter {
        position: sticky;
        top: 0;
        z-index: 1020;
        background: white;
        padding: 10px 0;
    }

    #map {
        height: 200px;
        width: 100%;
        border-radius: 15px;
    }

    @media (max-width: 576px) {

        /* Modal Fullscreen di HP */
        .modal-fullscreen-sm-down .modal-content {
            height: 100%;
            border-radius: 0;
        }

        .avatar-initial-large {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        /* Tombol melayang di bawah agar mudah dijangkau jempol */
        .sticky-bottom {
            position: sticky;
            bottom: 0;
            margin: 0 -1rem -1rem -1rem;
            padding: 1rem;
            box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.05);
        }
    }

    .rounded-4-desktop {
        border-radius: 1.5rem;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="container-fluid pb-5">
    <div class="sticky-filter border-bottom mb-3">
        <form action="{{ route('angsuran.index') }}" method="GET" id="form-filter">
            <div class="row g-2 px-2">
                <div class="col-4">
                    <select name="hari" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        @foreach(['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','MINGGU'] as $h)
                        <option value="{{ $h }}" {{ $harifilter == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-8">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari nasabah..." value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>

    <div id="nasabah-container" class="row g-3 px-2">
        @include('admin.angsuran._item_list')
    </div>

    <div id="loading" class="text-center my-4 d-none">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
</div>

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

<script>
    // Variable untuk Lazy Load
    let page = 1;
    let loading = false;

    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!loading) {
                page++;
                loadMoreData(page);
            }
        }
    });

    function loadMoreData(page) {
        loading = true;
        $('#loading').removeClass('d-none');

        $.ajax({
            url: "?page=" + page + "&hari={{ $harifilter }}&search={{ request('search') }}",
            type: "get",
            success: function(data) {
                if (data.trim() == "") return;
                $("#nasabah-container").append(data);
                loading = false;
                $('#loading').addClass('d-none');
            }
        });
    }

    // Fungsi Show Profil & Maps
    let mapProfil;

    // ... (Bagian atas sama seperti codingan Anda)

    function showProfil(id_pinjam) {
        if (!id_pinjam) return;

        $('#modalProfil').modal('show');
        // Bersihkan riwayat lama agar tidak bingung saat loading
        $('#list-riwayat-angsuran').html('<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

        // 1. Buat template URL dengan placeholder __ID__
        let urlTemplate = "{{ route('angsuran.get-detail-nasabah', ['id' => '__ID__']) }}";

        // 2. Ganti placeholder dengan variabel id_pinjam yang asli
        let finalUrl = urlTemplate.replace('__ID__', id_pinjam);


        $.ajax({
            // SESUAIKAN URL INI DENGAN ROUTE DI WEB.PHP
            url: finalUrl,
            method: "GET",
            success: function(res) {
                // n = data master_nasabah, p = data nasabah_pinjam, a = data angsuran
                const n = res.nasabah;
                const p = res.pinjam;
                const a = res.angsuran;

                $('#prof-nama').text(n.nama || '-');
                $('#prof-nik').text(n.nik || '-');
                $('#prof-alamat').text(n.alamat || '-');
                $('#prof-pekerjaan').text(n.pekerjaan || '-');
                $('#prof-tlp').attr('href', 'https://wa.me/' + (n.no_tlp || ''));

                if (n.foto) {
                    $('#prof-foto').attr('src', "{{ asset('foto') }}/" + n.foto).removeClass('d-none');
                    $('#prof-initial').addClass('d-none');
                } else {
                    $('#prof-initial').text((n.nama || '?').charAt(0)).removeClass('d-none');
                    $('#prof-foto').addClass('d-none');
                }

                $('#prof-t-pinjam').text('Rp ' + parseInt(p.t_pinjam || 0).toLocaleString('id-ID'));
                $('#prof-pembayaran').text('Rp ' + parseInt(p.pembayaran || 0).toLocaleString('id-ID'));
                $('#prof-jaminan').text(p.jaminan || '-');

                const jumBayar = a.length;
                const totalHarus = parseInt(p.angsuran || 0);
                $('#prof-progress').html(`${jumBayar} / ${totalHarus} <small class='text-muted'>Kali</small>`);

                // Maps Logic
                if (n.lat && n.lng && n.lat !== "0" && n.lng !== "0") {
                    $('#container-maps').removeClass('d-none');
                    setTimeout(() => {
                        if (mapProfil) mapProfil.remove();
                        mapProfil = L.map('map').setView([n.lat, n.lng], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapProfil);
                        L.marker([n.lat, n.lng]).addTo(mapProfil).bindPopup(n.nama);
                        $('#link-gmaps').attr('href', `https://www.google.com/maps?q=${n.lat},${n.lng}`);
                    }, 400);
                } else {
                    $('#container-maps').addClass('d-none');
                }

                // Render Tabel Angsuran
                let htmlAngsuran = '';

                if (a.length > 0) {
                    // 1. Parsing jadwal tanggal dan cek keberadaannya (Isset logic)
                    let jadwalTanggal = null;
                    if (p.detail_tgl) {
                        try {
                            jadwalTanggal = typeof p.detail_tgl === 'string' ? JSON.parse(p.detail_tgl) : p.detail_tgl;
                        } catch (e) {
                            console.error("Gagal parse detail_tgl", e);
                        }
                    }

                    a.forEach((item, index) => {
                        let isMatch = false;

                        // 2. Logika Pengecekan (Isset)
                        if (!jadwalTanggal || jadwalTanggal.length === 0) {
                            // Jika detail_tgl kosong/tidak ada, otomatis dianggap hijau (sesuai permintaan)
                            isMatch = true;
                        } else {
                            // Jika ada, bandingkan dengan tanggal yang sesuai index angsurannya
                            const targetTgl = jadwalTanggal[item.angsuran - 1];
                            const tglBayar = item.tgl; // Format YYYY-MM-DD
                            isMatch = (targetTgl === tglBayar);
                        }

                        // 3. Render Icon
                        const checkIcon = isMatch ? '<i class="bi bi-patch-check-fill text-success ms-1" title="Sesuai Jadwal"></i>' : '';

                        htmlAngsuran += `
        <tr>
            <td><span class="badge bg-secondary">${item.angsuran}</span></td>
            <td>
                ${new Date(item.tgl).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}
                ${checkIcon}
            </td>
            <td class="fw-bold text-success">
                Rp ${parseInt(item.nominal).toLocaleString('id-ID')}
            </td>
            <td class="text-end">
                <button onclick="hapusAngsuran('${item.id}')" class="btn btn-outline-danger btn-sm border-0">
                    <i class="bi bi-trash"></i>
                </button>
                <button onclick="printStruk('${item.id}')" class="btn btn-outline-primary btn-sm border-0">
                    <i class="bi bi-printer"></i>
                </button>
            </td>
        </tr>`;
                    });
                } else {
                    htmlAngsuran = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada catatan angsuran</td></tr>';
                }
                $('#list-riwayat-angsuran').html(htmlAngsuran);

                // Tombol Bayar
                let btnBayar = $('#btn-bayar-selanjutnya');
                if (jumBayar >= totalHarus) {
                    btnBayar.attr('disabled', true).text('PINJAMAN LUNAS').removeClass('btn-primary').addClass('btn-success');
                } else {
                    btnBayar.attr('disabled', false)
                        .html(`<i class="bi bi-cash-coin me-2"></i> Bayar Angsuran Ke-${jumBayar + 1}`)
                        .removeClass('btn-success').addClass('btn-primary')
                        .attr('onclick', `window.location.href='/pembayaran?id_pinjam=${p.id}&ke=${jumBayar + 1}'`);
                }
            },
            error: function() {
                alert('Gagal mengambil data nasabah.');
                $('#modalProfil').modal('hide');
            }
        });
    }
    // ...

    function printStruk(id_angsuran) {
        window.open("{{ url('print-angsuran') }}/" + id_angsuran, '_blank');
    }
</script>
@endsection