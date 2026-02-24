<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeopleRelationship extends Model
{
    protected $fillable = ['person_id', 'related_person_id', 'relationship_type'];

    public function person()
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    public function relatedPerson()
    {
        return $this->belongsTo(People::class, 'related_person_id');
    }
}
