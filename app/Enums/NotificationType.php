<?php

namespace App\Enums;

enum NotificationType: string
{
    case PhaseTransition = 'phase_transition';
    case AlertThreshold = 'alert_threshold';
    case ViolationDetected = 'violation_detected';
    case ConfigChange = 'config_change';
}
