<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PadBoarder extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'pad_boarders';

    protected $fillable = [
        'pad_id',
        'user_id',
        'status',
    ];

    // Relationships
    public function pad()
    {
        return $this->belongsTo(Pad::class, 'pad_id', 'padID');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class ,'user_id');
    }
}
