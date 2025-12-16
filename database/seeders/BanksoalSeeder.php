<?php

namespace Database\Seeders;

use App\Models\Banksoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanksoalSeeder extends Seeder
{
    public function run()
    {
        // Get the actual mapel and guru IDs
        $tkj = DB::table('mapels')->where('nama_mapel', 'Teknik Komputer Jaringan')->first();
        $budi = DB::table('gurus')->where('nama', 'Budi Santoso')->first();

        if ($tkj && $budi) {
            Banksoal::create([
                'mapel_id'    => $tkj->id,
                'guru_id'     => $budi->id,
                'pertanyaan'  => 'Apa ibu kota Indonesia?',
                'tipe_soal'   => 'pilihan_ganda',
                'opsi_a'      => 'Jakarta',
                'opsi_b'      => 'Bandung',
                'opsi_c'      => 'Surabaya',
                'opsi_d'      => 'Medan',
                'jawaban_benar'     => 'a',
            ]);
        }
    }
}