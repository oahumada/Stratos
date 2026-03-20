<script setup lang="ts">
/**
 * StGlowDivider
 * A premium, high-tech animated divider with neon glow and scanning pulse.
 */
interface Props {
  color?: string;
  intensity?: string;
  duration?: number;
  showPhoton?: boolean;
}

withDefaults(defineProps<Props>(), {
  color: '#6366f1', // Indigo-500 default
  intensity: '35px',
  duration: 3,
  showPhoton: true,
});
</script>

<template>
  <div class="relative my-8 h-px w-full overflow-visible">
    <!-- Base Background -->
    <div class="absolute inset-0 bg-linear-to-r from-transparent via-white/10 to-transparent"></div>

    <!-- Animated Scan Pulse -->
    <div class="absolute inset-0 overflow-hidden rounded-full">
      <div 
        class="animate-scan h-full w-full bg-linear-to-r from-transparent via-indigo-400 to-transparent opacity-30"
        :style="{ animationDuration: `${duration}s` }"
      ></div>
    </div>

    <!-- Main Glow Layer -->
    <div 
      class="absolute inset-x-1/6 h-full bg-linear-to-r from-transparent via-indigo-500 to-transparent"
      :style="{ boxShadow: `0 0 ${intensity} ${color}` }"
    ></div>

    <div 
      v-if="showPhoton"
      class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center pointer-events-none"
    >
      <div class="h-1 w-1 rounded-full bg-white shadow-[0_0_15px_#fff,0_0_30px_rgba(99,102,241,1)]"></div>
      <div class="absolute h-6 w-6 rounded-full border border-indigo-500/30 animate-ping"></div>
    </div>
  </div>
</template>

<style scoped>
@keyframes scan {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.animate-scan {
  animation: scan 3s linear infinite;
}
</style>
