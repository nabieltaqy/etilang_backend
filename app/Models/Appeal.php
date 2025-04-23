<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    protected $table = 'appeals';
    protected $guarded = ['id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
