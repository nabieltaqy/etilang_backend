<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $guarded = ['id'];


    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
