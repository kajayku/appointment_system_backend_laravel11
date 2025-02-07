<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id', 'email'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
