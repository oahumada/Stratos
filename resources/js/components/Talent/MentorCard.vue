<template>
    <div
        class="mentor-card group overflow-hidden rounded-2xl border border-white/5 bg-white/5 backdrop-blur-md transition-all duration-300 hover:border-white/10 hover:bg-white/10"
    >
        <div class="p-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div
                        class="mb-1 text-sm font-bold tracking-tight text-white"
                    >
                        {{ mentor.full_name }}
                    </div>
                    <div
                        class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                    >
                        {{ mentor.role }}
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <StBadgeGlass variant="success" size="sm">
                            <template #icon>
                                <PhStar :size="10" weight="fill" />
                            </template>
                            {{ mentor.match_score }}% Match
                        </StBadgeGlass>
                        <StBadgeGlass variant="glass" size="sm">
                            Nivel {{ mentor.expertise_level }}/5
                        </StBadgeGlass>
                    </div>
                </div>

                <div
                    class="relative h-16 w-16 overflow-hidden rounded-xl border border-white/10 bg-white/5 ring-1 ring-white/5 transition-transform duration-300 group-hover:scale-105"
                >
                    <img
                        v-if="mentor.avatar"
                        :src="mentor.avatar"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    />
                    <div
                        v-else
                        class="flex h-full w-full items-center justify-center text-lg font-black tracking-tighter text-white/20"
                    >
                        {{ initials }}
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"
                    />
                </div>
            </div>
        </div>

        <div class="border-t border-white/5 p-2">
            <StButtonGlass
                variant="primary"
                :icon="PhHandshake"
                block
                size="sm"
                @click="$emit('request')"
            >
                {{ t('talent_development.request_mentorship') }}
            </StButtonGlass>
        </div>
    </div>
</template>

<script setup>
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import { PhHandshake, PhStar } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    mentor: {
        type: Object,
        required: true,
        // Expected shape: { id, full_name, role, match_score, expertise_level, avatar }
    },
});

const initials = computed(() => {
    return props.mentor.full_name
        ? props.mentor.full_name
              .split(' ')
              .map((n) => n[0])
              .join('')
              .substring(0, 2)
              .toUpperCase()
        : '??';
});
</script>

<style scoped>
.mentor-card {
    box-shadow: 0 4px 15px -1px rgba(0, 0, 0, 0.1);
}
</style>
