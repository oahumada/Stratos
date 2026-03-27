<script setup lang="ts">
import { ref } from 'vue';
import { PhDownload, PhFilePdf, PhFileJson, PhLinkedinLogo } from '@phosphor-icons/vue';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@/composables/useNotification';

interface Props {
    talentPassId: number;
    talentPassTitle?: string;
}

defineProps<Props>();

const store = useTalentPassStore();
const { notify } = useNotification();

const isOpen = ref(false);
const isLoading = ref(false);

async function exportToPdf() {
    isLoading.value = true;

    try {
        // Call backend to generate PDF
        const response = await fetch(`/api/talent-pass/${talentPassId}/export/pdf`, {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf',
            },
        });

        if (!response.ok) throw new Error('Export failed');

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${talentPassTitle?.toLowerCase().replace(/\s+/g, '-')}-resume.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        notify({ type: 'success', text: 'Resume exported as PDF' });
        isOpen.value = false;
    } catch {
        notify({ type: 'error', text: 'Failed to export PDF' });
    } finally {
        isLoading.value = false;
    }
}

async function exportToJson() {
    isLoading.value = true;

    try {
        const response = await fetch(`/api/talent-pass/${talentPassId}/export/json`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) throw new Error('Export failed');

        const json = await response.json();
        const dataStr = JSON.stringify(json, null, 2);
        const blob = new Blob([dataStr], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${talentPassTitle?.toLowerCase().replace(/\s+/g, '-')}-data.json`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        notify({ type: 'success', text: 'Data exported as JSON' });
        isOpen.value = false;
    } catch {
        notify({ type: 'error', text: 'Failed to export JSON' });
    } finally {
        isLoading.value = false;
    }
}

function shareToLinkedin() {
    notify({ type: 'info', text: 'LinkedIn share feature coming soon' });
    isOpen.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-sm transition-all"
        >
            <PhDownload :size="18" />
            Export
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="isOpen"
            class="absolute top-full mt-2 right-0 w-48 rounded-lg bg-slate-900 border border-white/10 shadow-xl z-50 overflow-hidden"
        >
            <button
                @click="exportToPdf"
                :disabled="isLoading"
                type="button"
                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-left text-white text-sm transition-colors disabled:opacity-50"
            >
                <PhFilePdf :size="18" class="text-red-400" />
                <span>Export as PDF</span>
            </button>
            <button
                @click="exportToJson"
                :disabled="isLoading"
                type="button"
                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-left text-white text-sm transition-colors disabled:opacity-50 border-t border-white/5"
            >
                <PhFileJson :size="18" class="text-blue-400" />
                <span>Export as JSON</span>
            </button>
            <button
                @click="shareToLinkedin"
                type="button"
                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/5 text-left text-white text-sm transition-colors border-t border-white/5"
            >
                <PhLinkedinLogo :size="18" class="text-blue-600" />
                <span>Share to LinkedIn</span>
            </button>
        </div>

        <!-- Click Outside to Close -->
        <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-40"></div>
    </div>
</template>
