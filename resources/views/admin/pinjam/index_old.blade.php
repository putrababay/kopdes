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
        <form action="{{ route('pinjam.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="bg-warning-soft text-warning fw-bold px-3 py-2 rounded-3 small">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> 
                    {{ request('search') ? 'Semua Riwayat' : 'Filter: Belum Lunas' }}
                </div>
            </div>

            <div class="col-md-9">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari Nama Nasabah..." value="{{ request('search') }}">
                    
                    {{-- Tombol Reset (X) Merah --}}
                    @if(request()->filled('search'))
                        <a href="{{ route('pinjam.index') }}" class="btn btn-light border-0 bg-light d-flex align-items-center justify-content-center px-3">
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

<script>
    let modalPinjam, formPinjam, methodField;

    document.addEventListener("DOMContentLoaded", function() {
        modalPinjam = new bootstrap.Modal(document.getElementById('modalPinjam'));
        formPinjam = document.getElementById('formPinjam');
        methodField = document.getElementById('methodField');
    });

    function openModalPinjam(mode, data = null) {
    const form = document.getElementById('formPinjam');
    const methodField = document.getElementById('methodField');
    
    if (mode === 'tambah') {
        document.getElementById('modalTitle').innerText = 'Tambah Pinjaman';
        form.action = "{{ route('pinjam.store') }}";
        methodField.innerHTML = '';
        form.reset();
    } else {
        document.getElementById('modalTitle').innerText = 'Edit Pinjaman';
        // Pastikan route ini sesuai dengan php artisan route:list Anda
        form.action = "/admin/pinjam/" + data.id; 
        methodField.innerHTML = '@method("PUT")';
        
        // Isi field modal
        document.getElementById('id_nasaba').value = data.id_nasaba;
        document.getElementById('pinjam').value = data.pinjam;
        document.getElementById('angsuran').value = data.angsuran;
        document.getElementById('tgl_pinjam').value = data.tgl_pinjam;
        document.getElementById('status').value = data.status;
    }
    new bootstrap.Modal(document.getElementById('modalPinjam')).show();
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = "/admin/pinjam/" + id;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

    
</script>
@endsection



public function index(Request $request)
{
    $search = $request->search;

    // Dimulai dari Nasabah
    $query = Nasabah::select('id', 'nama', 'pekerjaan', 'alamat')
        ->with(['pinjamans' => function($q) use ($search) {
            // Jika TIDAK sedang mencari, filter hanya yang BELUM LUNAS
            if (!$search) {
                $q->where('status', '!=', 'LUNAS');
            }
            $q->orderBy('id', 'desc');
        }])
        ->withCount(['pinjamans as jumlah_transaksi' => function($q) use ($search) {
            if (!$search) $q->where('status', '!=', 'LUNAS');
        }])
        ->withMax(['pinjamans as pinjaman_terakhir' => function($q) use ($search) {
            if (!$search) $q->where('status', '!=', 'LUNAS');
        }], 'tgl_pinjam');

    // Logika Pencarian
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nama', 'LIKE', "%$search%")
              ->orWhere('pekerjaan', 'LIKE', "%$search%")
              ->orWhere('alamat', 'LIKE', "%$search%");
        });
    } else {
        // Default: Hanya tampilkan nasabah yang punya tanggungan (Belum Lunas)
        $query->whereHas('pinjamans', function($q) {
            $q->where('status', '!=', 'LUNAS');
        });
    }

    // Eksekusi dengan pagination
    $nasabahs = $query->orderByDesc('pinjaman_terakhir')->paginate(15)->appends($request->all());
    
    // Data untuk modal select
    $all_nasabahs = Nasabah::select('id', 'nama', 'nik')->get();
    
    return view('admin.pinjam.index', compact('nasabahs', 'all_nasabahs'));
}
