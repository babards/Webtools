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
        'pad_images',
        'padStatus',
        'latitude',
        'longitude',
        'vacancy',
        'padCreatedAt',
        'padUpdatedAt',
        'number_of_boarders'
    ];

    protected $casts = [
        'pad_images' => 'array',
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

    public function boarders()
    {
        return $this->hasMany(PadBoarder::class, 'pad_id', 'padID');
    }

    // Helper method to get the main image (first image or fallback to padImage)
    public function getMainImageAttribute()
    {
        if ($this->pad_images && count($this->pad_images) > 0) {
            return $this->pad_images[0];
        }
        return $this->padImage;
    }

    // Helper method to get all images
    public function getAllImagesAttribute()
    {
        $images = [];
        
        // Add images from pad_images array
        if ($this->pad_images && is_array($this->pad_images)) {
            $images = array_merge($images, $this->pad_images);
        }
        
        // If we have less than 3 images and padImage exists, add it as fallback
        if (count($images) === 0 && $this->padImage) {
            $images[] = $this->padImage;
        }
        
        return $images;
    }
}
