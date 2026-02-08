<?php

return [
    // Toggle automatic import of LLM-generated capabilities/competencies/skills
    // Can be set via env: IMPORT_GENERATION=true
    'import_generation' => (bool) env('IMPORT_GENERATION', true),
    // Toggle JSON Schema validation of LLM responses
    'validate_llm_response' => (bool) env('VALIDATE_LLM_RESPONSE', true),
];
