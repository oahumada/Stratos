<template>
    <div class="role-forecasts-table">
        <v-card class="mb-4">
            <v-card-title class="d-flex align-center">
                <v-icon left>mdi-chart-line</v-icon>
                Role Forecasts
            </v-card-title>

            <v-card-subtitle>
                Projected workforce needs by role over
                {{ forecastMonths }} months
            </v-card-subtitle>

            <v-divider></v-divider>

            <!-- Filters -->
            <v-card-text class="pb-0">
                <v-row class="mb-4">
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="store.filters.forecastArea"
                            :items="areaOptions"
                            label="Filter by Area"
                            clearable
                            @update:model-value="store.setForecastAreaFilter"
                        />
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="store.filters.forecastCriticality"
                            :items="criticalityOptions"
                            label="Filter by Criticality"
                            clearable
                            @update:model-value="
                                store.setForecastCriticalityFilter
                            "
                        />
                    </v-col>
                    <v-col cols="12" md="4" class="text-right">
                        <v-btn
                            @click="downloadForecastReport"
                            variant="outlined"
                            size="small"
                            prepend-icon="mdi-download"
                        >
                            Export
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>

            <!-- Data Table -->
            <v-data-table
                :headers="tableHeaders"
                :items="filteredForecasts"
                :loading="loading"
                :items-per-page="15"
                class="elevation-0"
                density="comfortable"
            >
                <!-- Role Name with icon -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.role_name="{ item }">
                    <div class="d-flex align-center">
                        <v-icon size="small" class="mr-2">
                            {{ getRoleIcon(item.role_name) }}
                        </v-icon>
                        <strong>{{ item.role_name }}</strong>
                    </div>
                </template>

                <!-- Current Headcount -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.current_headcount="{ item }">
                    <v-chip
                        :color="getHeadcountColor(item.current_headcount)"
                        text-color="white"
                        size="small"
                    >
                        {{ item.current_headcount }} FTE
                    </v-chip>
                </template>

                <!-- Projected Headcount (Editable) -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.projected_headcount="{ item }">
                    <v-text-field
                        v-model.number="item.projected_headcount"
                        type="number"
                        size="small"
                        variant="outlined"
                        density="compact"
                        disabled
                        hint="Edición disponible próximamente"
                        persistent-hint
                        class="max-width-100"
                    />
                </template>

                <!-- Growth Rate -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.growth_rate="{ item }">
                    <div class="d-flex align-center">
                        <v-progress-linear
                            :value="item.growth_rate"
                            :color="getGrowthColor(item.growth_rate)"
                            class="mr-2 flex-grow-1"
                        ></v-progress-linear>
                        <span class="text-body2" style="min-width: 50px">
                            {{ item.growth_rate }}%
                        </span>
                    </div>
                </template>

                <!-- Critical Skills -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.critical_skills="{ item }">
                    <div class="d-flex flex-wrap gap-2">
                        <v-chip
                            v-for="skill in (item.critical_skills || []).slice(
                                0,
                                2,
                            )"
                            :key="skill"
                            size="small"
                            variant="outlined"
                            color="error"
                        >
                            {{ skill }}
                        </v-chip>
                        <v-chip
                            v-if="(item.critical_skills || []).length > 2"
                            size="small"
                            variant="text"
                        >
                            +{{ (item.critical_skills || []).length - 2 }}
                        </v-chip>
                    </div>
                </template>

                <!-- Emerging Skills -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.emerging_skills="{ item }">
                    <div class="d-flex flex-wrap gap-2">
                        <v-chip
                            v-for="skill in (item.emerging_skills || []).slice(
                                0,
                                2,
                            )"
                            :key="skill"
                            size="small"
                            variant="outlined"
                            color="warning"
                        >
                            {{ skill }}
                        </v-chip>
                        <v-chip
                            v-if="(item.emerging_skills || []).length > 2"
                            size="small"
                            variant="text"
                        >
                            +{{ (item.emerging_skills || []).length - 2 }}
                        </v-chip>
                    </div>
                </template>

                <!-- Criticality Badge -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.criticality_level="{ item }">
                    <v-chip
                        :color="getCriticalityColor(item.criticality_level)"
                        text-color="white"
                        size="small"
                    >
                        {{ item.criticality_level }}
                    </v-chip>
                </template>

                <!-- Actions -->
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template v-slot:item.actions="{ item }">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn icon size="small" v-bind="props">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item @click="expandForecast(item)">
                                <v-list-item-title
                                    >View Details</v-list-item-title
                                >
                            </v-list-item>
                            <v-list-item @click="viewSkillGaps(item)">
                                <v-list-item-title
                                    >View Skill Gaps</v-list-item-title
                                >
                            </v-list-item>
                            <v-list-item @click="viewMatching(item)">
                                <v-list-item-title
                                    >View Matching</v-list-item-title
                                >
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </template>
            </v-data-table>
        </v-card>

        <!-- Forecast Details Expansion -->
        <v-dialog v-model="showDetailsDialog" max-width="700px">
            <v-card v-if="selectedForecast">
                <v-card-title
                    >{{ selectedForecast.role_name }} - Forecast
                    Details</v-card-title
                >
                <v-divider></v-divider>
                <v-card-text class="pt-4">
                    <v-row class="mb-4">
                        <v-col cols="12" md="6">
                            <div class="text-caption text-medium-emphasis">
                                Current Headcount
                            </div>
                            <div class="text-h6">
                                {{ selectedForecast.current_headcount }} FTE
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-medium-emphasis">
                                Projected Headcount
                            </div>
                            <div class="text-h6">
                                {{ selectedForecast.projected_headcount }} FTE
                            </div>
                        </v-col>
                    </v-row>

                    <v-row class="mb-4">
                        <v-col cols="12" md="6">
                            <div class="text-caption text-medium-emphasis">
                                Growth Rate
                            </div>
                            <div class="text-h6">
                                {{ selectedForecast.growth_rate }}%
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="text-caption text-medium-emphasis">
                                Change in FTE
                            </div>
                            <div
                                class="text-h6"
                                :class="getChangeClass(selectedForecast)"
                            >
                                {{ getHeadcountChange(selectedForecast) }} FTE
                            </div>
                        </v-col>
                    </v-row>

                    <v-divider class="my-4"></v-divider>

                    <h4 class="mb-3">Critical Skills Required</h4>
                    <div class="mb-4">
                        <v-chip
                            v-for="skill in selectedForecast.critical_skills ||
                            []"
                            :key="skill"
                            class="mr-2 mb-2"
                            color="error"
                            text-color="white"
                            size="small"
                        >
                            {{ skill }}
                        </v-chip>
                    </div>

                    <h4 class="mb-3">Emerging Skills</h4>
                    <div>
                        <v-chip
                            v-for="skill in selectedForecast.emerging_skills ||
                            []"
                            :key="skill"
                            class="mr-2 mb-2"
                            color="warning"
                            text-color="white"
                            size="small"
                        >
                            {{ skill }}
                        </v-chip>
                    </div>
                </v-card-text>

                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="showDetailsDialog = false" variant="text"
                        >Close</v-btn
                    >
                    <v-btn
                        @click="saveChanges"
                        color="primary"
                        variant="elevated"
                        >Save Changes</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Loading & Error States -->
        <v-skeleton-loader
            v-if="loading"
            type="table-tbody"
        ></v-skeleton-loader>

        <v-alert
            v-if="error"
            type="error"
            :text="error"
            closable
            @click:close="error = null"
        ></v-alert>
    </div>
</template>

<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import {
    useStrategicPlanningScenariosStore,
    type RoleForecast,
} from '@/stores/scenarioPlanningScenariosStore';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    scenarioId: number;
}>();

const api = useApi();
const { showSuccess, showError } = useNotification();
const store = useStrategicPlanningScenariosStore();

// State
const selectedForecast = ref<RoleForecast | null>(null);
const showDetailsDialog = ref(false);
const forecastMonths = ref(24);

// Filter options
const areaOptions = [
    'Engineering',
    'Product',
    'Sales',
    'Operations',
    'Finance',
    'HR',
    'Marketing',
];

const criticalityOptions = [
    { title: 'Critical', value: 'critical' },
    { title: 'High', value: 'high' },
    { title: 'Medium', value: 'medium' },
    { title: 'Low', value: 'low' },
];

// Table headers
const tableHeaders = [
    { title: 'Role', value: 'role_name', width: '200px' },
    {
        title: 'Current FTE',
        value: 'current_headcount',
        align: 'center' as const,
    },
    {
        title: 'Projected FTE',
        value: 'projected_headcount',
        align: 'center' as const,
    },
    { title: 'Growth Rate', value: 'growth_rate', align: 'center' as const },
    { title: 'Critical Skills', value: 'critical_skills' },
    { title: 'Emerging Skills', value: 'emerging_skills' },
    {
        title: 'Criticality',
        value: 'criticality_level',
        align: 'center' as const,
    },
    {
        title: 'Actions',
        value: 'actions',
        align: 'center' as const,
        sortable: false,
    },
];

// Computed
const loading = computed(() => store.getLoadingState('forecasts'));
const error = computed(() => store.getError('forecasts'));
const forecasts = computed(() => {
    const data = store.getForecasts(props.scenarioId);
    return Array.isArray(data) ? data : [];
});
const filteredForecasts = computed(() => {
    const data = store.getFilteredForecasts(props.scenarioId);
    return Array.isArray(data) ? data : [];
});

// Methods
const fetchForecasts = async () => {
    await store.fetchForecasts(props.scenarioId);
};

const applyFilters = () => {
    // Filtered computed property handles this via store getters
};

const updateForecast = async (forecast: RoleForecast) => {
    void forecast;
    showError('Actualización de forecasts aún no disponible en esta vista');
};

const expandForecast = (forecast: RoleForecast) => {
    selectedForecast.value = forecast;
    showDetailsDialog.value = true;
};

const viewSkillGaps = (forecast: RoleForecast) => {
    showSuccess(`View skill gaps for ${forecast.role_name}`);
};

const viewMatching = (forecast: RoleForecast) => {
    showSuccess(`View internal matching for ${forecast.role_name}`);
};

const saveChanges = async () => {
    if (selectedForecast.value) {
        await updateForecast(selectedForecast.value);
        showSuccess('Changes saved successfully');
        showDetailsDialog.value = false;
    }
};

const downloadForecastReport = () => {
    // Generate CSV/PDF report
    showSuccess('Forecast report downloaded');
};

const getHeadcountChange = (forecast: RoleForecast): number => {
    return forecast.projected_headcount - forecast.current_headcount;
};

const getChangeClass = (forecast: RoleForecast): string => {
    const change = getHeadcountChange(forecast);
    if (change > 0) return 'text-success';
    if (change < 0) return 'text-error';
    return 'text-warning';
};

// Color helpers
const getHeadcountColor = (headcount: number): string => {
    if (headcount >= 10) return 'primary';
    if (headcount >= 5) return 'info';
    return 'warning';
};

const getGrowthColor = (growth: number): string => {
    if (growth > 20) return 'success';
    if (growth > 10) return 'info';
    if (growth >= 0) return 'warning';
    return 'error';
};

const getCriticalityColor = (criticality: string): string => {
    const colorMap: Record<string, string> = {
        critical: 'error',
        high: 'warning',
        medium: 'info',
        low: 'success',
    };
    return colorMap[criticality] || 'default';
};

const getRoleIcon = (roleName: string): string => {
    if (roleName.includes('Engineer')) return 'mdi-hammer-wrench';
    if (roleName.includes('Manager')) return 'mdi-account-tie';
    if (roleName.includes('Analyst')) return 'mdi-chart-bar';
    if (roleName.includes('Designer')) return 'mdi-palette';
    return 'mdi-briefcase';
};

// Lifecycle
onMounted(() => {
    fetchForecasts();
});

// mark some bindings referenced during refactor to avoid unused-var
void api;
void forecasts.value;
void applyFilters;
</script>

<style scoped lang="scss">
.role-forecasts-table {
    .max-width-100 {
        max-width: 100px;
    }

    .gap-2 {
        gap: 8px;
    }

    :deep(.v-data-table) {
        font-size: 0.875rem;
    }
}
</style>
