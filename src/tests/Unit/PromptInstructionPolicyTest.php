<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Policies\PromptInstructionPolicy;
use App\Models\User;
use App\Models\PromptInstruction;

class PromptInstructionPolicyTest extends TestCase
{
    public function test_admin_and_operator_can_create_update_and_restore()
    {
        $policy = new PromptInstructionPolicy();

        $admin = new User();
        $admin->role = 'admin';

        $operator = new User();
        $operator->role = 'operator';

        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->create($operator));

        $pi = new PromptInstruction();
        $this->assertTrue($policy->update($admin, $pi));
        $this->assertTrue($policy->update($operator, $pi));

        $this->assertTrue($policy->restore($admin));
        $this->assertTrue($policy->restore($operator));
    }

    public function test_regular_user_cannot_create_update_or_restore()
    {
        $policy = new PromptInstructionPolicy();

        $user = new User();
        $user->role = 'user';

        $pi = new PromptInstruction();

        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->update($user, $pi));
        $this->assertFalse($policy->restore($user));
    }

    public function test_any_authenticated_user_can_view()
    {
        $policy = new PromptInstructionPolicy();
        $user = new User();
        $this->assertTrue($policy->view($user, new PromptInstruction()));
        $this->assertTrue($policy->viewAny($user));
    }
}
