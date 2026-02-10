<template>
  <g 
    class="capability-node-group"
    :class="{ 'is-focused': isFocused, 'is-visible': isVisible }"
    :transform="`translate(${node.x ?? 0}, ${node.y ?? 0})`"
    @click="$emit('click', $event)"
    @contextmenu="$emit('contextmenu', $event)"
    style="cursor: pointer"
  >
    <!-- Main circle: capability node -->
    <circle
      :r="radius"
      :fill="isFocused ? 'url(#capabilityFocusedGrad)' : 'url(#capabilityGrad)'"
      :stroke="isFocused ? 'rgba(255, 200, 100, 0.8)' : 'rgba(200, 200, 200, 0.4)'"
      stroke-width="2"
      :filter="isFocused ? 'url(#nodeGlowFocused)' : 'url(#nodeGlow)'"
      class="node-circle capability-circle"
    />

    <!-- Inner highlight for depth -->
    <circle
      :r="radius - 6"
      fill="none"
      :stroke="isFocused ? 'rgba(255, 255, 255, 0.3)' : 'rgba(255, 255, 255, 0.1)'"
      stroke-width="1"
      class="node-inner-highlight"
    />

    <!-- Icon/label inside circle -->
    <text
      x="0"
      y="4"
      text-anchor="middle"
      dominant-baseline="middle"
      font-size="16"
      :fill="isFocused ? '#fff' : 'rgba(200, 200, 200, 0.9)'"
      font-weight="600"
      class="node-label"
    >
      C
    </text>

    <!-- Node name label below (small text) -->
    <text
      x="0"
      :y="radius + 18"
      text-anchor="middle"
      font-size="11"
      fill="rgba(200, 200, 200, 0.7)"
      class="node-name"
    >
      {{ truncateLabel(node.name || 'Cap', 12) }}
    </text>

    <!-- Tooltip on hover -->
    <title>{{ node.name || 'Capacidad' }}</title>
  </g>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  node: {
    id: number;
    x?: number;
    y?: number;
    name?: string;
    visible?: boolean;
  };
  isFocused?: boolean;
  radius?: number;
}

const props = withDefaults(defineProps<Props>(), {
  isFocused: false,
  radius: 34,
});

defineEmits<{
  click: [event: MouseEvent];
  contextmenu: [event: MouseEvent];
}>();

const isVisible = computed(() => props.node.visible !== false);

function truncateLabel(label: string, maxLen: number): string {
  return label.length > maxLen ? label.substring(0, maxLen - 1) + '…' : label;
}
</script>

<style scoped>
.capability-node-group {
  transition: opacity 0.3s ease;
  opacity: 1;
}

.capability-node-group.is-visible {
  opacity: 1;
}

.capability-node-group:not(.is-visible) {
  opacity: 0.3;
  pointer-events: none;
}

.node-circle {
  transition: r 0.2s ease, stroke 0.2s ease;
}

.capability-node-group:hover .node-circle {
  stroke-width: 2.5;
}

.capability-node-group.is-focused .node-circle {
  animation: focus-pulse 0.4s ease-out;
}

@keyframes focus-pulse {
  0% {
    r: 38;
    stroke-width: 3;
  }
  100% {
    r: 34;
    stroke-width: 2;
  }
}

.node-inner-highlight {
  transition: stroke 0.2s ease;
}

.node-label {
  transition: fill 0.2s ease, font-size 0.2s ease;
  font-weight: 700;
}

.node-name {
  pointer-events: none;
  user-select: none;
}
</style>
