@extends('layouts.admin')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .avatar-wrapper {
        width: 45px;
        height: 45px;
        flex-shrink: 0;
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
        border-radius: 10px;
        border: 2px solid #fff;
    }

    .nasabah-row {
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .nasabah-row:hover {
        background-color: #f8faff !important;
    }

    [data-bs-toggle="collapse"] .bi-chevron-down {
        transition: transform 0.3s ease;
    }

    [data-bs-toggle="collapse"]:not(.collapsed) .bi-chevron-down {
        transform: rotate(180deg);
        color: #0d6efd;
    }

    /* Menyesuaikan tampilan Select2 dengan gaya input Anda */
    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f8f9fa !important;
        /* bg-light */
        border: none !important;
        border-radius: 0.5rem !important;
        /* rounded-4 */
        padding: 0.375rem 0.75rem;
        min-height: 40px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #212529;
        line-height: 24px;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Pulsa Pinjam</h4>
            <p class="text-muted small mb-0">Manajemen piutang pulsa</p>
        </div>
        <button class="btn btn-primary rounded-pill px-3 shadow-sm fw-bold" onclick="addPulsa()">
            <i class="bi bi-plus-lg me-2"></i>Tambah
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-12">
                    <form action="{{ route('pulsa.index') }}" method="GET" id="searchForm">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-search text-primary"></i>
                            </span>

                            <input type="text"
                                name="search"
                                id="searchInput"
                                class="form-control border-0 bg-light"
                                placeholder="Cari nama atau alamat nasabah..."
                                value="{{ request('search') }}">

                            {{-- Tombol Reset: Hanya muncul jika ada kata kunci pencarian --}}
                            @if(request()->filled('search'))
                            <a href="{{ route('pulsa.index') }}" class="btn btn-light border-0 bg-light text-danger d-flex align-items-center">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                            @endif

                            <button type="submit" class="btn btn-primary px-4 fw-bold">Cari</button>
                        </div>
                    </form>
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


<script>
    let page = 1;
    let loading = false;
    let hasMore = true;

    // --- 1. CORE FUNCTIONS ---

    // Fungsi utama memuat data (Search & Filter)
    function filterData() {
        page = 1;
        hasMore = true;
        loading = false;
        let search = $('#searchName').val();

        $.ajax({
            url: "{{ route('pulsa.index') }}",
            type: "GET",
            data: {
                search: search
            },
            beforeSend: function() {
                $('#dataContainer').html('');
                $('#loading').show();
            },
            success: function(data) {
                $('#loading').hide();
                $('#dataContainer').html(data);
                // Jika hasil pencarian kosong, hasMore matikan
                if ($.trim(data) == "") hasMore = false;
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
                    url: "{{ route('pulsa.index') }}",
                    type: "GET",
                    data: {
                        page: page,
                        search: $('#searchName').val()
                    },
                    success: function(data) {
                        $('#loading').hide();
                        if ($.trim(data) == "") {
                            hasMore = false;
                        } else {
                            $("#dataContainer").append(data);
                            loading = false; // Buka kunci untuk page berikutnya
                        }
                    },
                    error: function() {
                        loading = false;
                        $('#loading').hide();
                    }
                });
            }
        }
    });

    // --- 2. CRUD FUNCTIONS ---

    function addPulsa() {
        $('#id_pinjam').val('');
        $('#formPulsa')[0].reset();
        $('#id_nasaba').val(null).trigger('change');
        $('#status').val('').trigger('change'); // Reset status
        $('#modalTitle').text('Pinjam Pulsa Baru');
        $('#modalPulsa').modal('show');
    }

    function editPulsa(id, id_nasabah, nomer, harga, status) {
        $('#modalTitle').text('Edit Pinjaman Pulsa');
        $('#id_pinjam').val(id);
        $('#id_nasaba').val(id_nasabah).trigger('change');
        $('#nomer').val(nomer);
        $('#harga').val(harga);
        $('#harga_display').val(formatRupiah(harga ? harga.toString() : '0'));

        // --- LOGIKA SELECTED STATUS ---
        // Konversi ke string, hilangkan spasi, dan jadikan huruf besar untuk pengecekan
        let s = String(status).trim().toUpperCase();

        let valToSet = "";
        if (s === "1" || s === "LUNAS") {
            valToSet = "1";
        } else if (s === "0" || s === "BELUM LUNAS") {
            valToSet = "0";
        }

        // Set nilai ke elemen select
        $('#status').val(valToSet);

        // PENTING: Jika Anda menggunakan Select2 atau framework UI tertentu pada dropdown status, 
        // tambahkan .trigger('change') agar tampilannya terupdate
        $('#status').trigger('change');

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
                    url: "{{ url('pulsa/delete') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data dihapus!',
                            timer: 1000
                        });
                        // Tetap di filter yang sama, refresh container
                        filterData();
                    }
                });
            }
        });
    }

    // --- 3. FORM SUBMIT (UPDATE & STORE) ---

    $('#formPulsa').on('submit', function(e) {
        e.preventDefault();
        let id = $('#id_pinjam').val();
        let url = id ? "{{ url('pulsa/update') }}/" + id : "{{ url('pulsa/store') }}";
        let formData = $(this).serialize();

        if (id) formData += "&_method=PUT";

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            beforeSend: function() {
                $('#formPulsa button[type="submit"]').attr('disabled', true).text('Menyimpan...');
            },
            success: function(res) {
                $('#modalPulsa').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data disimpan!',
                    timer: 1000
                });
                // REFRESH DATA (Pencarian tetap terjaga karena filterData mengambil nilai dari #searchName)
                filterData();
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : "Terjadi kesalahan.";
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#formPulsa button[type="submit"]').attr('disabled', false).text('Simpan Data');
            }
        });
    });

    // --- 4. HELPERS & INITIALIZERS ---

    function formatRupiah(angka) {
        if (!angka) return '0';
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    $(document).ready(function() {
        // Init Select2 Nasabah
        $('#modalPulsa').on('shown.bs.modal', function() {
            $('.select2-nasabah').select2({
                theme: 'bootstrap-5',
                placeholder: "Cari Nama Nasabah...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPulsa')
            });
        });

        // Money Masking
        $('#harga_display').on('keyup', function() {
            let val = this.value;
            this.value = formatRupiah(val);
            $('#harga').val(val.replace(/\./g, ''));
        });
    });
</script>

@endsection