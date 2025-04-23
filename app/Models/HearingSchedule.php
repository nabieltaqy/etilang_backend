<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HearingSchedule extends Model
{
    protected $table = 'hearing_schedules';
    protected $guarded = ['id'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
