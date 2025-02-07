<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['title', 'description', 'appointment_date','status','reminder_time','user_id'];



    public function guests()
    {
        return $this->hasMany(Guest::class);
    }
}
