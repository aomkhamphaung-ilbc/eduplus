<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = ['start_time', 'end_time', 'meet_link'];

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
