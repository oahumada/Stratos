<script setup lang="ts">
import { computed } from 'vue';

interface SkillLevel {
  level: number;
  name: string;
  description: string;
  points: number;
  display_label: string;
}

interface Props {
  level: number;
  skillLevels: SkillLevel[];
  color?: string;
  size?: 'x-small' | 'small' | 'default' | 'large' | 'x-large';
  showTooltip?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  color: 'primary',
  size: 'x-small',
  showTooltip: true
});

const levelInfo = computed(() => {
  return props.skillLevels.find(l => l.level === props.level);
});

const displayText = computed(() => {
  return levelInfo.value?.display_label || `Nivel ${props.level}`;
});

// tooltipText removed — template uses `levelInfo` directly
</script>

<template>
  <v-tooltip v-if="showTooltip && levelInfo" location="top">
    <template #activator="{ props: tooltipProps }">
      <v-chip 
        v-bind="tooltipProps"
        :color="color" 
        :size="size"
      >
        {{ displayText }}
      </v-chip>
    </template>
    <div class="text-caption" style="max-width: 300px;">
      <div class="font-weight-bold mb-1">{{ levelInfo.name }}</div>
      <div>{{ levelInfo.description }}</div>
      <div class="mt-1 text-secondary">Puntos: {{ levelInfo.points }}</div>
    </div>
  </v-tooltip>
  <v-chip v-else :color="color" :size="size">
    {{ displayText }}
  </v-chip>
</template>
