<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CameraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cameras')->insert([
            [
                'location' => 'Bundaran Telkom',
                'stream_key' => 'ad23-2134s-321c23-1234',
                'server_url' => 'rtmp://bunderantelkom.com',
                'status' => 'active',
            ],
            [
                'location' => 'Depan Asrama',
                'stream_key' => 'ac23-32423-3242-1234',
                'server_url' => 'rtmp://depanasrama.com',
                'status' => 'active',
            ],
            [
                'location' => 'Gate 4',
                'stream_key' => 'al43-mkjl-3dxc23-1234',
                'server_url' => 'rtmp://gate4.com',
                'status' => 'active',
            ],
        ]);
    }
}
