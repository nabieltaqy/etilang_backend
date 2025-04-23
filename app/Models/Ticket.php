<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $guarded = ['id'];

    protected $keyType = 'string'; // ID become string from integer
    public $incrementing = false; // Non-auto increment
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid(); // Generate UUID automatically
            }
        });
    }
    public function appeal()
    {
        return $this->hasOne(Appeal::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hearingSchedule()
    {
        return $this->belongsTo(HearingSchedule::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
