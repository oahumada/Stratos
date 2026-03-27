<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import type { AddSkillRequest, TalentPassSkill } from '@/types/talentPass';
import { useNotification } from '@kyvg/vue3-notification';
import { PhMinus, PhPlus, PhStar } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';

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

    props.skills.forEach((skill) => {
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
        <div class="mb-6 flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-lg font-bold text-white">
                <PhStar :size="20" class="text-indigo-400" />
                Technical Skills
            </h3>
            <div
                v-if="!readonly"
                class="flex items-center gap-2 text-xs text-slate-400"
            >
                <span>{{ skills.length }} skills</span>
            </div>
        </div>

        <!-- Skills by Level -->
        <div class="space-y-6">
            <div
                v-for="(skills, level) in skillsByLevel"
                :key="level"
                v-show="skills.length > 0"
            >
                <h4
                    :class="levelColor[level]"
                    class="mb-3 text-xs font-bold tracking-wide uppercase"
                >
                    {{ levelLabel[level] }}
                </h4>
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="skill in skills"
                        :key="skill.id"
                        class="group flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 transition hover:border-indigo-500/50"
                    >
                        <span class="text-sm font-semibold text-white">{{
                            skill.name
                        }}</span>
                        <div class="flex gap-0.5">
                            <span
                                v-for="i in 5"
                                :key="i"
                                class="h-1.5 w-1.5 rounded-full"
                                :class="
                                    i <= level ? 'bg-indigo-400' : 'bg-white/20'
                                "
                            ></span>
                        </div>
                        <button
                            v-if="!readonly"
                            @click="removeSkill(skill.id)"
                            class="ml-auto text-red-400 opacity-0 transition group-hover:opacity-100 hover:text-red-300"
                            title="Remove skill"
                        >
                            <PhMinus :size="14" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="skills.length === 0 && !showAddForm"
            class="py-8 text-center"
        >
            <p class="mb-4 text-sm text-slate-400">No skills added yet</p>
        </div>

        <!-- Add Skill Form -->
        <div v-if="!readonly" class="mt-6 border-t border-white/10 pt-6">
            <button
                v-if="!showAddForm"
                @click="showAddForm = true"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-indigo-500/30 bg-indigo-500/20 px-4 py-2 font-semibold text-indigo-300 transition hover:bg-indigo-500/30"
            >
                <PhPlus :size="16" />
                Add Skill
            </button>

            <form v-else @submit.prevent="addSkill" class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-white"
                        >Skill Name</label
                    >
                    <input
                        v-model="newSkill.name"
                        type="text"
                        placeholder="e.g., Vue.js, Python, Leadership..."
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-white"
                        >Level</label
                    >
                    <select
                        v-model.number="newSkill.level"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
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
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-indigo-500 px-3 py-2 font-semibold text-white transition hover:bg-indigo-600 disabled:opacity-50"
                    >
                        <PhPlus :size="14" />
                        Add
                    </button>
                    <button
                        type="button"
                        @click="showAddForm = false"
                        class="flex-1 rounded-lg bg-white/5 px-3 py-2 font-semibold text-white transition hover:bg-white/10"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </StCardGlass>
</template>
