<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface RubricScore {
    criterion: string;
    score: number;
    max: number;
    comment: string;
}

interface PeerReview {
    id: number;
    assignment_id: number;
    reviewer_id: number;
    reviewee_id: number;
    submission_url: string | null;
    submission_text: string | null;
    review_score: number | null;
    review_feedback: string | null;
    rubric_scores: RubricScore[] | null;
    status: string;
    submitted_at: string | null;
    reviewed_at: string | null;
    reviewer?: { id: number; name: string };
    reviewee?: { id: number; name: string };
    assignment?: { id: number; title: string };
}

const tab = ref<'reviewee' | 'reviewer'>('reviewee');
const reviews = ref<PeerReview[]>([]);
const loading = ref(true);

const submissionDialog = ref(false);
const reviewDialog = ref(false);
const selectedReview = ref<PeerReview | null>(null);

const submissionUrl = ref('');
const submissionText = ref('');
const reviewScore = ref<number>(0);
const reviewFeedback = ref('');

async function fetchReviews() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/lms/peer-reviews', {
            params: { role: tab.value },
        });
        reviews.value = Array.isArray(data) ? data : data.data || [];
    } finally {
        loading.value = false;
    }
}

function openSubmission(review: PeerReview) {
    selectedReview.value = review;
    submissionUrl.value = '';
    submissionText.value = '';
    submissionDialog.value = true;
}

async function submitWork() {
    if (!selectedReview.value) return;
    await axios.post(
        `/api/lms/peer-reviews/${selectedReview.value.id}/submit-work`,
        {
            submission_url: submissionUrl.value || null,
            submission_text: submissionText.value || null,
        },
    );
    submissionDialog.value = false;
    await fetchReviews();
}

function openReview(review: PeerReview) {
    selectedReview.value = review;
    reviewScore.value = 0;
    reviewFeedback.value = '';
    reviewDialog.value = true;
}

async function submitReview() {
    if (!selectedReview.value) return;
    await axios.post(
        `/api/lms/peer-reviews/${selectedReview.value.id}/submit-review`,
        {
            score: reviewScore.value,
            feedback: reviewFeedback.value,
        },
    );
    reviewDialog.value = false;
    await fetchReviews();
}

const statusColor = computed(() => (status: string) => {
    const map: Record<string, string> = {
        pending_submission: 'grey',
        submitted: 'blue',
        under_review: 'orange',
        reviewed: 'green',
        disputed: 'red',
    };
    return map[status] || 'grey';
});

onMounted(fetchReviews);
</script>

<template>
    <v-container>
        <h1 class="text-h4 mb-4">Revisión entre Pares</h1>

        <v-tabs v-model="tab" class="mb-4" @update:model-value="fetchReviews">
            <v-tab value="reviewee">Mis Entregas</v-tab>
            <v-tab value="reviewer">Mis Revisiones</v-tab>
        </v-tabs>

        <v-progress-linear v-if="loading" indeterminate />

        <v-card
            v-for="review in reviews"
            :key="review.id"
            class="mb-3"
            variant="outlined"
        >
            <v-card-title>
                {{
                    review.assignment?.title ||
                    `Asignación #${review.assignment_id}`
                }}
                <v-chip
                    :color="statusColor(review.status)"
                    size="small"
                    class="ml-2"
                >
                    {{ review.status }}
                </v-chip>
            </v-card-title>
            <v-card-text>
                <div v-if="tab === 'reviewee'">
                    <p>
                        <strong>Revisor:</strong>
                        {{ review.reviewer?.name || review.reviewer_id }}
                    </p>
                    <div v-if="review.review_score !== null">
                        <p>
                            <strong>Puntuación:</strong>
                            {{ review.review_score }}
                        </p>
                        <p>
                            <strong>Retroalimentación:</strong>
                            {{ review.review_feedback }}
                        </p>
                    </div>
                </div>
                <div v-else>
                    <p>
                        <strong>Autor:</strong>
                        {{ review.reviewee?.name || review.reviewee_id }}
                    </p>
                    <div v-if="review.submission_text || review.submission_url">
                        <p v-if="review.submission_url">
                            <strong>Enlace:</strong> {{ review.submission_url }}
                        </p>
                        <p v-if="review.submission_text">
                            <strong>Texto:</strong> {{ review.submission_text }}
                        </p>
                    </div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    v-if="
                        tab === 'reviewee' &&
                        review.status === 'pending_submission'
                    "
                    color="primary"
                    @click="openSubmission(review)"
                >
                    Enviar Trabajo
                </v-btn>
                <v-btn
                    v-if="tab === 'reviewer' && review.status === 'submitted'"
                    color="primary"
                    @click="openReview(review)"
                >
                    Revisar
                </v-btn>
            </v-card-actions>
        </v-card>

        <v-alert v-if="!loading && reviews.length === 0" type="info">
            No hay revisiones asignadas.
        </v-alert>

        <!-- Submit Work Dialog -->
        <v-dialog v-model="submissionDialog" max-width="600">
            <v-card>
                <v-card-title>Enviar Trabajo</v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="submissionUrl"
                        label="URL del trabajo"
                        class="mb-3"
                    />
                    <v-textarea
                        v-model="submissionText"
                        label="Texto de la entrega"
                        rows="4"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="submissionDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="submitWork">Enviar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Review Dialog -->
        <v-dialog v-model="reviewDialog" max-width="600">
            <v-card>
                <v-card-title>Enviar Revisión</v-card-title>
                <v-card-text>
                    <v-slider
                        v-model="reviewScore"
                        label="Puntuación"
                        :min="0"
                        :max="100"
                        :step="1"
                        thumb-label
                        class="mb-3"
                    />
                    <v-textarea
                        v-model="reviewFeedback"
                        label="Retroalimentación"
                        rows="4"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="reviewDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="submitReview"
                        >Enviar Revisión</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
