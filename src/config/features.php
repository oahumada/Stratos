<?php

return [
    // Toggle automatic import of LLM-generated capabilities/competencies/skills
    // Can be set via env: IMPORT_GENERATION=true
    'import_generation' => (bool) env('IMPORT_GENERATION', true),
    // Toggle JSON Schema validation of LLM responses
    'validate_llm_response' => (bool) env('VALIDATE_LLM_RESPONSE', true),
    // When true, validation requires nested structure: capabilities -> competencies -> skills
    'validate_llm_response_strict' => (bool) env('VALIDATE_LLM_RESPONSE_STRICT', true),
    // Limits for generated items to avoid overly large responses
    'validate_llm_response_max_capabilities' => (int) env('LLM_MAX_CAPABILITIES', 10),
    'validate_llm_response_max_competencies' => (int) env('LLM_MAX_COMPETENCIES', 10),
    'validate_llm_response_max_skills' => (int) env('LLM_MAX_SKILLS', 10),
];
