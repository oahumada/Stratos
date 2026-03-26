<?php

namespace App\Services;

use App\Models\TalentPass;
use App\Models\TalentPassSkill;
use App\Models\TalentPassCredential;
use App\Models\TalentPassExperience;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TalentPassService
{
    /**
     * Create a new talent pass for a person
     */
    public function create(int $organizationId, int $peopleId, array $data = []): TalentPass
    {
        return TalentPass::create([
            'ulid' => Str::ulid(),
            'organization_id' => $organizationId,
            'people_id' => $peopleId,
            'title' => $data['title'] ?? 'My CV',
            'summary' => $data['summary'] ?? null,
            'status' => 'draft',
            'visibility' => $data['visibility'] ?? 'private',
        ]);
    }

    /**
     * Add a skill to a talent pass
     */
    public function addSkill(int $talentPassId, array $skillData): TalentPassSkill
    {
        $talentPass = TalentPass::find($talentPassId);
        if (!$talentPass) {
            throw new \Exception("Talent pass not found");
        }

        return $talentPass->skills()->create([
            'skill_name' => $skillData['skill_name'],
            'proficiency_level' => $skillData['proficiency_level'] ?? 'intermediate',
            'years_of_experience' => $skillData['years_of_experience'] ?? 0,
        ]);
    }

    /**
     * Add a credential to a talent pass
     */
    public function addCredential(int $talentPassId, array $credentialData): TalentPassCredential
    {
        $talentPass = TalentPass::find($talentPassId);
        if (!$talentPass) {
            throw new \Exception("Talent pass not found");
        }

        return $talentPass->credentials()->create([
            'credential_name' => $credentialData['credential_name'],
            'issuer' => $credentialData['issuer'],
            'issued_date' => $credentialData['issued_date'],
            'expiry_date' => $credentialData['expiry_date'] ?? null,
            'credential_url' => $credentialData['credential_url'] ?? null,
            'credential_id' => $credentialData['credential_id'] ?? null,
        ]);
    }

    /**
     * Add an experience to a talent pass
     */
    public function addExperience(int $talentPassId, array $experienceData): TalentPassExperience
    {
        $talentPass = TalentPass::find($talentPassId);
        if (!$talentPass) {
            throw new \Exception("Talent pass not found");
        }

        return $talentPass->experiences()->create([
            'job_title' => $experienceData['job_title'],
            'company' => $experienceData['company'],
            'description' => $experienceData['description'] ?? null,
            'start_date' => $experienceData['start_date'],
            'end_date' => $experienceData['end_date'] ?? null,
            'is_current' => $experienceData['is_current'] ?? false,
            'location' => $experienceData['location'] ?? null,
            'employment_type' => $experienceData['employment_type'] ?? null,
        ]);
    }

    /**
     * Update talent pass details
     */
    public function update(int $talentPassId, array $data): TalentPass
    {
        $talentPass = TalentPass::find($talentPassId);
        if (!$talentPass) {
            throw new \Exception("Talent pass not found");
        }
        
        $talentPass->update([
            'title' => $data['title'] ?? $talentPass->title,
            'summary' => $data['summary'] ?? $talentPass->summary,
            'visibility' => $data['visibility'] ?? $talentPass->visibility,
        ]);

        return $talentPass;
    }

    /**
     * Publish a talent pass
     */
    public function publish(int $talentPassId): void
    {
        $talentPass = TalentPass::find($talentPassId);
        if ($talentPass) {
            $talentPass->publish();
        }
    }

    /**
     * Archive a talent pass
     */
    public function archive(int $talentPassId): void
    {
        $talentPass = TalentPass::find($talentPassId);
        if ($talentPass) {
            $talentPass->archive();
        }
    }

    /**
     * Get all talent passes for an organization
     */
    public function getByOrganization(int $organizationId): Collection
    {
        return TalentPass::byOrganization($organizationId)->with(['skills', 'credentials', 'experiences'])->get();
    }

    /**
     * Get public talent passes (for search/discovery)
     */
    public function getPublicPublished(): Collection
    {
        return TalentPass::published()->public()->with(['person', 'skills', 'credentials', 'experiences'])->get();
    }

    /**
     * Increment view count
     */
    public function recordView(TalentPass $talentPass): void
    {
        $talentPass->increment('view_count');
    }

    /**
     * Clone a talent pass from another
     */
    public function clone(int $sourceId): TalentPass
    {
        $source = TalentPass::find($sourceId);
        if (!$source) {
            throw new \Exception("Source talent pass not found");
        }

        $cloned = $this->create(
            $source->organization_id,
            $source->people_id,
            [
                'title' => 'Copy of ' . $source->title,
                'summary' => $source->summary,
                'visibility' => 'private',
            ]
        );

        // Clone skills
        foreach ($source->skills as $skill) {
            $this->addSkill($cloned->id, [
                'skill_name' => $skill->skill_name,
                'proficiency_level' => $skill->proficiency_level,
                'years_of_experience' => $skill->years_of_experience,
            ]);
        }

        // Clone credentials
        foreach ($source->credentials as $credential) {
            $this->addCredential($cloned->id, [
                'credential_name' => $credential->credential_name,
                'issuer' => $credential->issuer,
                'issued_date' => $credential->issued_date,
                'expiry_date' => $credential->expiry_date,
                'credential_url' => $credential->credential_url,
            ]);
        }

        // Clone experiences
        foreach ($source->experiences as $experience) {
            $this->addExperience($cloned->id, [
                'job_title' => $experience->job_title,
                'company' => $experience->company,
                'description' => $experience->description,
                'start_date' => $experience->start_date,
                'end_date' => $experience->end_date,
                'is_current' => $experience->is_current,
                'location' => $experience->location,
                'employment_type' => $experience->employment_type,
            ]);
        }

        return $cloned;
    }
}
