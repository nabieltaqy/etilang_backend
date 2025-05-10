<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HearingSchedule;
use Carbon\Carbon;

class HearingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Pengadilan Negeri Kota Bandung',
            'Pengadilan Negeri Kab. Bandung',
        ];

        $totalSchedules = 40;
        $created = 0;
        $date = Carbon::now()->addDays(7); // Mulai dari 7 hari ke depan

        while ($created < $totalSchedules) {
            if ($date->isWeekday()) {
                HearingSchedule::create([
                    'location' => $locations[array_rand($locations)],
                    'date' => $date->copy()->setTime(9, 0, 0),
                ]);
                $created++;
            }

            $date->addDay(); // Lanjut ke hari berikutnya
        }
    }
}
