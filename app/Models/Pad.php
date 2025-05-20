<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pad extends Model
{
    use HasFactory;

    protected $primaryKey = 'padID';
    public $timestamps = false; // We'll handle timestamps manually

    protected $table = 'pads';

    protected $fillable = [
        'userID',
        'padName',
        'padDescription',
        'padLocation',
        'padRent',
        'padImage',
        'padStatus',
        'latitude',
        'longitude',
        'padCreatedAt',
        'padUpdatedAt',
        'number_of_boarders'
    ];

    // Relationship to landlord
    public function landlord()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    // Relationship to applications
    public function applications()
    {
        return $this->hasMany(PadApplication::class, 'pad_id', 'padID');
    }
}
