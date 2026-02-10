<template>
  <g 
    class="skill-node-group"
    :class="{ 'is-selected': isSelected }"
    :transform="`translate(${node.x ?? 0}, ${node.y ?? 0})`"
    @click="handleClick"
    @contextmenu="handleContextMenu"
    style="cursor: pointer"
  >
    <!-- Main circle: skill node (smaller than competency) -->
    <circle
      r="14"
      :fill="isSelected ? 'url(#skillSelectedGrad)' : 'url(#skillGrad)'"
      :stroke="isSelected ? 'rgba(100, 255, 150, 0.8)' : 'rgba(100, 200, 150, 0.5)'"
      stroke-width="1"
      filter="url(#nodeGlow)"
      class="node-circle skill-circle"
    />

    <!-- Inner dot for visual emphasis -->
    <circle
      r="8"
      fill="none"
      :stroke="isSelected ? 'rgba(150, 255, 180, 0.4)' : 'rgba(100, 200, 150, 0.2)'"
      stroke-width="0.5"
      class="node-inner-dot"
    />

    <!-- Icon/label (very small) -->
    <text
      x="0"
      y="2"
      text-anchor="middle"
      dominant-baseline="middle"
      font-size="9"
      :fill="isSelected ? '#fff' : 'rgba(100, 200, 150, 0.9)'"
      font-weight="600"
      class="node-label"
    >
      H
    </text>

    <!-- Tooltip -->
    <title>{{ node.name || node.raw?.name || 'Habilidad' }}</title>
  </g>
</template>

<script setup lang="ts">
interface Props {
  node: {
    id: number;
    x?: number;
    y?: number;
    name?: string;
    skillId?: number;
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

function handleClick(event: MouseEvent) {
  event.stopPropagation();
  const emit = defineEmits<{
    click: [event: MouseEvent];
    contextmenu: [event: MouseEvent];
  }>();
  emit('click', event);
}

function handleContextMenu(event: MouseEvent) {
  event.preventDefault();
  event.stopPropagation();
  const emit = defineEmits<{
    click: [event: MouseEvent];
    contextmenu: [event: MouseEvent];
  }>();
  emit('contextmenu', event);
}
</script>

<style scoped>
.skill-node-group {
  transition: opacity 0.2s ease;
}

.node-circle {
  transition: r 0.15s ease, stroke-width 0.15s ease;
}

.skill-node-group:hover .node-circle {
  stroke-width: 1.3;
}

.skill-node-group.is-selected .node-circle {
  animation: skill-select 0.25s ease-out;
}

@keyframes skill-select {
  0% {
    r: 17;
    stroke-width: 1.5;
  }
  100% {
    r: 14;
    stroke-width: 1;
  }
}

.node-inner-dot {
  transition: stroke 0.15s ease;
}

.node-label {
  transition: fill 0.15s ease;
  font-weight: 700;
  user-select: none;
  pointer-events: none;
}
</style>
