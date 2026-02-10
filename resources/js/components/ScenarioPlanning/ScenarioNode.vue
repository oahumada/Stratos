<template>
  <g 
    class="scenario-node-group"
    :transform="`translate(${node.x ?? 0}, ${node.y ?? 0})`"
    @click="$emit('click', $event)"
    @contextmenu="$emit('contextmenu', $event)"
    style="cursor: pointer"
  >
    <!-- Main circle: scenario node -->
    <circle
      r="28"
      fill="url(#scenarioGrad)"
      stroke="rgba(255, 255, 255, 0.3)"
      stroke-width="2"
      filter="url(#nodeGlow)"
      class="node-circle scenario-circle"
    />

    <!-- Inner glow circle -->
    <circle
      r="24"
      fill="none"
      stroke="rgba(255, 255, 255, 0.15)"
      stroke-width="1"
      class="node-inner-glow"
    />

    <!-- Icon or symbol inside -->
    <text
      x="0"
      y="6"
      text-anchor="middle"
      dominant-baseline="middle"
      font-size="20"
      fill="rgba(255, 255, 255, 0.9)"
      font-weight="700"
      class="node-label"
    >
      S
    </text>

    <!-- Tooltip on hover -->
    <title>{{ node.name || 'Escenario' }}</title>
  </g>
</template>

<script setup lang="ts">
interface Props {
  node: {
    id: number;
    x?: number;
    y?: number;
    name?: string;
  };
}

defineProps<Props>();
defineEmits<{
  click: [event: MouseEvent];
  contextmenu: [event: MouseEvent];
}>();
</script>

<style scoped>
.scenario-node-group {
  transition: filter 0.2s ease;
}

.scenario-node-group:hover {
  filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.3));
}

.node-circle {
  transition: r 0.2s ease;
}

.node-inner-glow {
  animation: pulse-subtle 3s infinite ease-in-out;
}

@keyframes pulse-subtle {
  0%, 100% {
    stroke-width: 1;
    opacity: 0.15;
  }
  50% {
    stroke-width: 1.5;
    opacity: 0.25;
  }
}
</style>
