<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarsLevel extends Model
{
    use HasFactory;

    protected $table = 'bars_levels';

    protected $fillable = ['name', 'min_value', 'max_value', 'description'];

    protected $casts = [
        'min_value' => 'integer',
        'max_value' => 'integer',
    ];
}
