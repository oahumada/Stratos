<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $organization_id
 * @property string $name
 * @property string $provider
 * @property string $model
 * @property string|null $system_prompt
 * @property array<array-key, mixed>|null $expertise_areas
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $role_description
 * @property string|null $persona
 * @property string $type
 * @property array<array-key, mixed>|null $capabilities_config
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Roles> $supportedRoles
 * @property-read int|null $supported_roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCapabilitiesConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereExpertiseAreas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent wherePersona($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereRoleDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereSystemPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereUpdatedAt($value)
 */
	class Agent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $job_opening_id
 * @property int $people_id
 * @property string $status
 * @property string|null $message
 * @property \Illuminate\Support\Carbon $applied_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $ai_analysis
 * @property int|null $match_score
 * @property-read \App\Models\JobOpening $jobOpening
 * @property-read \App\Models\People $people
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAiAnalysis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereJobOpeningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereMatchScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Application whereUpdatedAt($value)
 */
	class Application extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property string $mode
 * @property array<array-key, mixed>|null $schedule_config
 * @property array<array-key, mixed>|null $scope
 * @property array<array-key, mixed>|null $evaluators
 * @property array<array-key, mixed>|null $instruments
 * @property array<array-key, mixed>|null $notifications
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssessmentRequest> $requests
 * @property-read int|null $requests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereEvaluators($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereInstruments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereScheduleConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentCycle whereUpdatedAt($value)
 */
	class AssessmentCycle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $assessment_request_id
 * @property string $question
 * @property string $answer
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $skill_id
 * @property int|null $score Puntaje BARS 1-5
 * @property string|null $evidence_url
 * @property int|null $confidence_level Nivel de certeza del evaluador 1-100
 * @property-read \App\Models\AssessmentRequest $request
 * @property-read \App\Models\Skill|null $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereAssessmentRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereConfidenceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereEvidenceUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentFeedback whereUpdatedAt($value)
 */
	class AssessmentFeedback extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $assessment_session_id
 * @property string $role
 * @property string $content
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AssessmentSession $session
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereAssessmentSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentMessage whereUpdatedAt($value)
 */
	class AssessmentMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $target_type
 * @property int|null $target_id
 * @property int|null $frequency_months Periodicidad en meses
 * @property string|null $trigger_event Evento disparador: promotion, onboarding_end, etc
 * @property array<array-key, mixed>|null $evaluators_config Configuración de fuentes: jefe, pares, etc
 * @property int|null $owner_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_run_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organizations $organization
 * @property-read \App\Models\User|null $owner
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereEvaluatorsConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereFrequencyMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereLastRunAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereTriggerEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentPolicy whereUpdatedAt($value)
 */
	class AssessmentPolicy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int $evaluator_id
 * @property int $subject_id
 * @property string $relationship peer, supervisor, subordinate, etc
 * @property string $status
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $assessment_cycle_id
 * @property-read \App\Models\AssessmentCycle|null $cycle
 * @property-read \App\Models\People $evaluator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssessmentFeedback> $feedback
 * @property-read int|null $feedback_count
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\People $subject
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereAssessmentCycleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereEvaluatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentRequest whereUpdatedAt($value)
 */
	class AssessmentRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int $people_id
 * @property int|null $agent_id
 * @property int|null $scenario_id
 * @property string $type
 * @property string $status
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Agent|null $agent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssessmentMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Organizations $organization
 * @property-read \App\Models\People $person
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PsychometricProfile> $psychometricProfiles
 * @property-read int|null $psychometric_profiles_count
 * @property-read \App\Models\Scenario|null $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentSession whereUpdatedAt($value)
 */
	class AssessmentSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $icon
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $People
 * @property-read int|null $people_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereUpdatedAt($value)
 */
	class Badge extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $skill_id
 * @property int $level
 * @property string $level_name
 * @property string $behavioral_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $learning_content
 * @property string|null $performance_indicator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereBehavioralDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereLearningContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereLevelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel wherePerformanceIndicator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarsLevel whereUpdatedAt($value)
 */
	class BarsLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property numeric $position_x
 * @property numeric $position_y
 * @property int $importance
 * @property string $type
 * @property string $category
 * @property string $status
 * @property int|null $discovered_in_scenario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $llm_id
 * @property string|null $embedding Representación vectorial de la capability (nombre + descripción) para búsqueda semántica y detección de duplicados.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CapabilityCompetency> $capabilityCompetencies
 * @property-read int|null $capability_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read \App\Models\Scenario|null $discoveredInScenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDiscoveredInScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereImportance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereLlmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereUpdatedAt($value)
 */
	class Capability extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $capability_id
 * @property int $competency_id
 * @property int $required_level
 * @property int|null $priority
 * @property int|null $weight
 * @property string|null $rationale
 * @property bool $is_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Capability $capability
 * @property-read \App\Models\Competency $competency
 * @property-read \App\Models\Scenario $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereCapabilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapabilityCompetency whereWeight($value)
 */
	class CapabilityCompetency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $scenario_id
 * @property string $title
 * @property string|null $description
 * @property string|null $change_group_id
 * @property string $status
 * @property int|null $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $effective_from
 * @property \Illuminate\Support\Carbon|null $applied_at
 * @property array<array-key, mixed>|null $diff
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Organizations|null $organization
 * @property-read \App\Models\Scenario|null $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereAppliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereChangeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereDiff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereEffectiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeSet whereUpdatedAt($value)
 */
	class ChangeSet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $discovered_in_scenario_id
 * @property string|null $llm_id
 * @property string|null $embedding Representación vectorial de la competencia para búsqueda semántica.
 * @property string $status
 * @property int|null $agent_id
 * @property array<array-key, mixed>|null $cube_dimensions
 * @property-read \App\Models\Agent|null $agent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CapabilityCompetency> $capabilityCompetencies
 * @property-read int|null $capability_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompetencySkill> $competencySkills
 * @property-read int|null $competency_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleCompetency> $roleCompetencies
 * @property-read int|null $role_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Roles> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompetencyVersion> $versions
 * @property-read int|null $versions_count
 * @method static \Database\Factories\CompetencyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereCubeDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereDiscoveredInScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereLlmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Competency whereUpdatedAt($value)
 */
	class Competency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $skill_id
 * @property int $level 1-5
 * @property string|null $name Ej: Novato, Experto
 * @property string $description Descripción conductual del nivel
 * @property array<array-key, mixed>|null $key_behaviors Ejemplos específicos de comportamientos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereKeyBehaviors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyLevelBars whereUpdatedAt($value)
 */
	class CompetencyLevelBars extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $competency_id
 * @property int $skill_id
 * @property int $weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency $competency
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencySkill whereWeight($value)
 */
	class CompetencySkill extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $competency_id
 * @property string|null $version_group_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $effective_from
 * @property string $evolution_state
 * @property string|null $obsolescence_reason
 * @property array<array-key, mixed>|null $metadata
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency|null $competency
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereEffectiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereEvolutionState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereObsolescenceReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompetencyVersion whereVersionGroupId($value)
 */
	class CompetencyVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $parent_id Referencia al departamento padre
 * @property int|null $manager_id Referencia al People (dueño / líder)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $People
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Departments> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\People|null $manager
 * @property-read \App\Models\Organization $organization
 * @property-read Departments|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereUpdatedAt($value)
 */
	class Departments extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $development_path_id
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property string $strategy
 * @property int $order
 * @property string $status
 * @property int|null $estimated_hours
 * @property numeric $impact_weight
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $mentor_id
 * @property string|null $lms_course_id
 * @property string|null $lms_enrollment_id
 * @property string|null $lms_provider
 * @property-read \App\Models\Evaluation|null $evaluation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Evidence> $evidences
 * @property-read int|null $evidences_count
 * @property-read \App\Models\People|null $mentor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MentorshipSession> $mentorshipSessions
 * @property-read int|null $mentorship_sessions_count
 * @property-read \App\Models\DevelopmentPath $path
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereDevelopmentPathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereEstimatedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereImpactWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereLmsCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereLmsEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereLmsProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereMentorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentAction whereUpdatedAt($value)
 */
	class DevelopmentAction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $evaluation_id
 * @property int $people_id
 * @property string $action_title
 * @property string|null $description
 * @property int|null $estimated_weeks
 * @property numeric|null $estimated_impact
 * @property int $target_role_id
 * @property string $status
 * @property int $estimated_duration_months
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property array<array-key, mixed>|null $steps
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentAction> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\Evaluation|null $evaluation
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\People $people
 * @property-read \App\Models\Roles $targetRole
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereActionTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereEstimatedDurationMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereEstimatedImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereEstimatedWeeks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereEvaluationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereTargetRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevelopmentPath withoutTrashed()
 */
	class DevelopmentPath extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $people_id
 * @property int|null $e_nps 0-10 or 1-5 score for loyalty
 * @property int|null $stress_level 1-5 representing stress
 * @property int|null $engagement_level 1-5 representing engagement
 * @property string|null $comments
 * @property string|null $ai_turnover_risk low, medium, high
 * @property string|null $ai_turnover_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereAiTurnoverReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereAiTurnoverRisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereENps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereEngagementLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereStressLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeePulse whereUpdatedAt($value)
 */
	class EmployeePulse extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $skill_id
 * @property int|null $scenario_id
 * @property numeric|null $current_level
 * @property int|null $required_level
 * @property numeric|null $gap
 * @property array<array-key, mixed>|null $metadata
 * @property int $confidence_score
 * @property \Illuminate\Support\Carbon|null $evaluated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentPath> $developmentActions
 * @property-read int|null $development_actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Evidence> $evidences
 * @property-read int|null $evidences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\Scenario|null $scenario
 * @property-read \App\Models\Skill $skill
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereCurrentLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereEvaluatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereGap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evaluation whereUserId($value)
 */
	class Evaluation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $evaluation_id
 * @property int $evaluator_id
 * @property string $evaluator_role
 * @property int $bars_level_id
 * @property string|null $evidence_comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BarsLevel $barsLevel
 * @property-read \App\Models\Evaluation $evaluation
 * @property-read \App\Models\User $evaluator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereBarsLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereEvaluationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereEvaluatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereEvaluatorRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereEvidenceComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationResponse whereUpdatedAt($value)
 */
	class EvaluationResponse extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\DevelopmentAction|null $developmentAction
 * @property-read \App\Models\Evaluation|null $evaluation
 * @property-read \App\Models\User|null $validator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evidence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evidence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Evidence query()
 */
	class Evidence extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_generation_id
 * @property int $sequence
 * @property string $chunk
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ScenarioGeneration $generation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereChunk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereScenarioGenerationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GenerationChunk whereUpdatedAt($value)
 */
	class GenerationChunk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $title
 * @property int $role_id
 * @property string|null $department
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $deadline
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_external
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $requirements
 * @property string|null $benefits
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\Roles $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereBenefits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobOpening whereUpdatedAt($value)
 */
	class JobOpening extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $development_action_id
 * @property \Illuminate\Support\Carbon $session_date
 * @property string|null $summary
 * @property string|null $next_steps
 * @property int $duration_minutes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DevelopmentAction $developmentAction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereDevelopmentActionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereNextSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereSessionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MentorshipSession whereUpdatedAt($value)
 */
	class MentorshipSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $generation_id
 * @property array<array-key, mixed>|null $metadata
 * @property array<array-key, mixed>|null $errors
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereGenerationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MetadataValidationIssue whereUpdatedAt($value)
 */
	class MetadataValidationIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $subdomain
 * @property string|null $industry
 * @property string $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed> $active_modules Módulos activos de Stratos para esta organización
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $People
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentPath> $developmentPaths
 * @property-read int|null $development_paths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobOpening> $jobOpenings
 * @property-read int|null $job_openings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Roles> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SmartAlert> $smartAlerts
 * @property-read int|null $smart_alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\OrganizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereActiveModules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int $use_case_template_id
 * @property bool $is_active
 * @property array<array-key, mixed>|null $custom_config
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organizations $organization
 * @property-read \App\Models\ScenarioTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase forOrganization(int $organizationId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereCustomConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUseCase whereUseCaseTemplateId($value)
 */
	class OrganizationUseCase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $subdomain
 * @property string|null $industry
 * @property string $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed> $active_modules Módulos activos de Stratos para esta organización
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $People
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentPath> $developmentPaths
 * @property-read int|null $development_paths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobOpening> $jobOpenings
 * @property-read int|null $job_openings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Roles> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SmartAlert> $smartAlerts
 * @property-read int|null $smart_alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\OrganizationsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereActiveModules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organizations whereUpdatedAt($value)
 */
	class Organizations extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string|null $external_id
 * @property int|null $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int|null $role_id
 * @property int|null $department_id
 * @property \Illuminate\Support\Carbon|null $hire_date
 * @property string|null $termination_date
 * @property string $status
 * @property string|null $photo_url
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_high_potential
 * @property int $current_points
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRoleSkills> $activeSkills
 * @property-read int|null $active_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Application> $applications
 * @property-read int|null $applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Badge> $badges
 * @property-read int|null $badges_count
 * @property-read \App\Models\Departments|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentPath> $developmentPaths
 * @property-read int|null $development_paths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRoleSkills> $expiredSkills
 * @property-read int|null $expired_skills_count
 * @property-read string $full_name
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, People> $managers
 * @property-read int|null $managers_count
 * @property-read \App\Models\Organizations $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, People> $peers
 * @property-read int|null $peers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeoplePoint> $points
 * @property-read int|null $points_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PsychometricProfile> $psychometricProfiles
 * @property-read int|null $psychometric_profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRelationship> $relations
 * @property-read int|null $relations_count
 * @property-read \App\Models\Roles|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRoleSkills> $roleSkills
 * @property-read int|null $role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read \Illuminate\Database\Eloquent\Collection<int, People> $subordinates
 * @property-read int|null $subordinates_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PeopleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereCurrentPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereIsHighPotential($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People wherePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereTerminationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|People withoutTrashed()
 */
	class People extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $people_id
 * @property int $points
 * @property string|null $reason
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeoplePoint whereUpdatedAt($value)
 */
	class PeoplePoint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $person_id
 * @property int $related_person_id
 * @property string $relationship_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $person
 * @property-read \App\Models\People $relatedPerson
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship whereRelatedPersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship whereRelationshipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRelationship whereUpdatedAt($value)
 */
	class PeopleRelationship extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $people_id
 * @property int|null $role_id
 * @property int $skill_id
 * @property int $level Compatibilidad: alias para current_level en fixtures/tests
 * @property int $current_level Nivel actual de la persona en esta skill (1-5)
 * @property int $required_level Nivel requerido por el rol en el momento de asignación
 * @property bool $is_active Si está activa (rol actual) o es histórica (rol pasado)
 * @property \Illuminate\Support\Carbon|null $evaluated_at Fecha de última evaluación
 * @property \Illuminate\Support\Carbon|null $expires_at Fecha de caducidad - requiere reevaluación
 * @property int|null $evaluated_by
 * @property bool $verified
 * @property string $evidence_source
 * @property string|null $evidence_date
 * @property string|null $notes Notas de evaluación o cambios
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $evaluator
 * @property-read \App\Models\People $person
 * @property-read \App\Models\Roles|null $role
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills forPerson($personId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills forRole($roleId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills needsReevaluation()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereCurrentLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereEvaluatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereEvaluatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereEvidenceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereEvidenceSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PeopleRoleSkills whereVerified($value)
 */
	class PeopleRoleSkills extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $module
 * @property string $action
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $people_id
 * @property int $quest_id
 * @property string $status
 * @property array<array-key, mixed>|null $progress
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $person
 * @property-read \App\Models\Quest $quest
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereQuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonQuest whereUpdatedAt($value)
 */
	class PersonQuest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $language
 * @property string $content
 * @property bool $editable
 * @property int|null $created_by
 * @property string|null $author_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereAuthorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PromptInstruction whereUpdatedAt($value)
 */
	class PromptInstruction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $people_id
 * @property int|null $assessment_session_id
 * @property string $trait_name
 * @property float $score
 * @property string|null $rationale
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $people
 * @property-read \App\Models\People $person
 * @property-read \App\Models\AssessmentSession|null $session
 * @method static \Database\Factories\PsychometricProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereAssessmentSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereTraitName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PsychometricProfile whereUpdatedAt($value)
 */
	class PsychometricProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $pulse_survey_id
 * @property int $people_id
 * @property array<array-key, mixed> $answers
 * @property numeric|null $sentiment_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\People $people
 * @property-read \App\Models\PulseSurvey $survey
 * @method static \Database\Factories\PulseResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse whereAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse wherePeopleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse wherePulseSurveyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse whereSentimentScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseResponse whereUpdatedAt($value)
 */
	class PulseResponse extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property array<array-key, mixed> $questions
 * @property bool $is_active
 * @property int|null $department_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $ai_report
 * @property-read \App\Models\Departments|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PulseResponse> $responses
 * @property-read int|null $responses_count
 * @method static \Database\Factories\PulseSurveyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereAiReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PulseSurvey whereUpdatedAt($value)
 */
	class PulseSurvey extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property string $mode
 * @property array<array-key, mixed>|null $schedule_config
 * @property array<array-key, mixed>|null $scope
 * @property array<array-key, mixed>|null $topics
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Organization $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereScheduleConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PxCampaign whereUpdatedAt($value)
 */
	class PxCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $type
 * @property int $points_reward
 * @property int|null $badge_id
 * @property array<array-key, mixed>|null $requirements
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Badge|null $badge
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest wherePointsReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quest whereUpdatedAt($value)
 */
	class Quest extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleAiLeverage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleAiLeverage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleAiLeverage query()
 */
	class RoleAiLeverage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $competency_id
 * @property int $required_level Nivel mínimo esperado (1-5). Base fundamental para el cálculo de brechas (talent gaps).
 * @property int $criticity Prioridad de cierre de brecha (1-5). Las competencias críticas se priorizan en planes de desarrollo y sugerencias de IA.
 * @property string $change_type Indica la evolución del requerimiento (new, increased, stable). Crucial para proyecciones en Scenario IQ.
 * @property string|null $strategy Estrategia sugerida para cerrar brechas: Buy (externo), Build (interno), Borrow (movilidad) o Bot (automatización).
 * @property string|null $notes Contexto adicional para decisiones manuales o retroalimentación a agentes IA.
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Competency $competency
 * @property-read \App\Models\Roles $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereCriticity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleCompetency whereUpdatedAt($value)
 */
	class RoleCompetency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $skill_id
 * @property int $required_level
 * @property bool $is_critical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $source
 * @property int|null $competency_id
 * @property int $ai_leverage_score
 * @property string|null $ai_integration_notes
 * @property-read \App\Models\Roles $role
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereAiIntegrationNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereAiLeverageScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereIsCritical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSkill whereUpdatedAt($value)
 */
	class RoleSkill extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $scenario_id
 * @property int|null $role_id
 * @property int|null $mapped_role_id
 * @property string|null $sunset_reason
 * @property array<array-key, mixed>|null $metadata
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Roles|null $mappedRole
 * @property-read \App\Models\Roles|null $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereMappedRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereSunsetReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleSunsetMapping whereUpdatedAt($value)
 */
	class RoleSunsetMapping extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $role_id
 * @property string|null $version_group_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $effective_from
 * @property string $evolution_state
 * @property array<array-key, mixed>|null $metadata
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Roles|null $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereEffectiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereEvolutionState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleVersion whereVersionGroupId($value)
 */
	class RoleVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $department_id
 * @property string $name
 * @property string|null $department
 * @property string|null $family
 * @property string $level
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $embedding Representación vectorial del perfil del rol para búsqueda semántica.
 * @property string|null $llm_id
 * @property string $status
 * @property int|null $discovered_in_scenario_id
 * @property array<array-key, mixed>|null $ai_archetype_config
 * @property int|null $agent_id
 * @property int|null $blueprint_id
 * @property array<array-key, mixed>|null $cube_dimensions
 * @property int|null $parent_id Referencia al rol padre (jerarquía)
 * @property-read \App\Models\Agent|null $agent
 * @property-read \App\Models\TalentBlueprint|null $blueprint
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Roles> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DevelopmentPath> $developmentPaths
 * @property-read int|null $development_paths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JobOpening> $jobOpenings
 * @property-read int|null $job_openings_count
 * @property-read \App\Models\Organization $organization
 * @property-read Roles|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $people
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRoleSkills> $peopleRoleSkills
 * @property-read int|null $people_role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleCompetency> $roleCompetencies
 * @property-read int|null $role_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleSkill> $roleSkills
 * @property-read int|null $role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill> $skills
 * @property-read int|null $skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleVersion> $versions
 * @property-read int|null $versions_count
 * @method static \Database\Factories\RolesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereAiArchetypeConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereBlueprintId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereCubeDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereDiscoveredInScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereLlmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Roles whereUpdatedAt($value)
 */
	class Roles extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property array<array-key, mixed>|null $kpis
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int $horizon_months
 * @property int $fiscal_year
 * @property string $scope_type
 * @property string|null $scope_notes
 * @property string|null $strategic_context
 * @property string|null $budget_constraints
 * @property string|null $legal_constraints
 * @property string|null $labor_relations_constraints
 * @property string $status
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property array<array-key, mixed>|null $assumptions
 * @property int $owner_user_id
 * @property int|null $sponsor_user_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $version_group_id
 * @property int $version_number
 * @property bool $is_current_version
 * @property int|null $parent_id
 * @property string $f
 * @property int|null $scope_id
 * @property int $current_step
 * @property string $decision_status
 * @property string $execution_status
 * @property int|null $owner_id
 * @property string|null $last_simulated_at
 * @property string|null $scenario_type
 * @property int|null $time_horizon_weeks
 * @property array<array-key, mixed>|null $custom_config
 * @property int|null $estimated_budget
 * @property int|null $template_id
 * @property int|null $source_generation_id
 * @property string|null $accepted_prompt
 * @property bool $accepted_prompt_redacted
 * @property array<array-key, mixed>|null $accepted_prompt_metadata
 * @property array<array-key, mixed>|null $embedding Representación vectorial del escenario (nombre, descripción, assumptions) para comparación semántica.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CapabilityCompetency> $capabilityCompetencies
 * @property-read int|null $capability_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioClosureStrategy> $closureStrategies
 * @property-read int|null $closure_strategies_count
 * @property-read mixed $synthetization_index
 * @property-read \App\Models\User $owner
 * @property-read Scenario|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRole> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioCapability> $scenarioCapabilities
 * @property-read int|null $scenario_capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRoleCompetency> $scenarioRoleCompetencies
 * @property-read int|null $scenario_role_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRoleSkill> $scenarioRoleSkills
 * @property-read int|null $scenario_role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRole> $scenarioRoles
 * @property-read int|null $scenario_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioSkillDemand> $skillDemands
 * @property-read int|null $skill_demands_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioSkill> $skills
 * @property-read int|null $skills_count
 * @property-read \App\Models\ScenarioGeneration|null $sourceGeneration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioStatusEvent> $statusEvents
 * @property-read int|null $status_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TalentBlueprint> $talentBlueprints
 * @property-read int|null $talent_blueprints_count
 * @method static \Database\Factories\ScenarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereAcceptedPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereAcceptedPromptMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereAcceptedPromptRedacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereAssumptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereBudgetConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCustomConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDecisionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereEstimatedBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereExecutionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereF($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereFiscalYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereHorizonMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsCurrentVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereKpis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereLaborRelationsConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereLastSimulatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereLegalConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereOwnerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereScenarioType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereScopeNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereScopeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereSourceGenerationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereSponsorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereStrategicContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereTimeHorizonWeeks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereVersionGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereVersionNumber($value)
 */
	class Scenario extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $capability_id
 * @property string $strategic_role
 * @property int $strategic_weight
 * @property int $priority
 * @property string|null $rationale
 * @property int $required_level
 * @property bool $is_critical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float|null $position_x
 * @property float|null $position_y
 * @property bool $is_fixed
 * @property-read \App\Models\Capability $capability
 * @property-read \App\Models\Scenario $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereCapabilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereIsCritical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereIsFixed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability wherePositionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability wherePositionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereStrategicRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereStrategicWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioCapability whereUpdatedAt($value)
 */
	class ScenarioCapability extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int|null $skill_id
 * @property string $strategy
 * @property string $strategy_name
 * @property string|null $description
 * @property numeric|null $estimated_cost
 * @property int|null $estimated_time_weeks
 * @property numeric $success_probability
 * @property string $risk_level
 * @property string $status
 * @property array<array-key, mixed>|null $action_items
 * @property int|null $assigned_to
 * @property \Illuminate\Support\Carbon|null $target_completion_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $role_id
 * @property float|null $ia_confidence_score
 * @property string|null $ia_strategy_rationale
 * @property bool $is_ia_generated
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \App\Models\Roles|null $role
 * @property-read \App\Models\Scenario $scenario
 * @property-read \App\Models\Skill|null $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy byStrategy($strategy)
 * @method static \Database\Factories\ScenarioClosureStrategyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereActionItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereEstimatedTimeWeeks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereIaConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereIaStrategyRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereIsIaGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereStrategyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereSuccessProbability($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereTargetCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioClosureStrategy withoutTrashed()
 */
	class ScenarioClosureStrategy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed> $scenario_ids
 * @property array<array-key, mixed>|null $comparison_criteria
 * @property array<array-key, mixed>|null $comparison_results
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Organizations $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison forOrganization($organizationId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereComparisonCriteria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereComparisonResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereScenarioIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioComparison withoutTrashed()
 */
	class ScenarioComparison extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int|null $created_by
 * @property string|null $prompt
 * @property array<array-key, mixed>|null $llm_response
 * @property \Illuminate\Support\Carbon|null $generated_at
 * @property float|null $confidence_score
 * @property string $status
 * @property array<array-key, mixed>|null $metadata
 * @property string|null $model_version
 * @property bool $redacted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $chunk_count
 * @property \Illuminate\Support\Carbon|null $compacted_at
 * @property int|null $compacted_by
 * @property string|null $compacted
 * @property int|null $last_validation_issue_id
 * @property int|null $scenario_id
 * @property int|null $agent_id
 * @property string|null $hybrid_composition_summary
 * @property string|null $raw_prompt
 * @property string|null $embedding Representación vectorial de la interacción con el LLM (prompt/contexto) para trazabilidad y búsqueda de conocimiento.
 * @property-read mixed $raw_prompt_decrypted
 * @property-read mixed $synthetization_index
 * @property-read \App\Models\Scenario|null $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereChunkCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereCompacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereCompactedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereCompactedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereHybridCompositionSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereLastValidationIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereLlmResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereModelVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration wherePrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereRawPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereRedacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioGeneration whereUpdatedAt($value)
 */
	class ScenarioGeneration extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $target_date
 * @property \Illuminate\Support\Carbon|null $actual_date
 * @property string $status
 * @property int $completion_percentage
 * @property array<array-key, mixed>|null $deliverables
 * @property array<array-key, mixed>|null $dependencies
 * @property int|null $owner_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $owner
 * @property-read \App\Models\Scenario $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereActualDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereCompletionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereDeliverables($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereDependencies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereTargetDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioMilestone withoutTrashed()
 */
	class ScenarioMilestone extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $role_id
 * @property string $role_change
 * @property string $impact_level
 * @property string $evolution_type
 * @property string|null $rationale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property numeric $fte
 * @property array<array-key, mixed>|null $embedding Representación vectorial del rol en el contexto del escenario (rationale y ajustes específicos).
 * @property int|null $human_leverage
 * @property string|null $archetype
 * @property array<array-key, mixed>|null $ai_suggestions
 * @property-read \App\Models\Roles $role
 * @property-read \App\Models\Scenario $scenario
 * @method static \Database\Factories\ScenarioRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereAiSuggestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereArchetype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereEvolutionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereFte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereHumanLeverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereImpactLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereRoleChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRole whereUpdatedAt($value)
 */
	class ScenarioRole extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $role_id
 * @property int $competency_id
 * @property int $required_level
 * @property bool $is_core
 * @property string $change_type
 * @property string|null $rationale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $competency_version_id
 * @property bool $is_referent
 * @property string|null $suggested_strategy
 * @property string|null $strategy_rationale
 * @property float|null $ia_confidence_score
 * @property array<array-key, mixed>|null $ia_action_plan
 * @property string $source Origen del mapping: agent | manual | auto
 * @property-read \App\Models\Competency $competency
 * @property-read mixed $metadata
 * @property-read \App\Models\ScenarioRole $role
 * @property-read \App\Models\Scenario $scenario
 * @property-read \App\Models\CompetencyVersion|null $version
 * @method static \Database\Factories\ScenarioRoleCompetencyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereCompetencyVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereIaActionPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereIaConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereIsCore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereIsReferent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereStrategyRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereSuggestedStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleCompetency whereUpdatedAt($value)
 */
	class ScenarioRoleCompetency extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $role_id
 * @property int $skill_id
 * @property int $required_level
 * @property string $change_type
 * @property bool $is_critical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $source
 * @property int|null $competency_id
 * @property int $current_level
 * @property int|null $competency_version_id
 * @property array<array-key, mixed>|null $metadata
 * @property int|null $created_by
 * @property-read \App\Models\ScenarioRole $role
 * @property-read \App\Models\Scenario $scenario
 * @property-read \App\Models\ScenarioRole $scenarioRole
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereCompetencyVersionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereCurrentLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereIsCritical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioRoleSkill whereUpdatedAt($value)
 */
	class ScenarioRoleSkill extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $skill_id
 * @property string $strategic_role
 * @property int $priority
 * @property string|null $rationale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Scenario $scenario
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereStrategicRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkill whereUpdatedAt($value)
 */
	class ScenarioSkill extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $skill_id
 * @property int|null $role_id
 * @property string|null $department
 * @property int $required_headcount
 * @property numeric $required_level
 * @property int $current_headcount
 * @property numeric|null $current_avg_level
 * @property string $priority
 * @property string|null $rationale
 * @property \Illuminate\Support\Carbon|null $target_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Roles|null $role
 * @property-read \App\Models\StrategicPlanningScenarios $scenario
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand editable()
 * @method static \Database\Factories\ScenarioSkillDemandFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand mandatoryFromParent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereCurrentAvgLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereCurrentHeadcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereRationale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereRequiredHeadcount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereTargetDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioSkillDemand withoutTrashed()
 */
	class ScenarioSkillDemand extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property string|null $from_decision_status
 * @property string|null $to_decision_status
 * @property string|null $from_execution_status
 * @property string|null $to_execution_status
 * @property int $changed_by
 * @property string|null $notes
 * @property array<array-key, mixed>|null $metadata Datos adicionales del evento
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\StrategicPlanningScenarios $scenario
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereChangedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereFromDecisionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereFromExecutionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereToDecisionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioStatusEvent whereToExecutionStatus($value)
 */
	class ScenarioStatusEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $scenario_type
 * @property string $industry
 * @property string|null $icon
 * @property array<array-key, mixed>|null $config
 * @property bool $is_active
 * @property int $usage_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkforceScenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate byIndustry($industry)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate byType($type)
 * @method static \Database\Factories\ScenarioTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereScenarioType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate whereUsageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioTemplate withoutTrashed()
 */
	class ScenarioTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string|null $description
 * @property string $complexity_level
 * @property string $lifecycle_status
 * @property int|null $parent_skill_id
 * @property string $category
 * @property bool $is_critical
 * @property string $scope_type
 * @property string|null $domain_tag Ej: Ventas, TI, Legal, Marketing
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $discovered_in_scenario_id
 * @property string|null $llm_id
 * @property string|null $embedding Representación vectorial de la habilidad para búsqueda semántica.
 * @property string $status
 * @property int|null $parent_id Referencia al skill padre (ej. Familia de Skills)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $People
 * @property-read int|null $people_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BarsLevel> $barsLevels
 * @property-read int|null $bars_levels_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Skill> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read int $employees_count
 * @property-read int|null $roles_count
 * @property-read \App\Models\Organization $organization
 * @property-read Skill|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PeopleRoleSkills> $peopleRoleSkills
 * @property-read int|null $people_role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillQuestionBank> $questions
 * @property-read int|null $questions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoleSkill> $roleSkills
 * @property-read int|null $role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Roles> $roles
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill byDomain($domainTag)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill domain()
 * @method static \Database\Factories\SkillFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill specific()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill transversal()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereComplexityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereDiscoveredInScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereDomainTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereIsCritical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereLifecycleStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereLlmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereParentSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereScopeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Skill whereUpdatedAt($value)
 */
	class Skill extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $level Nivel numérico (1-5)
 * @property string $name Nombre del nivel (ej: Básico, Intermedio)
 * @property string $description Descripción detallada del nivel
 * @property int $points Puntos asociados al nivel (sistema de scoring)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $display_label
 * @property-read string $full_description
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevelDefinition whereUpdatedAt($value)
 */
	class SkillLevelDefinition extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $skill_id
 * @property string|null $archetype Ej: Liderazgo, Técnico, Operativo
 * @property string|null $target_relationship self, boss, peer, subordinate, all
 * @property string $question
 * @property bool $is_global Si es una pregunta de validación general
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $level Nivel de competencia asociado (1-5)
 * @property string $question_type behavioral, situational, technical
 * @property-read \App\Models\Skill $skill
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereArchetype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereIsGlobal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereTargetRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillQuestionBank whereUpdatedAt($value)
 */
	class SkillQuestionBank extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $level
 * @property string $category
 * @property string $title
 * @property string $message
 * @property bool $is_read
 * @property array<array-key, mixed>|null $action_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Organizations $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereActionLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartAlert whereUpdatedAt($value)
 */
	class SmartAlert extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property array<array-key, mixed>|null $kpis
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int $horizon_months
 * @property int $fiscal_year
 * @property string $scope_type
 * @property string|null $scope_notes
 * @property string|null $strategic_context
 * @property string|null $budget_constraints
 * @property string|null $legal_constraints
 * @property string|null $labor_relations_constraints
 * @property string $status
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property array<array-key, mixed>|null $assumptions
 * @property int $owner_user_id
 * @property int|null $sponsor_user_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $version_group_id
 * @property int $version_number
 * @property bool $is_current_version
 * @property int|null $parent_id
 * @property string $f
 * @property int|null $scope_id
 * @property int $current_step
 * @property string $decision_status
 * @property string $execution_status
 * @property int|null $owner_id
 * @property string|null $last_simulated_at
 * @property string|null $scenario_type
 * @property int|null $time_horizon_weeks
 * @property array<array-key, mixed>|null $custom_config
 * @property int|null $estimated_budget
 * @property int|null $template_id
 * @property int|null $source_generation_id
 * @property string|null $accepted_prompt
 * @property bool $accepted_prompt_redacted
 * @property array<array-key, mixed>|null $accepted_prompt_metadata
 * @property array<array-key, mixed>|null $embedding Representación vectorial del escenario (nombre, descripción, assumptions) para comparación semántica.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CapabilityCompetency> $capabilityCompetencies
 * @property-read int|null $capability_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioClosureStrategy> $closureStrategies
 * @property-read int|null $closure_strategies_count
 * @property-read mixed $synthetization_index
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\Scenario|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRole> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioCapability> $scenarioCapabilities
 * @property-read int|null $scenario_capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRoleCompetency> $scenarioRoleCompetencies
 * @property-read int|null $scenario_role_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRoleSkill> $scenarioRoleSkills
 * @property-read int|null $scenario_role_skills_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioRole> $scenarioRoles
 * @property-read int|null $scenario_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioSkillDemand> $skillDemands
 * @property-read int|null $skill_demands_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioSkill> $skills
 * @property-read int|null $skills_count
 * @property-read \App\Models\ScenarioGeneration|null $sourceGeneration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScenarioStatusEvent> $statusEvents
 * @property-read int|null $status_events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TalentBlueprint> $talentBlueprints
 * @property-read int|null $talent_blueprints_count
 * @method static \Database\Factories\StrategicPlanningScenariosFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereAcceptedPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereAcceptedPromptMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereAcceptedPromptRedacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereAssumptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereBudgetConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereCurrentStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereCustomConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereDecisionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereEstimatedBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereExecutionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereF($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereFiscalYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereHorizonMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereIsCurrentVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereKpis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereLaborRelationsConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereLastSimulatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereLegalConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereOwnerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereScenarioType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereScopeNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereScopeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereSourceGenerationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereSponsorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereStrategicContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereTimeHorizonWeeks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereVersionGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategicPlanningScenarios whereVersionNumber($value)
 */
	class StrategicPlanningScenarios extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $strategy_id
 * @property string|null $action_taken
 * @property string|null $result
 * @property string|null $executed_by
 * @property string|null $executed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereActionTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereExecutedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereExecutedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereStrategyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StrategyExecution whereUpdatedAt($value)
 */
	class StrategyExecution extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $organization_id
 * @property int $reporter_id
 * @property int|null $assigned_to
 * @property string $title
 * @property string $description
 * @property string $type
 * @property string $priority
 * @property string $status
 * @property array<array-key, mixed>|null $context
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\Organization|null $organization
 * @property-read \App\Models\User $reporter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUpdatedAt($value)
 */
	class SupportTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $scenario_id
 * @property string $role_name
 * @property numeric $total_fte_required
 * @property int $human_leverage
 * @property int $synthetic_leverage
 * @property string $recommended_strategy
 * @property array<array-key, mixed> $agent_specs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $status
 * @property string|null $role_description
 * @property array<array-key, mixed>|null $key_competencies
 * @property string|null $embedding
 * @property mixed $estimated_fte
 * @property mixed $human_percentage
 * @property mixed $synthetic_percentage
 * @property-read \App\Models\Scenario|null $scenario
 * @method static \Database\Factories\TalentBlueprintFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereAgentSpecs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereHumanLeverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereKeyCompetencies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereRecommendedStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereRoleDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereRoleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereSyntheticLeverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereTotalFteRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentBlueprint whereUpdatedAt($value)
 */
	class TalentBlueprint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $role_id
 * @property string $scenario_id
 * @property string|null $strategy_type
 * @property int|null $target_fte
 * @property string|null $execution_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereExecutionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereStrategyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereTargetFte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TalentStrategy whereUpdatedAt($value)
 */
	class TalentStrategy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $organization_id
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Organization|null $organization
 * @property-read \App\Models\People|null $people
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $organization_id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property int|null $planning_horizon_months
 * @property string|null $scope_type
 * @property string|null $scope_notes
 * @property string|null $strategic_context
 * @property string|null $budget_constraints
 * @property string|null $legal_constraints
 * @property string|null $labor_relations_constraints
 * @property string|null $status
 * @property int|null $owner_user_id
 * @property int|null $sponsor_user_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Database\Factories\WorkforcePlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereBudgetConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereLaborRelationsConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereLegalConstraints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereOwnerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan wherePlanningHorizonMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereScopeNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereScopeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereSponsorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereStrategicContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforcePlan whereUpdatedBy($value)
 */
	class WorkforcePlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $organization_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $timeframe_start
 * @property string|null $timeframe_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereTimeframeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereTimeframeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkforceScenario whereUpdatedAt($value)
 */
	class WorkforceScenario extends \Eloquent {}
}

