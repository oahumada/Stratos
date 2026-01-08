<script setup lang="ts">
import { ref, computed } from 'vue'

interface Props {
  currentStep: number
  totalSteps?: number
  scenarioStatus?: string
  decisionStatus?: string
}

interface Step {
  id: number
  title: string
  description: string
  icon: string
  guardRules: string[]
  requiredFor: string[]
}

const props = withDefaults(defineProps<Props>(), {
  totalSteps: 7,
  scenarioStatus: 'active',
  decisionStatus: 'draft'
})

const emit = defineEmits<{
  (e: 'update:currentStep', step: number): void
  (e: 'stepClick', step: number): void
}>()

const steps: Step[] = [
  {
    id: 1,
    title: 'Definir Escenario',
    description: 'Nombre, alcance, horizonte temporal y objetivos estratégicos',
    icon: 'mdi-target',
    guardRules: ['Debe tener nombre', 'Alcance definido (org/dept/role)', 'Horizonte temporal > 0 semanas'],
    requiredFor: ['Todos los pasos posteriores dependen de esta base']
  },
  {
    id: 2,
    title: 'Estimar Demanda',
    description: 'Añadir roles y skills requeridos con niveles esperados',
    icon: 'mdi-chart-line',
    guardRules: ['Al menos 1 skill demand añadido', 'Niveles de competencia definidos'],
    requiredFor: ['Calcular brechas (paso 4)']
  },
  {
    id: 3,
    title: 'Calcular Supply',
    description: 'Evaluar disponibilidad actual de talento y capacidades',
    icon: 'mdi-account-group',
    guardRules: ['Demanda definida (paso 2)', 'Supply se calcula automáticamente'],
    requiredFor: ['Identificar gaps (paso 4)']
  },
  {
    id: 4,
    title: 'Identificar Gaps',
    description: 'Detectar brechas de headcount y nivel de competencia',
    icon: 'mdi-alert-circle',
    guardRules: ['Supply calculado', 'Al menos 1 gap detectado para continuar'],
    requiredFor: ['Generar estrategias (paso 5)']
  },
  {
    id: 5,
    title: 'Generar Estrategias',
    description: 'Recomendar acciones: Buy, Build, Borrow, Bridge, Bind, Bot',
    icon: 'mdi-lightbulb-on',
    guardRules: ['Gaps identificados', 'Preferencias de estrategia configuradas'],
    requiredFor: ['Aprobación y ejecución']
  },
  {
    id: 6,
    title: 'Aprobar & Ejecutar',
    description: 'Transicionar estado y comenzar implementación',
    icon: 'mdi-play-circle',
    guardRules: [
      'decision_status = approved',
      'Estrategias definidas',
      'execution_status = planned antes de iniciar'
    ],
    requiredFor: ['Completar escenario']
  },
  {
    id: 7,
    title: 'Revisar Resultados',
    description: 'Métricas, progreso y ajustes finales',
    icon: 'mdi-chart-box',
    guardRules: ['execution_status = completed', 'Métricas de cierre calculadas'],
    requiredFor: ['Crear nueva versión si se requieren cambios']
  }
]

const canNavigateToStep = (stepId: number): boolean => {
  if (stepId <= props.currentStep) return true
  
  // No permitir saltos de más de 1 paso adelante (metodología secuencial)
  if (stepId > props.currentStep + 1) return false
  
  // Paso 6 requiere decision_status = 'approved'
  if (stepId === 6 && props.decisionStatus !== 'approved') return false
  
  // Paso 7 requiere execution_status = 'completed'
  if (stepId === 7 && props.scenarioStatus !== 'completed') return false
  
  return true
}

const getStepColor = (stepId: number): string => {
  if (stepId < props.currentStep) return 'success'
  if (stepId === props.currentStep) return 'primary'
  return 'grey-lighten-1'
}

const getStepVariant = (stepId: number): 'elevated' | 'outlined' | 'tonal' => {
  if (stepId === props.currentStep) return 'elevated'
  if (stepId < props.currentStep) return 'tonal'
  return 'outlined'
}

const handleStepClick = (stepId: number) => {
  if (canNavigateToStep(stepId)) {
    emit('stepClick', stepId)
    emit('update:currentStep', stepId)
  }
}

const stepStatus = computed(() => (stepId: number) => {
  if (stepId < props.currentStep) return 'Completado'
  if (stepId === props.currentStep) return 'En progreso'
  return 'Pendiente'
})
</script>

<template>
  <div class="scenario-stepper">
    <v-stepper
      :model-value="currentStep"
      alt-labels
      flat
      hide-actions
      class="elevation-0 bg-transparent"
    >
      <v-stepper-header>
        <template v-for="(step, index) in steps" :key="step.id">
          <v-stepper-item
            :value="step.id"
            :complete="step.id < currentStep"
            :color="getStepColor(step.id)"
            :title="step.title"
            :subtitle="stepStatus(step.id)"
            :editable="canNavigateToStep(step.id)"
            @click="handleStepClick(step.id)"
          >
            <template #icon>
              <v-icon :icon="step.icon" />
            </template>
          </v-stepper-item>

          <v-divider
            v-if="index < steps.length - 1"
            :key="`divider-${step.id}`"
            :thickness="2"
            :color="step.id < currentStep ? 'success' : 'grey-lighten-2'"
          />
        </template>
      </v-stepper-header>

      <v-stepper-window :model-value="currentStep">
        <v-stepper-window-item
          v-for="step in steps"
          :key="step.id"
          :value="step.id"
        >
          <v-card flat class="mt-4">
            <v-card-title class="d-flex align-center">
              <v-icon :icon="step.icon" :color="getStepColor(step.id)" class="mr-2" />
              {{ step.title }}
            </v-card-title>
            <v-card-subtitle>{{ step.description }}</v-card-subtitle>
            
            <v-card-text>
              <v-alert
                v-if="step.guardRules.length > 0"
                type="info"
                variant="tonal"
                density="compact"
                class="mb-4"
              >
                <div class="text-subtitle-2 mb-2">Guardrails - Requisitos:</div>
                <ul class="pl-4">
                  <li v-for="(rule, idx) in step.guardRules" :key="idx" class="text-body-2">
                    {{ rule }}
                  </li>
                </ul>
              </v-alert>

              <v-alert
                v-if="step.requiredFor.length > 0"
                type="warning"
                variant="outlined"
                density="compact"
              >
                <div class="text-subtitle-2 mb-1">⚠️ Este paso es requerido para:</div>
                <ul class="pl-4 mb-0">
                  <li v-for="(req, idx) in step.requiredFor" :key="idx" class="text-body-2">
                    {{ req }}
                  </li>
                </ul>
              </v-alert>

              <!-- Slot para contenido específico del paso -->
              <div class="mt-4">
                <slot :name="`step-${step.id}`" :step="step" />
              </div>
            </v-card-text>

            <v-card-actions class="justify-space-between px-4 pb-4">
              <v-btn
                v-if="step.id > 1"
                variant="outlined"
                prepend-icon="mdi-arrow-left"
                @click="handleStepClick(step.id - 1)"
              >
                Anterior
              </v-btn>
              <v-spacer v-else />
              
              <v-btn
                v-if="step.id < totalSteps"
                color="primary"
                :disabled="!canNavigateToStep(step.id + 1)"
                append-icon="mdi-arrow-right"
                @click="handleStepClick(step.id + 1)"
              >
                Siguiente
              </v-btn>
              <v-btn
                v-else
                color="success"
                prepend-icon="mdi-check-circle"
                :disabled="decisionStatus !== 'approved' || scenarioStatus !== 'completed'"
              >
                Finalizar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-window-item>
      </v-stepper-window>
    </v-stepper>
  </div>
</template>

<style scoped>
.scenario-stepper {
  width: 100%;
}

:deep(.v-stepper-item) {
  cursor: pointer;
}

:deep(.v-stepper-item:not(.v-stepper-item--editable)) {
  cursor: not-allowed;
  opacity: 0.6;
}

:deep(.v-stepper-item__avatar) {
  transition: all 0.3s ease;
}

:deep(.v-stepper-item:hover:not(.v-stepper-item--disabled) .v-stepper-item__avatar) {
  transform: scale(1.1);
}
</style>
