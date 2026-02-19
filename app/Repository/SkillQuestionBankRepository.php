<?php

namespace App\Repository;

use App\Models\SkillQuestionBank;

class SkillQuestionBankRepository extends Repository
{
    public function __construct()
    {
        $this->model = new SkillQuestionBank();
    }
}
