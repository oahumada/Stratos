<?php

/**
 * Verificación - Configuración de Despliegue de Phases
 *
 * Este archivo almacena la configuración seleccionada para el despliegue
 * de las 4 phases del sistema de verificación.
 *
 * Modos disponibles:
 * - auto_transitions: Cambios automáticos basados en métricas
 * - hybrid: Recolecta automática, decisiones manuales
 * - monitoring_only: Solo recolecta datos, sin automatización
 */

return [
    // Modo de despliegue actual
    'deployment_mode' => env('VERIFICATION_DEPLOYMENT_MODE', 'hybrid'),

    // Timestamp de última configuración
    'configured_at' => null,

    // ============================================================================
    // OPCIÓN 1: AUTO-TRANSITIONS
    // El sistema cambia automáticamente entre phases según métricas
    // ============================================================================
    'auto_transitions' => [
        // Cambiar a Phase 2 si error_rate está por debajo de este porcentaje
        'error_rate_threshold_phase2' => 15,

        // Cambiar a Phase 4 si retry_rate está por encima de este porcentaje
        'retry_rate_threshold_phase4' => 10,

        // Verificar criterios cada X minutos
        'check_interval_minutes' => 60,

        // Ventana de tiempo para análisis de datos (en horas)
        'data_window_hours' => 24,

        // Habilitar notificaciones cuando se hacen cambios automáticos
        'enable_notifications' => true,

        // Canal para notificaciones (log, slack, email)
        'notification_channel' => 'log',
    ],

    // ============================================================================
    // OPCIÓN 3: HYBRID
    // Recolecta automáticamente, pero tú decides cuándo cambiar
    // ============================================================================
    'hybrid' => [
        // Recolectar métricas cada X minutos
        'metrics_collection_interval' => 60,

        // Alertar si error_rate sube más del X% en 1 hora
        'alert_threshold_percent' => 20,

        // Mostrar sugerencias cuando sea momento de cambiar phase
        'enable_suggestions' => true,

        // Habilitar dashboard web de métricas
        'enable_web_dashboard' => true,

        // Dónde mostrar sugerencias (cli, web, both)
        'suggestion_channel' => 'both',
    ],

    // ============================================================================
    // OPCIÓN 2: MONITORING ONLY
    // Solo recolecta datos, sin automatización
    // ============================================================================
    'monitoring_only' => [
        // Recolectar métricas cada X minutos
        'metrics_collection_interval' => 60,

        // Cuántos días retener métricas antes de borrar
        'metrics_retention_days' => 30,
    ],

    // ============================================================================
    // CONFIGURACIÓN COMPARTIDA
    // ============================================================================

    // Tabla de eventos a monitorear
    'event_table' => 'event_store',

    // Eventos de verificación a trackear
    'tracked_events' => [
        'verification.violation_detected',
        'verification.accepted',
        'verification.rejected',
        'verification.recovered',
    ],

    // Habilitar logging detallado
    'debug_logging' => env('APP_DEBUG', false),

    // Archivo de log para despliegues
    'deployment_log_path' => storage_path('logs/verification-phase-deployment.log'),
];
