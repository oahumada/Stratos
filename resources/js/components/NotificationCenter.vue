<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import { computed } from 'vue';

const { notifications } = useNotification();

const notificationList = computed(() => notifications.value || []);
</script>

<template>
    <div class="fixed right-4 bottom-4 z-50 max-w-md space-y-2">
        <transition-group name="slide-fade">
            <div
                v-for="notification in notificationList"
                :key="notification.id"
                :class="[
                    'animate-slide-in rounded-lg p-4 text-sm text-white shadow-lg',
                    {
                        'bg-green-500': notification.type === 'success',
                        'bg-red-500': notification.type === 'error',
                        'bg-yellow-500': notification.type === 'warning',
                        'bg-blue-500': notification.type === 'info',
                    },
                ]"
            >
                {{ notification.message }}
            </div>
        </transition-group>
    </div>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateX(100px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(100px);
    opacity: 0;
}

@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>
