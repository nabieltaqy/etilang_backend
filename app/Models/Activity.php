<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $guarded = ['id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
