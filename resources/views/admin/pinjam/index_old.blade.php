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


<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Master Pinjaman</h4>
            <p class="text-muted small mb-0">Kelola data pinjaman nasabah tahun {{ $tahun }}</p>
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
                        @if(request()->filled('search'))
                        <a href="{{ route('pinjam.index', ['tahun' => $tahun]) }}" class="btn btn-light border-0 bg-light px-3">
                            <i class="bi bi-x-lg text-danger"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($nasabahs as $n)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
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
                            <div class="desktop-info gap-3 small text-muted">
                                <span><i class="bi bi-briefcase me-1"></i> {{ $n->pekerjaan }}</span>
                                <span><i class="bi bi-geo-alt me-1"></i> {{ Str::limit($n->alamat, 40) }}</span>
                                <span><i class="bi bi-clock-history me-1"></i> Terakhir: <b>{{ $n->pinjaman_terakhir ?? '-' }}</b></span>
                            </div>
                            <div class="mobile-info small text-muted">
                                <div><i class="bi bi-clock-history me-1"></i> {{ $n->pinjaman_terakhir ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center ms-3">
                            <div class="avatar-wrapper me-3">
                                @if($n->foto)
                                <img src="{{ asset('storage/'.$n->foto) }}" class="avatar-img">
                                @else
                                <div class="avatar-initial">{{ substr($n->nama, 0, 1) }}</div>
                                @endif
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
                                                <button class="btn btn-sm btn-light text-warning me-1" onclick="editPinjaman(...)"><i class="bi bi-pencil-square"></i></button>
                                                <button class="btn btn-sm btn-light text-danger" onclick="confirmDelete(...)"><i class="bi bi-trash"></i></button>
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
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Nominal</small>
                                                <span class="fw-bold">Rp{{ number_format($p->pinjam, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <small class="text-muted d-block">Total</small>
                                                <span class="fw-bold text-primary">Rp{{ number_format($p->t_pinjam, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ date('d/m/y', strtotime($p->tgl_pinjam)) }}</small>
                                            <div>
                                                <button class="btn btn-sm btn-warning text-white rounded-pill px-3" onclick="editPinjaman(...)"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="confirmDelete(...)"><i class="bi bi-trash"></i></button>
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
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2">Nasabah tidak ditemukan</p>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {!! $nasabahs->links('pagination::bootstrap-5') !!}
    </div>
</div>

@include('admin.pinjam.modal')



<script>
    $(document).ready(function() {
        // 1. Inisialisasi Select2
        const selectNasabah = $('.select2-nasabah').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalPinjam'),
            placeholder: 'Cari Nama atau Alamat...',
            allowClear: true
        });

        // 2. Autofokus pencarian saat modal dibuka
        $('#modalPinjam').on('shown.bs.modal', function() {
            // Sedikit delay agar fokus tidak tercuri kembali oleh modal Bootstrap
            setTimeout(() => {
                $(this).find('.select2-search__field').focus();
            }, 100);
        });

        // 3. Logic saat nasabah dipilih
        selectNasabah.on('change', function() {
            const selected = $(this).find(':selected');
            const foto = selected.data('foto');
            const nama = selected.data('nama');
            const alamat = selected.data('alamat');

            if (nama) {
                $('#nasabah-name-display').text(nama);
                $('#nasabah-alamat-display').text(alamat || 'Alamat tidak tersedia');

                if (foto && foto !== "") {
                    // Set SRC dan hapus d-none
                    // Tambahkan d-none kembali di onerror (jika file fisik tidak ada/404)
                    $('#preview-foto')
                        .attr('src', foto)
                        .removeClass('d-none')
                        .on('error', function() {
                            // Jika 404, otomatis tampilkan inisial
                            showInitial(nama);
                        });
                    $('#preview-initial').addClass('d-none');
                } else {
                    showInitial(nama);
                }
            } else {
                // Reset ke default jika dikosongkan (tombol X diklik)
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
        const previewContainer = document.getElementById('jadwal-preview'); // Pastikan ID ini ada di HTML

        if (!form) return;

        // Reset Form & UI
        form.reset();
        methodField.innerHTML = '';
        if (previewContainer) previewContainer.innerHTML = '<small class="text-muted italic">Menghitung jadwal...</small>';

        if (typeof jQuery !== 'undefined') {
            $('.select2-nasabah').val(null).trigger('change');
        }

        if (mode === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah Pinjaman';
            form.action = "{{ route('pinjam.store') }}";

            // Default values
            document.getElementById('tgl_pinjam').value = new Date().toISOString().split('T')[0];
            document.getElementById('angsuran').value = 10;
            document.getElementById('status').value = 'AKTIF';
            document.getElementById('tempo_hari').value = 'SENIN';

            // Autofokus Select2
            $('#modalPinjam').one('shown.bs.modal', function() {
                $('.select2-nasabah').select2('open');
            });

            // Trigger hitung jadwal default
            setTimeout(generateJadwalAngsuran, 300);

        } else {
            document.getElementById('modalTitle').innerText = 'Edit Pinjaman';

            // Fix Route Update dengan Placeholder
            let url = "{{ route('pinjam.update', ['pinjam' => ':id']) }}";
            form.action = url.replace(':id', data.id);

            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Isi field data
            document.getElementById('tgl_pinjam').value = data.tgl_pinjam;
            document.getElementById('angsuran').value = data.angsuran;
            document.getElementById('status').value = data.status;
            document.getElementById('tempo_hari').value = data.tempo_hari;
            document.getElementById('lokasi_penarikan').value = data.lokasi_penarikan || '';
            document.getElementById('pinjam').value = data.pinjam;
            document.getElementById('display_pinjam').value = formatVisualRupiah(data.pinjam);
            document.getElementById('t_pinjam').value = data.t_pinjam;
            document.getElementById('display_t_pinjam').value = formatVisualRupiah(data.t_pinjam);
            document.getElementById('jaminan').value = data.jaminan || '';

            // Set hidden fields jadwal jika ada
            document.getElementById('detail_tgl').value = data.detail_tgl || '';
            document.getElementById('tgl_akhir').value = data.tgl_akhir || '';

            // Trigger Select2 & Foto Preview
            if (typeof jQuery !== 'undefined') {
                $('.select2-nasabah').val(data.id_nasaba).trigger('change');
            }

            // Hitung ulang pembayaran & Refresh Preview Jadwal
            setTimeout(() => {
                calculatePembayaran();
                generateJadwalAngsuran();
            }, 200);
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

    function editPinjaman(id, id_nasaba, pinjam, tgl, angsuran, status, tempo, lokasi_penarikan, t_pinjam, jaminan) {
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
            jaminan: jaminan // tambahkan field jaminan

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