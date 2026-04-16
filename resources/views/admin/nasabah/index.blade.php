<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Nasabah - Mobile Style</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f7f6; padding: 15px; }
        
        /* Mengubah Tabel menjadi tampilan Card sesuai Gambar */
        @media screen and (max-width: 992px) {
            table#tableNasabah thead { display: none; } /* Sembunyikan Header */
            table#tableNasabah border { border: none; }
            
            table#tableNasabah tbody tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                overflow: hidden;
            }

            table#tableNasabah tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: none;
                border-bottom: 1px solid #f0f0f0;
                padding: 10px 15px;
                text-align: right;
            }

            /* Menambahkan Label di sebelah kiri (NO, JENIS, NAMA, dll) */
            table#tableNasabah tbody td:before {
                content: attr(data-label);
                font-weight: bold;
                text-transform: uppercase;
                color: #666;
                font-size: 0.85rem;
                float: left;
                margin-right: 10px;
                text-align: left;
            }
            
            table#tableNasabah tbody td:last-child { border-bottom: none; display: block; text-align: center; }
        }

        /* Styling Badge & Button sesuai gambar */
        .badge-status {
            background-color: #198754;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            width: 100%;
            display: inline-block;
            text-align: right;
            font-weight: bold;
        }
        .btn-group-custom { display: flex; width: 100%; }
        .btn-edit { background-color: #ffc107; color: #fff; flex: 1; border-radius: 0; border: none; padding: 10px; }
        .btn-delete { background-color: #dc3545; color: #fff; flex: 1; border-radius: 0; border: none; padding: 10px; }
        .text-detail { font-size: 0.9rem; color: #333; line-height: 1.4; font-weight: bold; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-list me-2"></i>DATA NASABAH</h5>
        <button class="btn btn-sm btn-primary px-3 shadow-sm rounded-pill"><i class="fas fa-plus"></i> TAMBAH</button>
    </div>

    <div class="card border-0 bg-transparent">
        <table id="tableNasabah" class="table w-100">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NIK/JENIS</th>
                    <th>NAMA</th>
                    <th>KETERANGAN</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tableNasabah').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('nasabah.data') }}",
        dom: '<"mb-3"f>rt<"mt-3"p>', // Hanya tampilkan search dan pagination
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-end' },
            { data: 'level', name: 'level' },
            { data: 'nama', name: 'nama', className: 'fw-bold' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        // Tambahkan label data untuk responsive CSS
        createdRow: function(row, data, dataIndex) {
            $('td', row).eq(0).attr('data-label', 'NO');
            $('td', row).eq(1).attr('data-label', 'JENIS');
            $('td', row).eq(2).attr('data-label', 'NAMA');
            $('td', row).eq(3).attr('data-label', 'KET');
            $('td', row).eq(4).attr('data-label', 'STATUS');
            $('td', row).eq(5).attr('data-label', 'AKSI');
        }
    });
});
</script>
</body>
</html>