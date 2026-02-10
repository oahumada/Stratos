<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptInstruction extends Model
{
    use HasFactory;

    protected $table = 'prompt_instructions';

    protected $fillable = [
        'language',
        'content',
        'editable',
        'created_by',
        'author_name',
    ];
}
