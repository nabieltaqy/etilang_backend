<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $table = 'fines';
    protected $guarded = ['id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
