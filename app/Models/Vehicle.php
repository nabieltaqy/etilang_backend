<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';
    protected $guarded = ['id'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

}
