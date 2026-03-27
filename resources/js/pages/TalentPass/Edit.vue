<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { UpdateTalentPassRequest } from '@/types/talentPass';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import { PhArrowLeft, PhCheck } from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

// Props Interface
interface Props {
    id: number | string;
}

const props = defineProps<Props>();

// Setup
const page = usePage();
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
    const id = Array.isArray(props.id)
        ? parseInt(
              Array.isArray(props.id)
                  ? props.id[0].toString()
                  : props.id.toString(),
          )
        : parseInt(props.id.toString());

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
                        Editar Talent Pass
                    </span>
                </div>

                <h1 class="mb-2 text-4xl font-black text-white">
                    Actualizar tu Perfil
                </h1>
            </div>

            <!-- Loading State -->
            <div
                v-if="initialLoading"
                class="flex items-center justify-center py-20"
            >
                <div
                    class="h-12 w-12 animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
                ></div>
            </div>

            <!-- Form Card -->
            <StCardGlass v-else class="p-8">
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
                            rows="5"
                            class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 focus:border-transparent focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ form.summary?.length || 0 }} caracteres
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
                                <div class="flex-1">
                                    <div class="font-semibold text-white">
                                        Privado
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Solo tú puedes ver este Talent Pass
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
                                <div class="flex-1">
                                    <div class="font-semibold text-white">
                                        A través de enlace
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Accesible con el enlace directo
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
                                <div class="flex-1">
                                    <div class="font-semibold text-white">
                                        Público
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        Visible en búsquedas públicas
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 border-t border-white/10 pt-6">
                        <Link
                            :href="`/talent-pass/${talentPassId}`"
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
                            {{
                                loading ? 'Actualizando...' : 'Guardar cambios'
                            }}
                        </button>
                    </div>
                </form>
            </StCardGlass>
        </div>
    </div>
</template>
