@extends('layouts.admin')

@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary">Master Pinjaman (Grouping)</h5>
        <button class="btn btn-primary rounded-pill px-4" onclick="openModalPinjam('tambah')">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <form action="{{ route('pinjam.index') }}" method="GET" class="row g-2">
                <div class="col-md-3">
                    <select name="tahun" class="form-select border-0 bg-light fw-bold" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-9">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari Nama..." value="{{ request('search') }}">

                        {{-- Tombol Reset (X Merah) --}}
                        @if(request()->filled('search'))
                        <a href="{{ route('pinjam.index', ['tahun' => $tahun]) }}" class="btn btn-light border-0 bg-light d-flex align-items-center justify-content-center px-3" title="Hapus Pencarian">
                            <i class="bi bi-x-lg text-danger fw-bold"></i>
                        </a>
                        @endif

                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-search me-1"></i> Cari
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
                    <div class="d-flex flex-column flex-md-row align-items-md-center p-3 bg-white cursor-pointer"
                        data-bs-toggle="collapse"
                        data-bs-target="#detail_{{ $n->id }}"
                        style="cursor: pointer;">

                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ $n->nama }}</h6>
                            <small class="text-muted d-block small">
                                <i class="bi bi-briefcase"></i> {{ $n->pekerjaan }} |
                                <i class="bi bi-geo-alt"></i> {{ $n->alamat }}
                            </small>
                        </div>

                        <div class="d-flex gap-4 text-md-end mt-2 mt-md-0 me-md-3">
                            <div>
                                <small class="text-muted d-block small">Transaksi</small>
                                <span class="badge bg-light text-primary border rounded-pill">{{ $n->jumlah_transaksi }} Kali</span>
                            </div>
                            <div>
                                <small class="text-muted d-block small">Terakhir Pinjam</small>
                                <span class="small fw-bold">{{ $n->pinjaman_terakhir ?? '-' }}</span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-down text-muted"></i>
                    </div>

                    <div class="collapse" id="detail_{{ $n->id }}">
                        <div class="bg-light p-3 border-top">
                            <div class="table-responsive bg-white rounded-3 shadow-sm">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-3">No</th>
                                            <th class="fw-bold">kode</th>
                                            <th>Nominal Pinjam</th>
                                            <th>Angs</th>
                                            <th>Total Pinjaman</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tempo</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($n->pinjamans as $key => $p)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $key + 1 }} </td>
                                            <td class="fw-bold">#{{ $p->id }}</td>
                                            <td class="fw-bold">Rp{{ number_format($p->pinjam, 0, ',', '.') }}</td>
                                            <td>{{ number_format($p->angsuran, 0, ',', '.') }}</td>
                                            <td class="fw-bold">Rp{{ number_format($p->t_pinjam, 0, ',', '.') }}</td>
                                            <td>{{ date('d-m-Y', strtotime($p->tgl_pinjam)) }}</td>
                                            <td>{{ $p->tempo_hari }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $p->status == 'LUNAS' ? 'success' : 'warning' }}">
                                                    {{ $p->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-warning"
                                                    onclick="editPinjaman({{ $p->id }}, {{ $p->id_nasaba }}, {{ $p->pinjam }}, '{{ $p->tgl_pinjam }}', {{ $p->angsuran }}, '{{ $p->status }}', '{{ $p->tempo_hari }}', '{{ $p->lokasi_penarikan }}', '{{ $p->t_pinjam }}', '{{ $p->jaminan }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $p->id }}')">
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
            <p class="text-muted">Data nasabah tidak ditemukan.</p>
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