<script setup lang="ts">
import { ref, computed } from 'vue';
import { PhPlus, PhTrash, PhCertificate } from '@phosphor-icons/vue';
import { formatDate } from '@/lib/utils';
import { useTalentPassStore } from '@/stores/talentPassStore';
import { useNotification } from '@/composables/useNotification';

interface Props {
    talentPassId: number;
    credentials?: StratosAPI.TalentPassCredential[];
    readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    credentials: () => [],
    readonly: false,
});

const emit = defineEmits<{
    added: [credential: StratosAPI.TalentPassCredential];
    deleted: [id: string];
}>();

const store = useTalentPassStore();
const { notify } = useNotification();

const showForm = ref(false);
const loading = ref(false);
const newCredential = ref({
    title: '',
    issuer: '',
    issue_date: '',
    expiry_date: '',
    credential_url: '',
    credential_id: '',
});

const credentialsByDate = computed(() => {
    return [...props.credentials].sort((a, b) => {
        const dateA = new Date(a.issue_date);
        const dateB = new Date(b.issue_date);
        return dateB.getTime() - dateA.getTime();
    });
});

const isFormValid = computed(() => {
    return newCredential.value.title && newCredential.value.issuer && newCredential.value.issue_date;
});

const isExpired = (expiryDate?: string): boolean => {
    if (!expiryDate) return false;
    return new Date(expiryDate) < new Date();
};

async function addCredential() {
    if (!isFormValid.value) return;

    loading.value = true;
    try {
        const added = await store.addCredential(props.talentPassId, newCredential.value);
        notify({
            type: 'success',
            text: `Added credential: ${newCredential.value.title}`,
        });
        emit('added', added);
        resetForm();
    } catch (error) {
        notify({
            type: 'error',
            text: 'Failed to add credential. Please try again.',
        });
    } finally {
        loading.value = false;
    }
}

async function deleteCredential(id: string) {
    loading.value = true;
    try {
        await store.deleteCredential(props.talentPassId, id);
        notify({
            type: 'success',
            text: 'Credential removed',
        });
        emit('deleted', id);
    } catch (error) {
        notify({
            type: 'error',
            text: 'Failed to delete credential.',
        });
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    newCredential.value = {
        title: '',
        issuer: '',
        issue_date: '',
        expiry_date: '',
        credential_url: '',
        credential_id: '',
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
                <PhCertificate :size="20" class="text-emerald-400" />
                Certifications & Credentials
            </h3>
            <button
                v-if="!readonly"
                @click="toggleForm"
                type="button"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold transition-colors"
            >
                <PhPlus :size="16" />
                <span>{{ showForm ? 'Cancel' : 'Add' }}</span>
            </button>
        </div>

        <!-- Add Form -->
        <div v-if="showForm && !readonly" class="p-4 rounded-lg bg-white/5 border border-emerald-500/20 space-y-3">
            <input
                v-model="newCredential.title"
                type="text"
                placeholder="Credential title (e.g., AWS Solutions Architect)"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
            />
            <input
                v-model="newCredential.issuer"
                type="text"
                placeholder="Issuer (e.g., AWS, Google, Coursera)"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
            />
            <div class="grid grid-cols-2 gap-3">
                <input
                    v-model="newCredential.issue_date"
                    type="date"
                    class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                />
                <input
                    v-model="newCredential.expiry_date"
                    type="date"
                    placeholder="Expiry (optional)"
                    class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-400"
                />
            </div>
            <input
                v-model="newCredential.credential_id"
                type="text"
                placeholder="Credential ID or license number (optional)"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
            />
            <input
                v-model="newCredential.credential_url"
                type="url"
                placeholder="Verification URL (optional)"
                class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all"
            />
            <button
                @click="addCredential"
                :disabled="!isFormValid || loading"
                type="button"
                class="w-full py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold transition-colors"
            >
                {{ loading ? 'Adding...' : 'Add Credential' }}
            </button>
        </div>

        <!-- Credentials List -->
        <div v-if="credentialsByDate.length > 0" class="space-y-3">
            <div
                v-for="cred in credentialsByDate"
                :key="cred.id"
                class="p-4 rounded-lg bg-white/5 border border-white/10 hover:border-emerald-500/50 transition-all group"
                :class="{ 'border-red-500/50 bg-red-500/5': isExpired(cred.expiry_date) }"
            >
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-white">{{ cred.title }}</p>
                            <span v-if="isExpired(cred.expiry_date)" class="text-xs px-2 py-1 rounded-full bg-red-500/20 text-red-300 font-semibold">
                                Expired
                            </span>
                            <span v-else-if="cred.expiry_date" class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold">
                                Active
                            </span>
                        </div>
                        <p class="text-sm text-emerald-400">{{ cred.issuer }}</p>
                    </div>
                    <button
                        v-if="!readonly"
                        @click="deleteCredential(cred.id)"
                        :disabled="loading"
                        type="button"
                        class="text-slate-400 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all disabled:opacity-50"
                    >
                        <PhTrash :size="18" />
                    </button>
                </div>
                <p class="text-xs text-slate-400 mb-2">
                    Issued {{ formatDate(cred.issue_date) }}
                    <span v-if="cred.expiry_date"> • Expires {{ formatDate(cred.expiry_date) }}</span>
                    <span v-else> • No expiration</span>
                </p>
                <div class="flex flex-wrap gap-2 items-center">
                    <span v-if="cred.credential_id" class="text-xs bg-slate-500/20 text-slate-300 px-2 py-1 rounded">
                        ID: {{ cred.credential_id }}
                    </span>
                    <a
                        v-if="cred.credential_url"
                        :href="cred.credential_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-xs text-indigo-400 hover:text-indigo-300 underline"
                    >
                        View credential ↗
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8 px-4 rounded-lg bg-white/5 border border-white/10">
            <p class="text-sm text-slate-400">No credentials added yet</p>
            <p class="text-xs text-slate-500 mt-1">Add your certifications to boost credibility</p>
        </div>
    </div>
</template>
