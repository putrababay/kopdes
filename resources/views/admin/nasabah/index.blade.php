<style>
    /* CSS tambahan untuk merapikan layout foto di mode mobile */
    @media screen and (max-width: 992px) {
        table#tableNasabah tbody td[data-label="FOTO"] {
            justify-content: center; /* Foto di tengah saat mode kartu */
            background-color: #fcfcfc;
        }
        
        table#tableNasabah tbody td[data-label="NAMA"] {
            font-size: 1.1rem;
            color: #0d6efd;
            text-align: center;
            display: block;
        }
    }

    .btn-group-custom {
        display: flex;
        width: 100%;
        border-top: 1px solid #eee;
    }
    
    .btn-edit { background-color: #f39c12; color: white; flex: 1; border: none; padding: 12px; }
    .btn-delete { background-color: #e74c3c; color: white; flex: 1; border: none; padding: 12px; }
    
    .text-detail { font-size: 0.85rem; line-height: 1.3; }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">NASABAH</h4>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-1"></i> TAMBAH
        </button>
    </div>

    <table id="tableNasabah" class="table w-100">
        <thead>
            <tr>
                <th>NO</th>
                <th>FOTO</th>
                <th>NAMA</th>
                <th>DETAIL NASABAH</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#tableNasabah').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('nasabah.data') }}",
        dom: '<"mb-3"f>rt<"mt-3"p>',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'foto_profile', name: 'foto', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'info_lengkap', name: 'info_lengkap' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        createdRow: function(row, data, dataIndex) {
            $('td', row).eq(0).attr('data-label', 'NO');
            $('td', row).eq(1).attr('data-label', 'FOTO');
            $('td', row).eq(2).attr('data-label', 'NAMA');
            $('td', row).eq(3).attr('data-label', 'DETAIL');
            $('td', row).eq(4).attr('data-label', 'AKSI');
        },
        language: {
            search: "",
            searchPlaceholder: "Cari Nama atau NIK..."
        }
    });
});
</script>