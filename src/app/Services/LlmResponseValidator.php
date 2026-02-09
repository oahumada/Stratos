<?php

namespace App\Services;

class LlmResponseValidator
{
    /**
     * Minimal validation of the LLM response structure.
     * Returns array: ['valid' => bool, 'errors' => array]
     */
    public function validate(array $llmResponse): array
    {
        $errors = [];
        $strict = (bool) config('features.validate_llm_response_strict', false);

        // scenario_metadata must exist and include a name
        if (! isset($llmResponse['scenario_metadata']) || ! is_array($llmResponse['scenario_metadata'])) {
            $errors[] = ['path' => 'scenario_metadata', 'message' => 'Missing or invalid object'];
        } else {
            if (empty(trim((string) ($llmResponse['scenario_metadata']['name'] ?? '')))) {
                $errors[] = ['path' => 'scenario_metadata.name', 'message' => 'Name is required'];
            }
        }

        // Capabilities: optional but when present must be an array of objects
        if (array_key_exists('capabilities', $llmResponse)) {
            if (! is_array($llmResponse['capabilities'])) {
                $errors[] = ['path' => 'capabilities', 'message' => 'Must be an array'];
            } else {
                // enforce max counts when present
                $maxCaps = (int) config('features.validate_llm_response_max_capabilities', 10);
                $maxComps = (int) config('features.validate_llm_response_max_competencies', 10);
                $maxSkills = (int) config('features.validate_llm_response_max_skills', 10);
                if (count($llmResponse['capabilities']) > $maxCaps) {
                    $errors[] = ['path' => 'capabilities', 'message' => "Too many capabilities (max {$maxCaps})"];
                }
                foreach ($llmResponse['capabilities'] as $i => $cap) {
                    $base = "capabilities[{$i}]";
                    if (! is_array($cap)) {
                        $errors[] = ['path' => $base, 'message' => 'Must be an object'];
                        continue;
                    }
                    if (empty(trim((string) ($cap['name'] ?? '')))) {
                        $errors[] = ['path' => $base . '.name', 'message' => 'Name is required'];
                    }
                    // competencies: if present must be array with name per competency
                    // in strict mode, competencies must be present and non-empty
                    if (array_key_exists('competencies', $cap)) {
                        if (! is_array($cap['competencies'])) {
                            $errors[] = ['path' => $base . '.competencies', 'message' => 'Must be an array'];
                        } else {
                            if ($strict && count($cap['competencies']) === 0) {
                                $errors[] = ['path' => $base . '.competencies', 'message' => 'At least one competency is required in strict mode'];
                            }
                            if (count($cap['competencies']) > $maxComps) {
                                $errors[] = ['path' => $base . '.competencies', 'message' => "Too many competencies (max {$maxComps})"];
                            }
                            foreach ($cap['competencies'] as $j => $comp) {
                                $cbase = $base . ".competencies[{$j}]";
                                if (! is_array($comp)) {
                                    $errors[] = ['path' => $cbase, 'message' => 'Must be an object'];
                                    continue;
                                }
                                if (empty(trim((string) ($comp['name'] ?? '')))) {
                                    $errors[] = ['path' => $cbase . '.name', 'message' => 'Name is required'];
                                }
                                // skills optional but if present must be array with name
                                if (array_key_exists('skills', $comp)) {
                                    if (! is_array($comp['skills'])) {
                                        $errors[] = ['path' => $cbase . '.skills', 'message' => 'Must be an array'];
                                    } else {
                                        if ($strict && count($comp['skills']) === 0) {
                                            $errors[] = ['path' => $cbase . '.skills', 'message' => 'At least one skill is required in strict mode'];
                                        }
                                        if (count($comp['skills']) > $maxSkills) {
                                            $errors[] = ['path' => $cbase . '.skills', 'message' => "Too many skills (max {$maxSkills})"];
                                        }
                                        foreach ($comp['skills'] as $k => $s) {
                                            $sbase = $cbase . ".skills[{$k}]";
                                            // Accept skill as string (name) or object with name
                                            if (is_string($s)) {
                                                if (empty(trim($s))) {
                                                    $errors[] = ['path' => $sbase, 'message' => 'Name is required'];
                                                }
                                                continue;
                                            }
                                            if (! is_array($s)) {
                                                $errors[] = ['path' => $sbase, 'message' => 'Must be an object or string'];
                                                continue;
                                            }
                                            if (empty(trim((string) ($s['name'] ?? '')))) {
                                                $errors[] = ['path' => $sbase . '.name', 'message' => 'Name is required'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Roles: optional but when present must be an array of objects with name and competencies
        if (array_key_exists('roles', $llmResponse)) {
            if (! is_array($llmResponse['roles'])) {
                $errors[] = ['path' => 'roles', 'message' => 'Must be an array'];
            } else {
                $maxRoles = (int) config('features.validate_llm_response_max_roles', 20);
                if (count($llmResponse['roles']) > $maxRoles) {
                    $errors[] = ['path' => 'roles', 'message' => "Too many roles (max {$maxRoles})"];
                }
                foreach ($llmResponse['roles'] as $i => $role) {
                    $rbase = "roles[{$i}]";
                    if (! is_array($role)) {
                        $errors[] = ['path' => $rbase, 'message' => 'Must be an object'];
                        continue;
                    }
                    if (empty(trim((string) ($role['name'] ?? '')))) {
                        $errors[] = ['path' => $rbase . '.name', 'message' => 'Name is required'];
                    }
                    if (array_key_exists('competencies', $role)) {
                        if (! is_array($role['competencies'])) {
                            $errors[] = ['path' => $rbase . '.competencies', 'message' => 'Must be an array'];
                        } else {
                            foreach ($role['competencies'] as $j => $rc) {
                                $rcbase = $rbase . ".competencies[{$j}]";
                                if (is_string($rc)) {
                                    if (empty(trim($rc))) {
                                        $errors[] = ['path' => $rcbase, 'message' => 'Name is required'];
                                    }
                                    continue;
                                }
                                if (! is_array($rc)) {
                                    $errors[] = ['path' => $rcbase, 'message' => 'Must be an object or string'];
                                    continue;
                                }
                                if (empty(trim((string) ($rc['name'] ?? '')))) {
                                    $errors[] = ['path' => $rcbase . '.name', 'message' => 'Name is required'];
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}

