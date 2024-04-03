<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'prep_time',
        'cook_time'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
