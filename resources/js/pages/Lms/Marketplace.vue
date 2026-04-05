<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Listing {
    id: number;
    title: string;
    description: string;
    price: number | null;
    currency: string;
    listing_type: string;
    category: string | null;
    tags: string[] | null;
    downloads_count: number;
    is_published: boolean;
}

const listings = ref<Listing[]>([]);
const loading = ref(true);
const search = ref('');
const categoryFilter = ref('');

async function fetchListings() {
    loading.value = true;
    try {
        const params: Record<string, string> = {};
        if (search.value) params.title = search.value;
        if (categoryFilter.value) params.category = categoryFilter.value;
        const { data } = await axios.get('/api/lms/marketplace', { params });
        listings.value = data.data || data;
    } finally {
        loading.value = false;
    }
}

async function purchaseListing(id: number) {
    await axios.post(`/api/lms/marketplace/${id}/purchase`);
    await fetchListings();
}

function formatPrice(listing: Listing) {
    if (listing.listing_type === 'free' || !listing.price) return 'Gratis';
    return `${listing.currency} ${Number(listing.price).toFixed(2)}`;
}

onMounted(fetchListings);
</script>

<template>
    <v-container>
        <v-row>
            <v-col>
                <h1 class="text-h4 mb-4">Marketplace LMS</h1>

                <v-row class="mb-4">
                    <v-col cols="6">
                        <v-text-field
                            v-model="search"
                            label="Buscar cursos"
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            @update:model-value="fetchListings"
                        />
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="categoryFilter"
                            label="Categoría"
                            clearable
                            @update:model-value="fetchListings"
                        />
                    </v-col>
                </v-row>

                <v-row v-if="loading">
                    <v-col class="text-center">
                        <v-progress-circular indeterminate />
                    </v-col>
                </v-row>

                <v-row v-else-if="listings.length">
                    <v-col
                        v-for="listing in listings"
                        :key="listing.id"
                        cols="12"
                        md="4"
                    >
                        <v-card>
                            <v-card-title>{{ listing.title }}</v-card-title>
                            <v-card-subtitle>
                                {{ formatPrice(listing) }}
                                <v-chip
                                    v-if="listing.category"
                                    size="small"
                                    class="ml-2"
                                    >{{ listing.category }}</v-chip
                                >
                            </v-card-subtitle>
                            <v-card-text>
                                <p class="text-body-2">
                                    {{ listing.description }}
                                </p>
                                <div v-if="listing.tags?.length" class="mt-2">
                                    <v-chip
                                        v-for="tag in listing.tags"
                                        :key="tag"
                                        size="x-small"
                                        class="mr-1"
                                        >{{ tag }}</v-chip
                                    >
                                </div>
                                <p class="text-caption mt-2">
                                    {{ listing.downloads_count }} descargas
                                </p>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    color="primary"
                                    block
                                    @click="purchaseListing(listing.id)"
                                >
                                    {{
                                        listing.listing_type === 'free'
                                            ? 'Obtener gratis'
                                            : 'Comprar'
                                    }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <v-card v-else>
                    <v-card-text
                        >No hay listados disponibles en el
                        marketplace.</v-card-text
                    >
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
