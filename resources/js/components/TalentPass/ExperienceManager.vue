<script setup lang="ts">
import { ref, computed } from 'vue';
import { PhPlus, PhTrash, PhPencil, PhX } from '@phosphor-icons/vue';
import { formatDate } from '@/lib/utils';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@/composables/useNotification';

interface Props {
    talentPassId: number;
    experiences?: StratosAPI.TalentPassExperience[];
    readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    experiences: () => [],
    readonly: false,
});

const emit = defineEmits<{
    added: [experience: StratosAPI.TalentPassExperience];
    deleted: [id: string];
}>();

const store = useTalentPassStore();
const { notify } = useNotification();

const showForm = ref(false);
const loading = ref(false);
const newExperience = ref({
    company: '',
    position: '',
    description: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

const experiencesByDate = computed(() => {
    return [...props.experiences].sort((a, b) => {
        const dateA = new Date(a.end_date || a.start_date);
        const dateB = new Date(b.end_date || b.start_date);
        return dateB.getTime() - dateA.getTime();
    });
});

const isFormValid = computed(() => {
    return newExperience.value.company && newExperience.value.position && newExperience.value.start_date;
});

async function addExperience() {
    if (!isFormValid.value) return;

    loading.value = true;
    try {
        const added = await store.addExperience(props.talentPassId, newExperience.value);
        notify({
            type: 'success',
            text: `Added experience at ${newExperience.value.company}`,
        });
        emit('added', added);
        resetForm();
    } catch (error) {
        notify({
            type: 'error',
            text: 'Failed to add experience. Please try again.',
        });
    } finally {
        loading.value = false;
    }
}

async function deleteExperience(id: string) {
    loading.value = true;
    try {
        await store.deleteExperience(props.talentPassId, id);
        notify({
            type: 'success',
            text: 'Experience removed',
        });
        emit('deleted', id);
    } catch (error) {
        notify({
            type: 'error',
            text: 'Failed to delete experience.',
        });
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    newExperience.value = {
        company: '',
        position: '',
        description: '',
        start_date: '',
        end_date: '',
        is_current: false,
    };
    showForm.value = false;
}

function toggleForm() {
    if (showForm.value) {
        resetForm();
    } else {
        showForm.value = true;
    }
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <PhPencil :size="20" class="text-indigo-400" />
                Professional Experience
            </h3>
            <button
                v-if="!readonly"
                @click="toggleForm"
                type="button"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-bold transition-colors"
            >
                <PhPlus :size="16" />
                <span>{{ showForm ? 'Cancel' : 'Add' }}</span>
            </button>
        </div>

        <!-- Add Form -->
        <div v-if="showForm && !readonly" class="p-4 rounded-lg bg-white/5 border border-indigo-500/20 space-y-3">
            <input
                v-model="newExperience.company"
                type="text"
                placeholder="Company name"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
            />
            <input
                v-model="newExperience.position"
                type="text"
                placeholder="Job title"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
            />
            <div class="grid grid-cols-2 gap-3">
                <input
                    v-model="newExperience.start_date"
                    type="date"
                    class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                />
                <input
                    v-model="newExperience.end_date"
                    type="date"
                    :disabled="newExperience.is_current"
                    class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all disabled:opacity-50"
                />
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input v-model="newExperience.is_current" type="checkbox" class="rounded border-white/20" />
                Currently working here
            </label>
            <textarea
                v-model="newExperience.description"
                placeholder="Description (optional)"
                maxlength="1000"
                rows="3"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all resize-none"
            ></textarea>
            <button
                @click="addExperience"
                :disabled="!isFormValid || loading"
                type="button"
                class="w-full py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold transition-colors"
            >
                {{ loading ? 'Adding...' : 'Add Experience' }}
            </button>
        </div>

        <!-- Experiences List -->
        <div v-if="experiencesByDate.length > 0" class="space-y-3">
            <div
                v-for="exp in experiencesByDate"
                :key="exp.id"
                class="p-4 rounded-lg bg-white/5 border border-white/10 hover:border-indigo-500/50 transition-all group"
            >
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-white">{{ exp.position }}</p>
                        <p class="text-sm text-indigo-400">{{ exp.company }}</p>
                    </div>
                    <button
                        v-if="!readonly"
                        @click="deleteExperience(exp.id)"
                        :disabled="loading"
                        type="button"
                        class="text-slate-400 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all disabled:opacity-50"
                    >
                        <PhTrash :size="18" />
                    </button>
                </div>
                <p class="text-xs text-slate-400 mb-2">
                    {{ formatDate(exp.start_date) }} - {{ exp.is_current ? 'Present' : formatDate(exp.end_date) }}
                </p>
                <p v-if="exp.description" class="text-sm text-slate-300 leading-relaxed">{{ exp.description }}</p>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8 px-4 rounded-lg bg-white/5 border border-white/10">
            <p class="text-sm text-slate-400">No experience added yet</p>
            <p class="text-xs text-slate-500 mt-1">Add your professional background to stand out</p>
        </div>
    </div>
</template>
