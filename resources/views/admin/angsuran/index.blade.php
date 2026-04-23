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
        top: 50px;
        z-index: 100;
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

    /* Memastikan SweetAlert selalu berada di depan modal Bootstrap manapun */
    .swal2-container {
        z-index: 9999 !important;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


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
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari nasabah..." value="{{ request('search') }}">

                    @if(request('search'))
                    <a href="{{ route('angsuran.index', ['hari' => $harifilter]) }}" class="btn btn-light border-0 text-muted" title="Bersihkan Pencarian">
                        <i class="bi bi-x-circle-fill"></i>
                    </a>
                    @endif

                    <button class="btn btn-light border-0" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="nasabah-container" class="row g-1 px-1">
    @include('admin.angsuran._item_list')
</div>

<div id="loading" class="text-center my-4 d-none">
    <div class="spinner-border text-primary" role="status"></div>
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
                    <img id="prof-foto" src="" class="avatar-initial-large mx-auto shadow d-none" style="object-fit: cover !important; width: 200px; height: 200px; border-radius: 15px;">
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

<script>
    // --- Global Variables ---
    let page = 1;
    let loading = false;
    let mapProfil;

    // --- Lazy Load Scroll ---
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

    // --- Fungsi Utama: Show Profil ---
    function showProfil(id_pinjam) {
        if (!id_pinjam) return;

        $('#modalProfil').modal('show');
        // Reset view ke state loading
        $('#list-riwayat-angsuran').html('<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

        let urlTemplate = "{{ route('angsuran.get-detail-nasabah', ['id' => '__ID__']) }}";
        let finalUrl = urlTemplate.replace('__ID__', id_pinjam);

        $.ajax({
            url: finalUrl,
            method: "GET",
            success: function(res) {
                const n = res.nasabah;
                const p = res.pinjam;
                const a = res.angsuran;

                // 1. Bind Data Identitas (Handling Null)
                $('#prof-id').text('#' + p.id_pinjam_asli || '-');
                $('#prof-tgl').text(p.tgl_pinjam || '-');
                $('#prof-nama').text(n.nama || '-');
                $('#prof-nik').text(n.nik || '-');
                $('#prof-alamat').text(n.alamat || '-');
                $('#prof-pekerjaan').text(n.pekerjaan || '-');
                $('#prof-tlp').attr('href', 'https://wa.me/' + (n.no_tlp || ''));

                // Handle Foto
                if (n.foto) {
                    $('#prof-foto').attr('src', "{{ asset('foto') }}/" + n.foto).removeClass('d-none');
                    $('#prof-initial').addClass('d-none');
                } else {
                    $('#prof-initial').text((n.nama || '?').charAt(0)).removeClass('d-none');
                    $('#prof-foto').addClass('d-none');
                }

                // 2. Bind Data Keuangan (Mencegah NaN)
                const totalPinjam = parseInt(p.t_pinjam) || 0;
                const totalBayar = parseInt(p.pembayaran) || 0; // Ambil dari p.pembayaran

                $('#prof-t-pinjam').text('Rp ' + totalPinjam.toLocaleString('id-ID'));
                $('#prof-pembayaran').text('Rp ' + totalBayar.toLocaleString('id-ID'));
                $('#prof-jaminan').text(p.jaminan || '-');

                // 3. Progress Angsuran
                const jumBayar = a.length;
                const targetAngsuran = parseInt(p.angsuran) || 0;
                $('#prof-progress').html(`${jumBayar} / ${targetAngsuran} <small class='text-muted'>Kali</small>`);

                // 4. Handle Google Maps
                handleMaps(n);

                // 5. Render Tabel & Tombol
                renderTabelAngsuran(a, p);
                handleTombolBayar(p, jumBayar, targetAngsuran);
            },
            error: function() {
                alert('Gagal mengambil data nasabah.');
                $('#modalProfil').modal('hide');
            }
        });
    }

    // --- Fungsi Render Tabel ---
    function renderTabelAngsuran(a, p) {
        let htmlStr = '';
        let jadwalArr = [];
        try {
            jadwalArr = typeof p.detail_tgl === 'string' ? JSON.parse(p.detail_tgl) : (p.detail_tgl || []);
        } catch (e) {
            console.error("Format jadwal salah");
        }

        if (a.length > 0) {
            a.forEach((item) => {
                let isMatch = false;
                let tglTargetStr = "-";

                // Logika Check Tanggal
                if (jadwalArr.length > 0) {
                    const target = jadwalArr[item.angsuran - 1];
                    if (target) {
                        tglTargetStr = target.split('-').reverse().join('-');
                        const tglRiwayat = (item.tgl || "").split(' ')[0];
                        isMatch = (target === tglRiwayat);
                    }
                } else {
                    isMatch = true;
                }

                const icon = isMatch ?
                    '<i class="bi bi-patch-check-fill text-success ms-1"></i>' :
                    `<br><small class="text-danger" style="font-size: 10px;">Harusnya: ${tglTargetStr}</small>`;

                htmlStr += `
                <tr>
                    <td><span class="badge bg-secondary">${item.angsuran}</span></td>
                    <td>${new Date(item.tgl).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})} ${icon}</td>
                    <td class="fw-bold text-success">Rp ${(parseInt(item.nominal) || 0).toLocaleString('id-ID')}</td>
                    <td class="text-end">
                        <button onclick="hapusAngsuran('${item.id}', '${p.id_pinjam_asli}')" class="btn btn-outline-danger btn-sm border-0"><i class="bi bi-trash"></i></button>
                        <button onclick="printStruk('${item.id}')" class="btn btn-outline-primary btn-sm border-0"><i class="bi bi-printer"></i></button>
                    </td>
                </tr>`;
            });
        } else {
            htmlStr = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada riwayat</td></tr>';
        }
        $('#list-riwayat-angsuran').html(htmlStr);
    }

    // --- Logic Tombol Bayar ---
    function handleTombolBayar(p, jumBayar, targetAngsuran) {
        let btn = $('#btn-bayar-selanjutnya');
        if (jumBayar >= targetAngsuran && targetAngsuran > 0) {
            btn.attr('disabled', true).text('PINJAMAN LUNAS').removeClass('btn-primary').addClass('btn-success').off('click');
        } else {
            const ke = jumBayar + 1;
            const saranNominal = parseInt(p.pembayaran) || 0;
            btn.attr('disabled', false)
                .html(`<i class="bi bi-cash-coin me-2"></i> Bayar Angsuran Ke-${ke}`)
                .removeClass('btn-success').addClass('btn-primary')
                .off('click').on('click', function() {
                    bayarAngsuranManual(p.id_pinjam_asli, ke, saranNominal);
                });
        }
    }

    // --- Modal Input Bayar (SweetAlert2) ---
    function bayarAngsuranManual(id_pinjam, ke, nominal) {
        const tglSkrg = new Date();
        tglSkrg.setMinutes(tglSkrg.getMinutes() - tglSkrg.getTimezoneOffset());
        const waktuDefault = tglSkrg.toISOString().slice(0, 16);

        Swal.fire({
            target: document.getElementById('modalProfil'),
            title: `Bayar Angsuran Ke-${ke}`,
            html: `
                <div class="text-start">
                    <label class="form-label fw-bold">Nominal Pembayaran</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="text" id="swal-mask" class="form-control" value="${parseInt(nominal).toLocaleString('id-ID')}">
                        <input type="hidden" id="swal-real" value="${nominal}">
                    </div>
                    <label class="form-label fw-bold">Status</label>
                    <select id="swal-status" class="form-select mb-3">
                        <option value="LUNAS">LUNAS</option>
                        <option value="TIDAK">TIDAK</option>
                    </select>
                    <label class="form-label fw-bold">Waktu Bayar</label>
                    <input type="datetime-local" id="swal-tgl" class="form-control" value="${waktuDefault}">
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'Simpan & Cetak',
            didOpen: () => {
                const m = Swal.getPopup().querySelector('#swal-mask');
                const r = Swal.getPopup().querySelector('#swal-real');
                m.focus();
                m.addEventListener('input', function() {
                    let v = this.value.replace(/[^0-9]/g, '');
                    r.value = v;
                    this.value = v ? parseInt(v).toLocaleString('id-ID') : '';
                });
            },
            preConfirm: () => {
                return {
                    id_pinjam: id_pinjam,
                    nominal: $('#swal-real').val(),
                    tgl: $('#swal-tgl').val(),
                    angsuran: ke,
                    status: $('#swal-status').val()
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('angsuran.store') }}",
                    method: "POST",
                    data: {
                        ...result.value,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire('Berhasil!', 'Mencetak struk...', 'success').then(() => {
                            if (res.id_angsuran) printStruk(res.id_angsuran);
                            showProfil(id_pinjam); // Refresh data tanpa reload
                        });
                    }
                });
            }
        });
    }

    // --- Helper Maps & Struk ---
    function handleMaps(n) {
        if (n.lat && n.lng && n.lat !== "0") {
            $('#container-maps').removeClass('d-none');
            setTimeout(() => {
                if (mapProfil) mapProfil.remove();
                mapProfil = L.map('map').setView([n.lat, n.lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapProfil);
                L.marker([n.lat, n.lng]).addTo(mapProfil).bindPopup(n.nama);
            }, 400);
        } else {
            $('#container-maps').addClass('d-none');
        }
    }

    function hapusAngsuran(id, id_pinjam) {
        Swal.fire({
            title: 'Hapus?',
            icon: 'warning',
            showCancelButton: true
        }).then((r) => {
            if (r.isConfirmed) {
                $.ajax({
                    url: "{{ url('/angsuran/delete') }}/" + id,
                    method: "DELETE",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function() {
                        Swal.fire('Berhasil!', 'Data berhasil dihapus...', 'success').then(() => {
                            showProfil(id_pinjam); // Refresh data tanpa reload
                        });
                    }
                });
            }
        });
    }

    function printStruk(id) {
        window.open("{{ url('/angsuran/printstruk') }}/" + id, '_blank');
    }
</script>
@endsection