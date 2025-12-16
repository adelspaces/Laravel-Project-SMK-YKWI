<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the actual mapel IDs
        $tkj = DB::table('mapels')->where('nama_mapel', 'Teknik Komputer Jaringan')->first();
        $tata = DB::table('mapels')->where('nama_mapel', 'Tata ')->first();

        if ($tkj && $tata) {
            DB::table('gurus')->insert([
                'nama' => 'Budi Santoso',
                'nip' => '1234567890',
                'mapel_id' => $tkj->id,
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Budi Santoso',
            ]);

            DB::table('gurus')->insert([
                'nama' => 'Gunawan Efendi',
                'nip' => '0987654321',
                'mapel_id' => $tata->id,
                'no_telp' => '089876543210',
                'alamat' => 'Jl. Gunawan Efendi',
            ]);
        }
    }
}