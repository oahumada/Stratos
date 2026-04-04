<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { UpdateTalentPassRequest } from '@/types/talentPass';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhArrowLeft, PhCheck } from '@phosphor-icons/vue';
import { onMounted, ref, watch } from 'vue';

defineOptions({ name: 'TalentPassEdit' });

interface Props {
    id: number;
}

const props = defineProps<Props>();
const store = useTalentPassStore();

const form = ref<UpdateTalentPassRequest>({
    title: '',
    summary: '',
    visibility: 'private',
});

const errors = ref<Partial<Record<keyof UpdateTalentPassRequest, string>>>({});
const submitting = ref(false);
const saved = ref(false);

// Populate form once loaded
watch(
    () => store.currentTalentPass,
    (tp) => {
        if (tp) {
            form.value = {
                title: tp.title,
                summary: tp.summary ?? '',
                visibility: tp.visibility,
            };
        }
    },
    { immediate: true },
);

function validate(): boolean {
    errors.value = {};
    if (!form.value.title?.trim()) {
        errors.value.title = 'El título es obligatorio';
    }
    return Object.keys(errors.value).length === 0;
}

async function handleSubmit() {
    if (!validate()) return;
    submitting.value = true;
    try {
        await store.updateTalentPass(props.id, form.value);
        saved.value = true;
        setTimeout(() => {
            saved.value = false;
            router.visit(route('talent-pass.show', { id: props.id }));
        }, 1200);
    } catch {
        // store.error already set
    } finally {
        submitting.value = false;
    }
}

onMounted(() => {
    if (!store.currentTalentPass || store.currentTalentPass.id !== props.id) {
        store.fetchTalentPass(props.id);
    }
});
</script>

<template>
    <AppLayout>
        <Head title="Editar Talent Pass" />

        <div class="min-h-screen bg-slate-950 p-6 md:p-10">
            <div class="mx-auto max-w-2xl space-y-8">
                <!-- BACK -->
                <Link
                    :href="route('talent-pass.show', { id })"
                    class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white"
                >
                    <PhArrowLeft :size="16" />
                    Volver al Talent Pass
                </Link>

                <div>
                    <h1 class="text-3xl font-black text-white">
                        Editar Talent Pass
                    </h1>
                    <p class="mt-1 text-white/60">
                        Actualiza el título, resumen y visibilidad.
                    </p>
                </div>

                <!-- LOADING -->
                <div
                    v-if="store.loading"
                    class="h-64 animate-pulse rounded-xl bg-white/5"
                />

                <!-- FORM -->
                <StCardGlass v-else class="p-8">
                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-white/80">
                                Título <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                maxlength="120"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-white/30 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                :class="{ 'border-red-500': errors.title }"
                            />
                            <p v-if="errors.title" class="text-xs text-red-400">
                                {{ errors.title }}
                            </p>
                            <p class="text-right text-xs text-white/30">
                                {{ form.title?.length ?? 0 }}/120
                            </p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-white/80"
                                >Resumen profesional</label
                            >
                            <textarea
                                v-model="form.summary"
                                rows="4"
                                class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-white/30 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-white/80"
                                >Visibilidad</label
                            >
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    v-for="opt in [
                                        {
                                            value: 'private',
                                            label: '🔒 Privado',
                                            desc: 'Solo tú puedes verlo',
                                        },
                                        {
                                            value: 'public',
                                            label: '🌍 Público',
                                            desc: 'Cualquiera con el enlace',
                                        },
                                    ]"
                                    :key="opt.value"
                                    :class="[
                                        'cursor-pointer rounded-lg border p-3 transition',
                                        form.visibility === opt.value
                                            ? 'border-indigo-500 bg-indigo-500/10'
                                            : 'border-white/10 bg-white/5 hover:border-white/20',
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        v-model="form.visibility"
                                        :value="opt.value"
                                        class="sr-only"
                                    />
                                    <div class="text-sm font-medium text-white">
                                        {{ opt.label }}
                                    </div>
                                    <div class="text-xs text-white/50">
                                        {{ opt.desc }}
                                    </div>
                                </label>
                            </div>
                        </div>

                        <p
                            v-if="store.error"
                            class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400"
                        >
                            {{ store.error }}
                        </p>

                        <div class="flex justify-end gap-3 pt-2">
                            <Link :href="route('talent-pass.show', { id })">
                                <StButtonGlass variant="ghost"
                                    >Cancelar</StButtonGlass
                                >
                            </Link>
                            <StButtonGlass
                                type="submit"
                                :variant="saved ? 'primary' : 'primary'"
                                :loading="submitting"
                                :disabled="submitting"
                                :icon="saved ? PhCheck : undefined"
                            >
                                {{ saved ? 'Guardado' : 'Guardar cambios' }}
                            </StButtonGlass>
                        </div>
                    </form>
                </StCardGlass>
            </div>
        </div>
    </AppLayout>
</template>
