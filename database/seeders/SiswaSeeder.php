<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the actual kelas IDs
        $ipa1 = DB::table('kelas')->where('nama_kelas', 'X IPA 1')->first();
        $ips1 = DB::table('kelas')->where('nama_kelas', 'X IPS 1')->first();

        if ($ipa1 && $ips1) {
            DB::table('siswas')->insert([
                'nama' => 'Kevin Hartanto',
                'nis' => '123454321',
                'kelas_id' => $ipa1->id,
                'telp' => '081234567890',
                'alamat' => 'Jl. Kevin Hartanto',
            ]);

            DB::table('siswas')->insert([
                'nama' => 'Siska Saraswati',
                'nis' => '543212345',
                'kelas_id' => $ips1->id,
                'telp' => '089876543210',
                'alamat' => 'Jl. Siska Saraswati',
            ]);
        }
    }
}