<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('violation_types')->insert([
                [
                    'name'=>'Tidak Menggunakan Helm',
                    'regulation'=>'Pasal 106 ayat (8) - Kewajiban Menggunakan Helm',
                    'description' => "Setiap orang yang mengemudikan Sepeda Motor dan Penumpangnya wajib mengenakan helm yang memenuhi standar nasional Indonesia.",
                ],
            ]);
    }
}
