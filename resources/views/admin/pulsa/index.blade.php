@extends('layouts.admin')

@section('content')
<style>
    :root { --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); }
    .cursor-pointer { cursor: pointer; }
    .avatar-wrapper { width: 45px; height: 45px; flex-shrink: 0; }
    .avatar-initial { 
        width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; 
        background: #eef2ff; color: #0d6efd; font-weight: bold; border-radius: 10px; border: 2px solid #fff; 
    }
    .nasabah-row { transition: all 0.2s ease; border: 1px solid transparent; }
    .nasabah-row:hover { background-color: #f8faff !important; }
    [data-bs-toggle="collapse"] .bi-chevron-down { transition: transform 0.3s ease; }
    [data-bs-toggle="collapse"]:not(.collapsed) .bi-chevron-down { transform: rotate(180deg); color: #0d6efd; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Master Pulsa Pinjam</h4>
            <p class="text-muted small mb-0">Manajemen piutang pulsa nasabah</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" onclick="addPulsa()">
            <i class="bi bi-plus-lg me-2"></i>Tambah Transaksi
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" id="searchName" class="form-control border-0 bg-light" placeholder="Cari nama atau alamat nasabah..." value="{{ request('search') }}">
                        <button class="btn btn-primary px-4" onclick="filterData()">Cari</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dataContainer" class="row g-3">
        @include('admin.pulsa._item_list')
    </div>

    <div id="loading" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="small text-muted">Memuat data...</p>
    </div>
</div>

@include('admin.pulsa.modal')


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let page = 1;
    let loading = false;
    let hasMore = true;

    // Filter & Search Function
    function filterData() {
        page = 1;
        hasMore = true;
        let search = $('#searchName').val();
        $.ajax({
            url: "{{ route('pulsa.index') }}",
            type: "GET",
            data: { search: search },
            beforeSend: function() { $('#dataContainer').html(''); $('#loading').show(); },
            success: function(data) {
                $('#loading').hide();
                $('#dataContainer').html(data);
            }
        });
    }

    // Lazy Loading Scroll
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
            if (!loading && hasMore) {
                loading = true;
                page++;
                $('#loading').show();
                $.ajax({
                    url: "?page=" + page + "&search=" + $('#searchName').val(),
                    type: "GET",
                    success: function(data) {
                        if ($.trim(data) == "") {
                            hasMore = false;
                        } else {
                            $("#dataContainer").append(data);
                            loading = false;
                        }
                        $('#loading').hide();
                    }
                });
            }
        }
    });

    // CRUD Functions
    function addPulsa() {
        $('#modalTitle').text('Tambah Pinjam Pulsa');
        $('#formPulsa')[0].reset();
        $('#id_pinjam').val('');
        $('#modalPulsa').modal('show');
    }

    function editPulsa(id, id_nasabah, nomer, harga) {
        $('#modalTitle').text('Edit Transaksi');
        $('#id_pinjam').val(id);
        $('#id_nasaba').val(id_nasabah);
        $('#nomer').val(nomer);
        $('#harga').val(harga);
        $('#modalPulsa').modal('show');
    }

    function deletePulsa(id) {
        Swal.fire({
            title: 'Hapus data?',
            text: "Transaksi ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/pulsa-pinjam/delete') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire('Berhasil', 'Data dihapus', 'success');
                        filterData();
                    }
                });
            }
        });
    }

    $('#formPulsa').on('submit', function(e) {
        e.preventDefault();
        let id = $('#id_pinjam').val();
        let url = id ? "{{ url('admin/pulsa-pinjam/update') }}/" + id : "{{ route('pulsa.store') }}";
        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize() + (id ? "&_method=PUT" : ""),
            success: function(res) {
                $('#modalPulsa').modal('hide');
                Swal.fire('Berhasil', 'Data disimpan', 'success');
                filterData();
            }
        });
    });
</script>


@endsection