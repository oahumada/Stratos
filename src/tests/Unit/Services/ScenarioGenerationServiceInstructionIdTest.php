<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ScenarioGenerationService;
use App\Models\PromptInstruction;
use App\Models\User;
use App\Models\Organizations;

class ScenarioGenerationServiceInstructionIdTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_uses_prompt_instruction_by_id_when_provided_and_matches_language()
    {
        $org = Organizations::create(['name' => 'ACME', 'industry' => 'Manufactura', 'size' => '250']);

        $user = User::factory()->create(['organization_id' => $org->id]);

        // create a default and a specific editable instruction
        $default = PromptInstruction::create([
            'language' => 'es',
            'content' => 'DEFAULT CONTENT',
            'editable' => false,
            'author_name' => 'system',
        ]);

        $selected = PromptInstruction::create([
            'language' => 'es',
            'content' => 'SELECTED BY ID CONTENT',
            'editable' => true,
            'author_name' => 'tester',
        ]);

        $svc = new ScenarioGenerationService();

        $payload = ['company_name' => 'ACME S.A.'];

        $result = $svc->composePromptWithInstruction($payload, $user, $org, 'es', $selected->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('instruction', $result);
        $this->assertEquals('SELECTED BY ID CONTENT', $result['instruction']['content']);
        $this->assertEquals('db_id', $result['instruction']['source']);
    }

    /** @test */
    public function client_provided_instruction_overrides_db_and_file()
    {
        $org = Organizations::create(['name' => 'ACME', 'industry' => 'Manufactura', 'size' => '250']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $svc = new ScenarioGenerationService();

        $payload = ['company_name' => 'ACME S.A.', 'instruction' => 'CLIENT INSTRUCTION TEXT'];

        $result = $svc->composePromptWithInstruction($payload, $user, $org, 'es', null);

        $this->assertEquals('CLIENT INSTRUCTION TEXT', $result['instruction']['content']);
        $this->assertEquals('client', $result['instruction']['source']);
    }

    /** @test */
    public function instruction_id_with_mismatched_language_is_ignored_and_fallback_used()
    {
        $org = Organizations::create(['name' => 'ACME', 'industry' => 'Manufactura', 'size' => '250']);

        $user = User::factory()->create(['organization_id' => $org->id]);

        // create an 'es' default and an 'en' instruction whose id we'll pass
        $defaultEs = PromptInstruction::create([
            'language' => 'es',
            'content' => 'ES DEFAULT',
            'editable' => false,
            'author_name' => 'system',
        ]);

        $enInstr = PromptInstruction::create([
            'language' => 'en',
            'content' => 'EN SELECTED',
            'editable' => true,
            'author_name' => 'tester',
        ]);

        $svc = new ScenarioGenerationService();

        $payload = ['company_name' => 'ACME S.A.'];

        // pass the EN instruction id but request language ES
        $result = $svc->composePromptWithInstruction($payload, $user, $org, 'es', $enInstr->id);

        $this->assertEquals('ES DEFAULT', $result['instruction']['content']);
        $this->assertEquals('db', $result['instruction']['source']);
    }
}
