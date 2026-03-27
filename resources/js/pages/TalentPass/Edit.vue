<script setup lang="ts">
import { Head, Link, useRoute } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@kyvg/vue3-notification';
import { PhArrowLeft, PhCheck } from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import { computed, onMounted, ref } from 'vue';
import type { UpdateTalentPassRequest } from '@/types/talentPass';

defineOptions({ layout: AppLayout });

// Setup
const route = useRoute();
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(false);
const initialLoading = ref(true);

const talentPassId = ref<number | null>(null);
const form = ref<UpdateTalentPassRequest>({
    title: '',
    summary: '',
    visibility: 'private',
});

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

async function loadTalentPass() {
    try {
        if (!talentPassId.value) return;

        await store.fetchTalentPass(talentPassId.value);
        const tp = store.currentTalentPass;

        if (tp) {
            form.value = {
                title: tp.title,
                summary: tp.summary,
                visibility: tp.visibility,
            };
        }
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error al cargar Talent Pass',
        });
    } finally {
        initialLoading.value = false;
    }
}

async function submitForm() {
    if (!validateForm()) {
        return;
    }

    if (!talentPassId.value) return;

    loading.value = true;

    try {
        await store.updateTalentPass(talentPassId.value, form.value);
        notify({
            type: 'success',
            text: 'Talent Pass actualizado',
        });

        window.location.href = `/talent-pass/${talentPassId.value}`;
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error al actualizar Talent Pass',
        });
    } finally {
        loading.value = false;
    }
}

// Lifecycle
onMounted(() => {
    const id = Array.isArray(route.params.id)
        ? parseInt(route.params.id[0])
        : parseInt(route.params.id);

    if (id) {
        talentPassId.value = id;
        loadTalentPass();
    }
});
</script>

<template>
    <Head title="Editar Talent Pass" />

    <div class="min-h-screen bg-[#020617] p-6 pb-20">
        <div class="mx-auto max-w-3xl">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    :href="`/talent-pass/${talentPassId}`"
                    class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 font-semibold mb-6 transition"
                >
                    <PhArrowLeft :size="18" />
                    Volver
                </Link>

                <div class="flex items-center gap-3 mb-2">
                    <div class="h-1 w-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <span class="text-xs font-black tracking-[0.2em] text-indigo-400 uppercase">
                        Editar Talent Pass
                    </span>
                </div>

                <h1 class="text-4xl font-black text-white mb-2">
                    Actualizar tu Perfil
                </h1>
            </div>

            <!-- Loading State -->
            <div v-if="initialLoading" class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
            </div>

            <!-- Form Card -->
            <StCardGlass v-else class="p-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Title Field -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            Título
                            <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :class="{ 'ring-2 ring-red-500/50': errors.title }"
                        />
                        <p v-if="errors.title" class="text-sm text-red-400 mt-1">
                            {{ errors.title }}
                        </p>
                    </div>

                    <!-- Summary Field -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">
                            Resumen (Opcional)
                        </label>
                        <textarea
                            v-model="form.summary"
                            rows="5"
                            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                        ></textarea>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ form.summary?.length || 0 }} caracteres
                        </p>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-3">
                            Visibilidad
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition"
                                :class="{ 'border-indigo-500 bg-indigo-500/10': form.visibility === 'private' }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="private"
                                    class="w-4 h-4 accent-indigo-500"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-white">Privado</div>
                                    <p class="text-xs text-slate-400">Solo tú puedes ver este Talent Pass</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition"
                                :class="{ 'border-indigo-500 bg-indigo-500/10': form.visibility === 'link' }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="link"
                                    class="w-4 h-4 accent-indigo-500"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-white">A través de enlace</div>
                                    <p class="text-xs text-slate-400">Accesible con el enlace directo</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-lg border border-white/10 cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition"
                                :class="{ 'border-indigo-500 bg-indigo-500/10': form.visibility === 'public' }"
                            >
                                <input
                                    v-model="form.visibility"
                                    type="radio"
                                    value="public"
                                    class="w-4 h-4 accent-indigo-500"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-white">Público</div>
                                    <p class="text-xs text-slate-400">Visible en búsquedas públicas</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-6 border-t border-white/10">
                        <Link
                            :href="`/talent-pass/${talentPassId}`"
                            class="flex-1 px-6 py-3 rounded-lg bg-white/5 hover:bg-white/10 text-white font-semibold transition text-center"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="!canSubmit || loading"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold transition"
                        >
                            <PhCheck :size="18" />
                            {{ loading ? 'Actualizando...' : 'Guardar cambios' }}
                        </button>
                    </div>
                </form>
            </StCardGlass>
        </div>
    </div>
</template>
