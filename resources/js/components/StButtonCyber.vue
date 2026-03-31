<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'danger' | 'success' | 'warning' | 'info';
    size?: 'sm' | 'md' | 'lg' | 'xl';
    loading?: boolean;
    disabled?: boolean;
    icon?: any;
    tag?: string; // Small technical tag in the corner
    cyberId?: string; // Small ID shown in the button
    glass?: boolean; // Enable glassmorphism hybrid mode
    type?: 'button' | 'submit' | 'reset';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    loading: false,
    disabled: false,
    tag: 'AUTH',
    cyberId: 'ST-26',
    glass: false,
    type: 'submit',
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'danger':
            return 'cyber-danger';
        case 'success':
            return 'cyber-success';
        case 'warning':
            return 'cyber-warning';
        case 'info':
            return 'cyber-info';
        default:
            return 'cyber-primary';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-8 px-4 text-[10px]';
        case 'lg':
            return 'h-14 px-8 text-sm';
        case 'xl':
            return 'h-16 px-12 text-base';
        default:
            return 'h-11 px-6 text-xs';
    }
});
</script>

<template>
    <button
        :type="type"
        class="st-button-cyber"
        :class="[
            variantClasses,
            sizeClasses,
            { 'is-loading': loading, 'is-glass': glass },
        ]"
        :disabled="disabled || loading"
    >
        <!-- Glitch layers -->
        <span class="glitch-layer shadow"></span>
        <span class="glitch-layer main"></span>

        <!-- Technical elements -->
        <span class="cyber-tag">{{ tag }}</span>
        <span class="cyber-id">[{{ cyberId }}]</span>
        <span class="scanline"></span>

        <div class="content">
            <template v-if="loading">
                <v-progress-circular
                    indeterminate
                    size="16"
                    width="2"
                    class="mr-2"
                />
                Sincronizando Neural...
            </template>
            <template v-else>
                <component
                    :is="icon"
                    v-if="icon"
                    :size="size === 'xl' ? 24 : 18"
                    class="icon-glow mr-2"
                />
                <span class="label"><slot /></span>
            </template>
        </div>

        <!-- Corner Glitch Shapes -->
        <span class="corner-bit corner-tl"></span>
        <span class="corner-bit corner-br"></span>
    </button>
</template>

<style scoped>
.st-button-cyber {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: white;
    cursor: pointer;
    overflow: hidden;
    border: none;
    clip-path: polygon(10% 0%, 100% 0%, 100% 70%, 90% 100%, 0% 100%, 0% 30%);
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    font-family: 'Outfit', sans-serif;
    user-select: none;
    outline: none !important;
}

.st-button-cyber:disabled {
    cursor: wait;
    opacity: 0.7;
}

.st-button-cyber::after {
    content: '';
    position: absolute;
    top: 0;
    left: -150%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transform: skewX(-25deg);
    transition: 0.75s cubic-bezier(0.19, 1, 0.22, 1);
    z-index: 7;
    pointer-events: none;
}

.st-button-cyber:hover::after {
    left: 150%;
}

.content {
    position: relative;
    z-index: 5;
    display: flex;
    align-items: center;
}

.cyber-tag {
    position: absolute;
    top: 2px;
    right: 12%;
    font-size: 7px;
    font-weight: 900;
    opacity: 0.4;
    z-index: 6;
    letter-spacing: 0.05em;
}

.cyber-id {
    position: absolute;
    bottom: 2px;
    left: 8%;
    font-size: 7px;
    font-weight: 900;
    opacity: 0.4;
    z-index: 6;
}

.corner-bit {
    position: absolute;
    background: currentColor;
    opacity: 0.8;
    pointer-events: none;
}

.corner-tl {
    top: 0;
    left: 0;
    width: 4px;
    height: 12px;
    clip-path: polygon(0 0, 100% 0, 0 100%);
}
.corner-br {
    bottom: 0;
    right: 0;
    width: 4px;
    height: 12px;
    clip-path: polygon(100% 100%, 0 100%, 100% 0);
}

/* --- Variants --- */
.cyber-primary {
    background: rgba(99, 102, 241, 0.15);
    border-left: 2px solid #818cf8;
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.2);
    color: #c7d2fe;
}
.cyber-primary:hover {
    background: rgba(99, 102, 241, 0.3);
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
    color: white;
}

.cyber-success {
    background: rgba(16, 185, 129, 0.15);
    border-left: 2px solid #10b981;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    color: #a7f3d0;
}
.cyber-success:hover {
    background: rgba(16, 185, 129, 0.3);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
    color: white;
}

.cyber-danger {
    background: rgba(244, 63, 94, 0.15);
    border-left: 2px solid #f43f5e;
    box-shadow: 0 0 10px rgba(244, 63, 94, 0.2);
    color: #fecdd3;
}
.cyber-danger:hover {
    background: rgba(244, 63, 94, 0.3);
    box-shadow: 0 0 20px rgba(244, 63, 94, 0.5);
    color: white;
}

/* --- Glass Hybrid --- */
.st-button-cyber.is-glass {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    border-left-width: 1px;
    clip-path: polygon(
        5% 0%,
        100% 0%,
        100% 80%,
        95% 100%,
        0% 100%,
        0% 20%
    ); /* Softer angles */
    box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.02);
}

.st-button-cyber.is-glass.cyber-primary {
    border-left-color: rgba(99, 102, 241, 0.5);
}
.st-button-cyber.is-glass.cyber-success {
    border-left-color: rgba(16, 185, 129, 0.5);
}
.st-button-cyber.is-glass.cyber-danger {
    border-left-color: rgba(244, 63, 94, 0.5);
}

.st-button-cyber.is-glass:hover {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
}

/* --- Effects --- */
.scanline {
    position: absolute;
    top: -100%;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        to bottom,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    z-index: 4;
    animation: scan 2.5s infinite linear;
    pointer-events: none;
}

@keyframes scan {
    0% {
        top: -100%;
    }
    100% {
        top: 100%;
    }
}

.icon-glow {
    filter: drop-shadow(0 0 3px currentColor);
}

.label {
    text-shadow: 0 0 8px currentColor;
}

.st-button-cyber:hover .label {
    animation: glitch 0.3s infinite;
}

@keyframes glitch {
    0% {
        transform: translate(0);
        opacity: 1;
    }
    20% {
        transform: translate(-2px, 1px);
        opacity: 0.8;
    }
    40% {
        transform: translate(2px, -1px);
        opacity: 0.8;
    }
    60% {
        transform: translate(-1px, 0);
        opacity: 0.9;
    }
    100% {
        transform: translate(0);
        opacity: 1;
    }
}
</style>
