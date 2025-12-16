<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the actual jurusan IDs
        $ipa = DB::table('jurusans')->where('nama_jurusan', 'IPA')->first();
        $ips = DB::table('jurusans')->where('nama_jurusan', 'IPS')->first();

        if ($ipa && $ips) {
            DB::table('mapels')->insert([
                'nama_mapel' => 'Teknik Komputer Jaringan',
                'jurusan_id' => $ipa->id,
            ]);

            DB::table('mapels')->insert([
                'nama_mapel' => 'Tata ',
                'jurusan_id' => $ips->id,
            ]);
        }
    }
}