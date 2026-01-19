<template>
    <div class="relative h-full w-full">
        <!-- Conexiones (líneas entre nodos) -->
        <svg class="pointer-events-none absolute inset-0 h-full w-full">
            <defs>
                <linearGradient
                    id="lineGradient"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="0%"
                >
                    <stop
                        offset="0%"
                        style="
                            stop-color: rgba(6, 182, 212, 0.2);
                            stop-opacity: 1;
                        "
                    />
                    <stop
                        offset="100%"
                        style="
                            stop-color: rgba(139, 92, 246, 0.2);
                            stop-opacity: 1;
                        "
                    />
                </linearGradient>
            </defs>

            <!-- Ejemplo de conexiones -->
            <line
                x1="30%"
                y1="60%"
                x2="50%"
                y2="40%"
                stroke="url(#lineGradient)"
                stroke-width="1"
            />
            <line
                x1="50%"
                y1="40%"
                x2="70%"
                y2="50%"
                stroke="url(#lineGradient)"
                stroke-width="1"
            />
            <line
                x1="30%"
                y1="60%"
                x2="20%"
                y2="35%"
                stroke="url(#lineGradient)"
                stroke-width="1"
            />
        </svg>

        <!-- Nodos de Capacidades -->
        <CapabilityNode
            v-for="cap in capabilities"
            :key="cap.id"
            :capability="cap"
            :style="{ left: `${cap.x}%`, top: `${cap.y}%` }"
        />
    </div>
</template>

<script setup>
import CapabilityNode from './CapabilityNode.vue';

defineProps(['capabilities']);
</script>
