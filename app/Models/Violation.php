<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $keyType = 'string'; // ID become string from integer
    public $incrementing = false; // Non-auto increment
    protected $guarded = ['id']; // all fillable fields are automatically fillable

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid(); // Generate UUID automatically
            }
        });
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function violationType()
    {
        return $this->belongsTo(ViolationType::class);
    }



    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'number', 'number');
    }
}
