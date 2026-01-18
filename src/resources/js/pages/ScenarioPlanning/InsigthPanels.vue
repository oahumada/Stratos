<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h2 class="text-2xl font-bold mb-2">Insights</h2>
      <p class="text-sm text-white/60">Strategic gaps identified · Potential for upskilling</p>
    </div>

    <!-- Métricas principales -->
    <div class="grid grid-cols-3 gap-3">
      <div class="glass-panel rounded-xl p-4 text-center">
        <div class="text-xs text-white/50 mb-1">Health</div>
        <div class="text-2xl font-bold text-cyan-400">72%</div>
      </div>
      <div class="glass-panel rounded-xl p-4 text-center">
        <div class="text-xs text-white/50 mb-1">Coverage</div>
        <div class="text-2xl font-bold text-teal-400">68%</div>
      </div>
      <div class="glass-panel rounded-xl p-4 text-center">
        <div class="text-xs text-white/50 mb-1">Risk</div>
        <div class="text-2xl font-bold text-red-400">3 roles</div>
      </div>
    </div>

    <!-- Top Action -->
    <div class="glass-panel-strong rounded-xl p-5">
      <div class="flex items-start justify-between mb-3">
        <div class="text-sm font-semibold">Top action</div>
        <div class="text-xs text-green-400">Impact est: +18% coverage</div>
      </div>
      
      <div class="space-y-2 text-sm text-white/70">
        <div class="flex items-center gap-2">
          <div class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
          <span>Build: Internal training program · 12 weeks</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
          <span>Buy: Hire 3 specialists · 8 weeks</span>
        </div>
      </div>

      <div class="mt-4 flex gap-2">
        <button class="flex-1 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-cyan-500/30 transition-all">
          Definir Scope
        </button>
        <button class="px-4 py-2 glass-panel rounded-lg text-sm hover:bg-white/10 transition-all">
          Ver simulación
        </button>
      </div>
    </div>

    <!-- Top 3 Recommended -->
    <div>
      <h3 class="text-sm font-semibold mb-3 text-white/80">Top 3 recommended</h3>
      <ol class="space-y-3 text-sm">
        <li class="flex gap-3">
          <span class="text-cyan-400 font-mono">1.</span>
          <div>
            <div class="font-medium">Build — Training program for Prompt Eng (12w)</div>
            <div class="text-xs text-white/50">Est. impact +22%</div>
          </div>
        </li>
        <li class="flex gap-3">
          <span class="text-cyan-400 font-mono">2.</span>
          <div>
            <div class="font-medium">Buy — Hire 2 Data Engineers</div>
            <div class="text-xs text-white/50">Est. impact +15%</div>
          </div>
        </li>
        <li class="flex gap-3">
          <span class="text-cyan-400 font-mono">3.</span>
          <div>
            <div class="font-medium">Borrow — External consultants (2w)</div>
            <div class="text-xs text-white/50">Quick triage</div>
          </div>
        </li>
      </ol>
    </div>
  </div>
</template>

<script setup>
defineProps(['scenario'])
</script>