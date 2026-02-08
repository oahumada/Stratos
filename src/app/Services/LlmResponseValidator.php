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
                    if (array_key_exists('competencies', $cap)) {
                        if (! is_array($cap['competencies'])) {
                            $errors[] = ['path' => $base . '.competencies', 'message' => 'Must be an array'];
                        } else {
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
                                        foreach ($comp['skills'] as $k => $s) {
                                            $sbase = $cbase . ".skills[{$k}]";
                                            if (! is_array($s)) {
                                                $errors[] = ['path' => $sbase, 'message' => 'Must be an object'];
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

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}
