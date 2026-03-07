<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useForm } from '@inertiajs/vue3';
import { AlertCircle, Code, Lightbulb, ShieldAlert, X } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits(['close']);

const ticketTypes = [
    {
        id: 'bug',
        label: 'Error (Bug)',
        icon: AlertCircle,
        color: 'text-red-400',
    },
    {
        id: 'improvement',
        label: 'Oportunidad de Mejora',
        icon: Lightbulb,
        color: 'text-amber-400',
    },
    {
        id: 'code_quality',
        label: 'Calidad de Código',
        icon: Code,
        color: 'text-blue-400',
    },
    {
        id: 'ux',
        label: 'Experiencia (UX)',
        icon: ShieldAlert,
        color: 'text-purple-400',
    },
];

const form = useForm({
    title: '',
    description: '',
    type: 'bug',
    priority: 'medium',
    context: {
        url: window.location.href,
        userAgent: navigator.userAgent,
        screenSize: `${window.innerWidth}x${window.innerHeight}`,
    },
});

const submit = () => {
    form.post('/api/support-tickets', {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-100 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
    >
        <StCardGlass
            class="w-full max-w-2xl animate-in overflow-hidden duration-300 fade-in zoom-in"
        >
            <div class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-primary/20 p-2">
                            <ShieldAlert class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">
                                Centro de Calidad y Mejora
                            </h2>
                            <p class="text-sm text-white/60">
                                Reporta errores o sugiere evoluciones para el
                                sistema.
                            </p>
                        </div>
                    </div>
                    <button
                        @click="emit('close')"
                        class="rounded-full p-2 transition-colors hover:bg-white/10"
                    >
                        <X class="h-5 w-5 text-white/70" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Tipo de Ticket -->
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                        <button
                            v-for="t in ticketTypes"
                            :key="t.id"
                            type="button"
                            @click="form.type = t.id"
                            class="flex flex-col items-center gap-2 rounded-xl border p-3 transition-all"
                            :class="
                                form.type === t.id
                                    ? 'border-primary bg-primary/20 shadow-lg shadow-primary/10'
                                    : 'border-white/10 bg-white/5 hover:bg-white/10'
                            "
                        >
                            <component
                                :is="t.icon"
                                class="h-6 w-6"
                                :class="t.color"
                            />
                            <span class="text-xs font-medium text-white/90">{{
                                t.label
                            }}</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-white/70"
                                >Título corto</label
                            >
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Ej: Error al cargar el heatmap de skills"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white focus:ring-2 focus:ring-primary/50 focus:outline-none"
                                required
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-white/70"
                                >Descripción detallada</label
                            >
                            <textarea
                                v-model="form.description"
                                rows="4"
                                placeholder="Describe qué pasó o qué te gustaría mejorar..."
                                class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white focus:ring-2 focus:ring-primary/50 focus:outline-none"
                                required
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <label
                                    class="mb-1 block text-sm font-medium text-white/70"
                                    >Prioridad</label
                                >
                                <select
                                    v-model="form.priority"
                                    class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-white focus:outline-none"
                                >
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                    <option value="critical">Crítica</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label
                                    class="mb-1 block text-sm font-medium text-white/70"
                                    >Contexto Automático</label
                                >
                                <div
                                    class="rounded-lg border border-dashed border-white/20 bg-white/5 px-4 py-2 text-xs text-white/40"
                                >
                                    Se adjuntará URL y datos del sistema.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex justify-end gap-3 border-t border-white/10 pt-4"
                    >
                        <StButtonGlass
                            variant="ghost"
                            @click="emit('close')"
                            type="button"
                        >
                            Cancelar
                        </StButtonGlass>
                        <StButtonGlass
                            variant="primary"
                            :disabled="form.processing"
                            type="submit"
                        >
                            {{
                                form.processing
                                    ? 'Enviando...'
                                    : 'Enviar Reporte'
                            }}
                        </StButtonGlass>
                    </div>
                </form>
            </div>
        </StCardGlass>
    </div>
</template>
