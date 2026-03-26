<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentPassSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'talent_pass_id',
        'skill_name',
        'proficiency_level',
        'years_of_experience',
        'endorsed_by_people_ids',
        'endorsement_count',
    ];

    protected function casts(): array
    {
        return [
            'years_of_experience' => 'integer',
            'endorsed_by_people_ids' => 'array',
            'endorsement_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function talentPass(): BelongsTo
    {
        return $this->belongsTo(TalentPass::class);
    }

    // Methods
    public function addEndorsement($peopleId)
    {
        $endorsements = $this->endorsed_by_people_ids ?? [];
        if (!in_array($peopleId, $endorsements)) {
            $endorsements[] = $peopleId;
            $this->update([
                'endorsed_by_people_ids' => $endorsements,
                'endorsement_count' => count($endorsements),
            ]);
        }
    }

    public function removeEndorsement($peopleId)
    {
        $endorsements = $this->endorsed_by_people_ids ?? [];
        $endorsements = array_filter($endorsements, fn($id) => $id !== $peopleId);
        $this->update([
            'endorsed_by_people_ids' => array_values($endorsements),
            'endorsement_count' => count($endorsements),
        ]);
    }
}
