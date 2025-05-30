<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $table = 'violation_types';
    protected $guarded = ['id'];

    public function violation()
    {
        return $this->hasMany(Violation::class);
    }
}
