<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SyncController extends Controller
{
    // Daftar tabel yang akan disinkronkan
    protected $syncTables = [
        'master_nasabah'      => 'id',
        'master_pinjam'       => 'id',
        'angsuran'            => 'id',
        'master_pulsa_pinjam' => 'id_pinjam'
    ];

    public function index()
    {
        $settings = DB::table('settings')->pluck('value', 'key');
        $this->setupOnlineConnection($settings);

        $detailStats = [];
        $totalOffline = 0;
        $totalOnline = 0;
        $onlineError = false;

        foreach ($this->syncTables as $table => $id) {
            $countLokal = DB::table($table)->count();
            $totalOffline += $countLokal;

            $countOnline = 0;
            try {
                if (!$onlineError) {
                    $countOnline = DB::connection('mysql_online')->table($table)->count();
                    $totalOnline += $countOnline;
                }
            } catch (\Exception $e) {
                $onlineError = true;
            }

            $detailStats[] = [
                'nama_tabel' => $table,
                'lokal' => $countLokal,
                'online' => $onlineError ? 'ERR' : $countOnline,
                'selisih' => $onlineError ? '-' : ($countLokal - $countOnline)
            ];
        }

        $stats = [
            'total_offline' => $totalOffline,
            'total_online'  => $onlineError ? 'Koneksi Gagal' : $totalOnline,
            'detail' => $detailStats
        ];

        return view('admin.sync.index', compact('settings', 'stats'));
    }

    private function setupOnlineConnection($settings)
    {
        if (isset($settings['db_host_online'])) {
            config(['database.connections.mysql_online.host' => $settings['db_host_online']]);
            config(['database.connections.mysql_online.database' => $settings['db_database_online']]);
            config(['database.connections.mysql_online.username' => $settings['db_username_online']]);
            config(['database.connections.mysql_online.password' => $settings['db_password_online']]);
        }
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'db_host_online' => 'required',
            'db_database_online' => 'required',
            'db_username_online' => 'required',
            'db_password_online' => 'nullable',
        ]);

        foreach ($data as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value ?? '', 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan database online berhasil disimpan.');
    }

    public function startSync()
    {
        if (session_id()) session_write_close();

        return new StreamedResponse(function () {
            set_time_limit(0);

            $settings = DB::table('settings')->pluck('value', 'key');
            $this->setupOnlineConnection($settings);

            $this->sendEvent('start', ['message' => 'Menghubungkan ke server...']);

            $tables = $this->syncTables;
            $totalTables = count($tables);
            $currentIndex = 0;

            foreach ($tables as $table => $idField) {
                $currentIndex++;
                try {
                    // 1. Ambil ID terakhir di server online
                    $lastOnlineId = DB::connection('mysql_online')->table($table)->max($idField) ?? 0;

                    // 2. Ambil data baru dari lokal
                    $newData = DB::table($table)->where($idField, '>', $lastOnlineId)->orderBy($idField, 'asc')->get();
                    $totalDataInTable = $newData->count();

                    if ($totalDataInTable > 0) {
                        $syncedInTable = 0;
                        // Chunk data agar tidak overload memory
                        foreach ($newData->chunk(50) as $chunk) {
                            $insertData = collect($chunk)->map(fn($item) => (array)$item)->toArray();
                            DB::connection('mysql_online')->table($table)->insert($insertData);

                            $syncedInTable += count($insertData);
                            $progress = floor(($syncedInTable / $totalDataInTable) * 100);
                            $overall = floor((($currentIndex - 1) / $totalTables) * 100 + ($progress / $totalTables));

                            $this->sendEvent('progress', [
                                'table' => $table,
                                'overall' => $overall,
                                'msg' => "Sinkronisasi $table: $syncedInTable / $totalDataInTable"
                            ]);
                        }
                    }

                    $this->sendEvent('table_complete', ['table' => $table, 'count' => $totalDataInTable]);
                } catch (\Exception $e) {
                    $this->sendEvent('error', ['table' => $table, 'message' => $e->getMessage()]);
                    return; // Hentikan jika ada error database
                }
            }

            $this->sendEvent('complete', ['message' => 'Seluruh data berhasil disinkronkan ke Cloud!']);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function sendEvent($event, $data)
    {
        echo "event: $event\n";
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }
}
