<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 64 64"
        :class="className"
        v-bind="$attrs"
    >
        <g transform="rotate(180 32 32)">
            <g transform="translate(0,64) scale(1,-1)">
                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" opacity="0.86">
                    <path d="M32 52L32 40" stroke-width="2.8" />
                    <path d="M32 40L25 31" stroke-width="2.2" />
                    <path d="M32 40L39 31" stroke-width="2.2" />
                    <path d="M32 40L22 40" stroke-width="1.8" opacity="0.88" />
                    <path d="M32 40L42 40" stroke-width="1.8" opacity="0.88" />
                </g>

                <g fill="currentColor">
                    <circle cx="32" cy="52" r="4" opacity="0.96" />
                    <circle cx="32" cy="40" r="3.2" opacity="0.9" />
                    <circle cx="25" cy="31" r="3.3" opacity="0.94" />
                    <circle cx="39" cy="31" r="3.3" opacity="0.94" />
                    <circle cx="22" cy="40" r="2.8" opacity="0.9" />
                    <circle cx="42" cy="40" r="2.8" opacity="0.9" />
                </g>
            </g>
        </g>
    </svg>
</template>
