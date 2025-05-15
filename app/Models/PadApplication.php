<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'pad_id',
        'user_id',
        'status',
        'application_date',
        'message',
    ];

    protected $casts = [
        'application_date' => 'datetime',
    ];

    public function pad()
    {
        return $this->belongsTo(Pad::class, 'pad_id', 'padID');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
