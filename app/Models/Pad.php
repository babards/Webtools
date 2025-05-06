<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pad extends Model
{
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
        'padCreatedAt', 
        'padUpdatedAt'
    ];

    // Relationship to landlord
    public function landlord()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
