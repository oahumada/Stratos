<template>
  <g 
    class="competency-node-group"
    :class="{ 'is-selected': isSelected, 'is-hovered': isHovered }"
    :transform="`translate(${node.x ?? 0}, ${node.y ?? 0})`"
    @click="handleClick"
    @contextmenu="handleContextMenu"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
    style="cursor: pointer"
  >
    <!-- Main circle: competency node -->
    <circle
      r="24"
      :fill="isSelected ? 'url(#competencySelectedGrad)' : 'url(#competencyGrad)'"
      :stroke="isSelected ? 'rgba(150, 200, 255, 0.8)' : 'rgba(150, 150, 200, 0.5)'"
      stroke-width="1.5"
      :filter="isSelected ? 'url(#nodeGlowFocused)' : 'url(#nodeGlow)'"
      class="node-circle competency-circle"
    />

    <!-- Inner accent -->
    <circle
      r="18"
      fill="none"
      :stroke="isSelected ? 'rgba(200, 220, 255, 0.4)' : 'rgba(150, 150, 200, 0.2)'"
      stroke-width="0.5"
      class="node-inner-accent"
    />

    <!-- Icon/label inside -->
    <text
      x="0"
      y="3"
      text-anchor="middle"
      dominant-baseline="middle"
      font-size="13"
      :fill="isSelected ? '#fff' : 'rgba(150, 150, 200, 0.8)'"
      font-weight="600"
      class="node-label"
    >
      K
    </text>

    <!-- Tooltip -->
    <title>{{ node.name || node.raw?.name || 'Competencia' }}</title>
  </g>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  node: {
    id: number;
    x?: number;
    y?: number;
    name?: string;
    compId?: number;
    raw?: Record<string, any>;
  };
  isSelected?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isSelected: false,
});

defineEmits<{
  click: [event: MouseEvent];
  contextmenu: [event: MouseEvent];
}>();

const isHovered = ref(false);

const emit = defineEmits<{
  click: [event: MouseEvent];
  contextmenu: [event: MouseEvent];
}>();

function handleClick(event: MouseEvent) {
  event.stopPropagation();
  emit('click', event);
}

function handleContextMenu(event: MouseEvent) {
  event.preventDefault();
  event.stopPropagation();
  emit('contextmenu', event);
}
</script>

<style scoped>
.competency-node-group {
  transition: opacity 0.2s ease;
}

.node-circle {
  transition: r 0.2s ease, stroke 0.2s ease;
}

.competency-node-group:hover .node-circle {
  stroke-width: 2;
}

.competency-node-group.is-selected .node-circle {
  animation: select-pulse 0.3s ease-out;
}

@keyframes select-pulse {
  0% {
    r: 28;
    stroke-width: 2.5;
  }
  100% {
    r: 24;
    stroke-width: 1.5;
  }
}

.node-inner-accent {
  transition: stroke 0.2s ease;
}

.node-label {
  transition: fill 0.2s ease;
  font-weight: 700;
  user-select: none;
  pointer-events: none;
}
</style>
