<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\People;
use App\Models\VerifiableCredential;
use App\Traits\ApiResponses;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TalentPassController extends Controller
{
    use ApiResponses;

    /**
     * Get the full Talent Pass (Wallet) for a person
     */
    public function show($people_id)
    {
        $person = People::with([
            'verifiableCredentials',
            'skills' => function ($query) {
                // Return active skills up to date
                $query->wherePivot('is_active', true)
                      ->wherePivot('current_level', '>=', 1);
            },
            'badges'
        ])->findOrFail($people_id);

        $wallet = [
            'did' => 'did:stratos:' . Str::uuid(), // Mock Decentralized Identifier
            'holder_name' => $person->full_name,
            'department' => $person->department->name ?? 'N/A',
            'role' => $person->role->name ?? 'N/A',
            'total_credentials' => $person->verifiableCredentials->count(),
            'credentials' => $person->verifiableCredentials->map(function ($vc) {
                return [
                    'id' => $vc->id,
                    'type' => $vc->type,
                    'issuer' => $vc->issuer_name,
                    'issued_at' => $vc->issued_at->toIso8601String(),
                    'payload' => $vc->credential_data,
                    'status' => $vc->status,
                    'signature' => $vc->cryptographic_signature,
                ];
            }),
            'unverified_achievements' => [
                'skills' => $person->skills->map(function ($s) {
                    return [
                        'skill_name' => $s->name,
                        'level' => $s->pivot->current_level,
                        'verified' => (bool)$s->pivot->verified,
                    ];
                }),
                'badges' => $person->badges->map(function ($b) {
                    return [
                        'badge_name' => $b->name,
                        'awarded_at' => $b->pivot->awarded_at,
                    ];
                })
            ]
        ];

        return $this->successResponse($wallet, 'Talent Pass retrieved successfully');
    }

    /**
     * Generate a Mock Verifiable Credential for an achievement
     * (In a real scenario, this would generate a W3C payload and sign it via Web3)
     */
    public function generateCredential(Request $request, $people_id)
    {
        $request->validate([
            'type' => 'required|string',
            'payload' => 'required|array'
        ]);

        $person = People::findOrFail($people_id);

        $vc = VerifiableCredential::create([
            'people_id' => $person->id,
            'type' => $request->type, // e.g., 'SkillAssessment', 'QuestCompletion'
            'issuer_name' => 'Stratos Platform',
            'issuer_did' => 'did:stratos:issuer:' . config('app.url'),
            'credential_data' => $request->payload,
            'cryptographic_signature' => '0x' . bin2hex(random_bytes(32)), // Mock signature
            'issued_at' => Carbon::now(),
            'status' => 'active'
        ]);

        return $this->successResponse($vc, 'Verifiable Credential issued successfully', 201);
    }
}
