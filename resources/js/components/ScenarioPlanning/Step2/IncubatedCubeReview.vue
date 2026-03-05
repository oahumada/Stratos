<template>
    <div
        class="incubated-cube-review animate-in space-y-8 duration-700 fade-in slide-in-from-bottom-4"
    >
        <!-- Dashboard Header -->
        <div class="cube-review-header">
            <StCardGlass variant="glass" class="overflow-hidden p-0!">
                <div
                    class="flex flex-col border-b border-white/5 bg-white/5 p-6 md:flex-row md:items-center md:gap-6"
                >
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                    >
                        <v-icon color="indigo-300" size="32"
                            >mdi-cube-scan</v-icon
                        >
                    </div>
                    <div class="mt-4 flex-1 md:mt-0">
                        <h4
                            class="mb-1 text-xl font-black tracking-tight text-white"
                        >
                            Role Engineering & Organizational Cube
                        </h4>
                        <p
                            class="max-w-3xl text-sm leading-relaxed font-medium text-white/50"
                        >
                            Validating tridimensional coherence
                            <span class="font-bold text-indigo-400">
                                (Processes × Archetypes × Mastery)</span
                            >. Elements here are in
                            <span
                                class="font-bold text-white underline decoration-indigo-500/50"
                                >laboratory mode</span
                            >
                            and won't affect the catalog until reconciled.
                        </p>
                    </div>
                    <div class="mt-6 flex shrink-0 items-center gap-3 md:mt-0">
                        <StButtonGlass
                            variant="ghost"
                            circle
                            icon="mdi-help-circle-outline"
                            @click="showMatchHelp = !showMatchHelp"
                            :class="
                                showMatchHelp ? 'bg-white/10 text-white' : ''
                            "
                        />
                        <StButtonGlass
                            variant="primary"
                            :loading="approving"
                            @click="approveSelection"
                            :disabled="selectedIds.length === 0"
                        >
                            <template #prepend>
                                <v-icon size="18"
                                    >mdi-checkbox-marked-circle-outline</v-icon
                                >
                            </template>
                            Approve for Engineering ({{ selectedIds.length }})
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Help Guide -->
                <v-expand-transition>
                    <div
                        v-if="showMatchHelp"
                        class="border-b border-white/5 bg-black/20 p-6"
                    >
                        <div class="mb-6 flex items-center gap-2">
                            <v-icon color="indigo-400" size="18"
                                >mdi-book-open-variant</v-icon
                            >
                            <h5
                                class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                            >
                                Organizational Reconciliation Guide
                            </h5>
                        </div>

                        <div
                            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <!-- Enrichment -->
                            <div
                                class="group rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4 transition-all hover:bg-emerald-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-emerald-400 uppercase"
                                        >New (0%)</span
                                    >
                                    <v-icon size="16" color="emerald-400"
                                        >mdi-trending-up</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    📈 Enrichment
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Enlargement</strong>: Horizontal
                                    growth. Creation of non-existent
                                    capacity/role.
                                </div>
                            </div>

                            <!-- Transformation -->
                            <div
                                class="group rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 transition-all hover:bg-indigo-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                        >Partial (40-85%)</span
                                    >
                                    <v-icon size="16" color="indigo-400"
                                        >mdi-auto-fix</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    🔄 Transformation
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Enrichment</strong>: Vertical
                                    growth. Role evolves in depth (Upskilling).
                                </div>
                            </div>

                            <!-- Maintenance -->
                            <div
                                class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition-all hover:bg-white/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >Existing (>85%)</span
                                    >
                                    <v-icon size="16" color="white/30"
                                        >mdi-check-circle-outline</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    ✅ Maintenance
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Stabilization</strong>: Current
                                    role is mature and sufficient for the
                                    design.
                                </div>
                            </div>

                            <!-- Extinction -->
                            <div
                                class="group rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4 transition-all hover:bg-rose-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-rose-400 uppercase"
                                        >Not Proposed</span
                                    >
                                    <v-icon size="16" color="rose-400"
                                        >mdi-close-circle-outline</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    📉 Legacy
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Substitution</strong>: Potential
                                    obsolescence due to strategic model shift.
                                </div>
                            </div>
                        </div>
                    </div>
                </v-expand-transition>
            </StCardGlass>
        </div>

        <!-- AI Capabilities Orchestration (Balde Verde y Amarillo) -->
        <v-expand-transition>
            <div
                v-if="roleStore.isOrchestrating"
                class="animate-pulse p-8 text-center"
            >
                <div
                    class="mb-4 inline-flex items-center justify-center rounded-full bg-indigo-500/20 p-4"
                >
                    <v-icon color="indigo-400" size="32" class="animate-spin"
                        >mdi-loading</v-icon
                    >
                </div>
                <h3 class="text-xl font-black text-indigo-300">
                    Calculando el Empaquetado Organizacional
                </h3>
                <p class="mt-2 text-sm text-white/50">
                    Cruzando el escenario de futuro con tu catálogo actual
                    usando similitud vectorial...
                </p>
            </div>

            <div
                v-else-if="roleStore.orchestrationPlan"
                class="grid grid-cols-1 gap-6 lg:grid-cols-2"
            >
                <!-- Impacto Orgánico (Balde Verde) -->
                <StCardGlass
                    variant="glass"
                    border-accent="emerald"
                    class="overflow-hidden p-0!"
                >
                    <div
                        class="border-b border-white/10 bg-emerald-500/10 px-6 py-4"
                    >
                        <h4
                            class="flex items-center gap-2 font-bold text-emerald-400"
                        >
                            <v-icon size="18">mdi-molecule</v-icon>
                            Organically Matched Competencies
                            <span
                                class="rounded bg-emerald-500/20 px-2 py-0.5 text-xs"
                            >
                                {{
                                    roleStore.orchestrationPlan.summary
                                        .existing_capabilities_matched
                                }}
                            </span>
                        </h4>
                        <p class="mt-1 text-xs text-emerald-400/70">
                            Se identificaron estas competencias del escenario
                            que coinciden (>90% vectorial) con activos
                            existentes.
                        </p>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4">
                            <li
                                v-for="(impact, i) in roleStore
                                    .orchestrationPlan.organic_impact"
                                :key="i"
                                class="rounded-xl border border-white/5 bg-white/5 p-4"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white">{{
                                        impact.official_competency
                                    }}</span>
                                    <StBadgeGlass variant="success"
                                        >{{
                                            impact.vector_similarity_score
                                        }}
                                        match</StBadgeGlass
                                    >
                                </div>
                                <p class="mt-2 text-sm text-white/60">
                                    Proviene de "{{
                                        impact.scenario_capability_name
                                    }}". Acción recomendada:
                                    <strong
                                        class="text-emerald-400 uppercase"
                                        >{{ impact.action_required }}</strong
                                    >
                                    a empleados vigentes.
                                </p>
                            </li>
                            <li
                                v-if="
                                    roleStore.orchestrationPlan.organic_impact
                                        .length === 0
                                "
                                class="text-sm text-white/40"
                            >
                                No se encontraron hubs orgánicos para
                                re-utilizar.
                            </li>
                        </ul>
                    </div>
                </StCardGlass>

                <!-- Empaquetado IA (Balde Amarillo) -->
                <StCardGlass
                    variant="glass"
                    border-accent="warning"
                    class="overflow-hidden p-0!"
                >
                    <div
                        class="border-b border-white/10 bg-amber-500/10 px-6 py-4"
                    >
                        <h4
                            class="flex items-center gap-2 font-bold text-amber-400"
                        >
                            <v-icon size="18">mdi-alien-outline</v-icon>
                            Alien Competencies Orchestration
                            <span
                                class="rounded bg-amber-500/20 px-2 py-0.5 text-xs"
                            >
                                {{
                                    roleStore.orchestrationPlan.summary
                                        .new_alien_capabilities
                                }}
                            </span>
                        </h4>
                        <p class="mt-1 text-xs text-amber-400/70">
                            Competencias radicalmente nuevas. Evita crear
                            micro-cargos aplicando el siguiente "Role Bundling":
                        </p>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4">
                            <li
                                v-for="(orch, i) in roleStore.orchestrationPlan
                                    .ai_orchestration"
                                :key="i"
                                class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-4"
                            >
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <span class="font-bold text-amber-300">{{
                                        orch.target_role_name
                                    }}</span>
                                    <StBadgeGlass
                                        :variant="
                                            orch.type === 'creation'
                                                ? 'primary'
                                                : 'warning'
                                        "
                                    >
                                        {{ orch.type }}
                                    </StBadgeGlass>
                                </div>
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <span
                                        v-for="comp in orch.assigned_competencies"
                                        :key="comp"
                                        class="rounded-full bg-white/10 px-2 py-1 text-xs text-white"
                                    >
                                        {{ comp }}
                                    </span>
                                </div>
                                <p
                                    class="border-t border-amber-500/20 pt-2 text-xs text-amber-300/80 italic"
                                >
                                    "{{ orch.rationale }}"
                                </p>
                            </li>
                            <li
                                v-if="
                                    roleStore.orchestrationPlan.ai_orchestration
                                        .length === 0
                                "
                                class="text-sm text-white/40"
                            >
                                Ningún empaquetado requerido.
                            </li>
                        </ul>
                    </div>
                </StCardGlass>
            </div>
        </v-expand-transition>

        <!-- Confirmation Dialog -->
        <v-dialog v-model="confirmApproval" max-width="550" persistent>
            <StCardGlass variant="glass" border-accent="indigo" class="p-0!">
                <div
                    class="flex items-center gap-4 border-b border-white/10 bg-indigo-500/10 px-8 py-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-indigo-400/30 bg-indigo-500/20"
                    >
                        <v-icon color="indigo-300" size="24"
                            >mdi-shield-alert</v-icon
                        >
                    </div>
                    <div>
                        <h3
                            class="text-xl font-black tracking-tight text-white"
                        >
                            Confirm Reconciliation
                        </h3>
                        <p
                            class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                        >
                            Engineering Phase Transition
                        </p>
                    </div>
                </div>

                <div class="p-8">
                    <p
                        class="text-sm leading-relaxed font-medium text-white/70"
                    >
                        You are about to promote
                        <span class="px-1 font-black text-indigo-400"
                            >{{ selectedIds.length }} elements</span
                        >
                        from laboratory to engineering phase.
                    </p>

                    <div
                        class="mt-6 rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5"
                    >
                        <div class="mb-2 flex items-center gap-3">
                            <v-icon color="amber-400" size="20"
                                >mdi-alert-octagon</v-icon
                            >
                            <span
                                class="text-xs font-black tracking-widest text-amber-400 uppercase"
                                >Engineering Warning</span
                            >
                        </div>
                        <p
                            class="text-[11px] leading-relaxed font-medium text-amber-200/80"
                        >
                            This step creates master records. Once promoted,
                            they cannot be reverted to "incubation" from this
                            view.
                        </p>
                    </div>

                    <div class="mt-8">
                        <div
                            class="mb-3 text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            Entities to promote:
                        </div>
                        <div
                            class="custom-scrollbar max-h-[180px] space-y-2 overflow-y-auto pr-2"
                        >
                            <div
                                v-for="s in selectedIds"
                                :key="s"
                                class="flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 p-3 text-xs font-bold text-white/80"
                            >
                                <v-icon size="14" color="indigo-400"
                                    >mdi-check-decagram</v-icon
                                >
                                <span class="capitalize">{{
                                    getItemLabel(s)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-3 border-t border-white/5 bg-black/20 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="confirmApproval = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        @click="executeApproval"
                        :loading="approving"
                        >Proceed to Engineering</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Main Content -->
        <transition name="fade" mode="out-in">
            <div
                v-if="loading"
                key="loading"
                class="flex flex-col items-center justify-center p-32"
            >
                <v-progress-circular
                    indeterminate
                    color="indigo-400"
                    size="64"
                    width="4"
                ></v-progress-circular>
                <span
                    class="mt-6 animate-pulse text-sm font-black tracking-widest text-indigo-400/60 uppercase"
                    >Analyzing Cube Geometry...</span
                >
            </div>

            <div
                v-else-if="!hasData"
                key="empty"
                class="flex flex-col items-center justify-center p-32 text-center"
            >
                <div
                    class="mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-white/10 bg-white/5 shadow-2xl"
                >
                    <v-icon size="48" color="white/20"
                        >mdi-cube-off-outline</v-icon
                    >
                </div>
                <h3 class="mb-2 text-2xl font-black tracking-tight text-white">
                    No Incubated Proposals Found
                </h3>
                <p
                    class="mx-auto max-w-md text-sm leading-relaxed font-medium text-white/40"
                >
                    Use the AI engine to generate roles and competencies
                    proposals for this specific scenario architecture.
                </p>
            </div>

            <div v-else key="content" class="space-y-10">
                <!-- ─── CUBE LEGEND ─── -->
                <div class="grid grid-cols-3 gap-4">
                    <div
                        v-for="arch in archetypes"
                        :key="arch.key"
                        class="rounded-2xl border p-4 text-center"
                        :class="arch.borderClass"
                    >
                        <div class="mb-1 text-2xl">{{ arch.icon }}</div>
                        <div
                            class="mb-0.5 text-[10px] font-black tracking-[0.25em] uppercase"
                            :class="arch.labelClass"
                        >
                            {{ arch.label }}
                        </div>
                        <div
                            class="text-[11px] leading-snug font-medium text-white/40"
                        >
                            {{ arch.description }}
                        </div>
                    </div>
                </div>

                <!-- ─── CUBE GRID: rows = Business Process (Z), cols = Archetype (X) ─── -->
                <div
                    v-for="process in businessProcesses"
                    :key="process"
                    class="group relative"
                >
                    <!-- Process Row Header -->
                    <div class="mb-4 flex items-center gap-4">
                        <v-icon size="14" color="white/20"
                            >mdi-arrow-right-circle-outline</v-icon
                        >
                        <span
                            class="text-[10px] font-black tracking-[0.3em] text-white/25 uppercase"
                            >Proceso de negocio</span
                        >
                        <span
                            class="text-sm font-black tracking-tight text-white/60"
                            >{{ process }}</span
                        >
                        <div class="h-px flex-1 bg-white/5"></div>
                        <span class="text-[10px] font-medium text-white/20">
                            {{ getRolesByProcess(process).length }} rol{{
                                getRolesByProcess(process).length !== 1
                                    ? 'es'
                                    : ''
                            }}
                        </span>
                    </div>

                    <!-- 3-Column Archetype Grid -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div
                            v-for="arch in archetypes"
                            :key="arch.key"
                            class="relative"
                        >
                            <!-- Column label (only in first process row) -->
                            <div
                                v-if="businessProcesses.indexOf(process) === 0"
                                class="mb-3 text-center text-[9px] font-black tracking-widest uppercase"
                                :class="arch.labelClass"
                            >
                                Eje X · {{ arch.label }}
                            </div>

                            <!-- Role cards in this archetype+process cell -->
                            <div class="space-y-3">
                                <div
                                    v-for="role in getRolesForCell(
                                        process,
                                        arch.key,
                                    )"
                                    :key="role.id"
                                    class="role-card-glass group/card relative flex flex-col rounded-2xl border border-white/10 bg-black/40 p-5 transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_16px_32px_rgba(0,0,0,0.4)]"
                                    :class="arch.cardHoverClass"
                                >
                                    <!-- Select checkbox -->
                                    <div
                                        class="absolute top-3 right-3 z-10 opacity-30 transition-opacity group-hover/card:opacity-100"
                                    >
                                        <v-checkbox
                                            :model-value="
                                                isSelected(role.id, 'role')
                                            "
                                            @update:model-value="
                                                toggleItem(role.id, 'role')
                                            "
                                            hide-details
                                            density="compact"
                                            color="indigo-accent-2"
                                        />
                                    </div>

                                    <!-- Mastery Level (Y axis) + change type badges -->
                                    <div
                                        class="mb-3 flex flex-wrap items-center gap-2"
                                    >
                                        <div
                                            v-if="
                                                role.cube_coordinates
                                                    ?.y_mastery_level
                                            "
                                            class="flex items-center gap-1 rounded-lg px-2 py-0.5 text-[10px] font-black"
                                            :class="arch.masteryClass"
                                        >
                                            <v-icon size="10"
                                                >mdi-layers-triple</v-icon
                                            >
                                            Tier
                                            {{
                                                role.cube_coordinates
                                                    .y_mastery_level
                                            }}
                                        </div>
                                        <StBadgeGlass
                                            :variant="
                                                getRoleChangeVariant(
                                                    role.role_change,
                                                )
                                            "
                                            size="sm"
                                        >
                                            {{
                                                getRoleChangeLabel(
                                                    role.role_change,
                                                )
                                            }}
                                        </StBadgeGlass>
                                    </div>

                                    <!-- Role name + description -->
                                    <h5
                                        class="mb-1.5 pr-6 text-base leading-tight font-black text-white transition-colors group-hover/card:text-indigo-300"
                                    >
                                        {{ role.role_name }}
                                    </h5>
                                    <p
                                        class="mb-4 line-clamp-2 text-[12px] leading-relaxed font-medium text-white/35"
                                    >
                                        {{
                                            role.role_description ||
                                            role.cube_coordinates
                                                ?.justification ||
                                            '—'
                                        }}
                                    </p>

                                    <!-- Context row: FTE + process confirmation -->
                                    <div
                                        class="mb-4 flex flex-wrap gap-x-3 gap-y-1 text-[10px]"
                                    >
                                        <div
                                            class="flex items-center gap-1 text-white/30"
                                        >
                                            <v-icon size="9" color="white/25"
                                                >mdi-account-supervisor</v-icon
                                            >
                                            <span>{{ role.fte }} FTE</span>
                                        </div>
                                        <div
                                            v-if="
                                                role.cube_coordinates
                                                    ?.z_business_process
                                            "
                                            class="flex max-w-full items-center gap-1 truncate text-indigo-400/50"
                                        >
                                            <v-icon
                                                size="9"
                                                color="indigo-400/35"
                                                >mdi-swap-horizontal</v-icon
                                            >
                                            <span class="truncate">{{
                                                role.cube_coordinates
                                                    .z_business_process
                                            }}</span>
                                        </div>
                                    </div>

                                    <!-- Required Competencies -->
                                    <div class="mt-auto">
                                        <div
                                            class="mb-2 flex items-center justify-between px-0.5"
                                        >
                                            <span
                                                class="text-[9px] font-black tracking-widest text-white/20 uppercase"
                                                >Competencias</span
                                            >
                                            <span
                                                class="text-[9px] font-medium text-white/15"
                                                >{{
                                                    getCompetenciesForRole(role)
                                                        .length
                                                }}</span
                                            >
                                        </div>
                                        <div class="space-y-1.5">
                                            <div
                                                v-for="(
                                                    comp, cIdx
                                                ) in getCompetenciesForRole(
                                                    role,
                                                ).slice(0, 3)"
                                                :key="cIdx"
                                                class="flex flex-col gap-1 rounded-lg border border-white/5 bg-white/5 px-3 py-2"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-2"
                                                >
                                                    <span
                                                        class="min-w-0 truncate text-[11px] font-bold text-white/75"
                                                        >{{ comp.name }}</span
                                                    >
                                                    <div
                                                        class="flex shrink-0 items-center gap-1"
                                                    >
                                                        <span
                                                            v-if="
                                                                comp.source ===
                                                                'agent'
                                                            "
                                                            class="text-[8px] font-black text-violet-400/50 uppercase"
                                                            >AI</span
                                                        >
                                                        <span
                                                            class="text-[10px] font-black"
                                                            :class="
                                                                comp.source ===
                                                                'agent'
                                                                    ? 'text-violet-400'
                                                                    : 'text-indigo-400'
                                                            "
                                                            >L{{
                                                                comp.level
                                                            }}</span
                                                        >
                                                    </div>
                                                </div>
                                                <div class="flex gap-0.5">
                                                    <div
                                                        v-for="n in 5"
                                                        :key="n"
                                                        class="h-0.5 flex-1 rounded-full"
                                                        :class="
                                                            n <= comp.level
                                                                ? comp.source ===
                                                                  'agent'
                                                                    ? 'bg-violet-500/50'
                                                                    : 'bg-indigo-500'
                                                                : 'bg-white/5'
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    getCompetenciesForRole(role)
                                                        .length > 3
                                                "
                                                class="py-1 text-center text-[10px] text-white/20"
                                            >
                                                +{{
                                                    getCompetenciesForRole(role)
                                                        .length - 3
                                                }}
                                                más
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty cell placeholder -->
                                <div
                                    v-if="
                                        getRolesForCell(process, arch.key)
                                            .length === 0
                                    "
                                    class="flex h-16 items-center justify-center rounded-2xl border border-dashed border-white/5 text-[11px] text-white/10"
                                >
                                    —
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unclassified roles (no cube_coordinates yet) -->
                <div v-if="unclassifiedRoles.length > 0" class="relative">
                    <div class="mb-4 flex items-center gap-4">
                        <v-icon size="14" color="amber-500/40"
                            >mdi-alert-circle-outline</v-icon
                        >
                        <span
                            class="text-[10px] font-black tracking-[0.3em] text-amber-500/40 uppercase"
                            >Roles sin clasificar (sin coordenadas del
                            cubo)</span
                        >
                        <div class="h-px flex-1 bg-white/5"></div>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="role in unclassifiedRoles"
                            :key="role.id"
                            class="relative flex flex-col rounded-2xl border border-dashed border-amber-500/15 bg-amber-500/5 p-5"
                        >
                            <div
                                class="absolute top-3 right-3 z-10 opacity-30 transition-opacity hover:opacity-100"
                            >
                                <v-checkbox
                                    :model-value="isSelected(role.id, 'role')"
                                    @update:model-value="
                                        toggleItem(role.id, 'role')
                                    "
                                    hide-details
                                    density="compact"
                                    color="indigo-accent-2"
                                />
                            </div>
                            <StBadgeGlass
                                variant="warning"
                                size="sm"
                                class="mb-3 self-start"
                                >Sin coordenadas</StBadgeGlass
                            >
                            <h5
                                class="mb-1 pr-6 text-base font-black text-white/80"
                            >
                                {{ role.role_name }}
                            </h5>
                            <p class="line-clamp-2 text-[12px] text-white/30">
                                {{ role.role_description || 'Sin descripción' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const roleStore = useRoleCompetencyStore();

const props = defineProps<{
    scenarioId: number;
}>();

const emit = defineEmits<(e: 'approved') => void>();

const { showSuccess, showError } = useNotification();

interface Capability {
    id: number;
    name: string;
    category: string;
    llm_id?: string;
}

interface Role {
    id: number;
    role_name: string;
    role_description: string;
    human_leverage: number;
    archetype: string;
    rationale: string;
    fte?: number;
    role_change?: string;
    key_competencies: any;
    similarity_warnings?: Array<{ id: number; name: string; score: number }>;
    cube_coordinates?: {
        x_archetype?: string;
        y_mastery_level?: number;
        z_business_process?: string;
        justification?: string;
    };
    ai_suggestions?: any;
}

interface Competency {
    id: number;
    name: string;
    capability_id?: number;
}

// ─── Cube Axis X: Archetype definitions ────────────────────────────────────
const archetypes = [
    {
        key: 'strategic',
        label: 'Estratégico',
        icon: '🧭',
        description: 'Dirección, visión y toma de decisiones de alto impacto',
        borderClass: 'border-primary/20 bg-primary/5',
        labelClass: 'text-primary',
        masteryClass: 'bg-primary/10 text-primary border border-primary/20',
        cardHoverClass: 'hover:border-primary/30',
    },
    {
        key: 'tactical',
        label: 'Táctico',
        icon: '⚙️',
        description: 'Coordinación, gestión y ejecución de planes operativos',
        borderClass: 'border-secondary/20 bg-secondary/5',
        labelClass: 'text-secondary',
        masteryClass:
            'bg-secondary/10 text-secondary border border-secondary/20',
        cardHoverClass: 'hover:border-secondary/30',
    },
    {
        key: 'operational',
        label: 'Operacional',
        icon: '🔧',
        description: 'Ejecución directa, producción y entrega de resultados',
        borderClass: 'border-success/20 bg-success/5',
        labelClass: 'text-success',
        masteryClass: 'bg-success/10 text-success border border-success/20',
        cardHoverClass: 'hover:border-success/30',
    },
] as const;

type ArchetypeKey = (typeof archetypes)[number]['key'];

// ─── State ─────────────────────────────────────────────────────────────────
const loading = ref(false);
const approving = ref(false);
const confirmApproval = ref(false);
const showMatchHelp = ref(false);
// capabilities kept for context only (not as primary axis)
const capabilities = ref<Capability[]>([]);
const roles = ref<Role[]>([]);
const competencies = ref<Competency[]>([]);
const selectedIds = ref<string[]>([]);

const hasData = computed(
    () => capabilities.value.length > 0 || roles.value.length > 0,
);

// ─── Cube Axis X: normalize archetype key ──────────────────────────────────
const normalizeArchetypeKey = (role: Role): ArchetypeKey => {
    const raw = (
        role.cube_coordinates?.x_archetype ||
        role.archetype ||
        ''
    ).toLowerCase();
    if (raw.includes('strat') || raw.includes('estratég')) return 'strategic';
    if (raw.includes('tact') || raw.includes('tácti')) return 'tactical';
    if (raw.includes('oper')) return 'operational';
    // Fallback from human_leverage
    if ((role.human_leverage ?? 0) > 70) return 'strategic';
    if ((role.human_leverage ?? 0) > 40) return 'tactical';
    return 'operational';
};

// ─── Cube Axis Z: derive unique business processes from roles ───────────────
const businessProcesses = computed<string[]>(() => {
    const processes = new Set<string>();
    roles.value.forEach((r) => {
        const proc = r.cube_coordinates?.z_business_process;
        if (proc) processes.add(proc);
    });
    // Process not defined → group them as "General"
    const hasUnclassified = roles.value.some(
        (r) => !r.cube_coordinates?.z_business_process && r.cube_coordinates,
    );
    if (hasUnclassified) processes.add('General');
    return Array.from(processes);
});

// ─── Roles without any cube coordinates ────────────────────────────────────
const unclassifiedRoles = computed(() =>
    roles.value.filter((r) => !r.cube_coordinates),
);

// ─── Cell lookup: process (Z) + archetype (X) ─────────────────────────────
const getRolesForCell = (
    process: string,
    archetypeKey: ArchetypeKey,
): Role[] => {
    return roles.value.filter((role) => {
        if (!role.cube_coordinates) return false;
        const roleProcess =
            role.cube_coordinates.z_business_process || 'General';
        if (roleProcess !== process) return false;
        return normalizeArchetypeKey(role) === archetypeKey;
    });
};

// ─── All roles in a process row (for count display) ───────────────────────
const getRolesByProcess = (process: string): Role[] =>
    roles.value.filter(
        (r) =>
            (r.cube_coordinates?.z_business_process || 'General') === process,
    );

// ─── Role change type helpers ──────────────────────────────────────────────
const getRoleChangeLabel = (change?: string) => {
    const map: Record<string, string> = {
        new: 'Rol Nuevo',
        evolve: 'Evolución',
        sunset: 'Extinción',
    };
    return map[change ?? ''] ?? 'Nuevo Diseño';
};

const getRoleChangeVariant = (change?: string) => {
    const map: Record<string, 'primary' | 'secondary' | 'error' | 'glass'> = {
        new: 'primary',
        evolve: 'secondary',
        sunset: 'error',
    };
    return map[change ?? ''] ?? 'glass';
};

// ─── Competencies ──────────────────────────────────────────────────────────
const getCompetenciesForRole = (role: Role) => {
    // Primary source: manual mappings from the matrix (key_competencies)
    let raw = role.key_competencies;
    if (typeof raw === 'string') {
        try {
            raw = JSON.parse(raw);
        } catch {
            raw = [];
        }
    }
    // Fallback: use agent's proposed core_competencies
    if (!Array.isArray(raw) || raw.length === 0) {
        const agentComps = role.ai_suggestions?.core_competencies;
        if (Array.isArray(agentComps) && agentComps.length > 0) {
            return agentComps.map((c: any) => ({
                name: c.name || 'Competencia',
                level: c.level ?? 3,
                source: 'agent' as const,
            }));
        }
        return [];
    }
    return raw.map((c: any) => ({
        name: c.name || c.key || 'Competencia',
        level: c.level || c.score || c.required_level || 3,
        source: 'matrix' as const,
    }));
};

// ─── Selection helpers ─────────────────────────────────────────────────────
const getItemLabel = (key: string) => {
    const [type, id] = key.split(':');
    const numericId = Number(id);
    if (type === 'role')
        return roles.value.find((r) => r.id === numericId)?.role_name || 'Role';
    return `ID: ${id}`;
};

const isSelected = (id: number, type: string) =>
    selectedIds.value.includes(`${type}:${id}`);

const toggleItem = (id: number, type: string) => {
    const key = `${type}:${id}`;
    const idx = selectedIds.value.indexOf(key);
    if (idx > -1) selectedIds.value.splice(idx, 1);
    else selectedIds.value.push(key);
};

// ─── Data fetching ─────────────────────────────────────────────────────────
const fetchData = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/scenarios/${props.scenarioId}/step2/cube`,
        );
        const remoteData = res.data.data || res.data;
        capabilities.value = remoteData.capabilities || [];
        roles.value = remoteData.roles || [];
        competencies.value = remoteData.competencies || [];

        // Ejecutar orquestación de competencias Capability-First!
        await roleStore.orchestrateCompetencies(props.scenarioId);
    } catch (error_) {
        showError('Could not load Cube data: ' + error_);
    } finally {
        loading.value = false;
    }
};

// ─── Approval ─────────────────────────────────────────────────────────────
const approveSelection = () => {
    confirmApproval.value = true;
};

const executeApproval = async () => {
    approving.value = true;
    try {
        await axios.post(
            `/api/scenarios/${props.scenarioId}/step2/approve-cube`,
            { selection: selectedIds.value },
        );
        showSuccess('Roles promovidos a la fase de ingeniería');
        confirmApproval.value = false;
        selectedIds.value = [];
        await fetchData();
        emit('approved');
    } catch (_e) {
        showError('No se pudo promover los elementos');
    } finally {
        approving.value = false;
    }
};

onMounted(fetchData);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}
</style>
