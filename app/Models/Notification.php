<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = ['id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
