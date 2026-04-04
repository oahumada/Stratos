<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    lesson?: {
        id: number;
        title: string;
        content_type: string;
        content_url?: string;
        content_body?: string;
    };
}>();

const loading = ref(true);
const error = ref<string | null>(null);
const progress = ref(0);

// Video tracking
const videoTracking = ref<any>(null);

// Micro content
const microContent = ref<any>(null);
const currentCard = ref(0);

// Interactive content
const interactiveContents = ref<any[]>([]);

const contentType = computed(() => props.lesson?.content_type ?? 'html');

async function loadContent() {
    if (!props.lesson) {
        loading.value = false;
        return;
    }

    try {
        switch (contentType.value) {
            case 'video':
                await loadVideoTracking();
                break;
            case 'micro':
                await loadMicroContent();
                break;
            case 'interactive':
                await loadInteractiveContent();
                break;
        }
    } catch (e: any) {
        error.value = e.message ?? 'Failed to load content';
    } finally {
        loading.value = false;
    }
}

async function loadVideoTracking() {
    const res = await axios.get(
        `/api/lms/lessons/${props.lesson!.id}/video/tracking`,
    );
    videoTracking.value = res.data.data;
    progress.value = res.data.data?.progress_percentage ?? 0;
}

async function loadMicroContent() {
    const res = await axios.get(`/api/lms/lessons/${props.lesson!.id}/micro`);
    microContent.value = res.data.data;
}

async function loadInteractiveContent() {
    const res = await axios.get(
        `/api/lms/lessons/${props.lesson!.id}/interactive`,
    );
    interactiveContents.value = res.data.data ?? [];
}

async function updateVideoProgress(
    watchedSeconds: number,
    lastPosition: number,
) {
    if (!props.lesson) return;
    try {
        const res = await axios.post(
            `/api/lms/lessons/${props.lesson.id}/video/progress`,
            {
                watched_seconds: watchedSeconds,
                last_position: lastPosition,
            },
        );
        progress.value = res.data.data?.progress_percentage ?? progress.value;
    } catch {
        // Silent fail for progress updates
    }
}

function nextCard() {
    const cards = microContent.value?.micro_content?.cards ?? [];
    if (currentCard.value < cards.length - 1) {
        currentCard.value++;
        progress.value = Math.round(
            ((currentCard.value + 1) / cards.length) * 100,
        );
    }
}

function prevCard() {
    if (currentCard.value > 0) {
        currentCard.value--;
    }
}

onMounted(loadContent);
</script>

<template>
    <v-container fluid class="pa-0 pa-md-4">
        <!-- Loading -->
        <v-row v-if="loading" justify="center" class="mt-8">
            <v-progress-circular indeterminate color="primary" size="64" />
        </v-row>

        <!-- Error -->
        <v-alert v-else-if="error" type="error" class="ma-4">{{
            error
        }}</v-alert>

        <!-- No lesson -->
        <v-alert v-else-if="!lesson" type="info" class="ma-4"
            >No lesson selected.</v-alert
        >

        <!-- Content -->
        <template v-else>
            <!-- Header -->
            <v-card flat class="mb-4">
                <v-card-title class="text-h6">{{ lesson.title }}</v-card-title>
                <v-progress-linear
                    :model-value="progress"
                    color="primary"
                    height="6"
                    rounded
                />
            </v-card>

            <!-- Video Player -->
            <v-card v-if="contentType === 'video' && videoTracking" flat>
                <div class="video-container">
                    <iframe
                        v-if="videoTracking.embed?.embed_url"
                        :src="videoTracking.embed.embed_url"
                        frameborder="0"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                        "
                        allowfullscreen
                        class="video-iframe"
                    />
                </div>
                <v-card-text class="text-center">
                    <v-chip color="primary" variant="tonal" class="mr-2">
                        {{ Math.round(progress) }}% watched
                    </v-chip>
                    <v-chip
                        v-if="videoTracking.tracking?.completed"
                        color="success"
                        variant="tonal"
                    >
                        Completed
                    </v-chip>
                </v-card-text>
            </v-card>

            <!-- SCORM Player -->
            <v-card v-else-if="contentType === 'scorm'" flat>
                <iframe
                    v-if="lesson.content_url"
                    :src="lesson.content_url"
                    class="scorm-iframe"
                    frameborder="0"
                />
                <v-card-text v-else class="text-grey text-center">
                    No SCORM package linked.
                </v-card-text>
            </v-card>

            <!-- Microlearning Cards -->
            <v-card v-else-if="contentType === 'micro' && microContent" flat>
                <v-card-text>
                    <div
                        v-if="microContent.micro_content?.cards?.length"
                        class="micro-card-container"
                    >
                        <v-card
                            variant="outlined"
                            class="mx-auto"
                            max-width="600"
                        >
                            <v-card-subtitle class="text-overline">
                                {{
                                    microContent.micro_content.cards[
                                        currentCard
                                    ]?.type
                                }}
                            </v-card-subtitle>
                            <v-card-title>{{
                                microContent.micro_content.cards[currentCard]
                                    ?.title
                            }}</v-card-title>
                            <v-card-text>{{
                                microContent.micro_content.cards[currentCard]
                                    ?.content
                            }}</v-card-text>
                            <v-card-actions class="justify-space-between">
                                <v-btn
                                    :disabled="currentCard === 0"
                                    @click="prevCard"
                                    >Previous</v-btn
                                >
                                <span class="text-caption"
                                    >{{ currentCard + 1 }} /
                                    {{
                                        microContent.micro_content.cards.length
                                    }}</span
                                >
                                <v-btn
                                    :disabled="
                                        currentCard ===
                                        microContent.micro_content.cards
                                            .length -
                                            1
                                    "
                                    @click="nextCard"
                                >
                                    Next
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Interactive Content -->
            <v-card v-else-if="contentType === 'interactive'" flat>
                <v-card-text>
                    <div
                        v-for="item in interactiveContents"
                        :key="item.id"
                        class="mb-4"
                    >
                        <v-card variant="outlined">
                            <v-card-subtitle class="text-overline">{{
                                item.widget_type
                            }}</v-card-subtitle>
                            <v-card-title>{{ item.title }}</v-card-title>
                            <v-card-text>
                                <!-- Accordion widget -->
                                <v-expansion-panels
                                    v-if="
                                        item.widget_type === 'accordion' &&
                                        item.config?.panels
                                    "
                                >
                                    <v-expansion-panel
                                        v-for="(panel, idx) in item.config
                                            .panels"
                                        :key="idx"
                                        :title="panel.title"
                                        :text="panel.content"
                                    />
                                </v-expansion-panels>

                                <!-- Tabs widget -->
                                <v-tabs
                                    v-else-if="
                                        item.widget_type === 'tabs' &&
                                        item.config?.tabs
                                    "
                                >
                                    <v-tab
                                        v-for="(tab, idx) in item.config.tabs"
                                        :key="idx"
                                        >{{ tab.label }}</v-tab
                                    >
                                </v-tabs>

                                <!-- Fallback -->
                                <pre v-else class="text-body-2">{{
                                    JSON.stringify(item.config, null, 2)
                                }}</pre>
                            </v-card-text>
                        </v-card>
                    </div>
                    <v-alert v-if="!interactiveContents.length" type="info">
                        No interactive content available.
                    </v-alert>
                </v-card-text>
            </v-card>

            <!-- HTML / Rich Text (default) -->
            <v-card v-else flat>
                <v-card-text>
                    <div
                        v-if="lesson.content_body"
                        v-html="lesson.content_body"
                        class="rich-content"
                    />
                    <p v-else class="text-grey">No content available.</p>
                </v-card-text>
            </v-card>
        </template>
    </v-container>
</template>

<style scoped>
.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
}
.video-iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.scorm-iframe {
    width: 100%;
    min-height: 600px;
    border: none;
}
.rich-content {
    max-width: 100%;
    overflow-wrap: break-word;
}
</style>
