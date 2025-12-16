<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the actual kelas and mapel IDs
        $ipa1 = DB::table('kelas')->where('nama_kelas', 'X IPA 1')->first();
        $ips1 = DB::table('kelas')->where('nama_kelas', 'X IPS 1')->first();
        $tkj = DB::table('mapels')->where('nama_mapel', 'Teknik Komputer Jaringan')->first();
        $tata = DB::table('mapels')->where('nama_mapel', 'Tata ')->first();

        if ($ipa1 && $ips1 && $tkj && $tata) {
            DB::table('jadwals')->insert([
                'kelas_id' => $ipa1->id,
                'mapel_id' => $tkj->id,
                'hari' => 'Senin',
                'dari_jam' => '07:00:00',
                'sampai_jam' => '08:00:00',
            ]);

            DB::table('jadwals')->insert([
                'kelas_id' => $ips1->id,
                'mapel_id' => $tata->id,
                'hari' => 'Selasa',
                'dari_jam' => '07:00:00',
                'sampai_jam' => '08:00:00',
            ]);
        }
    }
}