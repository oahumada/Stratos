<script setup lang="ts">
import { computed, ref } from 'vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@kyvg/vue3-notification';
import { PhPlus, PhMinus, PhStar } from '@phosphor-icons/vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import type { TalentPassSkill, AddSkillRequest } from '@/types/talentPass';

interface Props {
    talentPassId: number;
    skills?: TalentPassSkill[];
    readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    skills: () => [],
    readonly: false,
});

// Setup
const store = useTalentPassStore();
const { notify } = useNotification();
const loading = ref(false);
const showAddForm = ref(false);

const newSkill = ref<AddSkillRequest>({
    name: '',
    level: 3,
    category: 'technical',
});

// Computed
const skillsByLevel = computed(() => {
    const levels: Record<number, TalentPassSkill[]> = {
        5: [],
        4: [],
        3: [],
        2: [],
        1: [],
    };

    props.skills.forEach(skill => {
        if (levels[skill.level]) {
            levels[skill.level].push(skill);
        }
    });

    return levels;
});

const levelLabel: Record<number, string> = {
    5: 'Expert',
    4: 'Advanced',
    3: 'Intermediate',
    2: 'Beginner',
    1: 'Novice',
};

const levelColor: Record<number, string> = {
    5: 'text-emerald-400',
    4: 'text-blue-400',
    3: 'text-indigo-400',
    2: 'text-amber-400',
    1: 'text-slate-400',
};

// Methods
async function addSkill() {
    if (!newSkill.value.name.trim()) {
        notify({
            type: 'error',
            text: 'Skill name is required',
        });
        return;
    }

    loading.value = true;

    try {
        await store.addSkill(props.talentPassId, newSkill.value);

        notify({
            type: 'success',
            text: 'Skill added successfully',
        });

        newSkill.value = { name: '', level: 3, category: 'technical' };
        showAddForm.value = false;
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error adding skill',
        });
    } finally {
        loading.value = false;
    }
}

async function removeSkill(skillId: number) {
    if (!window.confirm('Remove this skill?')) return;

    loading.value = true;

    try {
        await store.removeSkill(props.talentPassId, skillId);

        notify({
            type: 'success',
            text: 'Skill removed',
        });
    } catch (err) {
        notify({
            type: 'error',
            text: 'Error removing skill',
        });
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <StCardGlass class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <PhStar :size="20" class="text-indigo-400" />
                Technical Skills
            </h3>
            <div v-if="!readonly" class="flex items-center gap-2 text-xs text-slate-400">
                <span>{{ skills.length }} skills</span>
            </div>
        </div>

        <!-- Skills by Level -->
        <div class="space-y-6">
            <div v-for="(skills, level) in skillsByLevel" :key="level" v-show="skills.length > 0">
                <h4 :class="levelColor[level]" class="text-xs font-bold uppercase tracking-wide mb-3">
                    {{ levelLabel[level] }}
                </h4>
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="skill in skills"
                        :key="skill.id"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 hover:border-indigo-500/50 transition group"
                    >
                        <span class="text-sm text-white font-semibold">{{ skill.name }}</span>
                        <div class="flex gap-0.5">
                            <span v-for="i in 5" :key="i" class="w-1.5 h-1.5 rounded-full"
                                :class="i <= level ? 'bg-indigo-400' : 'bg-white/20'"
                            ></span>
                        </div>
                        <button
                            v-if="!readonly"
                            @click="removeSkill(skill.id)"
                            class="ml-auto opacity-0 group-hover:opacity-100 transition text-red-400 hover:text-red-300"
                            title="Remove skill"
                        >
                            <PhMinus :size="14" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="skills.length === 0 && !showAddForm" class="py-8 text-center">
            <p class="text-sm text-slate-400 mb-4">No skills added yet</p>
        </div>

        <!-- Add Skill Form -->
        <div v-if="!readonly" class="mt-6 pt-6 border-t border-white/10">
            <button
                v-if="!showAddForm"
                @click="showAddForm = true"
                class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/30 border border-indigo-500/30 text-indigo-300 font-semibold transition"
            >
                <PhPlus :size="16" />
                Add Skill
            </button>

            <form v-else @submit.prevent="addSkill" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-white mb-1 block">Skill Name</label>
                    <input
                        v-model="newSkill.name"
                        type="text"
                        placeholder="e.g., Vue.js, Python, Leadership..."
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <div>
                    <label class="text-xs font-semibold text-white mb-1 block">Level</label>
                    <select
                        v-model.number="newSkill.level"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="1">Novice (1/5)</option>
                        <option value="2">Beginner (2/5)</option>
                        <option value="3">Intermediate (3/5)</option>
                        <option value="4">Advanced (4/5)</option>
                        <option value="5">Expert (5/5)</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 disabled:opacity-50 text-white font-semibold transition"
                    >
                        <PhPlus :size="14" />
                        Add
                    </button>
                    <button
                        type="button"
                        @click="showAddForm = false"
                        class="flex-1 px-3 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white font-semibold transition"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </StCardGlass>
</template>
