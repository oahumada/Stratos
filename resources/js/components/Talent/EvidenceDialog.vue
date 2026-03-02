<template>
    <v-dialog v-model="dialog" max-width="600" persistent class="glass-dialog">
        <div
            class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 p-1 shadow-2xl shadow-black/50 backdrop-blur-2xl"
        >
            <div
                class="relative overflow-hidden rounded-[1.8rem] bg-gradient-to-br from-white/5 to-transparent p-6"
            >
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-400"
                        >
                            <PhPaperclip :size="24" />
                        </div>
                        <div>
                            <h2
                                class="text-xl font-black tracking-tight text-white"
                            >
                                {{ t('talent_development.evidence.title') }}
                            </h2>
                            <p
                                class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                            >
                                {{ t('talent_development.evidence.subtitle') }}
                            </p>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        :icon="PhX"
                        circle
                        size="sm"
                        @click="dialog = false"
                    />
                </div>

                <!-- Tabs -->
                <div class="mb-6 flex gap-2">
                    <button
                        v-for="tab in [
                            {
                                id: 'list',
                                label: t(
                                    'talent_development.evidence.tabs.history',
                                ),
                                icon: PhArchive,
                            },
                            {
                                id: 'upload',
                                label: t(
                                    'talent_development.evidence.tabs.new',
                                ),
                                icon: PhPlus,
                            },
                        ]"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'flex flex-1 items-center justify-center gap-2 rounded-xl py-3 text-xs font-bold transition-all duration-300',
                            activeTab === tab.id
                                ? 'border border-blue-500/30 bg-blue-500/10 text-blue-300'
                                : 'text-white/40 hover:bg-white/5 hover:text-white',
                        ]"
                    >
                        <component :is="tab.icon" :size="14" />
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Content Area -->
                <div class="min-h-[350px]">
                    <!-- List View -->
                    <div
                        v-if="activeTab === 'list'"
                        class="animate-in space-y-4 duration-300 fade-in slide-in-from-bottom-2"
                    >
                        <div v-if="loading" class="flex justify-center py-12">
                            <div
                                class="h-8 w-8 animate-spin rounded-full border-2 border-blue-500/20 border-t-blue-500"
                            />
                        </div>

                        <div
                            v-else-if="evidences.length === 0"
                            class="flex flex-col items-center justify-center py-12 text-center"
                        >
                            <div class="mb-4 text-white/10">
                                <PhArchive :size="48" weight="thin" />
                            </div>
                            <p class="text-sm font-medium text-white/30">
                                {{ t('talent_development.evidence.empty') }}
                            </p>
                            <StButtonGlass
                                variant="ghost"
                                size="sm"
                                class="mt-4"
                                @click="activeTab = 'upload'"
                            >
                                {{ t('talent_development.evidence.tabs.new') }}
                            </StButtonGlass>
                        </div>

                        <div
                            v-else
                            class="custom-scrollbar max-h-[450px] space-y-4 overflow-y-auto pr-2"
                        >
                            <div
                                v-for="evidence in evidences"
                                :key="evidence.id"
                                class="group flex items-center gap-4 rounded-2xl border border-white/5 bg-white/5 p-4 transition-all hover:border-white/10 hover:bg-white/10"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-blue-500/20 bg-blue-500/10 text-blue-400"
                                >
                                    <component
                                        :is="getIcon(evidence.type)"
                                        :size="20"
                                    />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div
                                        class="truncate text-sm font-bold text-white"
                                    >
                                        {{ evidence.title }}
                                    </div>
                                    <div class="truncate text-xs text-white/40">
                                        {{ evidence.description }}
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-1 opacity-100 transition-opacity md:opacity-0 md:group-hover:opacity-100"
                                >
                                    <StButtonGlass
                                        v-if="evidence.file_path"
                                        variant="ghost"
                                        :icon="PhDownload"
                                        circle
                                        size="sm"
                                        @click="
                                            downloadFile(evidence.file_path)
                                        "
                                    />
                                    <StButtonGlass
                                        v-if="evidence.external_url"
                                        variant="ghost"
                                        :icon="PhArrowSquareOut"
                                        circle
                                        size="sm"
                                        @click="openUrl(evidence.external_url)"
                                    />
                                    <StButtonGlass
                                        variant="ghost"
                                        :icon="PhTrash"
                                        circle
                                        size="sm"
                                        class="hover:text-rose-400"
                                        @click="deleteEvidence(evidence.id)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload View -->
                    <div
                        v-else
                        class="animate-in duration-300 fade-in slide-in-from-bottom-2"
                    >
                        <form @submit.prevent="saveEvidence" class="space-y-5">
                            <div class="space-y-1.5">
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.title',
                                        )
                                    }}
                                </label>
                                <input
                                    v-model="newEvidence.title"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white transition-all focus:border-blue-500/50 focus:outline-none"
                                    :placeholder="
                                        t(
                                            'talent_development.evidence.form.title_placeholder',
                                        )
                                    "
                                />
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.description',
                                        )
                                    }}
                                </label>
                                <textarea
                                    v-model="newEvidence.description"
                                    rows="2"
                                    class="w-full resize-none rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white transition-all focus:border-blue-500/50 focus:outline-none"
                                    :placeholder="
                                        t(
                                            'talent_development.evidence.form.description_placeholder',
                                        )
                                    "
                                ></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.type',
                                        )
                                    }}
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="type in [
                                            {
                                                id: 'file',
                                                label: t(
                                                    'talent_development.evidence.types.file',
                                                ),
                                                icon: PhFileDoc,
                                            },
                                            {
                                                id: 'link',
                                                label: t(
                                                    'talent_development.evidence.types.link',
                                                ),
                                                icon: PhLink,
                                            },
                                            {
                                                id: 'text',
                                                label: t(
                                                    'talent_development.evidence.types.text',
                                                ),
                                                icon: PhTextT,
                                            },
                                        ]"
                                        :key="type.id"
                                        type="button"
                                        @click="newEvidence.type = type.id"
                                        :class="[
                                            'flex flex-col items-center gap-2 rounded-xl border p-3 transition-all',
                                            newEvidence.type === type.id
                                                ? 'border-blue-500/50 bg-blue-500/10 text-blue-300'
                                                : 'border-white/5 bg-white/5 text-white/40 hover:bg-white/10',
                                        ]"
                                    >
                                        <component :is="type.icon" :size="20" />
                                        <span
                                            class="text-[10px] font-bold uppercase"
                                            >{{ type.label }}</span
                                        >
                                    </button>
                                </div>
                            </div>

                            <div
                                v-if="newEvidence.type === 'file'"
                                class="animate-in space-y-1.5 duration-300 fade-in"
                            >
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.file',
                                        )
                                    }}
                                </label>
                                <div
                                    class="relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-white/10 bg-white/5 p-8 transition-all hover:border-blue-500/30 hover:bg-white/10"
                                    @click="$refs.fileInput.click()"
                                >
                                    <input
                                        ref="fileInput"
                                        type="file"
                                        class="hidden"
                                        @change="onFileChange"
                                    />
                                    <div class="mb-2 text-white/20">
                                        <PhPlus
                                            v-if="!newEvidence.file"
                                            :size="32"
                                            weight="thin"
                                        />
                                        <PhFileDoc
                                            v-else
                                            :size="32"
                                            class="text-blue-400"
                                        />
                                    </div>
                                    <p
                                        class="text-[10px] font-bold tracking-widest text-white/40 uppercase"
                                    >
                                        {{
                                            newEvidence.file
                                                ? newEvidence.file.name
                                                : t(
                                                      'talent_development.evidence.form.file_click',
                                                  )
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="newEvidence.type === 'link'"
                                class="animate-in space-y-1.5 duration-300 fade-in"
                            >
                                <label
                                    class="px-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.url',
                                        )
                                    }}
                                </label>
                                <input
                                    v-model="newEvidence.external_url"
                                    type="url"
                                    required
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white transition-all focus:border-blue-500/50 focus:outline-none"
                                    placeholder="https://..."
                                />
                            </div>

                            <div class="flex justify-end pt-4">
                                <StButtonGlass
                                    variant="primary"
                                    :loading="saving"
                                    type="submit"
                                >
                                    {{
                                        t(
                                            'talent_development.evidence.form.save',
                                        )
                                    }}
                                </StButtonGlass>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </v-dialog>
</template>

<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import {
    PhArchive,
    PhArrowSquareOut,
    PhDownload,
    PhFileDoc,
    PhLink,
    PhPaperclip,
    PhPlus,
    PhTextT,
    PhTrash,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    actionId: {
        type: Number,
        required: true,
    },
});

const dialog = ref(false);
const activeTab = ref('list');
const evidences = ref([]);
const loading = ref(false);
const saving = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const newEvidence = ref({
    title: '',
    description: '',
    type: 'file',
    file: null as File | null,
    external_url: '',
});

const open = () => {
    dialog.value = true;
    fetchEvidences();
};

const fetchEvidences = async () => {
    if (!props.actionId) return;
    loading.value = true;
    try {
        const response = await axios.get('/api/evidences', {
            params: { development_action_id: props.actionId },
        });
        evidences.value = response.data.data;
    } catch (e) {
        console.error('Error fetching evidences', e);
    } finally {
        loading.value = false;
    }
};

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        newEvidence.value.file = target.files[0];
    }
};

const saveEvidence = async () => {
    saving.value = true;
    try {
        const formData = new FormData();
        formData.append('development_action_id', props.actionId.toString());
        formData.append('title', newEvidence.value.title);
        formData.append('description', newEvidence.value.description);
        formData.append('type', newEvidence.value.type);

        if (newEvidence.value.type === 'file' && newEvidence.value.file) {
            formData.append('file', newEvidence.value.file);
        }
        if (
            newEvidence.value.type === 'link' &&
            newEvidence.value.external_url
        ) {
            formData.append('external_url', newEvidence.value.external_url);
        }

        await axios.post('/api/evidences', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        // Reset form
        newEvidence.value = {
            title: '',
            description: '',
            type: 'file',
            file: null,
            external_url: '',
        };
        activeTab.value = 'list';
        await fetchEvidences();
    } catch (e) {
        console.error('Error saving evidence', e);
    } finally {
        saving.value = false;
    }
};

const deleteEvidence = async (id: number) => {
    if (!confirm(t('talent_development.evidence.confirm_delete'))) return;
    try {
        await axios.delete(`/api/evidences/${id}`);
        await fetchEvidences();
    } catch (e) {
        console.error('Error deleting evidence', e);
    }
};

const downloadFile = (path: string) => {
    window.open('/storage/' + path, '_blank');
};

const openUrl = (url: string) => {
    window.open(url, '_blank');
};

const getIcon = (type: string) => {
    switch (type) {
        case 'file':
            return PhFileDoc;
        case 'link':
            return PhLink;
        case 'text':
            return PhTextT;
        default:
            return PhPaperclip;
    }
};

defineExpose({ open });
</script>

<style scoped>
.glass-dialog {
    backdrop-filter: blur(10px);
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
