<template>
    <v-dialog
        :model-value="visible"
        max-width="1100"
        scrollable
        persistent
        class="backdrop-blur-sm"
    >
        <StCardGlass
            variant="media"
            class="relative flex h-full !max-h-[90vh] flex-col overflow-hidden"
        >
            <!-- Header Glow -->
            <div
                class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 bg-indigo-500/10 blur-[120px]"
            ></div>
            <div
                class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 bg-emerald-500/10 blur-[120px]"
            ></div>

            <!-- Header -->
            <div
                class="z-10 flex items-center justify-between gap-4 border-b border-white/5 p-6"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20"
                    >
                        <v-icon color="indigo-300" size="24">mdi-robot</v-icon>
                    </div>
                    <div>
                        <h2
                            class="mb-1 text-xl leading-none font-black tracking-tight text-white"
                        >
                            {{ $t('agent_proposals.title') }}
                        </h2>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                >{{
                                    $t('agent_proposals.strategy_engine')
                                }}</span
                            >
                            <span class="text-xs font-bold text-indigo-400"
                                >Collaboration Protocol v2.4</span
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Alignment score badge -->
                    <div
                        v-if="proposals?.alignment_score"
                        class="hidden flex-col items-end md:flex"
                    >
                        <span
                            class="text-[9px] font-black tracking-widest text-white/30 uppercase"
                            >{{ $t('agent_proposals.alignment_score') }}</span
                        >
                        <div class="flex items-center gap-2">
                            <div
                                class="mt-1 h-1.5 w-24 overflow-hidden rounded-full border border-white/10 bg-white/5"
                            >
                                <div
                                    class="h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000"
                                    :style="{
                                        width: `${Math.round((proposals.alignment_score ?? 0) * 100)}%`,
                                    }"
                                ></div>
                            </div>
                            <span class="text-sm font-black text-indigo-300"
                                >{{
                                    Math.round(
                                        (proposals.alignment_score ?? 0) * 100,
                                    )
                                }}%</span
                            >
                        </div>
                    </div>

                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        circle
                        icon="mdi-close"
                        @click="$emit('close')"
                    />
                </div>
            </div>

            <div class="custom-scrollbar relative flex-1 overflow-y-auto">
                <!-- Loading state -->
                <div
                    v-if="loading"
                    class="flex flex-col items-center justify-center py-24"
                >
                    <div class="relative mb-8">
                        <div
                            class="absolute inset-0 scale-150 animate-pulse rounded-full bg-indigo-500/20 blur-2xl"
                        ></div>
                        <v-progress-circular
                            indeterminate
                            color="indigo-400"
                            size="80"
                            width="2"
                        >
                            <v-icon color="indigo-300" size="32"
                                >mdi-robot-outline</v-icon
                            >
                        </v-progress-circular>
                    </div>
                    <p
                        class="mb-2 text-lg font-black tracking-tight text-white"
                    >
                        {{ $t('agent_proposals.loading_title') }}
                    </p>
                    <p
                        class="max-w-sm text-center text-sm font-bold text-white/40"
                    >
                        {{ $t('agent_proposals.loading_desc') }}
                    </p>
                </div>

                <!-- Empty state -->
                <div
                    v-else-if="!proposals"
                    class="flex flex-col items-center justify-center py-24"
                >
                    <div
                        class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl border border-white/10 bg-white/5"
                    >
                        <v-icon size="40" color="white/20"
                            >mdi-robot-off</v-icon
                        >
                    </div>
                    <p
                        class="mb-2 text-lg font-black tracking-tight text-white"
                    >
                        {{ $t('agent_proposals.empty_title') }}
                    </p>
                    <p class="text-sm font-bold text-white/40">
                        {{ $t('agent_proposals.empty_desc') }}
                    </p>
                </div>

                <!-- Main content -->
                <div v-else class="space-y-12 p-8">
                    <!-- Intro Alert -->
                    <div
                        class="relative overflow-hidden rounded-3xl border border-indigo-500/20 bg-indigo-500/10 p-6 backdrop-blur-xl"
                    >
                        <div
                            class="pointer-events-none absolute -right-8 -bottom-8 h-32 w-32 rounded-full bg-indigo-500/10 blur-3xl"
                        ></div>
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-indigo-500/30 bg-indigo-500/20"
                            >
                                <v-icon color="indigo-300" size="20"
                                    >mdi-information-outline</v-icon
                                >
                            </div>
                            <div>
                                <h3
                                    class="mb-1 text-sm font-black tracking-widest text-indigo-200 uppercase"
                                >
                                    {{ $t('agent_proposals.protocol_title') }}
                                </h3>
                                <p
                                    class="text-sm leading-relaxed font-medium text-white/70"
                                    v-html="$t('agent_proposals.protocol_desc')"
                                ></p>
                            </div>
                        </div>
                    </div>

                    <!-- ROLES SECTION -->
                    <section>
                        <div class="mb-8 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5"
                                >
                                    <v-icon size="20" color="white/60"
                                        >mdi-account-tie</v-icon
                                    >
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-black tracking-tight text-white"
                                    >
                                        {{
                                            $t('agent_proposals.proposed_roles')
                                        }}
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <StBadgeGlass
                                            :variant="
                                                approvedRoleCount > 0
                                                    ? 'primary'
                                                    : 'glass'
                                            "
                                            size="sm"
                                        >
                                            {{
                                                $t('agent_proposals.approved', {
                                                    count: approvedRoleCount,
                                                    total: localRoleProposals.length,
                                                })
                                            }}
                                        </StBadgeGlass>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    @click="approveAllRoles"
                                    icon="mdi-check-all"
                                >
                                    {{ $t('agent_proposals.approve_all') }}
                                </StButtonGlass>
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    @click="rejectAllRoles"
                                    icon="mdi-close-circle"
                                >
                                    {{ $t('agent_proposals.reject_all') }}
                                </StButtonGlass>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div
                                v-for="(role, idx) in localRoleProposals"
                                :key="`role-${idx}`"
                                :class="`group relative transition-all duration-300 ${
                                    role._status === 'rejected'
                                        ? 'pointer-events-none opacity-40 grayscale'
                                        : ''
                                }`"
                            >
                                <div
                                    :class="`overflow-hidden rounded-3xl border transition-all duration-500 ${
                                        role._status === 'approved'
                                            ? 'border-indigo-500/30 bg-indigo-500/5 shadow-[0_0_40px_rgba(99,102,241,0.05)]'
                                            : 'border-white/10 bg-white/5'
                                    }`"
                                >
                                    <!-- Role Header -->
                                    <div
                                        class="flex flex-col justify-between gap-4 border-b border-white/5 bg-white/5 p-6 md:flex-row md:items-center"
                                    >
                                        <div class="flex items-center gap-4">
                                            <StBadgeGlass
                                                :variant="
                                                    getTypeVariant(role.type)
                                                "
                                                size="md"
                                            >
                                                <v-icon
                                                    size="14"
                                                    class="mr-1.5"
                                                    >{{
                                                        getTypeIcon(role.type)
                                                    }}</v-icon
                                                >
                                                {{ role.type }}
                                            </StBadgeGlass>
                                            <div>
                                                <h4
                                                    class="text-base font-black tracking-tight text-white"
                                                >
                                                    {{ role.proposed_name }}
                                                </h4>
                                                <p
                                                    class="line-clamp-1 max-w-md truncate text-xs leading-tight font-medium text-white/40"
                                                >
                                                    {{
                                                        role.proposed_description
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="flex shrink-0 items-center gap-2"
                                        >
                                            <StBadgeGlass
                                                v-if="
                                                    role._status === 'approved'
                                                "
                                                variant="secondary"
                                                size="sm"
                                            >
                                                <v-icon size="14" class="mr-1"
                                                    >mdi-check-circle</v-icon
                                                >{{
                                                    $t(
                                                        'agent_proposals.roles.approved',
                                                    )
                                                }}
                                            </StBadgeGlass>
                                            <div class="flex gap-1">
                                                <StButtonGlass
                                                    variant="ghost"
                                                    size="sm"
                                                    circle
                                                    icon="mdi-close"
                                                    @click="
                                                        role._status =
                                                            'rejected'
                                                    "
                                                />
                                                <StButtonGlass
                                                    :variant="
                                                        role._status ===
                                                        'approved'
                                                            ? 'secondary'
                                                            : 'primary'
                                                    "
                                                    size="sm"
                                                    @click="
                                                        role._status ===
                                                        'approved'
                                                            ? (role._status =
                                                                  'pending')
                                                            : approveRole(role)
                                                    "
                                                >
                                                    {{
                                                        role._status ===
                                                        'approved'
                                                            ? $t(
                                                                  'agent_proposals.undo',
                                                              )
                                                            : $t(
                                                                  'agent_proposals.approve_role',
                                                              )
                                                    }}
                                                </StButtonGlass>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Role Content -->
                                    <div class="space-y-8 p-6">
                                        <!-- Controls Row -->
                                        <div
                                            class="grid grid-cols-1 gap-6 md:grid-cols-4"
                                        >
                                            <!-- Archetype -->
                                            <div class="space-y-3">
                                                <span
                                                    class="block text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                                    >{{ $t('agent_proposals.role_archetype') }}</span
                                                >
                                                <div class="flex gap-2">
                                                    <button
                                                        v-for="a in [
                                                            'E',
                                                            'T',
                                                            'O',
                                                        ]"
                                                        :key="a"
                                                        @click="
                                                            role.archetype = a
                                                        "
                                                        :class="`h-10 w-10 rounded-xl border font-black transition-all duration-300 ${
                                                            role.archetype === a
                                                                ? 'border-indigo-400 bg-indigo-500 text-white shadow-[0_0_15px_rgba(99,102,241,0.3)]'
                                                                : 'border-white/10 bg-white/5 text-white/30 hover:border-white/20'
                                                        }`"
                                                    >
                                                        {{ a }}
                                                    </button>
                                                </div>
                                                <div
                                                    class="text-[10px] font-bold text-indigo-400/60 uppercase"
                                                >
                                                    {{
                                                        archetypeLabel(
                                                            role.archetype,
                                                        )
                                                    }}
                                                </div>
                                            </div>

                                            <!-- FTE -->
                                            <div class="space-y-3">
                                                <span
                                                    class="block text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                                    >{{ $t('agent_proposals.target_fte') }}</span
                                                >
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <input
                                                        v-model.number="
                                                            role.fte_suggested
                                                        "
                                                        type="number"
                                                        step="0.1"
                                                        min="0.1"
                                                        class="w-20 rounded-xl border border-white/10 bg-black/20 px-3 py-2 text-sm font-black text-white focus:border-indigo-500/50 focus:outline-none"
                                                    />
                                                    <span
                                                        class="text-xs font-bold text-white/20"
                                                        >{{ $t('agent_proposals.units') }}</span
                                                    >
                                                </div>
                                            </div>

                                            <!-- Composition -->
                                            <div
                                                v-if="role.talent_composition"
                                                class="space-y-3 md:col-span-2"
                                            >
                                                <span
                                                    class="block text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                                    >{{ $t('agent_proposals.talent_composition') }}</span
                                                >
                                                <div
                                                    class="flex items-center gap-4"
                                                >
                                                    <div
                                                        class="flex shrink-0 items-center gap-2 rounded-xl border border-white/10 bg-white/5 p-2 px-4"
                                                    >
                                                        <v-icon
                                                            size="16"
                                                            color="indigo-300"
                                                            >mdi-account</v-icon
                                                        >
                                                        <span
                                                            class="text-sm font-black text-white"
                                                            >{{
                                                                role
                                                                    .talent_composition
                                                                    .human_percentage
                                                            }}%</span
                                                        >
                                                        <div
                                                            class="mx-1 h-3 w-px bg-white/10"
                                                        ></div>
                                                        <v-icon
                                                            size="16"
                                                            color="emerald-300"
                                                            >mdi-robot</v-icon
                                                        >
                                                        <span
                                                            class="text-sm font-black text-white"
                                                            >{{
                                                                role
                                                                    .talent_composition
                                                                    .synthetic_percentage
                                                            }}%</span
                                                        >
                                                    </div>
                                                    <p
                                                        class="text-[10px] leading-tight font-medium text-white/40 italic"
                                                    >
                                                        "{{
                                                            role
                                                                .talent_composition
                                                                .logic_justification
                                                        }}"
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Competencies Table -->
                                        <div
                                            v-if="
                                                role.competency_mappings?.length
                                            "
                                            class="space-y-4"
                                        >
                                            <span
                                                class="block text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                                >{{ $t('agent_proposals.proposed_mapping', { count: role.competency_mappings.length }) }}</span
                                            >

                                            <div
                                                class="overflow-hidden rounded-2xl border border-white/5 bg-black/10"
                                            >
                                                <table
                                                    class="w-full border-collapse text-left"
                                                >
                                                    <thead>
                                                        <tr class="bg-white/5">
                                                            <th
                                                                class="px-4 py-3 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                            >
                                                                {{ $t('agent_proposals.col_competency') }}
                                                            </th>
                                                            <th
                                                                class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                            >
                                                                {{ $t('agent_proposals.col_change_type') }}
                                                            </th>
                                                            <th
                                                                class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                            >
                                                                {{ $t('agent_proposals.col_req_level') }}
                                                            </th>
                                                            <th
                                                                class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                            >
                                                                {{ $t('agent_proposals.col_core') }}
                                                            </th>
                                                            <th
                                                                class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                            >
                                                                {{ $t('agent_proposals.col_diagnostic') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="divide-y divide-white/5"
                                                    >
                                                        <tr
                                                            v-for="(
                                                                mapping, mIdx
                                                            ) in role.competency_mappings"
                                                            :key="mIdx"
                                                            class="transition-colors hover:bg-white/5"
                                                        >
                                                            <td
                                                                class="px-4 py-3"
                                                            >
                                                                <span
                                                                    class="text-sm font-bold text-white"
                                                                    >{{
                                                                        mapping.competency_name
                                                                    }}</span
                                                                >
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 text-center"
                                                            >
                                                                <StBadgeGlass
                                                                    :variant="
                                                                        getChangeTypeVariant(
                                                                            mapping.change_type,
                                                                        )
                                                                    "
                                                                    size="sm"
                                                                >
                                                                    {{
                                                                        changeTypeLabel(
                                                                            mapping.change_type,
                                                                        )
                                                                    }}
                                                                </StBadgeGlass>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3"
                                                            >
                                                                <div
                                                                    class="flex items-center justify-center gap-1"
                                                                >
                                                                    <button
                                                                        v-for="n in 5"
                                                                        :key="n"
                                                                        @click="
                                                                            mapping.required_level =
                                                                                n
                                                                        "
                                                                        :class="`pointer-events-auto h-7 w-7 rounded-lg text-[10px] font-black transition-all ${
                                                                            mapping.required_level ===
                                                                            n
                                                                                ? 'bg-indigo-500 text-white'
                                                                                : 'bg-white/5 text-white/20 hover:text-white/40'
                                                                        }`"
                                                                        :title="
                                                                            LEVEL_DESCRIPTIONS[
                                                                                n
                                                                            ]
                                                                                .detail
                                                                        "
                                                                    >
                                                                        {{ n }}
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 text-center"
                                                            >
                                                                <v-checkbox
                                                                    v-model="
                                                                        mapping.is_core
                                                                    "
                                                                    hide-details
                                                                    density="compact"
                                                                    color="indigo-accent-2"
                                                                    class="d-inline-flex"
                                                                />
                                                            </td>
                                                            <td
                                                                class="px-4 py-3 text-center"
                                                            >
                                                                <v-icon
                                                                    :color="
                                                                        cubeSignalColor(
                                                                            role.archetype,
                                                                            mapping.required_level,
                                                                            mapping.is_core,
                                                                        )
                                                                    "
                                                                    size="14"
                                                                    class="drop-shadow-[0_0_8px_currentColor]"
                                                                    :title="
                                                                        cubeSignalLabel(
                                                                            role.archetype,
                                                                            mapping.required_level,
                                                                            mapping.is_core,
                                                                        )
                                                                    "
                                                                >
                                                                    mdi-circle
                                                                </v-icon>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Restores button for rejected state -->
                                <div
                                    v-if="role._status === 'rejected'"
                                    class="absolute inset-0 z-10 flex items-center justify-center"
                                >
                                    <StButtonGlass
                                        variant="primary"
                                        @click="role._status = 'pending'"
                                        icon="mdi-undo"
                                    >
                                        {{ $t('agent_proposals.restore_role') }}
                                    </StButtonGlass>
                                </div>
                            </div>
                        </div>
                    </section>

                    <v-divider class="border-white/5" />

                    <!-- CATALOG SECTION -->
                    <section>
                        <div class="mb-8 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5"
                                >
                                    <v-icon size="20" color="white/60"
                                        >mdi-certificate</v-icon
                                    >
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-black tracking-tight text-white"
                                    >
                                        {{ $t('agent_proposals.proposed_catalog') }}
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <StBadgeGlass
                                            :variant="
                                                approvedCatalogCount > 0
                                                    ? 'secondary'
                                                    : 'glass'
                                            "
                                            size="sm"
                                        >
                                            {{ $t('agent_proposals.approved', { count: approvedCatalogCount, total: localCatalogProposals.length }) }}
                                        </StBadgeGlass>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    @click="approveAllCatalog"
                                    icon="mdi-check-all"
                                    >{{ $t('agent_proposals.approve_all') }}</StButtonGlass
                                >
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    @click="rejectAllCatalog"
                                    icon="mdi-close-circle"
                                    >{{ $t('agent_proposals.reject_all') }}</StButtonGlass
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                v-for="(comp, idx) in localCatalogProposals"
                                :key="`comp-${idx}`"
                                :class="`flex items-center justify-between gap-4 rounded-2xl border p-4 transition-all duration-300 ${
                                    comp._status === 'rejected'
                                        ? 'pointer-events-none border-white/5 bg-transparent opacity-40 grayscale'
                                        : comp._status === 'approved'
                                          ? 'border-emerald-500/20 bg-emerald-500/5'
                                          : 'border-white/10 bg-white/5'
                                }`"
                            >
                                <div class="flex flex-1 items-center gap-4">
                                    <StBadgeGlass
                                        :variant="
                                            getCatalogTypeVariant(comp.type)
                                        "
                                        size="sm"
                                    >
                                        {{ comp.type }}
                                    </StBadgeGlass>
                                    <div class="flex-1">
                                        <div
                                            class="text-sm font-bold text-white"
                                        >
                                            {{ comp.proposed_name }}
                                        </div>
                                        <div
                                            class="max-w-[200px] truncate text-[10px] text-white/30"
                                        >
                                            {{ comp.action_rationale }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <StButtonGlass
                                        v-if="comp._status !== 'rejected'"
                                        variant="ghost"
                                        size="sm"
                                        circle
                                        icon="mdi-close"
                                        @click="comp._status = 'rejected'"
                                    />
                                    <StButtonGlass
                                        v-if="comp._status === 'rejected'"
                                        variant="ghost"
                                        size="sm"
                                        circle
                                        icon="mdi-undo"
                                        @click="comp._status = 'pending'"
                                    />
                                    <StButtonGlass
                                        v-if="comp._status !== 'rejected'"
                                        :variant="
                                            comp._status === 'approved'
                                                ? 'secondary'
                                                : 'glass'
                                        "
                                        size="sm"
                                        circle
                                        :icon="
                                            comp._status === 'approved'
                                                ? 'mdi-check-circle'
                                                : 'mdi-circle-outline'
                                        "
                                        @click="
                                            comp._status =
                                                comp._status === 'approved'
                                                    ? 'pending'
                                                    : 'approved'
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Modal Actions (Footer) -->
            <div
                class="z-10 flex items-center justify-between border-t border-white/5 bg-black/20 p-6"
            >
                <div class="flex items-center gap-4">
                    <div class="flex flex-col">
                        <span
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >{{ $t('agent_proposals.selected_blueprint') }}</span
                        >
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-white"
                                >{{ $t('agent_proposals.roles_count', { count: approvedRoleCount }) }}</span
                            >
                            <div class="h-1 w-1 rounded-full bg-white/20"></div>
                            <span class="text-sm font-black text-white"
                                >{{ $t('agent_proposals.competencies_count', { count: approvedCatalogCount }) }}</span
                            >
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <StButtonGlass variant="ghost" @click="$emit('close')"
                        >{{ $t('agent_proposals.cancel') }}</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        @click="confirmApply"
                        :loading="applying"
                        :disabled="
                            approvedRoleCount === 0 &&
                            approvedCatalogCount === 0
                        "
                        icon="mdi-check-all"
                    >
                        {{ $t('agent_proposals.commit_changes') }}
                    </StButtonGlass>
                </div>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// ─── Types ────────────────────────────────────────────────────────────────────

type ProposalStatus = 'pending' | 'approved' | 'rejected';

interface CompetencyMapping {
    competency_name: string;
    competency_id?: number | null;
    change_type: string;
    required_level: number;
    is_core: boolean;
    rationale?: string;
}

interface RoleProposal {
    type: string;
    proposed_name: string;
    proposed_description?: string;
    archetype?: string;
    fte_suggested?: number;
    target_role_id?: number | null;
    competency_mappings?: CompetencyMapping[];
    talent_composition?: {
        human_percentage: number;
        synthetic_percentage: number;
        logic_justification?: string;
    };
    _status: ProposalStatus;
}

interface CatalogProposal {
    type: string;
    proposed_name: string;
    competency_id?: number | null;
    action_rationale?: string;
    _status: ProposalStatus;
}

interface Proposals {
    role_proposals?: Omit<RoleProposal, '_status'>[];
    catalog_proposals?: Omit<CatalogProposal, '_status'>[];
    alignment_score?: number;
}

// ─── Props & Emits ────────────────────────────────────────────────────────────

interface Props {
    visible: boolean;
    loading?: boolean;
    proposals?: Proposals | null;
    scenarioId?: number | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    close: [];
    applied: [];
}>();

// ─── Local reactive state ─────────────────────────────────────────────────────

const applying = ref(false);
const localRoleProposals = ref<RoleProposal[]>([]);
const localCatalogProposals = ref<CatalogProposal[]>([]);

// Rebuild local state when new proposals arrive
watch(
    () => props.proposals,
    (proposals) => {
        if (!proposals) return;
        localRoleProposals.value = (proposals.role_proposals ?? []).map(
            (r) => ({
                ...r,
                archetype: r.archetype ?? 'T',
                fte_suggested: r.fte_suggested ?? 1,
                competency_mappings: (r.competency_mappings ?? []).map((m) => ({
                    ...m,
                })),
                _status: 'pending' as ProposalStatus,
            }),
        );
        localCatalogProposals.value = (proposals.catalog_proposals ?? []).map(
            (c) => ({
                ...c,
                _status: 'pending' as ProposalStatus,
            }),
        );
    },
    { immediate: true },
);

// ─── Computed ─────────────────────────────────────────────────────────────────

const approvedRoleCount = computed(
    () =>
        localRoleProposals.value.filter((r) => r._status === 'approved').length,
);

const approvedCatalogCount = computed(
    () =>
        localCatalogProposals.value.filter((c) => c._status === 'approved')
            .length,
);

// ─── Bulk actions ─────────────────────────────────────────────────────────────

/**
 * Approves a role and auto-approves matching catalog proposals.
 */
const approveRole = (role: RoleProposal) => {
    role._status = 'approved';
    const mappingNames = new Set(
        (role.competency_mappings ?? []).map((m) =>
            m.competency_name?.trim().toLowerCase(),
        ),
    );
    localCatalogProposals.value.forEach((c) => {
        if (
            c._status !== 'rejected' &&
            mappingNames.has(c.proposed_name?.trim().toLowerCase())
        ) {
            c._status = 'approved';
        }
    });
};

const approveAllRoles = () =>
    localRoleProposals.value.forEach((r) => approveRole(r));
const rejectAllRoles = () =>
    localRoleProposals.value.forEach((r) => (r._status = 'rejected'));
const approveAllCatalog = () =>
    localCatalogProposals.value.forEach((c) => (c._status = 'approved'));
const rejectAllCatalog = () =>
    localCatalogProposals.value.forEach((c) => (c._status = 'rejected'));

// ─── Apply confirmed proposals ────────────────────────────────────────────────

const confirmApply = async () => {
    const approvedRoles = localRoleProposals.value.filter(
        (r) => r._status === 'approved',
    );
    const approvedCatalog = localCatalogProposals.value.filter(
        (c) => c._status === 'approved',
    );

    const scenarioId = props.scenarioId;

    if (!scenarioId) {
        alert('No se pudo determinar el ID del escenario.');
        return;
    }

    applying.value = true;
    try {
        const response = await fetch(
            `/api/scenarios/${scenarioId}/step2/agent-proposals/apply`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(
                        document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
                    ),
                },
                body: JSON.stringify({
                    approved_role_proposals: approvedRoles.map(
                        ({ _status, ...r }) => r,
                    ),
                    approved_catalog_proposals: approvedCatalog.map(
                        ({ _status, ...c }) => c,
                    ),
                }),
            },
        );

        if (!response.ok) {
            const err = await response.json();
            throw new Error(err.message ?? 'Error al aplicar propuestas');
        }

        emit('applied');
        emit('close');
    } catch (err: unknown) {
        const message =
            err instanceof Error ? err.message : 'Error desconocido';
        alert(`Error: ${message}`);
    } finally {
        applying.value = false;
    }
};

// ─── Display helpers ──────────────────────────────────────────────────────────

const getTypeVariant = (type: string) => {
    switch (type?.toLowerCase()) {
        case 'new':
            return 'secondary';
        case 'evolve':
            return 'primary';
        case 'replace':
            return 'warning';
        default:
            return 'glass';
    }
};

const getTypeIcon = (type: string): string => {
    switch (type?.toLowerCase()) {
        case 'new':
            return 'mdi-plus-circle';
        case 'evolve':
            return 'mdi-arrow-up-circle';
        case 'replace':
            return 'mdi-swap-horizontal';
        default:
            return 'mdi-circle';
    }
};

const getCatalogTypeVariant = (type: string) => {
    switch (type?.toLowerCase()) {
        case 'add':
            return 'secondary';
        case 'modify':
            return 'primary';
        case 'replace':
            return 'warning';
        default:
            return 'glass';
    }
};

const getChangeTypeVariant = (ct: string) => {
    switch (ct?.toLowerCase()) {
        case 'maintenance':
            return 'secondary';
        case 'transformation':
            return 'primary';
        case 'enrichment':
            return 'glass';
        case 'extinction':
            return 'error';
        default:
            return 'glass';
    }
};

const changeTypeLabel = (ct: string): string => {
    switch (ct?.toLowerCase()) {
        case 'maintenance':
            return 'Maintain';
        case 'transformation':
            return 'Transform';
        case 'enrichment':
            return 'Enrich';
        case 'extinction':
            return 'Legacy';
        default:
            return ct ?? '—';
    }
};

const archetypeLabel = (arch?: string): string => {
    switch (arch) {
        case 'E':
            return 'Strategic';
        case 'T':
            return 'Tactical';
        case 'O':
            return 'Operational';
        default:
            return 'Undefined';
    }
};

// ─── Level descriptions ───────────────────────────────────────────────────────

const LEVEL_DESCRIPTIONS: Record<number, { label: string; detail: string }> = {
    1: {
        label: 'Introductory',
        detail: 'Theoretical basic knowledge. Requires constant supervision.',
    },
    2: {
        label: 'Developing',
        detail: 'Executes routine tasks with occasional supervision.',
    },
    3: {
        label: 'Competent',
        detail: 'Autonomous in standard situations. Solves medium complexity problems.',
    },
    4: {
        label: 'Advanced',
        detail: 'Internal technical reference. Mentors others and leads complex initiatives.',
    },
    5: {
        label: 'Expert',
        detail: 'Recognized authority. Defines standards and innovates in the discipline.',
    },
};

// ─── Cube semaphore logic ─────────────────────────────────────────────────────

const cubeSignalColor = (
    archetype: string | undefined,
    level: number,
    isCore: boolean,
): string => {
    if (!archetype) return 'grey';
    const l = level ?? 0;
    if (archetype === 'O' && l > 3 && !isCore) return '#fbbf24'; // amber
    if (archetype === 'T' && l > 4 && !isCore) return '#fbbf24';
    if (archetype === 'E' && l < 3 && !isCore) return '#60a5fa'; // blue
    return '#10b981'; // emerald
};

const cubeSignalLabel = (
    archetype: string | undefined,
    level: number,
    isCore: boolean,
): string => {
    const color = cubeSignalColor(archetype, level, isCore);
    if (color === '#fbbf24') return '⚠️ Potential overhead for this archetype';
    if (color === '#60a5fa')
        return 'ℹ️ Support competency — low level for Strategic role';
    return '✅ Consistent with archetype';
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
