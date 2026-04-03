<template>
    <AppLayout v-bind="layoutProps">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-white drop-shadow-md"
                    >
                        <span class="mr-3 text-indigo-400">📊</span>
                        Workforce Planning
                    </h2>
                    <p class="mt-2 text-sm text-white/50">
                        Visualiza escenarios de demanda de talento y recibe
                        recomendaciones automáticas
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-6 space-y-6">
            <!-- KPIs Dashboard -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <StCardGlass
                    class="border-emerald-500/20 bg-emerald-500/5 transition-all duration-300 hover:border-emerald-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-emerald-400 uppercase"
                        >
                            Brechas Cerradas
                        </h3>
                        <PhChartPolar class="text-emerald-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.gaps_closed_percent ?? 0 }}%
                    </p>
                </StCardGlass>
                <StCardGlass
                    class="border-indigo-500/20 bg-indigo-500/5 transition-all duration-300 hover:border-indigo-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-indigo-400 uppercase"
                        >
                            Estrategias Activas
                        </h3>
                        <PhRocketLaunch class="text-indigo-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.active_strategies ?? 0 }}
                    </p>
                </StCardGlass>
                <StCardGlass
                    class="border-rose-500/20 bg-rose-500/5 transition-all duration-300 hover:border-rose-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-rose-400 uppercase"
                        >
                            Alertas de Riesgo
                        </h3>
                        <PhPulse class="text-rose-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.risk_alerts ?? 0 }}
                    </p>
                </StCardGlass>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Form: Create Scenario -->
                <StCardGlass>
                    <div class="mb-4">
                        <h3 class="mb-1 text-lg font-bold text-white">
                            <PhCompass
                                class="mr-2 inline text-indigo-300"
                                :size="20"
                            />Crear Escenario de Demanda
                        </h3>
                        <p class="text-xs text-white/40">
                            Parámetros del escenario de crecimiento u
                            optimización.
                        </p>
                    </div>

                    <form @submit.prevent="createScenario" class="space-y-4">
                        <div>
                            <label
                                for="scenario-name"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Nombre</label
                            >
                            <input
                                id="scenario-name"
                                v-model="form.name"
                                required
                                placeholder="Ej: Expansión Regional 2026"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="growth-percentage"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Crecimiento (%)</label
                            >
                            <input
                                id="growth-percentage"
                                v-model="form.growth_percentage"
                                type="number"
                                placeholder="Ej: 15"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    for="timeframe-start"
                                    class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                    >Inicio</label
                                >
                                <input
                                    id="timeframe-start"
                                    v-model="form.timeframe_start"
                                    type="date"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label
                                    for="timeframe-end"
                                    class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                    >Fin</label
                                >
                                <input
                                    id="timeframe-end"
                                    v-model="form.timeframe_end"
                                    type="date"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <StButtonGlass
                                id="workforce-create-scenario"
                                type="submit"
                                variant="primary"
                                :loading="loading"
                                class="w-full sm:w-auto"
                            >
                                Analizar Demanda
                            </StButtonGlass>
                        </div>
                    </form>
                </StCardGlass>

                <!-- Results: Recommendations -->
                <StCardGlass class="flex flex-col">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="mb-1 text-lg font-bold text-white">
                                <PhRoadHorizon
                                    class="mr-2 inline text-indigo-300"
                                    :size="20"
                                />Estrategias Recomendadas
                            </h3>
                            <p class="text-xs text-white/40">
                                Decisiones automáticas (Build, Buy, Borrow, Bot)
                            </p>
                        </div>
                        <StBadgeGlass
                            v-if="recommendations.length"
                            variant="success"
                            >Actualizado</StBadgeGlass
                        >
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label
                                for="planning-context"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Contexto de análisis</label
                            >
                            <select
                                id="planning-context"
                                v-model="planningContext"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option value="scenario" class="text-slate-900">
                                    Escenario futuro
                                </option>
                                <option value="baseline" class="text-slate-900">
                                    Baseline actual
                                </option>
                            </select>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                Cobertura
                            </p>
                            <p class="text-xl font-black text-white">
                                {{ analysisSummary?.coverage_pct ?? 0 }}%
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                Delta ejecutivo
                            </p>
                            <div
                                v-if="loadingResults"
                                class="animate-pulse space-y-2"
                            >
                                <div
                                    class="h-4 w-full rounded bg-white/10"
                                ></div>
                                <div
                                    class="h-4 w-3/4 rounded bg-white/10"
                                ></div>
                                <div
                                    class="h-4 w-5/6 rounded bg-white/10"
                                ></div>
                            </div>
                            <div v-else class="space-y-2 text-xs text-white/80">
                                <div class="flex flex-wrap items-center gap-2">
                                    <v-tooltip
                                        text="Delta de brecha en horas-hombre vs baseline. Menor brecha es mejor."
                                        location="top"
                                    >
                                        <template #activator="{ props }">
                                            <span
                                                v-bind="props"
                                                class="cursor-help decoration-dotted"
                                                >Gap HH</span
                                            >
                                        </template>
                                    </v-tooltip>
                                    <StBadgeGlass
                                        :variant="
                                            deltaBadgeVariant(
                                                baselineDelta?.delta_gap_hh ??
                                                    0,
                                                false,
                                            )
                                        "
                                    >
                                        {{
                                            formatSigned(
                                                baselineDelta?.delta_gap_hh ??
                                                    0,
                                            )
                                        }}
                                    </StBadgeGlass>
                                    <v-tooltip
                                        text="Delta de cobertura porcentual vs baseline. Mayor cobertura es mejor."
                                        location="top"
                                    >
                                        <template #activator="{ props }">
                                            <span
                                                v-bind="props"
                                                class="cursor-help decoration-dotted"
                                                >Cobertura</span
                                            >
                                        </template>
                                    </v-tooltip>
                                    <StBadgeGlass
                                        :variant="
                                            deltaBadgeVariant(
                                                baselineDelta?.delta_coverage_pct ??
                                                    0,
                                                true,
                                            )
                                        "
                                    >
                                        {{
                                            formatSigned(
                                                baselineDelta?.delta_coverage_pct ??
                                                    0,
                                            )
                                        }}%
                                    </StBadgeGlass>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <v-tooltip
                                        text="Delta de costo estimado vs baseline. Mayor costo es peor."
                                        location="top"
                                    >
                                        <template #activator="{ props }">
                                            <span
                                                v-bind="props"
                                                class="cursor-help decoration-dotted"
                                                >Costo</span
                                            >
                                        </template>
                                    </v-tooltip>
                                    <StBadgeGlass
                                        :variant="
                                            deltaBadgeVariant(
                                                impactDelta?.delta_cost_estimate ??
                                                    0,
                                                false,
                                            )
                                        "
                                    >
                                        ${{
                                            formatSigned(
                                                impactDelta?.delta_cost_estimate ??
                                                    0,
                                            )
                                        }}
                                    </StBadgeGlass>
                                    <v-tooltip
                                        text="Variación cualitativa de riesgo vs baseline: higher, lower o stable."
                                        location="top"
                                    >
                                        <template #activator="{ props }">
                                            <span
                                                v-bind="props"
                                                class="cursor-help decoration-dotted"
                                                >Riesgo</span
                                            >
                                        </template>
                                    </v-tooltip>
                                    <StBadgeGlass
                                        :variant="
                                            riskLevelVariant(
                                                impactDelta?.delta_risk_level ??
                                                    'stable',
                                            )
                                        "
                                    >
                                        {{
                                            impactDelta?.delta_risk_level ??
                                            'stable'
                                        }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="loadingResults"
                        class="flex grow items-center justify-center p-8"
                    >
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="h-10 w-10 animate-spin rounded-full border-2 border-indigo-500 border-t-transparent"
                            ></div>
                            <span class="text-sm text-white/50"
                                >Calculando supply vs demand...</span
                            >
                        </div>
                    </div>
                    <div
                        v-else-if="recommendations.length"
                        class="grow space-y-3 overflow-auto pr-2"
                    >
                        <div
                            v-for="(rec, idx) in recommendations"
                            :key="idx"
                            class="flex flex-col justify-between rounded-2xl border border-white/10 bg-white/5 p-4"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <div class="font-bold text-white">
                                    {{ rec.role }}
                                </div>
                                <StBadgeGlass
                                    :variant="
                                        getBadgeByStrategy(rec.strategy_type)
                                    "
                                    >{{ rec.strategy_type }}</StBadgeGlass
                                >
                            </div>
                            <div
                                class="mb-3 flex justify-between text-xs text-white/40"
                            >
                                <span
                                    >Demanda:
                                    <strong class="text-white/80">{{
                                        rec.demand
                                    }}</strong>
                                    FTE</span
                                >
                                <span
                                    >Oferta int:
                                    <strong class="text-white/80">{{
                                        rec.internal_supply
                                    }}</strong>
                                    FTE</span
                                >
                            </div>
                            <div
                                class="mt-auto rounded-lg border border-indigo-500/20 bg-indigo-500/10 px-3 py-2 text-xs text-indigo-300"
                            >
                                ➥ {{ rec.action }}
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex grow flex-col items-center justify-center rounded-2xl border border-dashed border-white/5 bg-white/5 p-8 text-center"
                    >
                        <PhCompass class="mb-3 text-white/10" :size="48" />
                        <p class="text-sm text-white/40">
                            Crea un escenario a la izquierda para ver las
                            estrategias generadas por Stratos AI.
                        </p>
                    </div>
                </StCardGlass>
            </div>
            <!-- Mis Escenarios -->
            <StCardGlass>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="mb-1 text-lg font-bold text-white">
                            <PhClipboardText
                                class="mr-2 inline text-indigo-300"
                                :size="20"
                            />Mis Escenarios
                        </h3>
                        <p class="text-xs text-white/40">
                            Selecciona uno para recargar el análisis sin crear
                            uno nuevo.
                        </p>
                    </div>
                    <StBadgeGlass v-if="scenarios.length" variant="glass">
                        {{ scenarios.length }}
                    </StBadgeGlass>
                </div>

                <!-- Skeleton escenarios -->
                <div v-if="loadingScenarios" class="animate-pulse space-y-3">
                    <div
                        v-for="n in 3"
                        :key="n"
                        class="h-14 rounded-2xl border border-white/10 bg-white/5"
                    ></div>
                </div>

                <!-- Lista de escenarios -->
                <div v-else-if="scenarios.length" class="space-y-3">
                    <div
                        v-for="sc in scenarios"
                        :key="sc.id"
                        class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 transition-colors"
                        :class="{
                            'border-indigo-500/50 bg-indigo-500/10':
                                currentScenarioId === String(sc.id),
                        }"
                    >
                        <div class="min-w-0 flex-1">
                            <p
                                class="truncate text-sm font-semibold text-white"
                            >
                                {{ sc.name }}
                            </p>
                            <p class="text-xs text-white/40">
                                {{ sc.status ?? 'draft' }}
                                <span v-if="sc.created_at">
                                    ·
                                    {{
                                        new Date(
                                            sc.created_at,
                                        ).toLocaleDateString('es-CL')
                                    }}</span
                                >
                            </p>
                        </div>
                        <StButtonGlass
                            variant="glass"
                            class="ml-4 shrink-0 text-xs"
                            :loading="
                                loadingResults &&
                                currentScenarioId === String(sc.id)
                            "
                            @click="loadScenario(String(sc.id))"
                        >
                            Cargar análisis
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Empty state -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/5 bg-white/5 p-8 text-center"
                >
                    <PhClipboardText class="mb-3 text-white/10" :size="40" />
                    <p class="text-sm text-white/40">
                        Aún no hay escenarios creados para tu organización.
                    </p>
                </div>
            </StCardGlass>

            <StCardGlass>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="mb-1 text-lg font-bold text-white">
                            <PhClipboardText
                                class="mr-2 inline text-indigo-300"
                                :size="20"
                            />Demand Lines
                        </h3>
                        <p class="text-xs text-white/40">
                            Carga demanda por unidad, rol y período para el
                            escenario activo.
                        </p>
                    </div>
                    <StBadgeGlass
                        :variant="currentScenarioId ? 'primary' : 'glass'"
                    >
                        {{
                            currentScenarioId
                                ? `Escenario #${currentScenarioId}`
                                : 'Sin escenario activo'
                        }}
                    </StBadgeGlass>
                </div>

                <div
                    class="mb-4 rounded-2xl border border-white/10 bg-white/5 p-4"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-xs font-bold tracking-widest text-white/50 uppercase"
                        >
                            Umbrales Semáforo
                        </p>
                        <StButtonGlass
                            id="workforce-save-thresholds"
                            v-if="canEditWorkforceThresholds"
                            variant="glass"
                            class="text-xs"
                            :loading="savingThresholds"
                            :disabled="
                                savingThresholds ||
                                recalculatingAfterThresholdSave
                            "
                            @click="saveWorkforceThresholds"
                        >
                            Guardar umbrales
                        </StButtonGlass>
                        <StBadgeGlass v-else variant="glass">
                            Solo lectura
                        </StBadgeGlass>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <label
                                for="threshold-coverage-success"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Cobertura OK (%)</label
                            >
                            <input
                                id="threshold-coverage-success"
                                v-model.number="
                                    thresholdsForm.coverage_success_min
                                "
                                type="number"
                                min="1"
                                max="300"
                                :disabled="!canEditWorkforceThresholds"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="threshold-coverage-warning"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Cobertura Medio (%)</label
                            >
                            <input
                                id="threshold-coverage-warning"
                                v-model.number="
                                    thresholdsForm.coverage_warning_min
                                "
                                type="number"
                                min="1"
                                max="300"
                                :disabled="!canEditWorkforceThresholds"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="threshold-gap-warning"
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Gap Medio máximo (%)</label
                            >
                            <input
                                id="threshold-gap-warning"
                                v-model.number="
                                    thresholdsForm.gap_warning_max_pct
                                "
                                type="number"
                                min="0"
                                max="100"
                                :disabled="!canEditWorkforceThresholds"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                    </div>
                    <div v-if="thresholdFeedback.message" class="mt-3">
                        <StBadgeGlass :variant="thresholdFeedback.variant">
                            {{ thresholdFeedback.message }}
                        </StBadgeGlass>
                    </div>
                    <div v-if="recalculatingAfterThresholdSave" class="mt-2">
                        <StBadgeGlass variant="primary">
                            Recalculando…
                        </StBadgeGlass>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
                    <div class="lg:col-span-1">
                        <label
                            for="demand-unit"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Unidad</label
                        >
                        <input
                            id="demand-unit"
                            v-model="demandLineForm.unit"
                            placeholder="Sales"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50': demandLineErrors.unit,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.unit"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.unit }}
                        </p>
                    </div>
                    <div class="lg:col-span-1">
                        <label
                            for="demand-role"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Rol</label
                        >
                        <input
                            id="demand-role"
                            v-model="demandLineForm.role_name"
                            placeholder="Account Executive"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50':
                                    demandLineErrors.role_name,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.role_name"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.role_name }}
                        </p>
                    </div>
                    <div class="lg:col-span-1">
                        <label
                            for="demand-period"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Período</label
                        >
                        <input
                            id="demand-period"
                            v-model="demandLineForm.period"
                            placeholder="2026-09"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50':
                                    demandLineErrors.period,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.period"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.period }}
                        </p>
                    </div>
                    <div class="lg:col-span-1">
                        <label
                            for="demand-volume"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Volumen</label
                        >
                        <input
                            id="demand-volume"
                            v-model.number="demandLineForm.volume_expected"
                            type="number"
                            min="0"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50':
                                    demandLineErrors.volume_expected,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.volume_expected"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.volume_expected }}
                        </p>
                    </div>
                    <div class="lg:col-span-1">
                        <label
                            for="demand-minutes"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Min/unidad</label
                        >
                        <input
                            id="demand-minutes"
                            v-model.number="
                                demandLineForm.time_standard_minutes
                            "
                            type="number"
                            min="1"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50':
                                    demandLineErrors.time_standard_minutes,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.time_standard_minutes"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.time_standard_minutes }}
                        </p>
                    </div>
                    <div class="lg:col-span-1">
                        <label
                            for="demand-coverage"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Cobertura %</label
                        >
                        <input
                            id="demand-coverage"
                            v-model.number="demandLineForm.coverage_target_pct"
                            type="number"
                            min="1"
                            max="200"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            :class="{
                                'ring-2 ring-red-500/50':
                                    demandLineErrors.coverage_target_pct,
                            }"
                        />
                        <p
                            v-if="demandLineErrors.coverage_target_pct"
                            class="mt-1 text-xs text-red-300"
                        >
                            {{ demandLineErrors.coverage_target_pct }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <StButtonGlass
                        id="workforce-add-demand-draft"
                        variant="glass"
                        :disabled="!canAddDemandLine"
                        @click="addDemandLineDraft"
                    >
                        Agregar al lote
                    </StButtonGlass>
                    <StButtonGlass
                        id="workforce-save-demand-lines"
                        variant="primary"
                        :disabled="
                            !currentScenarioId || !draftDemandLines.length
                        "
                        :loading="savingDemandLines"
                        @click="saveDemandLines"
                    >
                        Guardar líneas
                    </StButtonGlass>
                    <StBadgeGlass
                        v-if="draftDemandLines.length"
                        variant="warning"
                    >
                        {{ draftDemandLines.length }} en borrador
                    </StBadgeGlass>
                </div>

                <div v-if="demandLineFeedback.message" class="mt-3">
                    <StBadgeGlass :variant="demandLineFeedback.variant">
                        {{ demandLineFeedback.message }}
                    </StBadgeGlass>
                </div>

                <div
                    v-if="draftDemandLines.length"
                    class="mt-4 space-y-2 rounded-2xl border border-amber-500/20 bg-amber-500/5 p-4"
                >
                    <p
                        class="text-xs font-bold tracking-widest text-amber-300 uppercase"
                    >
                        Lote pendiente
                    </p>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                HH requeridas
                            </p>
                            <StBadgeGlass :variant="draftRequiredVariant">
                                {{ draftRequiredHhTotal }}
                            </StBadgeGlass>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                HH efectivas
                            </p>
                            <StBadgeGlass :variant="draftEffectiveVariant">
                                {{ draftEffectiveHhTotal }}
                            </StBadgeGlass>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                Cobertura
                            </p>
                            <p class="text-lg font-black text-white">
                                {{ draftCoveragePct }}%
                                <StBadgeGlass
                                    class="ml-2"
                                    :variant="draftCoverageVariant"
                                >
                                    {{ draftCoverageVariantLabel }}
                                </StBadgeGlass>
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3"
                        >
                            <p
                                class="text-[11px] tracking-widest text-white/40 uppercase"
                            >
                                Gap HH
                            </p>
                            <p class="text-lg font-black text-white">
                                {{ draftGapHh }}
                                <StBadgeGlass
                                    class="ml-2"
                                    :variant="draftGapVariant"
                                >
                                    {{ draftGapVariantLabel }}
                                </StBadgeGlass>
                            </p>
                        </div>
                    </div>
                    <div
                        v-for="(line, index) in draftDemandLines"
                        :key="`${line.unit}-${line.role_name}-${index}`"
                        class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/80"
                    >
                        <div>
                            <strong class="text-white">{{ line.unit }}</strong>
                            · {{ line.role_name }} · {{ line.period }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span>{{ line.volume_expected }} vol</span>
                            <span>{{ line.time_standard_minutes }} min</span>
                            <button
                                type="button"
                                class="text-xs text-rose-300 hover:text-rose-200"
                                @click="removeDraftDemandLine(index)"
                            >
                                Quitar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-xs font-bold tracking-widest text-white/50 uppercase"
                        >
                            Líneas persistidas
                        </p>
                        <StBadgeGlass v-if="demandLines.length" variant="glass">
                            {{ filteredDemandLines.length }} /
                            {{ demandLines.length }}
                            guardadas
                        </StBadgeGlass>
                    </div>

                    <div
                        v-if="demandLines.length"
                        class="mb-3 grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 md:grid-cols-5"
                    >
                        <div>
                            <label
                                for="filter-unit"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Filtrar unidad</label
                            >
                            <input
                                id="filter-unit"
                                v-model="persistedFilters.unit"
                                placeholder="Ej: Sales"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="filter-period"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Filtrar período</label
                            >
                            <input
                                id="filter-period"
                                v-model="persistedFilters.period"
                                placeholder="Ej: 2026-09"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="filter-page-size"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Filas por página</label
                            >
                            <select
                                id="filter-page-size"
                                v-model.number="persistedPageSize"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option :value="5" class="text-slate-900">
                                    5
                                </option>
                                <option :value="10" class="text-slate-900">
                                    10
                                </option>
                                <option :value="20" class="text-slate-900">
                                    20
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="filter-sort-field"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Ordenar por</label
                            >
                            <select
                                id="filter-sort-field"
                                v-model="persistedSort.field"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option value="period" class="text-slate-900">
                                    Período
                                </option>
                                <option value="unit" class="text-slate-900">
                                    Unidad
                                </option>
                                <option
                                    value="volume_expected"
                                    class="text-slate-900"
                                >
                                    Volumen
                                </option>
                                <option
                                    value="required_hh"
                                    class="text-slate-900"
                                >
                                    HH req.
                                </option>
                                <option
                                    value="effective_hh"
                                    class="text-slate-900"
                                >
                                    HH efect.
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                for="filter-sort-direction"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Dirección</label
                            >
                            <select
                                id="filter-sort-direction"
                                v-model="persistedSort.direction"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option value="asc" class="text-slate-900">
                                    Ascendente
                                </option>
                                <option value="desc" class="text-slate-900">
                                    Descendente
                                </option>
                            </select>
                        </div>
                    </div>

                    <div
                        v-if="loadingDemandLines"
                        class="animate-pulse space-y-3"
                    >
                        <div
                            class="h-14 rounded-2xl border border-white/10 bg-white/5"
                        ></div>
                        <div
                            class="h-14 rounded-2xl border border-white/10 bg-white/5"
                        ></div>
                    </div>

                    <div v-else-if="demandLines.length" class="space-y-3">
                        <div
                            v-if="!filteredDemandLines.length"
                            class="rounded-2xl border border-dashed border-white/5 bg-white/5 p-6 text-center text-sm text-white/40"
                        >
                            No hay líneas que coincidan con los filtros.
                        </div>

                        <div
                            v-for="line in paginatedDemandLines"
                            :key="line.id"
                            class="space-y-2"
                        >
                            <div
                                class="grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 md:grid-cols-7"
                            >
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        Unidad
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.unit }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        Rol
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.role_name }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        Período
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.period }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        Volumen
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.volume_expected }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        HH req.
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.required_hh }}
                                    </p>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] tracking-widest text-white/40 uppercase"
                                    >
                                        HH efect.
                                    </p>
                                    <p class="text-sm font-semibold text-white">
                                        {{ line.effective_hh }}
                                    </p>
                                </div>
                                <div
                                    class="flex items-center gap-2 md:justify-end"
                                >
                                    <StButtonGlass
                                        variant="glass"
                                        class="text-xs"
                                        @click="startEditDemandLine(line)"
                                    >
                                        Editar
                                    </StButtonGlass>
                                    <StButtonGlass
                                        variant="error"
                                        class="text-xs"
                                        :loading="isDeletingDemandLine(line.id)"
                                        @click="deleteDemandLine(line.id)"
                                    >
                                        Eliminar
                                    </StButtonGlass>
                                </div>
                            </div>

                            <div
                                v-if="editingDemandLineId === line.id"
                                class="rounded-2xl border border-indigo-500/30 bg-indigo-500/10 p-4"
                            >
                                <div
                                    class="grid grid-cols-1 gap-3 md:grid-cols-6"
                                >
                                    <input
                                        v-model="editingDemandLineForm.unit"
                                        placeholder="Unidad"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                    <input
                                        v-model="
                                            editingDemandLineForm.role_name
                                        "
                                        placeholder="Rol"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                    <input
                                        v-model="editingDemandLineForm.period"
                                        placeholder="YYYY-MM"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                    <input
                                        v-model.number="
                                            editingDemandLineForm.volume_expected
                                        "
                                        type="number"
                                        min="0"
                                        placeholder="Volumen"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                    <input
                                        v-model.number="
                                            editingDemandLineForm.time_standard_minutes
                                        "
                                        type="number"
                                        min="1"
                                        placeholder="Min/unidad"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                    <input
                                        v-model.number="
                                            editingDemandLineForm.coverage_target_pct
                                        "
                                        type="number"
                                        min="1"
                                        max="200"
                                        placeholder="Cobertura %"
                                        class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                    />
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <StButtonGlass
                                        variant="primary"
                                        class="text-xs"
                                        :loading="updatingDemandLine"
                                        @click="saveDemandLineEdit"
                                    >
                                        Guardar cambios
                                    </StButtonGlass>
                                    <StButtonGlass
                                        variant="glass"
                                        class="text-xs"
                                        @click="cancelEditDemandLine"
                                    >
                                        Cancelar
                                    </StButtonGlass>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="filteredDemandLines.length"
                            class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3"
                        >
                            <p class="text-xs text-white/50">
                                Página {{ persistedPage }} de
                                {{ totalPersistedPages }}
                            </p>
                            <div class="flex items-center gap-2">
                                <StButtonGlass
                                    variant="glass"
                                    class="text-xs"
                                    :disabled="persistedPage <= 1"
                                    @click="
                                        goToPersistedPage(persistedPage - 1)
                                    "
                                >
                                    Anterior
                                </StButtonGlass>
                                <StButtonGlass
                                    variant="glass"
                                    class="text-xs"
                                    :disabled="
                                        persistedPage >= totalPersistedPages
                                    "
                                    @click="
                                        goToPersistedPage(persistedPage + 1)
                                    "
                                >
                                    Siguiente
                                </StButtonGlass>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-2xl border border-dashed border-white/5 bg-white/5 p-6 text-center text-sm text-white/40"
                    >
                        Selecciona un escenario y guarda líneas de demanda para
                        ver el resumen aquí.
                    </div>
                </div>
            </StCardGlass>

            <StCardGlass>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="mb-1 text-lg font-bold text-white">
                            <PhClipboardText
                                class="mr-2 inline text-indigo-300"
                                :size="20"
                            />Action Plan
                        </h3>
                        <p class="text-xs text-white/40">
                            Ejecuta y monitorea acciones por escenario activo.
                        </p>
                    </div>
                    <StBadgeGlass
                        :variant="currentScenarioId ? 'primary' : 'glass'"
                    >
                        {{
                            currentScenarioId
                                ? `Escenario #${currentScenarioId}`
                                : 'Sin escenario activo'
                        }}
                    </StBadgeGlass>
                </div>

                <div
                    class="mb-4 grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 md:grid-cols-2 lg:grid-cols-6"
                >
                    <div class="lg:col-span-2">
                        <label
                            for="action-title"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Título</label
                        >
                        <input
                            id="action-title"
                            v-model="actionPlanForm.action_title"
                            placeholder="Ej: Capacitar célula comercial"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <div class="lg:col-span-2">
                        <label
                            for="action-description"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Descripción</label
                        >
                        <input
                            id="action-description"
                            v-model="actionPlanForm.description"
                            placeholder="Detalle opcional"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            for="action-priority"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Prioridad</label
                        >
                        <select
                            id="action-priority"
                            v-model="actionPlanForm.priority"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                        >
                            <option value="low" class="text-slate-900">
                                low
                            </option>
                            <option value="medium" class="text-slate-900">
                                medium
                            </option>
                            <option value="high" class="text-slate-900">
                                high
                            </option>
                            <option value="critical" class="text-slate-900">
                                critical
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            for="action-owner-user"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Owner user ID</label
                        >
                        <input
                            id="action-owner-user"
                            v-model.number="actionPlanForm.owner_user_id"
                            type="number"
                            min="1"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            for="action-due-date"
                            class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                            >Vence</label
                        >
                        <input
                            id="action-due-date"
                            v-model="actionPlanForm.due_date"
                            type="date"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="mb-4 flex justify-end">
                    <StButtonGlass
                        id="workforce-add-action"
                        variant="primary"
                        :disabled="!canCreateActionPlan"
                        :loading="creatingActionPlan"
                        @click="createActionPlan"
                    >
                        Agregar acción
                    </StButtonGlass>
                </div>

                <div v-if="actionPlanFeedback.message" class="mb-4">
                    <StBadgeGlass :variant="actionPlanFeedback.variant">
                        {{ actionPlanFeedback.message }}
                    </StBadgeGlass>
                </div>

                <div
                    v-if="executionDashboard"
                    class="mb-4 grid grid-cols-2 gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 md:grid-cols-3 lg:grid-cols-6"
                >
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            Total
                        </p>
                        <p class="text-lg font-black text-white">
                            {{ executionDashboard.total_actions }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            Completadas
                        </p>
                        <p class="text-lg font-black text-emerald-300">
                            {{ executionDashboard.completed_actions }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            En progreso
                        </p>
                        <p class="text-lg font-black text-indigo-300">
                            {{ executionDashboard.in_progress_actions }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            Bloqueadas
                        </p>
                        <p class="text-lg font-black text-rose-300">
                            {{ executionDashboard.blocked_actions }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            Vencidas
                        </p>
                        <p class="text-lg font-black text-amber-300">
                            {{ executionDashboard.overdue_actions }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[11px] tracking-widest text-white/40 uppercase"
                        >
                            Avance promedio
                        </p>
                        <p class="text-lg font-black text-white">
                            {{ executionDashboard.avg_progress_pct }}%
                        </p>
                    </div>
                </div>

                <div v-if="loadingActionPlan" class="animate-pulse space-y-3">
                    <div
                        class="h-14 rounded-2xl border border-white/10 bg-white/5"
                    ></div>
                    <div
                        class="h-14 rounded-2xl border border-white/10 bg-white/5"
                    ></div>
                </div>

                <div v-else-if="actionPlanItems.length" class="space-y-3">
                    <div
                        v-for="action in actionPlanItems"
                        :key="action.id"
                        class="grid grid-cols-1 gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 lg:grid-cols-12"
                    >
                        <div class="lg:col-span-4">
                            <p class="text-sm font-semibold text-white">
                                {{ action.action_title }}
                            </p>
                            <p class="text-xs text-white/40">
                                {{ action.description || 'Sin descripción' }}
                            </p>
                        </div>
                        <div class="lg:col-span-2">
                            <label
                                :for="`action-status-${action.id}`"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Estado</label
                            >
                            <select
                                :id="`action-status-${action.id}`"
                                v-model="action.status"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option value="planned" class="text-slate-900">
                                    planned
                                </option>
                                <option
                                    value="in_progress"
                                    class="text-slate-900"
                                >
                                    in_progress
                                </option>
                                <option value="blocked" class="text-slate-900">
                                    blocked
                                </option>
                                <option
                                    value="completed"
                                    class="text-slate-900"
                                >
                                    completed
                                </option>
                                <option
                                    value="cancelled"
                                    class="text-slate-900"
                                >
                                    cancelled
                                </option>
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label
                                :for="`action-priority-${action.id}`"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Prioridad</label
                            >
                            <select
                                :id="`action-priority-${action.id}`"
                                v-model="action.priority"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            >
                                <option value="low" class="text-slate-900">
                                    low
                                </option>
                                <option value="medium" class="text-slate-900">
                                    medium
                                </option>
                                <option value="high" class="text-slate-900">
                                    high
                                </option>
                                <option value="critical" class="text-slate-900">
                                    critical
                                </option>
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label
                                :for="`action-progress-${action.id}`"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Progreso %</label
                            >
                            <input
                                :id="`action-progress-${action.id}`"
                                v-model.number="action.progress_pct"
                                type="number"
                                min="0"
                                max="100"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div class="lg:col-span-2">
                            <label
                                :for="`action-owner-${action.id}`"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Owner user ID</label
                            >
                            <input
                                :id="`action-owner-${action.id}`"
                                v-model.number="action.owner_user_id"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div class="lg:col-span-2">
                            <label
                                :for="`action-due-date-${action.id}`"
                                class="mb-1 block text-[11px] tracking-widest text-white/40 uppercase"
                                >Vence</label
                            >
                            <input
                                :id="`action-due-date-${action.id}`"
                                v-model="action.due_date"
                                type="date"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div
                            class="flex items-end justify-start lg:col-span-12 lg:justify-end"
                        >
                            <StButtonGlass
                                variant="glass"
                                class="text-xs"
                                :loading="isUpdatingActionPlan(action.id)"
                                @click="updateActionPlan(action)"
                            >
                                Guardar
                            </StButtonGlass>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-white/5 bg-white/5 p-6 text-center text-sm text-white/40"
                >
                    Sin acciones aún. Crea la primera para iniciar la ejecución.
                </div>
            </StCardGlass>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import {
    PhChartPolar,
    PhClipboardText,
    PhCompass,
    PhPulse,
    PhRoadHorizon,
    PhRocketLaunch,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref, watch } from 'vue';

type PersistedSortField =
    | 'period'
    | 'unit'
    | 'volume_expected'
    | 'required_hh'
    | 'effective_hh';
type SortDirection = 'asc' | 'desc';
type ThresholdFeedbackVariant = 'success' | 'error' | 'warning' | 'glass';
type PlanningContext = 'baseline' | 'scenario';
type Nullable<T> = T | null;

const layoutProps = {
    title: 'Workforce Planning',
};

const loading = ref(false);
const loadingResults = ref(false);
const api = useApi();
const page = usePage();

const canEditWorkforceThresholds = computed(() => {
    const auth: any = page.props?.auth ?? {};
    const role = auth?.role ?? auth?.user?.role ?? null;

    return role === 'admin' || role === 'hr_leader';
});

const form = ref({
    name: '',
    growth_percentage: null,
    timeframe_start: '',
    timeframe_end: '',
});

const recommendations = ref<any[]>([]);
const scenarios = ref<any[]>([]);
const loadingScenarios = ref(false);
const demandLines = ref<any[]>([]);
const draftDemandLines = ref<any[]>([]);
const loadingDemandLines = ref(false);
const savingDemandLines = ref(false);
const updatingDemandLine = ref(false);
const deletingDemandLineIds = ref<number[]>([]);
const editingDemandLineId = ref<Nullable<number>>(null);
const persistedFilters = ref({
    unit: '',
    period: '',
});
const persistedSort = ref<{
    field: PersistedSortField;
    direction: SortDirection;
}>({
    field: 'period',
    direction: 'asc',
});
const persistedPage = ref(1);
const persistedPageSize = ref(10);
const persistedDemandLinesViewStorageKey = 'wfp_demand_lines_view';
const isHydratingPersistedDemandLinesView = ref(false);
const editingDemandLineForm = ref({
    unit: '',
    role_name: '',
    period: '',
    volume_expected: 0,
    time_standard_minutes: 60,
    coverage_target_pct: 95,
});
const workforceThresholds = ref<any>(null);
const savingThresholds = ref(false);
const recalculatingAfterThresholdSave = ref(false);
const thresholdFeedback = ref<{
    message: string;
    variant: ThresholdFeedbackVariant;
}>({
    message: '',
    variant: 'glass',
});
let thresholdFeedbackTimeout: Nullable<ReturnType<typeof setTimeout>> = null;
const thresholdsForm = ref({
    coverage_success_min: 100,
    coverage_warning_min: 90,
    gap_warning_max_pct: 10,
});
const activeKpis = ref<any>(null);
const planningContext = ref<PlanningContext>(
    (localStorage.getItem('wfp_context') as PlanningContext) ?? 'scenario',
);
const analysisSummary = ref<any>(null);
const baselineDelta = ref<any>(null);
const impactDelta = ref<any>(null);
const currentScenarioId = ref<Nullable<string>>(null);
const demandLineForm = ref({
    unit: '',
    role_name: '',
    period: '',
    volume_expected: 0,
    time_standard_minutes: 60,
    productivity_factor: 1,
    coverage_target_pct: 95,
    attrition_pct: 0,
    ramp_factor: 1,
    notes: '',
});
const demandLineErrors = ref<Record<string, string>>({});
const demandLineFeedback = ref<{
    message: string;
    variant: ThresholdFeedbackVariant;
}>({
    message: '',
    variant: 'glass',
});
let demandLineFeedbackTimeout: Nullable<ReturnType<typeof setTimeout>> = null;
const actionPlanItems = ref<any[]>([]);
const loadingActionPlan = ref(false);
const creatingActionPlan = ref(false);
const updatingActionPlanIds = ref<number[]>([]);
const executionDashboard = ref<any>(null);
const actionPlanForm = ref({
    action_title: '',
    description: '',
    owner_user_id: null,
    priority: 'medium',
    due_date: '',
});
const actionPlanFeedback = ref<{
    message: string;
    variant: ThresholdFeedbackVariant;
}>({
    message: '',
    variant: 'glass',
});
let actionPlanFeedbackTimeout: Nullable<ReturnType<typeof setTimeout>> = null;

const canCreateActionPlan = computed(() => {
    return (
        Boolean(currentScenarioId.value) &&
        actionPlanForm.value.action_title.trim().length > 0
    );
});

function isUpdatingActionPlan(actionId: number): boolean {
    return updatingActionPlanIds.value.includes(Number(actionId));
}

function resetActionPlanForm(): void {
    actionPlanForm.value = {
        action_title: '',
        description: '',
        owner_user_id: null,
        priority: 'medium',
        due_date: '',
    };
}

function setActionPlanFeedback(
    message: string,
    variant: ThresholdFeedbackVariant,
): void {
    actionPlanFeedback.value = {
        message,
        variant,
    };

    if (actionPlanFeedbackTimeout) {
        clearTimeout(actionPlanFeedbackTimeout);
        actionPlanFeedbackTimeout = null;
    }

    if (message) {
        actionPlanFeedbackTimeout = setTimeout(() => {
            actionPlanFeedback.value = {
                message: '',
                variant: 'glass',
            };
            actionPlanFeedbackTimeout = null;
        }, 3500);
    }
}

function resetDemandLineForm(): void {
    demandLineForm.value = {
        unit: '',
        role_name: '',
        period: '',
        volume_expected: 0,
        time_standard_minutes: 60,
        productivity_factor: 1,
        coverage_target_pct: 95,
        attrition_pct: 0,
        ramp_factor: 1,
        notes: '',
    };

    demandLineErrors.value = {};
}

function canParsePeriod(period: string): boolean {
    return /^\d{4}-(0[1-9]|1[0-2])$/.test(period);
}

function setDemandLineFeedback(
    message: string,
    variant: ThresholdFeedbackVariant,
): void {
    demandLineFeedback.value = {
        message,
        variant,
    };

    if (demandLineFeedbackTimeout) {
        clearTimeout(demandLineFeedbackTimeout);
        demandLineFeedbackTimeout = null;
    }

    if (message) {
        demandLineFeedbackTimeout = setTimeout(() => {
            demandLineFeedback.value = {
                message: '',
                variant: 'glass',
            };
            demandLineFeedbackTimeout = null;
        }, 4000);
    }
}

function validateDemandLineForm(): boolean {
    const nextErrors: Record<string, string> = {};

    if (!demandLineForm.value.unit.trim()) {
        nextErrors.unit = 'La unidad es obligatoria.';
    }

    if (!demandLineForm.value.role_name.trim()) {
        nextErrors.role_name = 'El rol es obligatorio.';
    }

    if (!canParsePeriod(demandLineForm.value.period.trim())) {
        nextErrors.period = 'El período debe usar formato YYYY-MM.';
    }

    if (Number(demandLineForm.value.volume_expected) < 0) {
        nextErrors.volume_expected = 'El volumen no puede ser negativo.';
    }

    if (Number(demandLineForm.value.time_standard_minutes) <= 0) {
        nextErrors.time_standard_minutes = 'Min/unidad debe ser mayor a 0.';
    }

    const coverage = Number(demandLineForm.value.coverage_target_pct);
    if (coverage < 1 || coverage > 200) {
        nextErrors.coverage_target_pct = 'Cobertura debe estar entre 1 y 200.';
    }

    demandLineErrors.value = nextErrors;

    return Object.keys(nextErrors).length === 0;
}

function getCanAddDemandLine(): boolean {
    return (
        demandLineForm.value.unit.trim().length > 0 &&
        demandLineForm.value.role_name.trim().length > 0 &&
        canParsePeriod(demandLineForm.value.period.trim()) &&
        demandLineForm.value.volume_expected >= 0 &&
        demandLineForm.value.time_standard_minutes > 0
    );
}

const canAddDemandLine = computed(() => getCanAddDemandLine());

const filteredDemandLines = computed(() => {
    const unitFilter = persistedFilters.value.unit.trim().toLowerCase();
    const periodFilter = persistedFilters.value.period.trim().toLowerCase();

    return demandLines.value.filter((line: any) => {
        const unit = String(line?.unit ?? '').toLowerCase();
        const period = String(line?.period ?? '').toLowerCase();

        const unitMatches = !unitFilter || unit.includes(unitFilter);
        const periodMatches = !periodFilter || period.includes(periodFilter);

        return unitMatches && periodMatches;
    });
});

const sortedDemandLines = computed(() => {
    const field = persistedSort.value.field;
    const direction = persistedSort.value.direction;

    return [...filteredDemandLines.value].sort((left: any, right: any) => {
        const leftValue = left?.[field];
        const rightValue = right?.[field];

        let comparison = 0;
        if (field === 'period' || field === 'unit') {
            comparison = String(leftValue ?? '').localeCompare(
                String(rightValue ?? ''),
                undefined,
                { sensitivity: 'base' },
            );
        } else {
            comparison = Number(leftValue ?? 0) - Number(rightValue ?? 0);
        }

        return direction === 'asc' ? comparison : -comparison;
    });
});

const totalPersistedPages = computed(() => {
    if (!sortedDemandLines.value.length) {
        return 1;
    }

    return Math.max(
        1,
        Math.ceil(sortedDemandLines.value.length / persistedPageSize.value),
    );
});

const paginatedDemandLines = computed(() => {
    const start = (persistedPage.value - 1) * persistedPageSize.value;
    const end = start + persistedPageSize.value;

    return sortedDemandLines.value.slice(start, end);
});

function goToPersistedPage(page: number): void {
    if (page < 1) {
        persistedPage.value = 1;

        return;
    }

    if (page > totalPersistedPages.value) {
        persistedPage.value = totalPersistedPages.value;

        return;
    }

    persistedPage.value = page;
}

function restorePersistedDemandLinesView(): void {
    const raw = localStorage.getItem(persistedDemandLinesViewStorageKey);
    if (!raw) {
        return;
    }

    try {
        const parsed = JSON.parse(raw) as {
            unit?: string;
            period?: string;
            sortField?: PersistedSortField;
            sortDirection?: SortDirection;
            page?: number;
            pageSize?: number;
        };

        isHydratingPersistedDemandLinesView.value = true;

        persistedFilters.value.unit = String(parsed.unit ?? '');
        persistedFilters.value.period = String(parsed.period ?? '');

        const allowedSortFields = [
            'period',
            'unit',
            'volume_expected',
            'required_hh',
            'effective_hh',
        ];
        const normalizedSortField = String(parsed.sortField ?? 'period');
        if (allowedSortFields.includes(normalizedSortField)) {
            persistedSort.value.field =
                normalizedSortField as PersistedSortField;
        }

        const normalizedSortDirection = String(parsed.sortDirection ?? 'asc');
        if (
            normalizedSortDirection === 'asc' ||
            normalizedSortDirection === 'desc'
        ) {
            persistedSort.value.direction = normalizedSortDirection;
        }

        const normalizedPageSize = Number(parsed.pageSize ?? 10);
        if ([5, 10, 20].includes(normalizedPageSize)) {
            persistedPageSize.value = normalizedPageSize;
        }

        const normalizedPage = Number(parsed.page ?? 1);
        if (Number.isFinite(normalizedPage) && normalizedPage >= 1) {
            persistedPage.value = Math.floor(normalizedPage);
        }
    } catch {
        localStorage.removeItem(persistedDemandLinesViewStorageKey);
    } finally {
        isHydratingPersistedDemandLinesView.value = false;
    }
}

function persistDemandLinesView(): void {
    localStorage.setItem(
        persistedDemandLinesViewStorageKey,
        JSON.stringify({
            unit: persistedFilters.value.unit,
            period: persistedFilters.value.period,
            sortField: persistedSort.value.field,
            sortDirection: persistedSort.value.direction,
            page: persistedPage.value,
            pageSize: persistedPageSize.value,
        }),
    );
}

function roundToTwoDecimals(value: number): number {
    return Math.round((value + Number.EPSILON) * 100) / 100;
}

function requiredHhFromLine(line: any): number {
    const volumeExpected = Number(line?.volume_expected ?? 0);
    const timeStandardMinutes = Number(line?.time_standard_minutes ?? 0);

    return roundToTwoDecimals((volumeExpected * timeStandardMinutes) / 60);
}

function effectiveHhFromLine(line: any): number {
    const requiredHh = requiredHhFromLine(line);
    const coverageFactor = Number(line?.coverage_target_pct ?? 0) / 100;
    const productivityFactor = Math.max(
        Number(line?.productivity_factor ?? 1),
        0.1,
    );
    const rampFactor = Math.max(Number(line?.ramp_factor ?? 1), 0.1);

    return roundToTwoDecimals(
        (requiredHh * coverageFactor) / productivityFactor / rampFactor,
    );
}

const draftRequiredHhTotal = computed(() =>
    roundToTwoDecimals(
        draftDemandLines.value.reduce((accumulator: number, line: any) => {
            return accumulator + requiredHhFromLine(line);
        }, 0),
    ),
);

const draftEffectiveHhTotal = computed(() =>
    roundToTwoDecimals(
        draftDemandLines.value.reduce((accumulator: number, line: any) => {
            return accumulator + effectiveHhFromLine(line);
        }, 0),
    ),
);

const draftCoveragePct = computed(() => {
    if (draftRequiredHhTotal.value <= 0) {
        return 0;
    }

    return roundToTwoDecimals(
        (draftEffectiveHhTotal.value / draftRequiredHhTotal.value) * 100,
    );
});

const draftGapHh = computed(() =>
    roundToTwoDecimals(
        draftRequiredHhTotal.value - draftEffectiveHhTotal.value,
    ),
);

const draftEffectiveMeetsRequired = computed(
    () =>
        draftRequiredHhTotal.value > 0 &&
        draftEffectiveHhTotal.value >= draftRequiredHhTotal.value,
);

const draftRequiredVariant = computed(() => {
    if (draftRequiredHhTotal.value <= 0) {
        return 'glass';
    }

    return draftEffectiveMeetsRequired.value ? 'success' : 'warning';
});

const draftEffectiveVariant = computed(() => {
    if (draftRequiredHhTotal.value <= 0) {
        return 'glass';
    }

    return draftEffectiveMeetsRequired.value ? 'success' : 'error';
});

const draftCoverageVariant = computed(() => {
    const successMin = Number(workforceThresholds.value?.coverage?.success_min);
    const warningMin = Number(workforceThresholds.value?.coverage?.warning_min);

    if (!Number.isFinite(successMin) || !Number.isFinite(warningMin)) {
        return 'glass';
    }

    if (draftCoveragePct.value >= successMin) {
        return 'success';
    }
    if (draftCoveragePct.value >= warningMin) {
        return 'warning';
    }

    return 'error';
});

const draftCoverageVariantLabel = computed(() => {
    if (draftCoverageVariant.value === 'success') {
        return 'OK';
    }
    if (draftCoverageVariant.value === 'warning') {
        return 'Medio';
    }
    if (draftCoverageVariant.value === 'glass') {
        return 'N/A';
    }

    return 'Crítico';
});

const draftGapVariant = computed(() => {
    const warningMaxPct = Number(
        workforceThresholds.value?.gap?.warning_max_pct,
    );

    if (!Number.isFinite(warningMaxPct)) {
        return 'glass';
    }

    if (draftGapHh.value <= 0) {
        return 'success';
    }

    const gapRatio =
        draftRequiredHhTotal.value > 0
            ? (draftGapHh.value / draftRequiredHhTotal.value) * 100
            : 0;

    if (gapRatio <= warningMaxPct) {
        return 'warning';
    }

    return 'error';
});

const draftGapVariantLabel = computed(() => {
    if (draftGapVariant.value === 'success') {
        return 'OK';
    }
    if (draftGapVariant.value === 'warning') {
        return 'Medio';
    }
    if (draftGapVariant.value === 'glass') {
        return 'N/A';
    }

    return 'Crítico';
});

async function createScenario() {
    loading.value = true;
    try {
        const res: any = await api.post(
            '/api/strategic-planning/scenarios',
            form.value,
        );

        const scenarioId =
            res?.data?.id ??
            res?.data?.scenario?.id ??
            res?.scenario?.id ??
            res?.id;

        if (scenarioId) {
            currentScenarioId.value = String(scenarioId);
            await Promise.all([
                fetchRecommendations(String(scenarioId)),
                fetchScenarios(),
                fetchDemandLines(String(scenarioId)),
            ]);
        }
    } catch (err) {
        console.error('Error creating scenario', err);
    } finally {
        loading.value = false;
    }
}

async function fetchScenarios(): Promise<void> {
    loadingScenarios.value = true;
    try {
        const res: any = await api.get('/api/strategic-planning/scenarios');
        scenarios.value = res?.data ?? res ?? [];
    } catch (err) {
        console.error('Error fetching scenarios', err);
    } finally {
        loadingScenarios.value = false;
    }
}

async function fetchWorkforceThresholds(): Promise<void> {
    try {
        const res: any = await api.get(
            '/api/strategic-planning/workforce-planning/thresholds',
        );
        workforceThresholds.value = res?.data ?? res ?? null;

        thresholdsForm.value = {
            coverage_success_min: Number(
                workforceThresholds.value?.coverage?.success_min ?? 100,
            ),
            coverage_warning_min: Number(
                workforceThresholds.value?.coverage?.warning_min ?? 90,
            ),
            gap_warning_max_pct: Number(
                workforceThresholds.value?.gap?.warning_max_pct ?? 10,
            ),
        };
    } catch (err) {
        console.error('Error fetching workforce thresholds', err);
        workforceThresholds.value = null;
    }
}

async function saveWorkforceThresholds(): Promise<void> {
    if (!canEditWorkforceThresholds.value) {
        return;
    }

    thresholdFeedback.value = {
        message: '',
        variant: 'glass',
    };
    if (thresholdFeedbackTimeout) {
        clearTimeout(thresholdFeedbackTimeout);
        thresholdFeedbackTimeout = null;
    }

    savingThresholds.value = true;
    try {
        const res: any = await api.patch(
            '/api/strategic-planning/workforce-planning/thresholds',
            {
                coverage: {
                    success_min: Number(
                        thresholdsForm.value.coverage_success_min,
                    ),
                    warning_min: Number(
                        thresholdsForm.value.coverage_warning_min,
                    ),
                },
                gap: {
                    warning_max_pct: Number(
                        thresholdsForm.value.gap_warning_max_pct,
                    ),
                },
            },
        );

        workforceThresholds.value = res?.data ?? res ?? null;
        thresholdFeedback.value = {
            message: 'Umbrales actualizados correctamente.',
            variant: 'success',
        };

        if (currentScenarioId.value) {
            recalculatingAfterThresholdSave.value = true;
            try {
                await fetchRecommendations(currentScenarioId.value);
            } finally {
                recalculatingAfterThresholdSave.value = false;
            }
        }

        thresholdFeedbackTimeout = setTimeout(() => {
            thresholdFeedback.value = {
                message: '',
                variant: 'glass',
            };
            thresholdFeedbackTimeout = null;
        }, 4000);
    } catch (err) {
        console.error('Error saving workforce thresholds', err);
        thresholdFeedback.value = {
            message: 'No se pudieron guardar los umbrales. Intenta nuevamente.',
            variant: 'error',
        };
        thresholdFeedbackTimeout = setTimeout(() => {
            thresholdFeedback.value = {
                message: '',
                variant: 'glass',
            };
            thresholdFeedbackTimeout = null;
        }, 4000);
    } finally {
        savingThresholds.value = false;
    }
}

async function loadScenario(scenarioId: string): Promise<void> {
    currentScenarioId.value = scenarioId;
    await Promise.all([
        fetchRecommendations(scenarioId),
        fetchDemandLines(scenarioId),
        fetchActionPlan(scenarioId),
        fetchExecutionDashboard(scenarioId),
    ]);
}

async function fetchActionPlan(scenarioId: string): Promise<void> {
    loadingActionPlan.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/action-plan`,
        );
        actionPlanItems.value = res?.data?.actions ?? res?.actions ?? [];
    } catch (err) {
        console.error('Error fetching action plan', err);
        actionPlanItems.value = [];
    } finally {
        loadingActionPlan.value = false;
    }
}

async function fetchExecutionDashboard(scenarioId: string): Promise<void> {
    try {
        const res: any = await api.get(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/execution-dashboard`,
        );
        executionDashboard.value = res?.data?.summary ?? res?.summary ?? null;
    } catch (err) {
        console.error('Error fetching execution dashboard', err);
        executionDashboard.value = null;
    }
}

async function createActionPlan(): Promise<void> {
    if (!currentScenarioId.value || !canCreateActionPlan.value) {
        return;
    }

    creatingActionPlan.value = true;
    try {
        setActionPlanFeedback('', 'glass');

        await api.post(
            `/api/strategic-planning/workforce-planning/scenarios/${currentScenarioId.value}/action-plan`,
            {
                action_title: actionPlanForm.value.action_title.trim(),
                description: actionPlanForm.value.description.trim() || null,
                owner_user_id: actionPlanForm.value.owner_user_id || null,
                priority: actionPlanForm.value.priority,
                due_date: actionPlanForm.value.due_date || null,
                status: 'planned',
                progress_pct: 0,
            },
        );

        resetActionPlanForm();
        await Promise.all([
            fetchActionPlan(currentScenarioId.value),
            fetchExecutionDashboard(currentScenarioId.value),
        ]);

        setActionPlanFeedback('Acción creada correctamente.', 'success');
    } catch (err) {
        console.error('Error creating action plan item', err);
        setActionPlanFeedback('No se pudo crear la acción.', 'error');
    } finally {
        creatingActionPlan.value = false;
    }
}

async function updateActionPlan(action: any): Promise<void> {
    if (!currentScenarioId.value || !action?.id) {
        return;
    }

    const normalizedId = Number(action.id);
    updatingActionPlanIds.value.push(normalizedId);

    try {
        setActionPlanFeedback('', 'glass');

        await api.patch(
            `/api/strategic-planning/workforce-planning/scenarios/${currentScenarioId.value}/action-plan/${normalizedId}`,
            {
                status: action.status,
                priority: action.priority,
                progress_pct: Number(action.progress_pct ?? 0),
                owner_user_id: action.owner_user_id || null,
                due_date: action.due_date || null,
            },
        );

        await Promise.all([
            fetchActionPlan(currentScenarioId.value),
            fetchExecutionDashboard(currentScenarioId.value),
        ]);

        setActionPlanFeedback('Acción actualizada correctamente.', 'success');
    } catch (err) {
        console.error('Error updating action plan item', err);
        setActionPlanFeedback('No se pudo actualizar la acción.', 'error');
    } finally {
        updatingActionPlanIds.value = updatingActionPlanIds.value.filter(
            (id) => id !== normalizedId,
        );
    }
}

async function fetchDemandLines(scenarioId: string): Promise<void> {
    loadingDemandLines.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/demand-lines`,
        );
        demandLines.value = res?.data?.lines ?? res?.lines ?? [];
    } catch (err) {
        console.error('Error fetching demand lines', err);
        demandLines.value = [];
        setDemandLineFeedback(
            'No se pudieron cargar las líneas de demanda.',
            'error',
        );
    } finally {
        loadingDemandLines.value = false;
    }
}

function startEditDemandLine(line: any): void {
    editingDemandLineId.value = Number(line.id);
    editingDemandLineForm.value = {
        unit: line.unit ?? '',
        role_name: line.role_name ?? '',
        period: line.period ?? '',
        volume_expected: Number(line.volume_expected ?? 0),
        time_standard_minutes: Number(line.time_standard_minutes ?? 60),
        coverage_target_pct: Number(line.coverage_target_pct ?? 95),
    };
}

function cancelEditDemandLine(): void {
    editingDemandLineId.value = null;
}

function isDeletingDemandLine(lineId: number): boolean {
    return deletingDemandLineIds.value.includes(Number(lineId));
}

async function saveDemandLineEdit(): Promise<void> {
    if (!currentScenarioId.value || !editingDemandLineId.value) {
        return;
    }

    updatingDemandLine.value = true;
    try {
        await api.patch(
            `/api/strategic-planning/workforce-planning/scenarios/${currentScenarioId.value}/demand-lines/${editingDemandLineId.value}`,
            {
                ...editingDemandLineForm.value,
            },
        );

        await Promise.all([
            fetchDemandLines(currentScenarioId.value),
            fetchRecommendations(currentScenarioId.value),
        ]);

        editingDemandLineId.value = null;
    } catch (err) {
        console.error('Error updating demand line', err);
        setDemandLineFeedback(
            (err as any)?.friendlyMessage ??
                'No se pudo actualizar la línea de demanda.',
            'error',
        );
    } finally {
        updatingDemandLine.value = false;
    }
}

async function deleteDemandLine(lineId: number): Promise<void> {
    if (!currentScenarioId.value) {
        return;
    }

    const confirmed = globalThis.confirm('¿Eliminar esta línea de demanda?');
    if (!confirmed) {
        return;
    }

    const normalizedId = Number(lineId);
    deletingDemandLineIds.value.push(normalizedId);
    try {
        await api.delete(
            `/api/strategic-planning/workforce-planning/scenarios/${currentScenarioId.value}/demand-lines/${normalizedId}`,
        );

        await Promise.all([
            fetchDemandLines(currentScenarioId.value),
            fetchRecommendations(currentScenarioId.value),
        ]);

        if (editingDemandLineId.value === normalizedId) {
            editingDemandLineId.value = null;
        }
    } catch (err) {
        console.error('Error deleting demand line', err);
        setDemandLineFeedback(
            (err as any)?.friendlyMessage ??
                'No se pudo eliminar la línea de demanda.',
            'error',
        );
    } finally {
        deletingDemandLineIds.value = deletingDemandLineIds.value.filter(
            (id) => id !== normalizedId,
        );
    }
}

function addDemandLineDraft(): void {
    if (!validateDemandLineForm()) {
        setDemandLineFeedback(
            'Corrige los campos marcados antes de agregar al lote.',
            'warning',
        );

        return;
    }

    setDemandLineFeedback('', 'glass');

    draftDemandLines.value.push({
        ...demandLineForm.value,
        unit: demandLineForm.value.unit.trim(),
        role_name: demandLineForm.value.role_name.trim(),
        period: demandLineForm.value.period.trim(),
    });

    resetDemandLineForm();
}

function removeDraftDemandLine(index: number): void {
    draftDemandLines.value.splice(index, 1);
}

async function saveDemandLines(): Promise<void> {
    if (!currentScenarioId.value || !draftDemandLines.value.length) {
        setDemandLineFeedback(
            'Selecciona un escenario y agrega líneas al lote.',
            'warning',
        );

        return;
    }

    savingDemandLines.value = true;
    try {
        setDemandLineFeedback('', 'glass');

        await api.post(
            `/api/strategic-planning/workforce-planning/scenarios/${currentScenarioId.value}/demand-lines`,
            {
                lines: draftDemandLines.value,
            },
        );

        draftDemandLines.value = [];

        await Promise.all([
            fetchDemandLines(currentScenarioId.value),
            fetchRecommendations(currentScenarioId.value),
        ]);

        setDemandLineFeedback('Líneas guardadas correctamente.', 'success');
    } catch (err) {
        console.error('Error saving demand lines', err);
        setDemandLineFeedback(
            (err as any)?.friendlyMessage ??
                'No se pudieron guardar las líneas de demanda.',
            'error',
        );
    } finally {
        savingDemandLines.value = false;
    }
}

async function fetchRecommendations(scenarioId: string) {
    loadingResults.value = true;
    try {
        const recRes: any = await api.get(
            `/api/workforce-planning/scenarios/${scenarioId}/recommendations`,
        );
        if (recRes?.recommendations) {
            recommendations.value = recRes.recommendations;
        }
        if (recRes?.kpis) {
            activeKpis.value = recRes.kpis;
        }

        const analysisRes: any = await api.post(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/analyze`,
            {
                planning_context: planningContext.value,
            },
        );
        analysisSummary.value =
            analysisRes?.data?.summary ?? analysisRes?.summary ?? null;

        const deltaRes: any = await api.post(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/compare-baseline`,
        );
        baselineDelta.value = deltaRes?.data?.delta ?? deltaRes?.delta ?? null;

        const impactRes: any = await api.post(
            `/api/strategic-planning/workforce-planning/scenarios/${scenarioId}/compare-baseline-impact`,
        );
        impactDelta.value = impactRes?.data?.delta ?? impactRes?.delta ?? null;
    } catch (err) {
        console.error('Error fetching recommendations', err);
    } finally {
        loadingResults.value = false;
    }
}

function getBadgeByStrategy(strategy: string) {
    switch (strategy.toUpperCase()) {
        case 'BUILD':
            return 'success';
        case 'BUY':
            return 'primary';
        case 'BORROW':
            return 'warning';
        case 'BOT':
            return 'error';
        default:
            return 'glass';
    }
}

function formatSigned(value: number): string {
    if (value > 0) {
        return `+${value}`;
    }

    return `${value}`;
}

function deltaBadgeVariant(
    value: number,
    positiveIsGood: boolean,
): ThresholdFeedbackVariant {
    if (value === 0) {
        return 'glass';
    }

    if (positiveIsGood) {
        return value > 0 ? 'success' : 'error';
    }

    return value < 0 ? 'success' : 'error';
}

function riskLevelVariant(riskLevel: string): ThresholdFeedbackVariant {
    switch (riskLevel) {
        case 'lower':
            return 'success';
        case 'higher':
            return 'error';
        case 'stable':
            return 'glass';
        default:
            return 'warning';
    }
}

onMounted(() => {
    restorePersistedDemandLinesView();
    fetchScenarios();
    fetchWorkforceThresholds();
});

watch(planningContext, async (newValue) => {
    localStorage.setItem('wfp_context', newValue);
    if (currentScenarioId.value) {
        await fetchRecommendations(currentScenarioId.value);
    }
});

watch(
    [
        () => persistedFilters.value.unit,
        () => persistedFilters.value.period,
        () => persistedPageSize.value,
        () => persistedSort.value.field,
        () => persistedSort.value.direction,
    ],
    () => {
        if (isHydratingPersistedDemandLinesView.value) {
            return;
        }

        persistedPage.value = 1;
    },
);

watch(
    () => filteredDemandLines.value.length,
    () => {
        if (persistedPage.value > totalPersistedPages.value) {
            persistedPage.value = totalPersistedPages.value;
        }
    },
);

watch(
    [
        () => persistedFilters.value.unit,
        () => persistedFilters.value.period,
        () => persistedSort.value.field,
        () => persistedSort.value.direction,
        () => persistedPage.value,
        () => persistedPageSize.value,
    ],
    () => {
        if (isHydratingPersistedDemandLinesView.value) {
            return;
        }

        persistDemandLinesView();
    },
);
</script>

<style scoped>
/* Scoped styles */
</style>
