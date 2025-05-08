<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_public',
        'preview_audio_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(SheetImage::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function isFavoritedBy($user_id)
    {
        return $this->favoritedBy()->where('user_id', $user_id)->exists();//there exists a record in the pivot table
    }

    // is_favourite

    public function favoritedBy()//many-to-many relationship
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

}
