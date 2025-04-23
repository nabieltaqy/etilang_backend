<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $table = 'cameras';
    protected $guarded = ['id'];

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
