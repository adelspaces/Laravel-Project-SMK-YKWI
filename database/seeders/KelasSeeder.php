<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the actual jurusan and guru IDs
        $ipa = DB::table('jurusans')->where('nama_jurusan', 'IPA')->first();
        $ips = DB::table('jurusans')->where('nama_jurusan', 'IPS')->first();
        $budi = DB::table('gurus')->where('nama', 'Budi Santoso')->first();
        $gunawan = DB::table('gurus')->where('nama', 'Gunawan Efendi')->first();

        if ($ipa && $ips && $budi && $gunawan) {
            DB::table('kelas')->insert([
                'nama_kelas' => 'X IPA 1',
                'jurusan_id' => $ipa->id,
                'guru_id' => $budi->id,
            ]);

            DB::table('kelas')->insert([
                'nama_kelas' => 'X IPS 1',
                'jurusan_id' => $ips->id,
                'guru_id' => $gunawan->id,
            ]);
        }
    }
}