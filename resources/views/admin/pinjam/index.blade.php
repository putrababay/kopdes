@extends('layouts.admin')

@section('content')
<style>
    .input-group .btn-light {
        z-index: 4; /* Agar tombol berada di atas border input */
        border-radius: 0;
    }
    .input-group input:focus {
        box-shadow: none; /* Menghindari garis biru menutupi tombol X */
        background-color: #f1f3f5 !important;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
    z-index: 1060 !important;
}
</style>

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
                                            <th>Nominal Pinjam</th>
                                            <th>Jumlah Angsuran</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tempo</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($n->pinjamans as $key => $p)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $key + 1 }}</td>
                                            <td class="fw-bold">Rp{{ number_format($p->pinjam, 0, ',', '.') }}</td>
                                            <td>{{ number_format($p->angsuran, 0, ',', '.') }}</td>
                                            <td>{{ date('d-m-Y', strtotime($p->tgl_pinjam)) }}</td>
                                            <td>{{ $p->tempo_hari }}</td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $p->status == 'LUNAS' ? 'success' : 'warning' }}">
                                                    {{ $p->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                               <button class="btn btn-sm btn-outline-warning" onclick='openModalPinjam("edit", @json($p))'>
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


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // FUNGSI GLOBAL (Harus di luar $(document).ready agar bisa dipanggil onclick)
    function openModalPinjam(mode, data = null) {
        const modalEl = document.getElementById('modalPinjam');
        const form = document.getElementById('formPinjam');
        const methodField = document.getElementById('methodField');

        if (!form) return;

        form.reset();
        methodField.innerHTML = '';

        // Gunakan jQuery secara aman untuk Select2
        if (typeof jQuery !== 'undefined') {
            $('.select2-nasabah').val(null).trigger('change');
        }

        if (mode === 'tambah') {
            document.getElementById('modalTitle').innerText = 'Tambah Pinjaman';
            form.action = "{{ route('pinjam.store') }}";
            document.getElementById('tgl_pinjam').value = new Date().toISOString().split('T')[0];
            document.getElementById('angsuran').value = 10;
        } else {
            document.getElementById('modalTitle').innerText = 'Edit Pinjaman';
            form.action = "/admin/pinjam/" + data.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Set value nasabah dan trigger Select2 jika ada
            if (typeof jQuery !== 'undefined') {
                $('.select2-nasabah').val(data.id_nasaba).trigger('change');
            }

            document.getElementById('tgl_pinjam').value = data.tgl_pinjam;
            document.getElementById('angsuran').value = data.angsuran;
            document.getElementById('status').value = data.status;
            document.getElementById('tempo_hari').value = data.tempo_hari;
            document.getElementById('lokasi_penarikan').value = data.lokasi_penarikan || '';

            // Nilai Rupiah & Hidden
            document.getElementById('pinjam').value = data.pinjam;
            document.getElementById('display_pinjam').value = formatVisualRupiah(data.pinjam);
            document.getElementById('t_pinjam').value = data.t_pinjam;
            document.getElementById('display_t_pinjam').value = formatVisualRupiah(data.t_pinjam);

            // Hitung otomatis pembayaran
            setTimeout(calculatePembayaran, 200);
        }

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    }

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
        // Cek jQuery untuk Select2
        if (typeof jQuery !== 'undefined') {
            $('.select2-nasabah').select2({
                dropdownParent: $('#modalPinjam'),
                placeholder: "Cari Nama atau Alamat...",
                allowClear: true,
                theme: 'bootstrap-5'
            });
            
            // Fix agar pencarian select2 bisa diketik
            $('#modalPinjam').on('shown.bs.modal', function () {
                $(this).find('.select2-search__field').focus();
            });
        }

        // Event listener manual (Tanpa jQuery agar tidak error $)
        document.querySelectorAll('.rupiah-input').forEach(input => {
            input.addEventListener('keyup', function() {
                let cleanVal = this.value.replace(/[^0-9]/g, '');
                if (this.id === 'display_pinjam') document.getElementById('pinjam').value = cleanVal;
                if (this.id === 'display_t_pinjam') document.getElementById('t_pinjam').value = cleanVal;
                this.value = formatVisualRupiah(cleanVal);
                calculatePembayaran();
            });
        });

        document.getElementById('angsuran').addEventListener('input', calculatePembayaran);
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "{{ session('error') }}",
    });
</script>
@endif

@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Berhasil', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false });
</script>
@endif

@endsection