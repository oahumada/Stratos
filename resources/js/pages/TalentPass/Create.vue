<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { CreateTalentPassRequest } from '@/types/talentPass';
import { Head, Link } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import { PhArrowLeft, PhCheck } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';

defineOptions({ layout: AppLayout });

// Setup
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(false);

const form = ref<CreateTalentPassRequest>({
    title: '',
    summary: '',
    visibility: 'private',
});

// Validation
const errors = ref<Record<string, string>>({});

const canSubmit = computed(() => {
    return form.value.title && form.value.title.trim().length > 0;
});

// Methods
function validateForm(): boolean {
    errors.value = {};

    if (!form.value.title || form.value.title.trim().length === 0) {
        errors.value.title = 'El título es requerido';
    }

    return Object.keys(errors.value).length === 0;
}

async function submitForm() {
    if (!validateForm()) {
        return;
    }

    loading.value = true;

    try {
        await store.createTalentPass(form.value);
        notify({
            type: 'success',
            text: 'Talent Pass creado correctamente',
        });

        // Redirect to index
        window.location.href = '/talent-pass';
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error al crear Talent Pass',
        });
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Crear Talent Pass" />

    <div class="min-h-screen bg-[#020617] p-6 pb-20">
        <div class="mx-auto max-w-3xl">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    href="/talent-pass"
                    class="mb-6 inline-flex items-center gap-2 font-semibold text-indigo-400 transition hover:text-indigo-300"
                >
                    <PhArrowLeft :size="18" />
                    Volver
                </Link>

                <div class="mb-2 flex items-center gap-3">
                    <div
                        class="h-1 w-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"
                    ></div>
                    <span
                        class="text-xs font-black tracking-[0.2em] text-indigo-400 uppercase"
                    >
                        Nuevo Talent Pass
                    </span>
                </div>

                <h1 class="mb-2 text-4xl font-black text-white">
                    Crear tu Portafolio
                </h1>
                <p class="text-slate-400">
                    Define los detalles básicos de tu Talent Pass. Después
                    podrás agregar skills, experiencia y credenciales.
                </p>
            </div>

            <!-- Form Card -->
            <StCardGlass class="p-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Title Field -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-white"
                        >
                            Título
                            <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="ej: Ingeniero Full-Stack Senior"
                            class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 focus:border-transparent focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="{ 'ring-2 ring-red-500/50': errors.title }"
                        />
                        <p
                            v-if="errors.title"
                            class="mt-1 text-sm text-red-400"
                        >
                            {{ errors.title }}
                        </p>
                    </div>

                    <!-- Summary Field -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-semibold text-white"
                        >
                            Resumen (Opcional)
                        </label>
                        <textarea
                            v-model="form.summary"
                            placeholder="Describe brevemente tu perfil profesional, logros destacados o áreas de especialización..."
                            rows="5"
                            class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 focus:border-transparent focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ form.summary?.length || 0 }} caracteres (máximo
                            1000)
                        </p>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label
                            class="mb-3 block text-sm font-semibold text-white"
                        >
                            Visibilidad
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 p-3 transition hover:border-indigo-500/50 hover:bg-white/5"
                                :class="{
                                    'border-indigo-500 bg-indigo-500/10':
                                        form.visibility === 'private',
                                }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="private"
                                    class="h-4 w-4 accent-indigo-500"
                                />
                                <div>
                                    <div class="font-semibold text-white">
                                        Privado
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Solo tú puedes ver tu Talent Pass
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 p-3 transition hover:border-indigo-500/50 hover:bg-white/5"
                                :class="{
                                    'border-indigo-500 bg-indigo-500/10':
                                        form.visibility === 'link',
                                }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="link"
                                    class="h-4 w-4 accent-indigo-500"
                                />
                                <div>
                                    <div class="font-semibold text-white">
                                        A través de enlace
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Accesible solo con el enlace directo
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-3 rounded-lg border border-white/10 p-3 transition hover:border-indigo-500/50 hover:bg-white/5"
                                :class="{
                                    'border-indigo-500 bg-indigo-500/10':
                                        form.visibility === 'public',
                                }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="public"
                                    class="h-4 w-4 accent-indigo-500"
                                />
                                <div>
                                    <div class="font-semibold text-white">
                                        Público
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Visible en búsquedas y perfiles públicos
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 border-t border-white/10 pt-6">
                        <Link
                            href="/talent-pass"
                            class="flex-1 rounded-lg bg-white/5 px-6 py-3 text-center font-semibold text-white transition hover:bg-white/10"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="!canSubmit || loading"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-3 font-semibold text-white transition hover:from-indigo-600 hover:to-purple-600 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <PhCheck :size="18" />
                            {{ loading ? 'Creando...' : 'Crear Talent Pass' }}
                        </button>
                    </div>
                </form>
            </StCardGlass>

            <!-- Tips -->
            <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                <StCardGlass class="p-4">
                    <h4 class="mb-2 text-sm font-bold text-white">💡 Tip #1</h4>
                    <p class="text-xs text-slate-400">
                        Elige un título claro que describe tu rol o
                        especialización profesional.
                    </p>
                </StCardGlass>
                <StCardGlass class="p-4">
                    <h4 class="mb-2 text-sm font-bold text-white">💡 Tip #2</h4>
                    <p class="text-xs text-slate-400">
                        Puedes cambiar la visibilidad en cualquier momento desde
                        la página de detalles.
                    </p>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>
