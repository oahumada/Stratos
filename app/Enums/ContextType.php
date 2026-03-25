<?php

namespace App\Enums;

enum ContextType: string
{
    case NONE = 'none';                           // Ad-hoc conversation
    case LEARNING_ASSIGNMENT = 'learning_assignment';
    case PERFORMANCE_REVIEW = 'performance_review';
    case INCIDENT = 'incident';
    case SURVEY = 'survey';
    case ONBOARDING = 'onboarding';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Sin contexto',
            self::LEARNING_ASSIGNMENT => 'Asignación de aprendizaje',
            self::PERFORMANCE_REVIEW => 'Revisión de desempeño',
            self::INCIDENT => 'Incidente',
            self::SURVEY => 'Encuesta',
            self::ONBOARDING => 'Incorporación',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NONE => 'mdi-chat',
            self::LEARNING_ASSIGNMENT => 'mdi-school',
            self::PERFORMANCE_REVIEW => 'mdi-chart-line',
            self::INCIDENT => 'mdi-alert',
            self::SURVEY => 'mdi-clipboard-list',
            self::ONBOARDING => 'mdi-plus-circle',
        };
    }
}
