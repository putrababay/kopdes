<div class="table-responsive p-3">
    <table id="tableNasabah" class="table table-hover align-middle w-100">
        <thead class="bg-light">
            <tr class="small text-uppercase">
                <th>Nasabah</th>
                <th>Kontak</th>
                <th class="none">NIK</th> <th class="none">Alamat</th>
                <th>Pekerjaan</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nasabah as $row)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        @if($row->foto)
                            <img src="{{ asset('storage/foto/'.$row->foto) }}" class="rounded-circle me-2 shadow-sm" width="40" height="40" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 shadow-sm fw-bold" style="width:40px; height:40px;">
                                {{ strtoupper(substr($row->nama, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold">{{ $row->nama }}</div>
                            <small class="text-muted">{{ $row->username }}</small>
                        </div>
                    </div>
                </td>
                <td>{{ $row->no_tlp }}</td>
                <td>{{ $row->nik }}</td>
                <td>{{ $row->alamat }}</td>
                <td>{{ $row->pekerjaan }}</td>
                <td><span class="badge bg-light text-dark border">{{ $row->level }}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-warning border-0" onclick='editData(@json($row))'>
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0" onclick="deleteData({{ $row->id }})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>