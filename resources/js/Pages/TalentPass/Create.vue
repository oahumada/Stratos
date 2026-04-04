<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as talentPassIndex,
    show as talentPassShow,
} from '@/routes/talent-pass';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { CreateTalentPassRequest } from '@/types/talentPass';
import { Head, router } from '@inertiajs/vue3';
import { PhArrowLeft } from '@phosphor-icons/vue';
import { ref } from 'vue';

defineOptions({ name: 'TalentPassCreate' });

const store = useTalentPassStore();

const form = ref<CreateTalentPassRequest>({
    title: '',
    summary: '',
    visibility: 'private',
});

const errors = ref<Partial<Record<keyof CreateTalentPassRequest, string>>>({});
const submitting = ref(false);

function validate(): boolean {
    errors.value = {};
    if (!form.value.title.trim()) {
        errors.value.title = 'El título es obligatorio';
    } else if (form.value.title.length > 120) {
        errors.value.title = 'Máximo 120 caracteres';
    }
    return Object.keys(errors.value).length === 0;
}

async function handleSubmit() {
    if (!validate()) return;
    submitting.value = true;
    try {
        await store.createTalentPass(form.value);
        if (store.currentTalentPass?.id) {
            router.visit(talentPassShow(store.currentTalentPass.id).url);
        }
    } catch {
        // store.error already set
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AppLayout>
        <Head>
            <title>Nuevo Talent Pass</title>
        </Head>

        <div class="min-h-screen bg-slate-950 p-6 md:p-10">
            <div class="mx-auto max-w-2xl space-y-8">
                <!-- BACK -->
                <a
                    :href="talentPassIndex().url"
                    class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white"
                >
                    <PhArrowLeft :size="16" />
                    Volver a mis Talent Passes
                </a>

                <!-- HEADER -->
                <div>
                    <h1 class="text-3xl font-black text-white">
                        Nuevo Talent Pass
                    </h1>
                    <p class="mt-1 text-white/60">
                        Crea tu CV 2.0 con skills, experiencias y credenciales
                        verificables.
                    </p>
                </div>

                <!-- FORM -->
                <StCardGlass class="p-8">
                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <!-- Título -->
                        <div class="space-y-1">
                            <label
                                for="title"
                                class="text-sm font-medium text-white/80"
                            >
                                Título <span class="text-red-400">*</span>
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="ej. Senior Full Stack Engineer"
                                maxlength="120"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-white/30 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                :class="{ 'border-red-500': errors.title }"
                            />
                            <p v-if="errors.title" class="text-xs text-red-400">
                                {{ errors.title }}
                            </p>
                            <p class="text-right text-xs text-white/30">
                                {{ form.title.length }}/120
                            </p>
                        </div>

                        <!-- Resumen -->
                        <div class="space-y-1">
                            <label
                                for="summary"
                                class="text-sm font-medium text-white/80"
                            >
                                Resumen profesional
                            </label>
                            <textarea
                                id="summary"
                                v-model="form.summary"
                                rows="4"
                                placeholder="Una breve descripción de tu perfil profesional..."
                                class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-white/30 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <!-- Visibilidad -->
                        <div class="space-y-2">
                            <label
                                for="visibility"
                                class="text-sm font-medium text-white/80"
                            >
                                Visibilidad
                            </label>
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
                                        :id="`visibility-${opt.value}`"
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

                        <!-- Error global -->
                        <p
                            v-if="store.error"
                            class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400"
                        >
                            {{ store.error }}
                        </p>

                        <!-- ACTIONS -->
                        <div class="flex justify-end gap-3 pt-2">
                            <a :href="talentPassIndex().url">
                                <StButtonGlass variant="ghost"
                                    >Cancelar</StButtonGlass
                                >
                            </a>
                            <StButtonGlass
                                type="submit"
                                variant="primary"
                                :loading="submitting"
                                :disabled="submitting"
                            >
                                Crear Talent Pass
                            </StButtonGlass>
                        </div>
                    </form>
                </StCardGlass>
            </div>
        </div>
    </AppLayout>
</template>
