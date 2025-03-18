<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicles')->insert([
            [
                'number' => 'D4678ADL',
                'category' => 'MOTOR',
                'brand' => 'YAMAHA',
                'type' => 'B6H-F A/T',
                'color' => 'BIRU',
                'owner_name' => 'Aksanardian Alberami',
                'owner_phone' => '082111672920',
                'owner_email' => 'aksanardian310@gmail.com'
            ],
            [
                'number' => 'BM4468ZAZ',
                'category' => 'MOTOR',
                'brand' => 'YAMAHA',
                'type' => 'BBP-A A/T',
                'color' => 'HITAM',
                'owner_name' => 'Sumanto Lesmana Putra',
                'owner_phone' => '08117676477',
                'owner_email' => 'lesmana.pta@gmail.com'
            ],
            [
                'number' => 'B4349KLZ',
                'category' => 'MOTOR',
                'brand' => 'YAMAHA',
                'type' => 'B65-R',
                'color' => 'PERAK BIRU',
                'owner_name' => 'Avicena Naufaldo',
                'owner_phone' => '085155375377',
                'owner_email' => 'avicena.n@gmail.com'
            ],
            [
                'number' => 'AG6033RCE',
                'category' => 'MOTOR',
                'brand' => 'HONDA',
                'type' => 'B65-R',
                'color' => 'BROWN',
                'owner_name' => 'Hana Kamila Naura Yasmin',
                'owner_phone' => '085330663107',
                'owner_email' => 'naurayasmin2018@gmail.com'
            ],
            [
                'number' => 'DR4657BY',
                'category' => 'MOTOR',
                'brand' => 'HONDA',
                'type' => 'NC11D1CF A/T',
                'color' => 'HITAM',
                'owner_name' => 'Nabil Afkar',
                'owner_phone' => '082147588138',
                'owner_email' => 'nabilafkar2@gmail.com'
            ],
            [
                'number' => 'B3507EPE',
                'category' => 'MOTOR',
                'brand' => 'HONDA',
                'type' => 'V1J02Q32L0 A/T',
                'color' => 'COKLAT',
                'owner_name' => 'Muhamad Rizky Rifaldi',
                'owner_phone' => '082210852679',
                'owner_email' => 'mrizkyrifaldi28@gmail.com'
            ],
            [
                'number' => 'D3365UEE',
                'category' => 'MOTOR',
                'brand' => 'YAMAHA',
                'type' => '2DP R A/T',
                'color' => 'MERAH',
                'owner_name' => 'Ridho indra',
                'owner_phone' => '082257423118',
                'owner_email' => 'indraridho26@gmail.com'
            ],
            [
                'number' => 'B6914JME',
                'category' => 'MOTOR',
                'brand' => 'HONDA',
                'type' => 'NF11C1C M/T',
                'color' => 'HIJAU PUTIH',
                'owner_name' => 'Nabiel Taqy',
                'owner_phone' => '085156644369',
                'owner_email' => 'nabieltaqy@gmail.com'
            ],
            [
                'number' => 'B6344PUY',
                'category' => 'MOTOR',
                'brand' => 'YAMAHA',
                'type' => '1LB A/T',
                'color' => 'MERAH',
                'owner_name' => 'Reyhan Faqih Ashuri',
                'owner_phone' => '085775346608',
                'owner_email' => 'reyhanfaqihh@gmail.com'
            ],
        ]);
    }
}
