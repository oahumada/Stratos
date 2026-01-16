<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidences';

    protected $fillable = ['evaluable_type', 'evaluable_id', 'file_url', 'uploaded_by', 'notes'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
