<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SheetImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sheet_id',
        'image_path',
    ];

    public function sheet()
    {
        return $this->belongsTo(Sheet::class);
    }
}
