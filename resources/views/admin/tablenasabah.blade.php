<div class="table-responsive">
    <table id="tableNasabah" class="table table-hover align-middle w-100 display nowrap">
        <thead class="bg-light">
            <tr class="small text-uppercase">
                <th>Profil</th>
                <th>Informasi Pribadi</th>
                <th class="all">Kontak & Pekerjaan</th> <th>Lokasi & Akun</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nasabah as $row)
            <tr>
                <td>
                    @if($row->foto)
                        <img src="{{ asset('storage/foto/'.$row->foto) }}" class="rounded-circle object-fit-cover shadow-sm" width="45" height="45">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width:45px; height:45px;">
                            {{ strtoupper(substr($row->nama, 0, 2)) }}
                        </div>
                    @endif
                </td>
                <td>
                    <div class="fw-bold text-dark">{{ $row->nama }}</div>
                    <div class="small text-muted">NIK: {{ $row->nik }}</div>
                </td>
                <td>
                    <div class="small fw-bold text-success"><i class="fas fa-phone me-1"></i> {{ $row->no_tlp }}</div>
                    <div class="small badge bg-light text-dark border">{{ $row->pekerjaan }}</div>
                </td>
                <td>
                    <div class="small">User: <span class="fw-bold">{{ $row->username }}</span></div>
                    @if($row->lat)
                    <a href="https://www.google.com/maps?q={{ $row->lat }},{{ $row->lng }}" target="_blank" class="badge bg-danger text-decoration-none">
                        <i class="fas fa-location-arrow"></i> GPS
                    </a>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-warning btn-edit-nasabah" data-nasabah="{{ json_encode($row) }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $row->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>