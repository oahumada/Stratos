<script setup lang="ts">
import { ref, computed } from 'vue';
import { PhX, PhCheck, PhShare2, PhLink, PhEnvelope } from '@phosphor-icons/vue';

interface Props {
    isOpen: boolean;
    talentPassUlid: string;
    talentPassTitle?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
}>();

const copied = ref(false);

const publicUrl = computed(() => {
    if (!process.env.APP_URL) return window.location.origin;
    return `${process.env.APP_URL}/public/talent-pass/${props.talentPassUlid}`;
});

function copyToClipboard() {
    navigator.clipboard.writeText(publicUrl.value).then(() => {
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    });
}

function shareViaEmail() {
    const subject = `Check out my Talent Pass: ${props.talentPassTitle}`;
    const body = `I'd like to share my professional profile with you. View it here: ${publicUrl.value}`;
    window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
}

function shareViaTwitter() {
    const text = `Check out my Talent Pass: ${publicUrl.value}`;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`, '_blank');
}

function shareViaLinkedin() {
    window.open(
        `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(publicUrl.value)}`,
        '_blank'
    );
}
</script>

<template>
    <!-- Modal Overlay -->
    <Teleport to="body">
        <transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <!-- Modal Content -->
                <div class="bg-slate-900 rounded-xl border border-white/10 w-full max-w-sm shadow-2xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 border-b border-white/10">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <PhShare2 :size="24" class="text-indigo-400" />
                            Share Your Profile
                        </h2>
                        <button
                            @click="emit('close')"
                            type="button"
                            class="text-slate-400 hover:text-white transition-colors"
                        >
                            <PhX :size="24" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <!-- Copy Link Section -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300">Public Link</label>
                            <div class="flex items-center gap-2">
                                <input
                                    :value="publicUrl"
                                    type="text"
                                    readonly
                                    class="flex-1 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-300 text-sm focus:outline-none"
                                />
                                <button
                                    @click="copyToClipboard"
                                    type="button"
                                    class="px-3 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white font-semibold text-sm transition-colors flex items-center gap-2"
                                >
                                    <PhCheck v-if="copied" :size="18" />
                                    <PhLink v-else :size="18" />
                                    {{ copied ? 'Copied!' : 'Copy' }}
                                </button>
                            </div>
                            <p class="text-xs text-slate-400">Anyone with this link can view your profile</p>
                        </div>

                        <!-- Share Options -->
                        <div class="space-y-2 pt-4 border-t border-white/10">
                            <p class="text-sm font-semibold text-slate-300">Share via</p>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    @click="shareViaEmail"
                                    type="button"
                                    class="px-3 py-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
                                >
                                    <PhEnvelope :size="18" />
                                    Email
                                </button>
                                <button
                                    @click="shareViaLinkedin"
                                    type="button"
                                    class="px-3 py-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
                                >
                                    <span class="text-lg">in</span>
                                    LinkedIn
                                </button>
                                <button
                                    @click="shareViaTwitter"
                                    type="button"
                                    class="px-3 py-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
                                >
                                    <span class="text-lg">𝕏</span>
                                    Twitter
                                </button>
                                <button
                                    @click="emit('close')"
                                    type="button"
                                    class="px-3 py-3 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white text-sm font-bold transition-colors"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>

    <!-- Fade Transition CSS -->
    <style scoped>
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.2s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }
    </style>
</template>
