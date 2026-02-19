<template>
    <v-card class="mentor-card mx-auto mb-4" variant="outlined" hover>
        <v-card-item>
            <div class="d-flex flex-no-wrap justify-space-between">
                <div>
                    <v-card-title class="text-h6">
                        {{ mentor.full_name }}
                    </v-card-title>

                    <v-card-subtitle>{{ mentor.role }}</v-card-subtitle>

                    <div class="d-flex align-center mt-2">
                        <v-chip
                            color="success"
                            size="small"
                            variant="flat"
                            class="mr-2"
                        >
                            <v-icon start icon="mdi-star"></v-icon>
                            {{ mentor.match_score }} Match
                        </v-chip>
                        <v-chip color="primary" size="small" variant="outlined">
                            Nivel {{ mentor.expertise_level }}/5
                        </v-chip>
                    </div>
                </div>

                <v-avatar class="ma-3" size="80" rounded="0">
                    <v-img
                        v-if="mentor.avatar"
                        :src="mentor.avatar"
                        cover
                    ></v-img>
                    <span v-else class="text-h5">{{ initials }}</span>
                </v-avatar>
            </div>
        </v-card-item>

        <v-card-actions>
            <v-btn
                prepend-icon="mdi-handshake"
                variant="tonal"
                color="primary"
                block
                @click="$emit('request')"
            >
                Solicitar Mentoría
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script setup>
import { computed } from 'vue';

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
    transition:
        transform 0.2s,
        box-shadow 0.2s;
}
</style>
