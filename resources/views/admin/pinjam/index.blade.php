@extends('layouts.admin')

@section('content')
<style>
    /* Global & Variables */
    :root {
        --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Avatar Styling */
    .avatar-wrapper {
        width: 55px;
        height: 55px;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #fff;
    }

    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #0d6efd;
        font-weight: bold;
        border-radius: 12px;
        font-size: 1.2rem;
        border: 2px solid #fff;
    }

    /* Card & List Effects */
    .nasabah-row {
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .nasabah-row:hover {
        background-color: #f8faff !important;
        border-color: #dee2e6;
    }

    /* Animation Chevron */
    [data-bs-toggle="collapse"] .bi-chevron-down {
        transition: transform 0.3s ease;
    }

    [data-bs-toggle="collapse"]:not(.collapsed) .bi-chevron-down {
        transform: rotate(180deg);
        color: #0d6efd;
    }

    /* Layout Toggle */
    @media (max-width: 767.98px) {
        .desktop-info {
            display: none;
        }

        .mobile-info {
            display: block;
        }
    }

    @media (min-width: 768px) {
        .desktop-info {
            display: flex;
        }

        .mobile-info {
            display: none;
        }
    }
</style>


<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-0">Master Pinjaman</h4>
        <p class="text-muted small mb-0">Pinjaman nasabah tahun {{ $tahun }}</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="openModalPinjam('tambah')">
        <i class="bi bi-plus-lg me-2"></i>Tambah
    </button>
</div>

<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body p-3">
        <form action="{{ route('pinjam.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-check text-primary"></i></span>
                    <select name="tahun" class="form-select border-0 bg-light fw-bold" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="col-md-9">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari nama nasabah..." value="{{ request('search') }}">

                    @if(request('search'))
                    <a href="{{ route('pinjam.index', ['tahun' => $tahun]) }}" class="btn btn-light border-0 text-muted" title="Bersihkan Pencarian">
                        <i class="bi bi-x-circle-fill"></i>
                    </a>
                    @endif
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row g-3" id="nasabah-container">
    @if($nasabahs->count() > 0)
    @include('admin.pinjam._item_nasabah', ['nasabahs' => $nasabahs])
    @else
    <div class="col-12 text-center py-5">
        <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">Nasabah tidak ditemukan</p>
    </div>
    @endif
</div>

<div id="infinite-scroll-marker" class="text-center my-4 py-3">
    @if($nasabahs->hasMorePages())
    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
    <span class="ms-2 small text-muted">Memuat data otomatis...</span>
    @endif
</div>

<script>
    let isLoading = false;
    let nextUrl = "{{ $nasabahs->nextPageUrl() }}";

    // Observer untuk mendeteksi scroll
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading && nextUrl) {
            loadMoreData();
        }
    }, {
        rootMargin: '200px'
    });

    const marker = document.getElementById('infinite-scroll-marker');
    if (marker) observer.observe(marker);

    function loadMoreData() {
        isLoading = true;

        fetch(nextUrl, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.text())
            .then(html => {
                if (html.trim().length > 0) {
                    // Tambahkan data ke container
                    document.getElementById('nasabah-container').insertAdjacentHTML('beforeend', html);

                    // Ambil URL Page berikutnya dari string HTML atau manipulasi URL
                    let url = new URL(nextUrl);
                    let page = parseInt(url.searchParams.get('page')) + 1;
                    url.searchParams.set('page', page);
                    nextUrl = url.toString();

                    isLoading = false;
                } else {
                    nextUrl = null;
                    marker.innerHTML = "<small class='text-muted'>Semua data telah dimuat</small>";
                }
            })
            .catch(err => {
                console.error(err);
                isLoading = false;
            });
    }
</script>


@include('admin.pinjam.modal')
@include('admin.pinjam.modaldetail')

<script>
    $(document).ready(function() {
        // 1. Inisialisasi Select2
        const selectNasabah = $('.select2-nasabah').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalPinjam'),
            placeholder: 'Cari Nama atau Alamat...',
            allowClear: true
        });

        // Fungsi Pembantu untuk Menampilkan Inisial
        function showInitial(nama) {
            const initial = nama ? nama.charAt(0).toUpperCase() : '?';
            $('#preview-foto').addClass('d-none').attr('src', ''); // Kosongkan src agar tidak trigger error lagi
            $('#preview-initial').removeClass('d-none').text(initial);
        }

        // 2. Autofokus pencarian saat modal dibuka
        $('#modalPinjam').on('shown.bs.modal', function() {
            // Gunakan selector yang lebih spesifik untuk menargetkan input search select2
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        });

        // 3. Logic saat nasabah dipilih
        selectNasabah.on('change', function() {
            const selected = $(this).find(':selected');
            const foto = selected.data('foto');
            const nama = selected.data('nama');
            const alamat = selected.data('alamat');

            // Reset state gambar setiap kali ganti nasabah
            $('#preview-foto').off('error');

            if (nama) {
                $('#nasabah-name-display').text(nama);
                $('#nasabah-alamat-display').text(alamat || 'Alamat tidak tersedia');

                if (foto && foto !== "") {
                    $('#preview-initial').addClass('d-none');
                    $('#preview-foto')
                        .attr('src', foto)
                        .removeClass('d-none')
                        .one('error', function() {
                            // Menggunakan .one() agar hanya berjalan sekali per ganti foto
                            showInitial(nama);
                        });
                } else {
                    showInitial(nama);
                }
            } else {
                // Reset ke default jika dikosongkan
                $('#nasabah-name-display').text('-');
                $('#nasabah-alamat-display').text('Pilih Nasabah');
                showInitial('?');
            }
        });
    });

    /**
     * Fungsi pembantu untuk menampilkan inisial
     * Dipindahkan ke luar agar bisa diakses global atau oleh event error
     */
    function showInitial(text) {
        const previewFoto = $('#preview-foto');
        const previewInitial = $('#preview-initial');
        const char = text ? text.charAt(0).toUpperCase() : '?';

        previewInitial.text(char).removeClass('d-none');
        previewFoto.addClass('d-none').attr('src', ''); // Kosongkan SRC agar tidak terus-terusan hit 404
    }

    // FUNGSI GLOBAL (Harus di luar $(document).ready agar bisa dipanggil onclick)
    // FUNGSI GLOBAL
    function openModalPinjam(mode, data = null) {
        const modalEl = document.getElementById('modalPinjam');
        const form = document.getElementById('formPinjam');
        const methodField = document.getElementById('methodField');
        const previewContainer = document.getElementById('jadwal-preview');

        if (!form) return;

        // 1. Reset Form & UI
        form.reset();
        methodField.innerHTML = '';
        if (previewContainer) previewContainer.innerHTML = '<small class="text-muted italic">Menghitung jadwal...</small>';

        if (mode === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah Pinjaman';
            form.action = "{{ route('pinjam.store') }}";

            // Default values
            document.getElementById('tgl_pinjam').value = new Date().toISOString().split('T')[0];
            document.getElementById('angsuran').value = 10;
            document.getElementById('status').value = 'AKTIF';
            document.getElementById('tempo_hari').value = 'SENIN';

            if (typeof jQuery !== 'undefined') {
                $('.select2-nasabah').val(null).trigger('change');
            }

            setTimeout(generateJadwalAngsuran, 300);

        } else {
            document.getElementById('modalTitle').innerText = 'Edit Pinjaman';

            // 2. Set Action URL & Method PUT
            let url = "{{ route('pinjam.update', ['pinjam' => ':id']) }}";
            form.action = url.replace(':id', data.id);
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // 3. Isi Field Input Biasa
            document.getElementById('tgl_pinjam').value = data.tgl_pinjam;
            document.getElementById('angsuran').value = data.angsuran;
            document.getElementById('status').value = data.status;
            document.getElementById('lokasi_penarikan').value = data.lokasi_penarikan || '';
            document.getElementById('jaminan').value = data.jaminan || '';

            // 4. Handle Tempo Hari (Pastikan Uppercase sesuai option)
            if (data.tempo_hari) {
                document.getElementById('tempo_hari').value = data.tempo_hari.toUpperCase();
            }

            // 5. Handle Nominal & Hidden Fields
            document.getElementById('pinjam').value = data.pinjam;
            document.getElementById('display_pinjam').value = formatVisualRupiah(data.pinjam);
            document.getElementById('t_pinjam').value = data.t_pinjam;
            document.getElementById('display_t_pinjam').value = formatVisualRupiah(data.t_pinjam);
            document.getElementById('detail_tgl').value = data.detail_tgl || '';
            document.getElementById('tgl_akhir').value = data.tgl_akhir || '';

            // 6. Sinkronisasi Select2 Nasabah      
            // 6. Sinkronisasi Select2 Nasabah
            if (typeof jQuery !== 'undefined') {
                const $selectNasabah = $('.select2-nasabah');

                // Pastikan value di-set dan trigger 'change' agar listener foto/alamat berjalan
                $selectNasabah.val(data.id_nasaba).trigger('change');

                // Tambahan: Kadang Select2 butuh trigger 'select2:select' secara manual untuk memperbarui tampilan teks
                if ($selectNasabah.val() !== data.id_nasaba) {
                    // Jika nilai masih belum berubah, paksa manual berdasarkan value
                    $selectNasabah.val(data.id_nasaba).trigger('change.select2');
                }
            }

            // 7. Hitung ulang UI (Pembayaran & Preview Jadwal)
            // Diberi delay sedikit agar trigger change Select2 selesai diproses
            setTimeout(() => {
                if (typeof calculatePembayaran === "function") calculatePembayaran();
                if (typeof generateJadwalAngsuran === "function") generateJadwalAngsuran();
            }, 300);
        }

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    }
    /**
     * LOGIKA MENGHITUNG JADWAL ANGSURAN (MINGGUAN)
     */
    function generateJadwalAngsuran() {
        const tglPinjam = document.getElementById('tgl_pinjam').value;
        const tempoHari = document.getElementById('tempo_hari').value;
        const jumlahAngsuran = parseInt(document.getElementById('angsuran').value);
        const previewContainer = document.getElementById('jadwal-preview');

        if (!tglPinjam || !tempoHari || isNaN(jumlahAngsuran) || !previewContainer) return;

        const hariMap = {
            'MINGGU': 0,
            'SENIN': 1,
            'SELASA': 2,
            'RABU': 3,
            'KAMIS': 4,
            'JUMAT': 5,
            'SABTU': 6
        };
        let jadwal = [];
        let startDate = new Date(tglPinjam);
        const targetDay = hariMap[tempoHari];

        // Cari hari jatuh tempo pertama
        let firstDate = new Date(startDate);
        let diff = (targetDay + 7 - startDate.getDay()) % 7;
        if (diff === 0) diff = 7;
        firstDate.setDate(startDate.getDate() + diff);

        let htmlPreview = '<ul class="list-unstyled mb-0 small text-dark">';
        let currentDate = new Date(firstDate);

        for (let i = 1; i <= jumlahAngsuran; i++) {
            let dateIso = currentDate.toISOString().split('T')[0];

            // --- PERBAIKAN FORMAT BULAN INDONESIA DI SINI ---
            let dateDisplay = currentDate.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long', // Gunakan 'short' untuk Jan, Feb atau 'long' untuk Januari, Februari
                year: 'numeric'
            });

            jadwal.push(dateIso);
            htmlPreview += `
            <li class="mb-1 d-flex justify-content-between border-bottom pb-1">
                <span><span class="badge bg-secondary me-2">${i}</span> ${dateDisplay}</span>
                <span class="text-muted small">${tempoHari}</span>
            </li>`;

            if (i === jumlahAngsuran) {
                document.getElementById('tgl_akhir').value = dateIso;
            }

            currentDate.setDate(currentDate.getDate() + 7);
        }
        htmlPreview += '</ul>';

        document.getElementById('detail_tgl').value = JSON.stringify(jadwal);
        previewContainer.innerHTML = htmlPreview;
    }

    /**
     * EVENT LISTENERS
     * Jalankan perhitungan ulang saat parameter jadwal berubah
     */
    document.addEventListener('DOMContentLoaded', function() {
        const ids = ['tgl_pinjam', 'tempo_hari', 'angsuran'];
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', generateJadwalAngsuran);
        });
    });



    // Fungsi Format Rupiah & Perhitungan
    function formatVisualRupiah(angka) {
        if (!angka) return '';
        let val = angka.toString().replace(/[^0-9]/g, '');
        let sisa = val.length % 3;
        let rupiah = val.substr(0, sisa);
        let ribuan = val.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return 'Rp ' + rupiah;
    }

    function calculatePembayaran() {
        let tPinjam = document.getElementById('t_pinjam').value || 0;
        let angsuran = document.getElementById('angsuran').value || 1;
        let hasil = Math.round(parseFloat(tPinjam) / parseInt(angsuran));

        document.getElementById('pembayaran').value = hasil;
        document.getElementById('display_pembayaran').value = formatVisualRupiah(hasil);
    }

    // Inisialisasi saat halaman siap
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi Select2
        if (typeof jQuery !== 'undefined') {
            $('.select2-nasabah').select2({
                theme: 'bootstrap-5',
                placeholder: "Cari Nama atau Alamat...",
                allowClear: true,
                dropdownParent: $('#modalPinjam') // Penting agar bisa diketik dalam modal
            });
        }

        // Event listener untuk perhitungan otomatis
        const inputPinjam = document.getElementById('display_pinjam');
        const inputTPinjam = document.getElementById('display_t_pinjam');
        const inputAngsuran = document.getElementById('angsuran');

        if (inputPinjam) inputPinjam.addEventListener('keyup', handleRupiahInput);
        if (inputTPinjam) inputTPinjam.addEventListener('keyup', handleRupiahInput);
        if (inputAngsuran) inputAngsuran.addEventListener('input', calculatePembayaran);
    });

    // Helper untuk menangani input rupiah
    function handleRupiahInput(e) {
        let cleanVal = e.target.value.replace(/[^0-9]/g, '');
        let targetId = e.target.id.replace('display_', '');

        document.getElementById(targetId).value = cleanVal;
        e.target.value = formatVisualRupiah(cleanVal);

        if (e.target.id === 'display_t_pinjam') {
            calculatePembayaran();
        }
    }

    function editPinjaman(id, id_nasaba, pinjam, tgl, angsuran, status, tempo, lokasi_penarikan, t_pinjam, jaminan, detail_tgl, tgl_akhir) {
        const data = {
            id: id,
            id_nasaba: id_nasaba,
            pinjam: pinjam,
            tgl_pinjam: tgl,
            angsuran: angsuran,
            status: status,
            tempo_hari: tempo,
            lokasi_penarikan: lokasi_penarikan,
            t_pinjam: t_pinjam, // sesuaikan jika t_pinjam berbeda
            jaminan: jaminan, // tambahkan field jaminan
            detail_tgl: detail_tgl,
            tgl_akhir: tgl_akhir,

        };
        openModalPinjam('edit', data);
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data pinjaman ini tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('pinjam.destroy', ':id') }}";
                let form = document.createElement('form');
                form.action = url.replace(':id', id);
                form.method = 'POST';
                form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // --- Global Variables ---
    let page = 1;
    let loading = false;
    let mapProfil;


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
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    });
</script>
@endif

@endsection