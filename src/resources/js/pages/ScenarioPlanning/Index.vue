<template>
    <div class="prototype-map-root" ref="mapRoot" :class="{ 'with-node-sidebar': nodeSidebarVisible, 'with-node-sidebar-collapsed': nodeSidebarCollapsed }">
        <div
            class="map-controls"
            style="
                margin-bottom: 8px;
                display: flex;
                gap: 8px;
                align-items: center;
            "
        >
            <!-- top control removed; 'Crear capacidad' integrated next to home control -->
            <div v-if="props.scenario && props.scenario.id">
                Escenario: {{ props.scenario?.name || '—' }}
            </div>
            <!-- Position controls removed: positions are saved/reset by default -->
            <!-- 'Volver a la vista inicial' integrado en la esfera del escenario y en el borde derecho del diagrama -->
                        <!-- extra soft halo/gloss to ensure bubble effect is visible on all nodes -->
                        <circle
                            class="node-gloss"
                            r="36"
                            fill="none"
                            stroke="#ffffff"
                            stroke-opacity="0.04"
                            stroke-width="6"
                            filter="url(#softGlow)"
                        />
            <!-- fullscreen button removed (disabled for now) -->
            <v-btn
                small
                :variant="followScenario ? 'tonal' : 'text'"
                :color="followScenario ? 'primary' : undefined"
                @click="followScenario = !followScenario"
                :title="followScenario ? 'Seguir origen: activado' : 'Seguir origen: desactivado'"
            >
                Seguir origen
            </v-btn>
        </div>
        <div v-if="!loaded">Cargando mapa...</div>
        <div v-else>
            <svg
                :width="width"
                :height="height"
                :viewBox="`0 0 ${width} ${height}`"
                class="map-canvas"
                style="touch-action: none"
            >
                <defs>
                    <linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#040914" stop-opacity="1" />
                        <stop offset="25%" stop-color="#071029" stop-opacity="1" />
                        <stop offset="70%" stop-color="#071a2a" stop-opacity="1" />
                        <stop offset="100%" stop-color="#051018" stop-opacity="1" />
                    </linearGradient>

                    <radialGradient id="nodeGrad" cx="30%" cy="25%" r="70%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.75" />
                        <stop offset="12%" stop-color="#e8f6ff" stop-opacity="0.55" />
                        <stop offset="45%" stop-color="#6fc3ff" stop-opacity="0.95" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="1" />
                    </radialGradient>
                    <!-- iridescent overlay to simulate soap-bubble sheen -->
                    <radialGradient id="iridescentGrad" cx="70%" cy="30%" r="90%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.0" />
                        <stop offset="18%" stop-color="#ffd6f7" stop-opacity="0.06" />
                        <stop offset="32%" stop-color="#d6f7ff" stop-opacity="0.07" />
                        <stop offset="48%" stop-color="#fff2d6" stop-opacity="0.06" />
                        <stop offset="68%" stop-color="#d6fff3" stop-opacity="0.05" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.0" />
                    </radialGradient>
                    <!-- bubble outer gradient: darker near rim, lighter inward to simulate inner glow -->
                    <radialGradient id="bubbleOuterGrad" cx="50%" cy="50%" r="80%">
                        <stop offset="0%" stop-color="#0b66b2" stop-opacity="0.06" />
                        <stop offset="60%" stop-color="#6fc3ff" stop-opacity="0.18" />
                        <stop offset="85%" stop-color="#6fc3ff" stop-opacity="0.06" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="0.02" />
                    </radialGradient>

                    <!-- core gradient: small bright core to suggest nucleus -->
                    <radialGradient id="bubbleCoreGrad" cx="35%" cy="30%" r="60%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.9" />
                        <stop offset="35%" stop-color="#dffaff" stop-opacity="0.7" />
                        <stop offset="100%" stop-color="#6fc3ff" stop-opacity="0.0" />
                    </radialGradient>

                    <!-- inner glow filter: blur + composite to push glow inward -->
                    <filter id="innerGlow" x="-30%" y="-30%" width="160%" height="160%">
                        <feGaussianBlur in="SourceAlpha" stdDeviation="6" result="blurInner" />
                        <feComposite in="blurInner" in2="SourceGraphic" operator="arithmetic" k1="0" k2="1" k3="-1" k4="0" result="innerComp" />
                        <feMerge>
                            <feMergeNode in="innerComp" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- glass fill for glassmorphism appearance on main nodes -->
                    <radialGradient id="glassGrad" cx="35%" cy="28%" r="72%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.30" />
                        <stop offset="30%" stop-color="#dff6ff" stop-opacity="0.12" />
                        <stop offset="70%" stop-color="#9fd8ff" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="0.18" />
                    </radialGradient>

                    <filter id="glassBlur" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur stdDeviation="4" result="gblur" />
                        <feMerge>
                            <feMergeNode in="gblur" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <filter
                        id="softGlow"
                        x="-50%"
                        y="-50%"
                        width="200%"
                        height="200%"
                    >
                        <feGaussianBlur stdDeviation="6" result="blur" />
                        <feMerge>
                            <feMergeNode in="blur" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- gradient for child edges -->
                    <linearGradient id="childGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#7dd3fc" stop-opacity="1" />
                        <stop offset="100%" stop-color="#60a5fa" stop-opacity="1" />
                    </linearGradient>

                    <!-- gradient for scenario->child edges (distinct visual) -->
                    <linearGradient id="scenarioEdgeGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#9be7ff" stop-opacity="0.98" />
                        <stop offset="50%" stop-color="#6fb8ff" stop-opacity="0.9" />
                        <stop offset="100%" stop-color="#3fa6ff" stop-opacity="0.82" />
                    </linearGradient>

                    <!-- subtle gradient + glow for main edges -->
                    <linearGradient id="edgeGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#6fe7ff" stop-opacity="0.85" />
                        <stop offset="60%" stop-color="#66b8ff" stop-opacity="0.7" />
                        <stop offset="100%" stop-color="#9bd0ff" stop-opacity="0.55" />
                    </linearGradient>

                    <filter id="edgeGlow" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="3" result="blurEdge" />
                        <feMerge>
                            <feMergeNode in="blurEdge" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- arrow marker for child edges -->
                    <marker id="childArrow" markerUnits="strokeWidth" markerWidth="8" markerHeight="8" refX="6" refY="4" orient="auto">
                        <path d="M0,0 L8,4 L0,8 z" fill="url(#childGrad)" />
                    </marker>

                    <!-- arrow marker for scenario edges -->
                    <!-- scenario arrow removed: prefer clean lines without arrowheads -->

                    <filter
                        id="innerShadow"
                        x="-20%"
                        y="-20%"
                        width="140%"
                        height="140%"
                    >
                        <feOffset dx="0" dy="2" result="off" />
                        <feGaussianBlur
                            in="off"
                            stdDeviation="2"
                            result="blur2"
                        />
                        <feComposite
                            in="SourceGraphic"
                            in2="blur2"
                            operator="over"
                        />
                    </filter>

                    <!-- soft specular blur for highlights (used on small highlight shapes) -->
                    <filter id="specular" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="3" result="spec" />
                        <feMerge>
                            <feMergeNode in="spec" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>
                </defs>

                <!-- subtle background rect for contrast (rounded + border/glow) -->
                <rect
                    x="0"
                    y="0"
                    :width="width"
                    :height="height"
                    rx="12"
                    ry="12"
                    fill="url(#bgGrad)"
                />
                <!-- container border/glow to emulate glass frame -->
                <rect
                    x="1"
                    y="1"
                    :width="width - 2"
                    :height="height - 2"
                    rx="12"
                    ry="12"
                    fill="none"
                    stroke="rgba(255,255,255,0.04)"
                    stroke-width="1"
                    filter="url(#softGlow)"
                />

                <!-- edges -->
                <g class="viewport-group" :style="viewportStyle">
                    <!-- edges -->
                    <g class="edges">
                        <line
                            v-for="(e, idx) in edges"
                            :key="`edge-${idx}`"
                            :x1="renderedNodeById(e.source)?.x ?? undefined"
                            :y1="renderedNodeById(e.source)?.y ?? undefined"
                            :x2="renderedNodeById(e.target)?.x ?? undefined"
                            :y2="renderedNodeById(e.target)?.y ?? undefined"
                            class="edge-line"
                            :stroke="`url(#edgeGrad)`"
                            stroke-width="2"
                            stroke-linecap="round"
                            filter="url(#edgeGlow)"
                            stroke-opacity="0.9"
                        />
                    </g>
                    <!-- scenario -> capability edges (distinct group so we can style/animate) -->
                    <g class="scenario-edges">
                        <line
                            v-for="(e, idx) in scenarioEdges"
                            :key="`scenario-edge-${idx}`"
                            :x1="renderedNodeById(e.source)?.x ?? undefined"
                            :y1="renderedNodeById(e.source)?.y ?? undefined"
                            :x2="renderedNodeById(e.target)?.x ?? undefined"
                            :y2="renderedNodeById(e.target)?.y ?? undefined"
                                class="edge-line scenario-edge"
                                stroke="url(#scenarioEdgeGrad)"
                                stroke-width="2.6"
                                stroke-linecap="round"
                                filter="url(#edgeGlow)"
                                stroke-opacity="0.95"
                        />
                    </g>

                    <!-- nodes -->
                    <g class="nodes">
                    <!-- scenario/origin node (optional) -->
                    <g
                        v-if="scenarioNode"
                        :style="{ transform: `translate(${scenarioNode.x}px, ${scenarioNode.y}px)` }"
                        class="node-group scenario-node"
                        :data-node-id="scenarioNode.id"
                        @click.stop="openScenarioInfo"
                        :title="'Ver información del escenario'"
                        style="cursor: pointer"
                    >
                        <title>{{ scenarioNode.name }}</title>
                        <!-- Smaller parent node (scenario) with icon support -->
                        <circle
                            class="node-circle"
                            r="22"
                            fill="url(#glassGrad)"
                            filter="url(#innerGlow)"
                            stroke="rgba(255,255,255,0.14)"
                            stroke-width="1.2"
                        />
                        <circle class="node-iridescence" r="22" fill="url(#iridescentGrad)" opacity="0.18" style="mix-blend-mode: screen" />
                        <circle class="node-rim" r="22" fill="none" stroke="#ffffff" stroke-opacity="0.08" stroke-width="1.2" />

                        <!-- Icon inside the scenario node: map-pin SVG -->
                        <g class="scenario-icon" transform="translate(0,-4)">
                            <circle r="12" fill="rgba(245, 26, 26, 0.04)" />
                            <g transform="translate(0,0) scale(0.9)">
                                <!-- pin shape -->
                                <path d="M0,-8 C4,-8 7,-5 7,-1 C7,4 0,11 0,11 C0,11 -7,4 -7,-1 C-7,-5 -4,-8 0,-8 Z" fill="rgba(255,255,255,0.06)" />
                                <circle cx="0" cy="-2" r="1.8" fill="#ffcc66" />
                            </g>
                        </g>

                        <!-- label above the smaller node for clearer hierarchy -->
                        <text x="0" y="-48" text-anchor="middle" class="node-label" style="font-size:16px; font-weight:700">{{ scenarioNode.name }}</text>
                    </g>
                    <!-- Small integrated control on the right edge to open scenario info (subtle) -->
                    <g
                        class="scenario-edge-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 36)`"
                        @click.stop="openScenarioInfo"
                        style="cursor: pointer"
                        aria-label="Abrir información del escenario"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)"/>
                        <title>Volver a la vista inicial / Mostrar información del escenario</title>
                        <!-- Home icon (simple polygon) -->
                        <polygon points="0,-8 -10,2 -5,2 -5,9 5,9 5,2 10,2" fill="#dbeafe" transform="scale(0.9)" />
                    </g>
                    <!-- Create capability control: placed under the home control -->
                    <g
                        class="scenario-create-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 72)`"
                        @click.stop="createCapabilityClicked"
                        style="cursor: pointer"
                        aria-label="Crear capacidad"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.05)"/>
                        <title>Crear capacidad</title>
                        <text x="0" y="4" text-anchor="middle" font-size="14" fill="#dbeafe" style="font-weight:700">+</text>
                    </g>
                    <!-- child edges -->
                    <g class="child-edges">
                        <line
                            v-for="(e, idx) in childEdges"
                            :key="`child-edge-${idx}`"
                            :x1="renderedNodeById(e.source)?.x ?? (e.source < 0 ? (childNodeById(e.source)?.x ?? undefined) : undefined)"
                            :y1="renderedNodeById(e.source)?.y ?? (e.source < 0 ? (childNodeById(e.source)?.y ?? undefined) : undefined)"
                            :x2="childNodeById(e.target)?.x ?? undefined"
                            :y2="childNodeById(e.target)?.y ?? undefined"
                            :class="['edge-line','child-edge', (e as any).isScenarioEdge ? 'scenario-edge' : '']"
                            :stroke="(e as any).isScenarioEdge ? 'url(#scenarioEdgeGrad)' : 'url(#childGrad)'"
                            :stroke-width="(e as any).isScenarioEdge ? 2.8 : 2.2"
                            stroke-linecap="round"
                            :marker-end="(e as any).isScenarioEdge ? undefined : 'url(#childArrow)'"
                            :filter="(e as any).isScenarioEdge ? 'url(#edgeGlow)' : 'url(#softGlow)'"
                            :stroke-opacity="(e as any).isScenarioEdge ? 0.98 : 0.95"
                        />
                    </g>

                    <g
                        v-for="node in nodes"
                        :key="node.id"
                        :style="{ transform: `translate(${renderNodeX(node)}px, ${node.y}px)` }"
                        class="node-group"
                        :data-node-id="node.id"
                        :class="{
                            critical: !!node.is_critical,
                            focused: focusedNode && focusedNode.id === node.id,
                            dragging: dragging && dragging.id === node.id,
                            small: focusedNode && focusedNode.id !== node.id,
                        }"
                        @pointerdown.prevent="startDrag(node, $event)"
                        @click.stop="(e) => handleNodeClick(node, e)"
                    >
                        <title>{{ node.name }}</title>
                        <circle
                            class="node-circle"
                            r="34"
                            fill="url(#bubbleOuterGrad)"
                            filter="url(#innerGlow)"
                            stroke="rgba(255,255,255,0.12)"
                            stroke-opacity="1"
                            stroke-width="1.2"
                        />
                        <!-- iridescent sheen overlay: semitransparent, uses blend to simulate soap colors -->
                        <circle
                            class="node-iridescence"
                            r="34"
                            fill="url(#iridescentGrad)"
                            opacity="0.22"
                            style="mix-blend-mode: screen"
                        />
                        <!-- bubble-style highlight: small blurred specular on top-left -->
                        <!-- <ellipse
                            class="node-reflection"
                            cx="-12"
                            cy="-14"
                            rx="14"
                            ry="9"
                            fill="#ffffff"
                            fill-opacity="0.18"
                            transform="rotate(-22)"
                            filter="url(#specular)"
                        /> -->
                        <!-- inner core that suggests nucleus -->
                        <circle
                            class="node-core"
                            r="12"
                            fill="url(#bubbleCoreGrad)"
                            filter="url(#specular)"
                        />
                        <!-- subtle glossy rim to enhance bubble feel -->
                        <circle
                            class="node-rim"
                            r="34"
                            fill="none"
                            stroke="#ffffff"
                            stroke-opacity="0.08"
                            stroke-width="1.4"
                        />
                        <circle
                            v-if="node.is_critical"
                            class="node-inner"
                            r="12"
                            fill="#ff5050"
                            fill-opacity="0.95"
                        />
                        <text :x="0" :y="38" text-anchor="middle" class="node-label">
                            <tspan v-for="(line, idx) in ((node as any).displayName ?? node.name).split('\n')" :key="idx" :x="0" :dy="idx === 0 ? 0 : 12">{{ line }}</tspan>
                        </text>
                    </g>

                    <!-- child nodes (competencies) -->
                    <g class="child-nodes">
                        <g
                            v-for="c in childNodes"
                            :key="c.id"
                            :style="{ transform: `translate(${c.x}px, ${c.y}px) scale(${c.__scale ?? 1})`, opacity: (c.__opacity ?? 1), transitionDelay: (c.__delay ? c.__delay + 'ms' : undefined), filter: c.__filter ? c.__filter : undefined }"
                                class="node-group child-node"
                            :data-node-id="c.id"
                            @click.stop="(e) => handleNodeClick(c, e)"
                        >
                            <title>{{ c.name }}</title>
                            <circle
                                class="node-circle"
                                :r="20"
                                fill="#2b2b2b"
                                stroke="#ffffff"
                                stroke-opacity="0.06"
                                stroke-width="1"
                            />
                            <!-- child node: iridescent sheen + small reflection to match bubble style -->
                            <circle
                                class="node-iridescence child-iridescence"
                                :r="20"
                                fill="url(#iridescentGrad)"
                                opacity="0.18"
                                style="mix-blend-mode: screen"
                            />
                            <ellipse
                                class="node-reflection child-reflection"
                                cx="-6"
                                cy="-6"
                                rx="7"
                                ry="4.5"
                                fill="#ffffff"
                                fill-opacity="0.14"
                                transform="rotate(-22)"
                                filter="url(#specular)"
                            />
                            <circle
                                class="node-rim child-rim"
                                :r="20"
                                fill="none"
                                stroke="#ffffff"
                                stroke-opacity="0.06"
                                stroke-width="1"
                            />
                            <circle
                                class="node-gloss child-gloss"
                                :r="22"
                                fill="none"
                                stroke="#ffffff"
                                stroke-opacity="0.04"
                                stroke-width="4"
                                filter="url(#softGlow)"
                            />
                            <text :x="0" :y="22" text-anchor="middle" class="node-label" style="font-size:10px">
                                <tspan v-for="(line, idx) in String((c as any).displayName ?? c.name).split('\n')" :key="idx" :x="0" :dy="idx === 0 ? 0 : 10">{{ line }}</tspan>
                            </text>
                        </g>
                    </g>
                    </g>
                </g>
            </svg>

            <!-- Reorder control -->
            <div style="position:absolute; right:16px; top:12px; z-index:99999">
                <v-btn small color="secondary" @click="reorderNodes" title="Reordenar nodos">
                    Reordenar
                </v-btn>
            </div>

            <!-- Nodo: panel lateral que desplaza contenido en vista normal -->
            <transition name="slide-fade">
                            <aside v-show="showSidebar || focusedNode"
                                class="node-details-sidebar glass-panel-strong"
                                :class="[{ collapsed: nodeSidebarCollapsed }, sidebarTheme === 'dark' ? 'theme-dark' : 'theme-light']"
                                :style="panelStyle"
                            >
                    <div class="d-flex justify-space-between align-center mb-2 panel-header">
                        <strong>{{ focusedNode ? focusedNode.name : (showSidebar ? 'Escenario' : 'Detalle') }}</strong>
                        <div class="d-flex align-center" style="gap:8px">
                            <v-btn icon small variant="text" @click="toggleSidebarTheme" :title="sidebarTheme === 'dark' ? 'Tema claro' : 'Tema oscuro'">
                                <v-icon :icon="sidebarTheme === 'dark' ? 'mdi-weather-sunny' : 'mdi-weather-night'" />
                            </v-btn>
                            <v-btn icon small variant="text" @click="focusedNode ? closeTooltip() : toggleSidebar()" v-if="!nodeSidebarCollapsed">
                                <v-icon icon="mdi-close" />
                            </v-btn>
                        </div>
                        <!-- collapse/expand toggle (visible on the inner edge) -->
                        <div class="sidebar-collapse-toggle">
                            <v-btn icon small variant="text" @click="toggleNodeSidebarCollapse" :title="nodeSidebarCollapsed ? 'Mostrar panel' : 'Ocultar panel'">
                                <v-icon :icon="nodeSidebarCollapsed ? 'mdi-chevron-left' : 'mdi-chevron-right'" />
                            </v-btn>
                        </div>
                    </div>

                    <template v-if="focusedNode">
                    <!-- Basic metadata block -->
                    <div class="text-xs text-white/60 mb-2">
                        <div><strong>ID:</strong> {{ (focusedNode as any).id ?? '—' }}</div>
                        <div><strong>Competencias:</strong> {{ ((focusedNode as any).competencies || []).length }}</div>
                    </div>

                    <div class="text-small text-medium-emphasis mb-2">
                        <!-- If focusedNode is a competency (child node), show its attributes -->
                        <template v-if="(focusedNode as any).skills || (focusedNode as any).compId">
                            <div v-if="(focusedNode as any).description">{{ (focusedNode as any).description }}</div>
                            <div class="text-xs text-white/60">Readiness: {{ (focusedNode as any).readiness ?? '—' }}%</div>
                            <div class="mt-2 text-xs text-white/60 mb-1">Skills</div>
                            <ul class="pl-3 mb-0">
                                <li v-for="(s, idx) in (focusedNode as any).skills" :key="idx">
                                    {{ s.name }} <span class="text-white/50">(weight: {{ s.weight ?? s.pivot?.weight ?? '—' }}, readiness: {{ s.readiness ?? '—' }}%)</span>
                                </li>
                            </ul>
                        </template>

                        <!-- If focusedNode is a capability, show its competencies list and description -->
                        <template v-else>
                            <!-- Editable form for capability and pivot with scroll + slider -->
                            <div class="sidebar-body text-sm mt-2" style="position:relative;">
                                <div ref="editFormScrollEl" style="max-height:360px; overflow:auto; padding-right:12px;">
                                    <v-form>
                                        <v-text-field v-model="editCapName" label="Nombre" required />
                                        <v-textarea v-model="editCapDescription" label="Descripción" rows="3" />
                                        <div style="display:flex; gap:8px">
                                            <v-text-field v-model="editCapImportance" label="Importancia" type="number" style="flex:1" />
                                            <v-text-field v-model="editCapLevel" label="Nivel" type="number" style="flex:1" />
                                        </div>

                                        <div style="margin-top:12px; font-weight:700">Atributos de la relación con el escenario</div>
                                        <v-select v-model="editPivotStrategicRole" :items="['target','watch','sunset']" label="Strategic role" />
                                        <div style="display:flex; gap:8px">
                                            <v-text-field v-model="editPivotStrategicWeight" label="Strategic weight" type="number" style="flex:1" />
                                            <v-text-field v-model="editPivotPriority" label="Priority" type="number" style="flex:1" />
                                        </div>
                                        <v-text-field v-model="editPivotRequiredLevel" label="Required level" type="number" />
                                        <v-checkbox v-model="editPivotIsCritical" label="Is critical" />
                                        <v-textarea v-model="editPivotRationale" label="Rationale" rows="2" />

                                        <div style="display:flex; gap:8px; margin-top:12px">
                                            <v-btn color="error" @click="deleteFocusedNode" :loading="savingNode">Eliminar</v-btn>
                                            <v-spacer />
                                            <v-btn color="primary" @click="saveFocusedNode" :loading="savingNode">Guardar</v-btn>
                                            <v-btn text @click="resetFocusedEdits">Cancelar</v-btn>
                                        </div>
                                    </v-form>
                                </div>
                                <!-- vertical slider to control scroll position (0-100) -->
                                <v-slider
                                    v-model="editFormScrollPercent"
                                    vertical
                                    hide-details
                                    :min="0"
                                    :max="100"
                                    step="1"
                                    @input="onEditSliderInput"
                                    style="position:absolute; right:8px; top:8px; height: calc(100% - 16px); width:28px;"
                                />
                            </div>
                        </template>
                        </div>
                    </template>
                </aside>
            </transition>
            <!-- Create capability modal: form exposes fields from `capabilities` and `scenario_capabilities` -->
            <v-dialog v-model="createModalVisible" max-width="720">
                <v-card>
                    <v-card-title>Crear capacidad</v-card-title>
                    <v-card-text>
                        <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                            <v-text-field v-model="newCapName" label="Nombre" required />
                            <v-text-field v-model="newCapType" label="Tipo" />
                            <v-text-field v-model="newCapCategory" label="Categoría" />
                            <v-text-field v-model="newCapImportance" label="Importancia (1-5)" type="number" />
                            <v-textarea v-model="newCapDescription" label="Descripción" rows="3" style="grid-column: 1 / -1" />
                        </div>

                        <div class="mt-3" style="margin-top:12px">
                            <div style="font-weight:700; margin-bottom:6px">Atributos para el escenario (scenario_capabilities)</div>
                            <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                                <v-select v-model="pivotStrategicRole" :items="['target','watch','sunset']" label="Strategic role" />
                                <v-text-field v-model="pivotStrategicWeight" label="Strategic weight" type="number" />
                                <v-text-field v-model="pivotPriority" label="Priority (1-5)" type="number" />
                                <v-text-field v-model="pivotRequiredLevel" label="Required level (1-5)" type="number" />
                                <v-checkbox v-model="pivotIsCritical" label="Is critical" />
                                <v-textarea v-model="pivotRationale" label="Rationale" rows="2" style="grid-column: 1 / -1" />
                            </div>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="createModalVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" :loading="creating" @click="saveNewCapability">Guardar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            
            <div class="cap-list" v-if="nodes.length === 0">
                No hay capacidades para mostrar.
            </div>
                <!-- debug controls removed -->
        </div>
    </div>
</template>

<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import * as d3 from 'd3';
import { onMounted, ref, watch, onBeforeUnmount, computed, nextTick } from 'vue';
import type { CSSProperties } from 'vue';
import type { NodeItem, Edge, ConnectionPayload } from '@/types/brain';
interface Props {
    scenario?: {
        id?: number;
        name?: string;
        description?: string;
        status?: string;
        fiscal_year?: number | string;
        organization_id?: number | string;
        capabilities?: any[];
        connections?: any[];
        created_at?: string | null;
        updated_at?: string | null;
    };
    // optional: number of columns for child competencies layout
    childColumns?: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'createCapability'): void;
}>();

// Create-capability modal state
const createModalVisible = ref(false);
const availableCapabilities = ref<any[]>([]);
const newCapName = ref('');
const newCapDescription = ref('');
const newCapType = ref('');
const newCapCategory = ref('');
const newCapImportance = ref<number | null>(3);
// pivot fields
const pivotStrategicRole = ref('target');
const pivotStrategicWeight = ref<number | null>(10);
const pivotPriority = ref<number | null>(1);
const pivotRationale = ref('');
const pivotRequiredLevel = ref<number | null>(3);
const pivotIsCritical = ref(false);
const creating = ref(false);
const api = useApi();
const loaded = ref(false);
const nodes = ref<Array<NodeItem>>([]);
const edges = ref<Array<Edge>>([]);
const dragging = ref<any>(null);
const dragOffset = ref({ x: 0, y: 0 });
const positionsDirty = ref(false);
const { showSuccess, showError } = useNotification();
const width = ref(900);
const height = ref(600);
const mapRoot = ref<HTMLElement | null>(null);
let lastItems: any[] = [];
const focusedNode = ref<NodeItem | null>(null);
const tooltipX = ref(0);
const tooltipY = ref(0);
const childNodes = ref<Array<any>>([]);
const childEdges = ref<Array<Edge>>([]);
const scenarioEdges = ref<Array<Edge>>([]);
const showSidebar = ref(false);
// editing focused node / pivot
const editCapName = ref('');
const editCapDescription = ref('');
const editCapImportance = ref<number | null>(null);
const editCapLevel = ref<number | null>(null);

const editPivotStrategicRole = ref('target');
const editPivotStrategicWeight = ref<number | null>(10);
const editPivotPriority = ref<number | null>(1);
const editPivotRationale = ref('');
const editPivotRequiredLevel = ref<number | null>(3);
const editPivotIsCritical = ref(false);
const savingNode = ref(false);
// slider / scroll sync for edit form
const editFormScrollEl = ref<HTMLElement | null>(null);
const editFormScrollPercent = ref<number>(0); // 0..100
let editFormScrollHandler: ((ev: Event) => void) | null = null;

function onEditSliderInput(val: number) {
    const el = editFormScrollEl.value;
    if (!el) return;
    const max = Math.max(0, el.scrollHeight - el.clientHeight);
    const target = Math.round((val / 100) * max);
    el.scrollTop = target;
}

function syncSliderFromScroll() {
    const el = editFormScrollEl.value;
    if (!el) return;
    const max = Math.max(0, el.scrollHeight - el.clientHeight);
    const percent = max === 0 ? 0 : Math.round((el.scrollTop / max) * 100);
    editFormScrollPercent.value = percent;
}
// localStorage keys
const LS_KEYS = {
    collapsed: 'stratos:scenario:nodeSidebarCollapsed',
    lastView: 'stratos:scenario:lastView',
    lastFocusedId: 'stratos:scenario:lastFocusedNodeId',
};
const savedFocusedNodeId = ref<number | null>(null);

// viewport transform for pan/zoom
const viewX = ref(0);
const viewY = ref(0);
const viewScale = ref(1);
const viewportStyle = computed(() => ({
    transform: `translate(${viewX.value}px, ${viewY.value}px) scale(${viewScale.value})`,
    transformOrigin: '0 0',
}));

// scenario/origin node that can follow a selected child
const scenarioNode = ref<any>(null);
const followScenario = ref<boolean>(false);

// Transition timing used by `.node-group` CSS (keep in sync with CSS)
const TRANSITION_MS = 420;
const TRANSITION_BUFFER = 60; // small buffer to ensure browser finished
function wait(ms: number) {
    return new Promise((res) => setTimeout(res, ms));
}

// Wait until the element for a node finishes its CSS transition (transform).
// Resolves true if transitionend fired, false if timed out or not found.
function waitForTransitionForNode(nodeId: number | string, timeoutMs = TRANSITION_MS * 2 + TRANSITION_BUFFER) {
    return new Promise<boolean>((resolve) => {
        const sel = `[data-node-id="${nodeId}"]`;
        let el: Element | null = document.querySelector(sel);
        let timer: ReturnType<typeof setTimeout> | null = null;
        const cleanup = () => {
            if (el) el.removeEventListener('transitionend', onEnd as EventListener);
            if (timer) clearTimeout(timer);
        };
        const onEnd = (ev: Event) => {
            const te = ev as TransitionEvent;
            if (!te.propertyName || te.propertyName === 'transform') {
                cleanup();
                resolve(true);
            }
        };

        timer = setTimeout(() => {
            cleanup();
            resolve(false);
        }, timeoutMs);

        if (!el) {
            // element may not be in DOM yet; wait a tick then try again
            nextTick().then(() => {
                el = document.querySelector(sel);
                if (!el) {
                    if (timer) clearTimeout(timer);
                    resolve(false);
                    return;
                }
                el.addEventListener('transitionend', onEnd as EventListener);
            });
        } else {
            el.addEventListener('transitionend', onEnd as EventListener);
        }
    });
}

const originalPositions = ref<Map<number, { x: number; y: number }>>(new Map());

function centerOnNode(node: NodeItem, prev?: NodeItem) {
    if (!node) return;
    // If we're focusing a different node, immediately clear any existing child nodes/edges
    // so previous child animations don't continue while the parent moves.
    if (prev && prev.id !== node.id) {
        childNodes.value = [];
        childEdges.value = [];
    }
    // Save original positions the first time we focus a node so we can restore later
    if (originalPositions.value.size === 0) {
        nodes.value.forEach((n) => {
            originalPositions.value.set(n.id, { x: n.x ?? 0, y: n.y ?? 0 });
        });
        // also save scenario node if present
        if (scenarioNode.value) originalPositions.value.set(scenarioNode.value.id, { x: scenarioNode.value.x, y: scenarioNode.value.y });
    }

    // If there was a previously focused node, swap positions with it and keep others unchanged.
    if (prev && prev.id !== node.id) {
        const prevNode = nodes.value.find((n) => n.id === prev.id);
        const newNode = nodes.value.find((n) => n.id === node.id);
        if (prevNode && newNode) {
            const tx = prevNode.x ?? 0;
            const ty = prevNode.y ?? 0;
            prevNode.x = newNode.x ?? tx;
            prevNode.y = newNode.y ?? ty;
            newNode.x = tx;
            newNode.y = ty;
            // apply updated nodes (keep rest intact)
            nodes.value = nodes.value.map((n) => {
                if (!n) return n;
                if (n.id === newNode.id) return { ...newNode } as any;
                if (n.id === prevNode.id) return { ...prevNode } as any;
                return n;
            });
            // update scenario node if following (keep previous behavior)
            if (followScenario.value && scenarioNode.value) {
                // place scenario relative to the currently focused (new) node
                const centerX = Math.round(width.value / 2);
                const VERTICAL_FOCUS_RATIO = 0.25;
                const centerY = Math.round(height.value * VERTICAL_FOCUS_RATIO);
                const offsetY = 80;
                scenarioNode.value.x = centerX;
                scenarioNode.value.y = Math.round(centerY - offsetY);
            }
            return;
        }
    }

    // compute absolute center position for focused node
    const centerX = Math.round(width.value / 2);
    const VERTICAL_FOCUS_RATIO = 0.25;
    const centerY = Math.round(height.value * VERTICAL_FOCUS_RATIO);

    // fixed side columns (absolute x coords)
    const leftX = Math.round(width.value * 0.18);
    const rightX = Math.round(width.value * 0.82);

    // separate other nodes into balanced left/right groups.
    // Sort remaining nodes by their original X (fallback to current x), then split into two halves
    const others = nodes.value.filter((n) => n && n.id !== node.id);
    const othersSorted = others
        .map((n) => ({ n, origX: n.x ?? originalPositions.value.get(n.id)?.x ?? width.value / 2 }))
        .sort((a, b) => a.origX - b.origX)
        .map((o) => o.n);
    const mid = Math.ceil(othersSorted.length / 2);
    const leftGroup = othersSorted.slice(0, mid);
    const rightGroup = othersSorted.slice(mid);

    // sort by original Y to keep visual order
    const getOrigY = (n: any) => n.y ?? originalPositions.value.get(n.id)?.y ?? centerY;
    leftGroup.sort((a, b) => getOrigY(a) - getOrigY(b));
    rightGroup.sort((a, b) => getOrigY(a) - getOrigY(b));

    // compute vertical distribution bounds
    const minY = 64;
    const maxY = Math.max(120, height.value - 64);

    const distribute = (group: any[], targetX: number, side: 'left' | 'right') => {
        if (group.length === 0) return;
        const len = group.length;
        // compute spacing dynamically based on available vertical space to avoid overlaps
        const available = Math.max(0, maxY - minY);
        // node visual sizes: main node radius ~34, ensure center-to-center spacing avoids overlap
        const FOCUS_RADIUS = 34;
        const minSpacing = Math.max(48, FOCUS_RADIUS * 2 + 8); // safe minimum spacing between centers
        const maxSpacing = 140; // cap spacing to avoid overly spread columns
        const spacing = len > 1 ? Math.min(maxSpacing, Math.max(minSpacing, Math.floor(available / (len - 1)))) : 0;

        // protect a vertical band around the focused node so distributed nodes don't overlap it
        const focusBand = Math.round(FOCUS_RADIUS + 12);
        const protectedTop = Math.max(minY, centerY - focusBand);
        const protectedBottom = Math.min(maxY, centerY + focusBand);

        // if group is large, split into multiple parallel columns to avoid vertical crowding
        const maxPerColumn = 5;
        if (len <= maxPerColumn) {
            let startY = Math.round(centerY - ((len - 1) * spacing) / 2);
            const endY = startY + (len - 1) * spacing;
            // If this span intersects the protected focused band, shift up or down to avoid overlap
            const intersectsProtected = !(endY < protectedTop || startY > protectedBottom);
            if (intersectsProtected) {
                // prefer shifting up if there is more room above, otherwise shift down
                const roomAbove = protectedTop - minY;
                const roomBelow = maxY - protectedBottom;
                if (roomAbove >= roomBelow) {
                    const shift = Math.min(roomAbove, protectedBottom - startY + focusBand);
                    startY = Math.max(minY, startY - shift);
                } else {
                    const shift = Math.min(roomBelow, endY - protectedTop + focusBand);
                    startY = Math.min(maxY - (len - 1) * spacing, startY + shift);
                }
            }

            for (let i = 0; i < len; i++) {
                const n = group[i];
                const proposedY = startY + i * spacing;
                n.x = targetX;
                n.y = clampY(proposedY);
            }
            return;
        }

        // multiple columns: compute number of columns and distribute items into them
        const cols = Math.ceil(len / maxPerColumn);
        const perCol = Math.ceil(len / cols);
        const colGap = 56; // horizontal gap between sub-columns

        for (let c = 0; c < cols; c++) {
            const colItems = group.slice(c * perCol, c * perCol + perCol);
            const colLen = colItems.length;
            const colSpacing = colLen > 1 ? Math.min(spacing, Math.floor(available / (colLen - 1))) : 0;
            const startY = Math.round(centerY - ((colLen - 1) * colSpacing) / 2);
            // compute x offset for this sub-column
            const offsetMult = (c - (cols - 1) / 2);
            const xOffset = Math.round(offsetMult * colGap) * (side === 'left' ? -1 : 1);
            const colX = targetX + xOffset;
            for (let i = 0; i < colLen; i++) {
                const n = colItems[i];
                const proposedY = startY + i * colSpacing;
                n.x = Math.min(Math.max(32, colX), Math.max(48, width.value - 32));
                n.y = clampY(proposedY);
            }
        }
    };

    // apply distribution
    distribute(leftGroup, leftX, 'left');
    distribute(rightGroup, rightX, 'right');

    // set focused node at center
    nodes.value = nodes.value.map((n) => {
        if (!n) return n;
        if (n.id === node.id) return { ...n, x: centerX, y: centerY } as any;
        const matched = leftGroup.find((m) => m.id === n.id) || rightGroup.find((m) => m.id === n.id);
        if (matched) return { ...n, x: matched.x, y: matched.y } as any;
        // fallback: clamp existing
        return { ...n, x: Math.min(Math.max(48, n.x ?? centerX), Math.max(160, width.value - 48)), y: clampY(n.y ?? centerY) } as any;
    });

    // Position scenario node (if following) relative to focused node
    if (followScenario.value && scenarioNode.value) {
        const offsetY = 80;
        scenarioNode.value.x = centerX;
        scenarioNode.value.y = Math.round(centerY - offsetY);
    }

    // keep tooltip/layout responsibilities handled elsewhere; we no longer pan the viewport
}

function setScenarioInitial() {
    scenarioNode.value = {
        id: 0,
        name: (props.scenario && (props.scenario.name || 'Escenario')) as string,
        x: Math.round(width.value / 2),
        y: Math.round(height.value * 0.12),
    };
}

// (Using CSS scale for visual shrinking; radii kept constant)

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

function openScenarioInfo() {
    // close any focused node and its children, then reset view and show scenario info
    closeTooltip();
    viewScale.value = 1;
    viewX.value = 0;
    viewY.value = 0;
    showSidebar.value = true;
}

// initialize edit fields when focusedNode changes
watch(focusedNode, (nv) => {
    if (!nv) {
        editCapName.value = '';
        editCapDescription.value = '';
        editCapImportance.value = null;
        editCapLevel.value = null;
        editPivotStrategicRole.value = 'target';
        editPivotStrategicWeight.value = 10;
        editPivotPriority.value = 1;
        editPivotRationale.value = '';
        editPivotRequiredLevel.value = 3;
        editPivotIsCritical.value = false;
        return;
    }
    // populate from focused node and its raw payload if present
    editCapName.value = (nv as any).name ?? '';
    editCapDescription.value = (nv as any).description ?? (nv as any).raw?.description ?? '';
    editCapImportance.value = (nv as any).importance ?? (nv as any).raw?.importance ?? null;
    editCapLevel.value = (nv as any).level ?? null;

    // pivot values: try several locations
    editPivotStrategicRole.value = (nv as any).strategic_role ?? (nv as any).raw?.strategic_role ?? 'target';
    editPivotStrategicWeight.value = (nv as any).raw?.strategic_weight ?? 10;
    editPivotPriority.value = (nv as any).raw?.priority ?? 1;
    editPivotRationale.value = (nv as any).raw?.rationale ?? '';
    editPivotRequiredLevel.value = (nv as any).raw?.required_level ?? (nv as any).required ?? 3;
    editPivotIsCritical.value = !!((nv as any).raw?.is_critical || (nv as any).is_critical);
});

function resetFocusedEdits() {
    // reset edits to current focusedNode state
    if (focusedNode.value) {
        const f = focusedNode.value as any;
        editCapName.value = f.name ?? '';
        editCapDescription.value = f.description ?? f.raw?.description ?? '';
        editCapImportance.value = f.importance ?? f.raw?.importance ?? null;
        editCapLevel.value = f.level ?? null;
        editPivotStrategicRole.value = f.strategic_role ?? f.raw?.strategic_role ?? 'target';
        editPivotStrategicWeight.value = f.raw?.strategic_weight ?? 10;
        editPivotPriority.value = f.raw?.priority ?? 1;
        editPivotRationale.value = f.raw?.rationale ?? '';
        editPivotRequiredLevel.value = f.raw?.required_level ?? f.required ?? 3;
        editPivotIsCritical.value = !!(f.raw?.is_critical || f.is_critical);
    }
}

async function saveFocusedNode() {
    if (!focusedNode.value) return;
    const id = (focusedNode.value as any).id;
    savingNode.value = true;
    try {
        // 1) attempt to update capability entity (if endpoint exists)
        const capPayload: any = {
            name: editCapName.value,
            description: editCapDescription.value,
            importance: editCapImportance.value,
            position_x: (focusedNode.value as any).x ?? undefined,
            position_y: (focusedNode.value as any).y ?? undefined,
        };
        try {
            await api.patch(`/api/capabilities/${id}`, capPayload);
            showSuccess('Capacidad actualizada');
        } catch (err) {
            // ignore if endpoint missing; leave server to handle via other flows
        }

        // 2) attempt to update pivot via best-effort PATCH endpoint
        const pivotPayload = {
            strategic_role: editPivotStrategicRole.value,
            strategic_weight: editPivotStrategicWeight.value,
            priority: editPivotPriority.value,
            rationale: editPivotRationale.value,
            required_level: editPivotRequiredLevel.value,
            is_critical: !!editPivotIsCritical.value,
        };
        try {
            // preferred: PATCH to scenario-specific pivot endpoint
            await api.patch(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${id}`, pivotPayload);
            showSuccess('Relación escenario–capacidad actualizada');
        } catch (errPivot) {
            try {
                // fallback: POST to create association (if missing) — server inserts only if not exists
                await api.post(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities`, {
                    name: editCapName.value,
                    description: editCapDescription.value || '',
                    importance: editCapImportance.value ?? 3,
                    type: null,
                    category: null,
                    strategic_role: pivotPayload.strategic_role,
                    strategic_weight: pivotPayload.strategic_weight,
                    priority: pivotPayload.priority,
                    rationale: pivotPayload.rationale,
                    required_level: pivotPayload.required_level,
                    is_critical: pivotPayload.is_critical,
                });
                showSuccess('Relación actualizada (fallback)');
            } catch (err2) {
                // final fallback: inform user
                showError('No se pudo actualizar la relación. Verifica el backend.');
            }
        }

        // after successful ops, refresh tree and restore focus without losing center position
        const focusedId = focusedNode.value ? (focusedNode.value as any).id : null;
        await loadTreeFromApi(props.scenario?.id);
        if (focusedId != null) {
            const restored = nodeById(focusedId);
            if (restored) {
                // set focused node to the refreshed object and re-center it
                focusedNode.value = restored as any;
                // allow DOM update then center to ensure visual centering
                await nextTick();
                centerOnNode(restored as NodeItem);
            }
        }
    } finally {
        savingNode.value = false;
    }
}

async function deleteFocusedNode() {
    if (!focusedNode.value) return;
    let id = (focusedNode.value as any).id;
    // if a child node (negative id), find its parent capability id
    if (typeof id === 'number' && id < 0) {
        const parentEdge = childEdges.value.find((e) => e.target === id);
        if (parentEdge) id = parentEdge.source;
    }
    // confirm destructive action
    const ok = window.confirm('¿Eliminar esta capacidad y su relación con el escenario? Esta acción es irreversible.');
    if (!ok) return;
    savingNode.value = true;
    try {
        let pivotErrStatus: number | null = null;
        let capErrStatus: number | null = null;
        // 1) attempt to delete pivot relation first (best-effort)
        try {
            await api.delete(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${id}`);
        } catch (e: any) {
            pivotErrStatus = e?.response?.status ?? null;
        }

        // 2) attempt to delete capability entity
        try {
            await api.delete(`/api/capabilities/${id}`);
            // remove locally if present
            nodes.value = nodes.value.filter((n) => n.id !== id);
            // remove any childNodes and edges referencing this capability
            childNodes.value = childNodes.value.filter((c) => !(c.__parentId === id || c.parentId === id || (c.raw && c.raw.capability_id === id)));
            edges.value = edges.value.filter((e) => e.source !== id && e.target !== id);
            childEdges.value = childEdges.value.filter((e) => e.source !== id && e.target !== id);
        } catch (e: any) {
            capErrStatus = e?.response?.status ?? null;
        }

        // If both endpoints returned 404 (not found), assume backend doesn't expose delete and remove locally
        if ((pivotErrStatus === 404 || pivotErrStatus === null) && (capErrStatus === 404 || capErrStatus === null)) {
            // remove locally anyway
            nodes.value = nodes.value.filter((n) => n.id !== id);
            childNodes.value = childNodes.value.filter((c) => !(c.__parentId === id || c.parentId === id || (c.raw && c.raw.capability_id === id)));
            edges.value = edges.value.filter((e) => e.source !== id && e.target !== id);
            childEdges.value = childEdges.value.filter((e) => e.source !== id && e.target !== id);
            showError('Eliminado localmente. El backend no expone endpoints DELETE; implementar API para eliminación permanente.');
        } else {
            showSuccess('Capacidad y relación eliminadas');
        }

        // refresh tree (best-effort)
        await loadTreeFromApi(props.scenario?.id);
        // clear focus
        focusedNode.value = null;
    } catch (err) {
        showError('Error al eliminar la capacidad');
    } finally {
        savingNode.value = false;
    }
}

function createCapabilityClicked() {
    // open the create-capability modal (prefer modal over sidebar for quick create)
    // initialize defaults for the form
    newCapName.value = '';
    newCapDescription.value = '';
    newCapType.value = '';
    newCapCategory.value = '';
    newCapImportance.value = 3;
    pivotStrategicRole.value = 'target';
    pivotStrategicWeight.value = 10;
    pivotPriority.value = 1;
    pivotRationale.value = '';
    pivotRequiredLevel.value = 3;
    pivotIsCritical.value = false;
    createModalVisible.value = true;
}

async function saveNewCapability() {
    if (!props.scenario || !props.scenario.id) return showError('Escenario no seleccionado');
    if (!newCapName.value || !newCapName.value.trim()) return showError('El nombre es obligatorio');
    creating.value = true;
    try {
        const payload: any = {
            name: newCapName.value.trim(),
            description: newCapDescription.value || null,
            importance: newCapImportance.value ?? 3,
            type: newCapType.value || null,
            category: newCapCategory.value || null,
            // pivot attributes
            strategic_role: pivotStrategicRole.value,
            strategic_weight: pivotStrategicWeight.value ?? 10,
            priority: pivotPriority.value ?? 1,
            rationale: pivotRationale.value || null,
            required_level: pivotRequiredLevel.value ?? 3,
            is_critical: !!pivotIsCritical.value,
        };

        const res: any = await api.post(`/api/strategic-planning/scenarios/${props.scenario.id}/capabilities`, payload);
        const created = res?.data ?? res;
        showSuccess('Capacidad creada y asociada al escenario');
        // refresh tree from API to include pivot attributes
        await loadTreeFromApi(props.scenario.id);
        // close modal and reset
        createModalVisible.value = false;
        newCapName.value = '';
        newCapDescription.value = '';
        newCapType.value = '';
        newCapCategory.value = '';
        newCapImportance.value = 3;
        pivotStrategicRole.value = 'target';
        pivotStrategicWeight.value = 10;
        pivotPriority.value = 1;
        pivotRationale.value = '';
        pivotRequiredLevel.value = 3;
        pivotIsCritical.value = false;
    } catch (err: any) {
        showError(err?.response?.data?.message || 'Error creando capacidad');
    } finally {
        creating.value = false;
    }
}

function truncateLabel(s: any, max = 14) {
    if (s == null) return '';
    const str = String(s);
    return str.length > max ? str.slice(0, max - 1) + '…' : str;
}

function wrapLabel(s: any, max = 14) {
    // Wrap into at most two lines, each up to `max` chars. If text exceeds two lines,
    // truncate the second line and add an ellipsis.
    if (s == null) return '';
    const str = String(s).trim();
    if (str.length <= max) return str;

    // Helper to cut a line at word boundary if possible
    const cutLine = (text: string, limit: number) => {
        if (text.length <= limit) return { line: text, rest: '' };
        // Try to break at last space within limit
        const slice = text.slice(0, limit + 1);
        const lastSpace = slice.lastIndexOf(' ');
        if (lastSpace > 0) {
            const line = text.slice(0, lastSpace);
            const rest = text.slice(lastSpace + 1).trim();
            return { line, rest };
        }
        // No space found: hard cut
        return { line: text.slice(0, limit), rest: text.slice(limit).trim() };
    };

    const first = cutLine(str, max);
    if (!first.rest) return first.line;

    // build second line (truncate with ellipsis if needed)
    const secondRaw = first.rest;
    if (secondRaw.length <= max) return first.line + '\n' + secondRaw;
    // try to cut second at word boundary
    const secondCut = cutLine(secondRaw, max);
    let second = secondCut.line;
    if (secondCut.rest && second.length >= max) {
        // ensure room for ellipsis
        second = second.slice(0, Math.max(0, max - 1));
        second = second.replace(/\s+$/,'');
        second = second + '…';
    } else if (secondCut.rest) {
        // append ellipsis if anything remains
        second = second + '…';
    }
    return first.line + '\n' + second;
}

function computeInitialPosition(idx: number, total: number) {
    // Grid-based centered layout: place nodes in a nearly square grid centered
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;

    if (total <= 1) return { x: Math.round(centerX), y: Math.round(centerY) };

    // compute grid dimensions: choose a balanced grid (near-square) up to 5 columns
    // previous behaviour used `columns = total` which produced a single row for small totals
    // (e.g. 4 nodes -> 4 columns). Use sqrt-based heuristic so 4 -> 2x2, 3 -> 2x2, 5 -> 3x2, etc.
    const columns = Math.min(5, Math.max(1, Math.ceil(Math.sqrt(total))));
    const rows = Math.max(1, Math.ceil(total / columns));

    // margins and spacing (more compact vertical spacing)
    const margin = 24;
    const availableW = Math.max(120, width.value - margin * 2);
    const availableH = Math.max(120, height.value - margin * 2);
    const spacingX = columns > 1 ? Math.min(160, Math.floor(availableW / columns)) : 0;
    // increase vertical spacing cap to provide more room between rows
    const spacingY = rows > 1 ? Math.min(140, Math.floor(availableH / rows)) : 0;

    const col = idx % columns;
    const row = Math.floor(idx / columns);

    // center the whole grid around centerX/centerY
    const totalGridW = (columns - 1) * spacingX;
    const totalGridH = (rows - 1) * spacingY;
    const offsetX = col * spacingX - totalGridW / 2;
    const offsetY = row * spacingY - totalGridH / 2;

    return { x: Math.round(centerX + offsetX), y: Math.round(centerY + offsetY) };
}

/**
 * Reorder main nodes according to simple layout rules requested by the user:
 * - 1: centered
 * - 2: side-by-side
 * - 3: center + two sides
 * - 4..6: single row evenly spaced
 * - >=7: grid with up to 6 columns (rows = ceil(total/cols))
 * After reordering nodes are updated in `nodes.value` and `positionsDirty` is set.
 */
function reorderNodes() {
    const total = nodes.value.length;
    if (!total) return;
    const cx = Math.round(width.value / 2);
    const cy = Math.round(height.value / 2 - 30);
    const marginH = 48;
    const availableW = Math.max(200, width.value - marginH * 2);

    if (total === 1) {
        nodes.value = nodes.value.map((n: any) => ({ ...n, x: cx, y: cy }));
    } else if (total === 2) {
        const gap = Math.min(220, Math.floor(availableW / 3));
        nodes.value = nodes.value.map((n: any, i: number) => ({ ...n, x: cx + (i === 0 ? -gap / 2 : gap / 2), y: cy }));
    } else if (total === 3) {
        const gap = Math.min(220, Math.floor(availableW / 4));
        nodes.value = nodes.value.map((n: any, i: number) => {
            if (i === 1) return { ...n, x: cx, y: cy };
            return { ...n, x: cx + (i === 0 ? -gap : gap), y: cy };
        });
    } else if (total >= 4 && total <= 6) {
        const cols = total;
        const spacing = Math.min(160, Math.floor(availableW / cols));
        const totalW = (cols - 1) * spacing;
        const startX = Math.round(cx - totalW / 2);
        nodes.value = nodes.value.map((n: any, i: number) => ({ ...n, x: startX + i * spacing, y: cy }));
    } else {
        // grid layout for larger sets: choose a near-square grid (ceil(sqrt(total))) up to 6 columns
        const cols = Math.min(6, Math.max(2, Math.ceil(Math.sqrt(total))));
        const rows = Math.ceil(total / cols);
        const spacingX = Math.min(140, Math.floor(availableW / cols));
        const spacingY = Math.min(140, Math.floor((height.value - 160) / Math.max(1, rows)));
        const totalGridW = (cols - 1) * spacingX;
        const totalGridH = (rows - 1) * spacingY;
        // push grid slightly down to avoid the scenario header/origin area
        const startX = Math.round(cx - totalGridW / 2);
        const downwardBias = 40; // pixels to lower the grid from center
        const startY = Math.round(cy - totalGridH / 2 + downwardBias);
        nodes.value = nodes.value.map((n: any, i: number) => {
            const r = Math.floor(i / cols);
            const c = i % cols;
            return { ...n, x: startX + c * spacingX, y: clampY(startY + r * spacingY) } as any;
        });
    }

    // ensure nodes are not placed on top of the scenario origin
    try {
        const MIN_ORIGIN_SEPARATION = 180; // increased separation for safety
        if (scenarioNode.value) {
            const sx = scenarioNode.value.x ?? Math.round(width.value / 2);
            const sy = scenarioNode.value.y ?? Math.round(height.value * 0.12);
            // compute maximum downward shift required so ALL nodes are at least MIN_ORIGIN_SEPARATION away
            let requiredShift = 0;
            nodes.value.forEach((n: any) => {
                if (!n || n.x == null || n.y == null) return;
                const dx = n.x - sx;
                const dy = n.y - sy;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MIN_ORIGIN_SEPARATION) {
                    const need = MIN_ORIGIN_SEPARATION - dist;
                    if (need > requiredShift) requiredShift = need;
                }
            });
            if (requiredShift > 0) {
                // add a small margin and shift all nodes down together to avoid splitting rows
                const margin = 20;
                const shift = Math.round(requiredShift + margin);
                nodes.value = nodes.value.map((n: any) => {
                    if (!n || n.y == null) return n;
                    return { ...n, y: clampY((n.y ?? 0) + shift) } as any;
                });
            }
        }
    } catch (err) {
        void err;
    }

    positionsDirty.value = true;
    // Clear focus/children so renderNodeX doesn't snap nodes into side columns
    try {
        focusedNode.value = null;
        childNodes.value = [];
        childEdges.value = [];
        showSidebar.value = false;
    } catch (err) {
        void err;
    }
}

function nodeRenderShift(n: any) {
    // New behavior: when a node is focused, place other nodes in fixed left/right columns
    if (!focusedNode.value) return 0;
    if (!n || n.id === focusedNode.value.id) return 0;
    // Decide side based on original x relative to focused pivot
    const pivotX = focusedNode.value.x ?? width.value / 2;
    const originalX = n.x ?? width.value / 2;
    const leftX = Math.round(width.value * 0.18);
    const rightX = Math.round(width.value * 0.82);
    return originalX < pivotX ? leftX - (n.x ?? 0) : rightX - (n.x ?? 0);
}

// Prevent linter false-positives for locally declared but template-used helpers
void truncateLabel;
void nodeRenderShift;
void startPanelDrag;

function renderNodeX(n: any) {
    const minX = 48;
    const maxX = Math.max(160, width.value - 48);
    // When a node is focused, non-selected nodes should snap to fixed side columns
    if (focusedNode.value && n && n.id !== focusedNode.value.id) {
        const pivotX = focusedNode.value.x ?? width.value / 2;
        const originalX = n.x ?? width.value / 2;
        const leftX = Math.round(width.value * 0.18);
        const rightX = Math.round(width.value * 0.82);
        const target = originalX < pivotX ? leftX : rightX;
        return Math.min(Math.max(minX, target), maxX);
    }
    const base = (n.x ?? 0);
    return Math.min(Math.max(minX, base), maxX);
}

function renderedNodeById(id: number) {
    if (id == null) return null;
    // special-case: scenarioNode
    if (scenarioNode.value && id === scenarioNode.value.id) {
        return { x: scenarioNode.value.x, y: scenarioNode.value.y } as any;
    }
    if (id < 0) {
        return childNodeById(id);
    }
    const n = nodeById(id);
    if (!n) return null;
    // ensure we pass a number to clampY (avoid undefined)
    return { x: renderNodeX(n), y: clampY(n.y ?? 0) } as any;
}

// Debug helpers removed

function buildNodesFromItems(items: any[]) {
    if (!Array.isArray(items) || items.length === 0) {
        nodes.value = [];
        return;
    }
    // prefer provided position_x/position_y or numeric x/y; if missing leave undefined so force layout can compute
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;
    const radius = Math.min(width.value, height.value) / 3;
    const angleStep = (2 * Math.PI) / items.length;
    // avoid lint 'assigned but never used' for debugging helpers
    void centerX;
    void centerY;
    void radius;
    void angleStep;
    const mapped = items.map((it: any, idx: number) => {
        const rawX = it.position_x ?? it.x ?? it.cx ?? null;
        const rawY = it.position_y ?? it.y ?? it.cy ?? null;
            const parsedX = rawX != null ? parseFloat(String(rawX)) : NaN;
            const parsedY = rawY != null ? parseFloat(String(rawY)) : NaN;
            // If stored coordinates are valid numbers, prefer them so nodes remain fixed where the user left them.
            // Heuristic: legacy `capabilities.position_x/position_y` were stored as percentages (0..100).
            // Newer pivot `scenario_capabilities.position_x` may be absolute pixels (>100). Detect small values
            // and convert from percentage -> pixels using current canvas size to avoid overlaps with the scenario origin.
            const hasPos = !Number.isNaN(parsedX) && !Number.isNaN(parsedY);
            let x: number | undefined = undefined;
            let y: number | undefined = undefined;
            if (hasPos) {
                const looksLikePercent = parsedX >= 0 && parsedX <= 100 && parsedY >= 0 && parsedY <= 100;
                if (looksLikePercent) {
                    x = Math.round((parsedX / 100) * width.value);
                    y = Math.round((parsedY / 100) * height.value);
                } else {
                    x = Math.round(parsedX);
                    y = Math.round(parsedY);
                }
            }
        // if missing position, place roughly on a circle initially (helps force start) but mark undefined so we can re-run force
        const fallbackPos = computeInitialPosition(idx, items.length);
        const fallbackX = Math.round(fallbackPos.x);
        const fallbackY = Math.round(fallbackPos.y);
        return {
            id: Number(it.id),
            name: it.name,
            x: x ?? fallbackX,
            y: y ?? fallbackY,
            _hasCoords: hasPos,
            is_critical: it.is_critical ?? it.isCritical ?? false,
            description: it.description ?? it.desc ?? null,
            competencies: Array.isArray(it.competencies)
                ? it.competencies
                : Array.isArray(it.competency)
                ? it.competency
                : [],
            importance: it.importance ?? it.rank ?? null,
            level: it.level ?? null,
            required: it.required ?? null,
            raw: it,
        } as any;
    });
    nodes.value = mapped.map((m: any) => ({
        id: m.id,
        name: m.name,
        displayName: wrapLabel(m.name, 14),
        x: m.x,
        y: m.y,
        is_critical: !!m.is_critical,
        description: m.description,
        competencies: m.competencies,
        importance: m.importance,
        level: m.level,
        required: m.required,
        raw: m.raw,
    }));
    // initialize scenario node after building nodes
    setScenarioInitial();
    // Ensure no node is placed on top of the scenario origin. If a stored/initial
    // position is too close to the scenario node, push it outward beneath the origin
    // to avoid visual overlap. This handles legacy coords and saved positions.
    try {
        const MIN_ORIGIN_SEPARATION = 140; // pixels
        if (scenarioNode.value) {
            const sx = scenarioNode.value.x ?? Math.round(width.value / 2);
            const sy = scenarioNode.value.y ?? Math.round(height.value * 0.12);
            nodes.value = nodes.value.map((n: any) => {
                if (!n || n.x == null || n.y == null) return n;
                const dx = n.x - sx;
                const dy = n.y - sy;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MIN_ORIGIN_SEPARATION) {
                    // push node downwards preferentially; if directly above, nudge down
                    if (dist === 0) {
                        return { ...n, x: n.x, y: clampY(sy + MIN_ORIGIN_SEPARATION) } as any;
                    }
                    const scale = MIN_ORIGIN_SEPARATION / Math.max(1, dist);
                    const newX = Math.round(sx + dx * scale);
                    const newY = Math.round(sy + dy * scale);
                    return { ...n, x: newX, y: clampY(newY) } as any;
                }
                return n;
            });
        }
    } catch (err) {
        void err;
    }
    // build scenario->capability edges so initial view shows connections from scenario to capabilities
    scenarioEdges.value = nodes.value.map((n: any) => ({ source: scenarioNode.value?.id ?? 0, target: n.id, isScenarioEdge: true } as Edge));
    // build edges before attempting a force layout
    buildEdgesFromItems(items);
    // Only run force layout if some nodes originally had real coordinates.
    // When we intentionally ignore stored coords (default grid), do not run the simulation
    const hadAnyCoords = mapped.some((m: any) => !!m._hasCoords);
    if (hadAnyCoords) runForceLayout();
    // store last items so we can recompute layout on resize
    lastItems = items;
}

function runForceLayout() {
    try {
        // prepare mutable nodes/links for simulation
        const simNodes = nodes.value.map((n) => ({
            id: n.id,
            x: n.x || 0,
            y: n.y || 0,
        }));
        const simLinks = edges.value.map((l) => ({
            source: l.source,
            target: l.target,
        }));
        const simulation = d3
            .forceSimulation(simNodes as any)
            .force(
                'link',
                (d3 as any)
                    .forceLink(simLinks)
                    .id((d: any) => d.id)
                    .distance(120)
                    .strength(0.5),
            )
            .force('charge', (d3 as any).forceManyBody().strength(-220))
            .force('center', (d3 as any).forceCenter(width.value / 2, height.value / 2));

        // run a fixed number of synchronous ticks to stabilise layout
        for (let i = 0; i < 300; i++) simulation.tick();
        simulation.stop();

        const pos = new Map(
            simNodes.map((n: any) => [n.id, { x: Math.round(n.x), y: Math.round(n.y) }]),
        );
        // Preserve existing node metadata (competencies, description, flags) when applying positions
        nodes.value = nodes.value.map((n: any) => {
            const p = pos.get(n.id);
            return { ...n, x: p?.x ?? n.x, y: p?.y ?? n.y } as any;
        });
    } catch (err) {
        void err;
        // if simulation fails, silently skip (fallback positions already set)
        // console.warn('[PrototypeMap] force layout failed', err)
    }
}

function buildEdgesFromItems(items: any[]) {
    const result: Edge[] = [];
    // support explicit connections list from scenario.connections
    const conns = (props as any).scenario?.connections;
    if (Array.isArray(conns) && conns.length > 0) {
        (conns as ConnectionPayload[]).forEach((c) => {
            const s = c.source ?? c.source_id ?? null;
            const t = c.target ?? c.target_id ?? null;
            if (s != null && t != null) {
                result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
            }
        });
    } else {
        // if items have linked_to, connected_to, or links arrays
        items.forEach((it: any) => {
            if (Array.isArray(it.connected_to)) {
                it.connected_to.forEach((t: any) => {
                    result.push({ source: Number(it.id), target: Number(t) });
                });
            }
            if (Array.isArray(it.links)) {
                it.links.forEach((t: any) =>
                    result.push({ source: Number(it.id), target: Number(t) }),
                );
            }
            // support nested connections on item (source/target or source_id/target_id)
            if (Array.isArray(it.connections)) {
                it.connections.forEach((c: ConnectionPayload) => {
                    const s = c.source ?? c.source_id ?? null;
                    const t = c.target ?? c.target_id ?? null;
                    if (s != null && t != null)
                        result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
                });
            }
        });
    }
    edges.value = result;
}

function nodeById(id: number) {
    return nodes.value.find((n) => n.id === id) || null;
}

function childNodeById(id: number) {
    return childNodes.value.find((n) => n.id === id) || null;
}

const handleNodeClick = async (node: NodeItem, event?: MouseEvent) => {
    // capture previous focused node so we can swap positions when appropriate
    const prev = focusedNode.value ? { ...focusedNode.value } : undefined;
    let updated: NodeItem | null = null;

    // If the clicked node is a child (id < 0), first locate its parent and center on the parent
    if (node && node.id != null && node.id < 0) {
        // find parent edge linking to this child
        const parentEdge = childEdges.value.find((e) => e.target === node.id);
        const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
        if (parentNode) {
            // capture parent's previous position so children can animate from there in sync
            const parentPrevPos = { x: parentNode.x ?? 0, y: parentNode.y ?? 0 };
            // center parent first so child positions are computed relative to it
            centerOnNode(parentNode, prev);
            // start expanding children slightly before the parent's full transition ends
            // (race between transitionend and a lead timeout = 60% of TRANSITION_MS)
            const parentLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
            await Promise.race([waitForTransitionForNode(parentNode.id), wait(parentLead)]);
            // rebuild children under the parent (positions will use updated parent coords)
            const updatedParent = nodeById(parentNode.id) || parentNode;
            expandCompetencies(updatedParent as NodeItem, parentPrevPos);
            // find the freshly created child node by id
            const freshChild = childNodeById(node.id);
            // set focused to the child (if present) so sidebar shows child details
            focusedNode.value = freshChild || node;
            // set updated reference for later use
            updated = (freshChild as NodeItem) || updatedParent || nodeById(node.id) || node;
            // if the child itself has inner skills/competencies, expand them now
            if (freshChild && ((freshChild as any).skills || (freshChild as any).competencies)) {
                // expand grandchildren under the child. Start them slightly before the child
                // finishes by racing the child's transitionend with a short lead timeout.
                const childId = (freshChild as NodeItem).id;
                const childLead = Math.max(0, Math.round(TRANSITION_MS * 0.5));
                await Promise.race([waitForTransitionForNode(childId), wait(childLead)]);
                expandCompetencies(freshChild as NodeItem, parentPrevPos);
            }
        } else {
            // fallback: treat as normal node click
            focusedNode.value = node;
            const nodePrevPos = { x: node.x ?? 0, y: node.y ?? 0 };
            centerOnNode(node, prev);
            // start expanding a bit earlier: race transitionend with a lead timeout
            const nodeLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
            await Promise.race([waitForTransitionForNode(node.id), wait(nodeLead)]);
            updated = nodeById(node.id) || node;
            expandCompetencies(updated as NodeItem, nodePrevPos);
        }
    } else {
        // normal capability node click
        focusedNode.value = node;
        // first reposition nodes so focused node is centered and others snap aside
        const nodePrevPos = { x: node.x ?? 0, y: node.y ?? 0 };
        centerOnNode(node, prev);
        // start expanding a bit earlier: race transitionend with a lead timeout
        const centeredLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
        await Promise.race([waitForTransitionForNode(node.id), wait(centeredLead)]);
        // then expand competencies using the updated focused node coordinates
        updated = nodeById(node.id) || node;
        expandCompetencies(updated as NodeItem, nodePrevPos);
    }

    // If we are NOT in true fullscreen mode, fix panel to top-left corner
    const inTrueFullscreen = !!document.fullscreenElement;
    if (!inTrueFullscreen) {
        tooltipX.value = 24;
        tooltipY.value = 24;
    } else if (event) {
        // when in fullscreen, use event coords
        tooltipX.value = event.clientX + 12;
        tooltipY.value = event.clientY + 12;
    } else {
        // fallback: derive from node position
        tooltipX.value = (node.x ?? 0) + 12;
        tooltipY.value = (node.y ?? 0) + 12;
    }

    // debug coords removed
    // If followScenario is enabled, move scenarioNode to follow the clicked node (smoothly)
    try {
        if (followScenario.value && scenarioNode.value && (updated || node)) {
            // position scenario node slightly above the clicked node to act as origin
            const offsetY = 80;
            const refNode = updated || node;
            scenarioNode.value.x = (refNode.x ?? 0);
            scenarioNode.value.y = Math.round((refNode.y ?? 0) - offsetY);
        }
    } catch (err) {
        void err;
        // ignore
    }
};

const closeTooltip = () => {
    focusedNode.value = null;
    childNodes.value = [];
    childEdges.value = [];
    // restore original node positions if we have them
    if (originalPositions.value && originalPositions.value.size > 0) {
        nodes.value = nodes.value.map((n) => {
            const p = originalPositions.value.get(n.id);
            if (p) return { ...n, x: p.x, y: p.y } as any;
            return n;
        });
        // restore scenario node
        if (scenarioNode.value) {
            const sp = originalPositions.value.get(scenarioNode.value.id);
            if (sp) {
                scenarioNode.value.x = sp.x;
                scenarioNode.value.y = sp.y;
            }
        }
        originalPositions.value.clear();
    }
    // reset pan to default (kept for compatibility)
    viewX.value = 0;
    viewY.value = 0;
};

// panel drag (make the glass panel movable)
const panelDragging = ref(false);
const panelDragOffset = ref({ x: 0, y: 0 });

// sidebar collapsed state: false -> visible (anchored), true -> collapsed (narrow)
const nodeSidebarCollapsed = ref(false);

function toggleNodeSidebarCollapse() {
    nodeSidebarCollapsed.value = !nodeSidebarCollapsed.value;
}

// sidebar theme: 'light' | 'dark'
const sidebarTheme = ref<'light' | 'dark'>('light');

function toggleSidebarTheme() {
    sidebarTheme.value = sidebarTheme.value === 'light' ? 'dark' : 'light';
}

// visible when not collapsed; when collapsed the sidebar shows a narrow tab
const nodeSidebarVisible = computed(() => {
    return !nodeSidebarCollapsed.value;
});

// helper to avoid referencing `document` from the template context (template type-checker complains)
// (we no longer keep a local CSS-only fullscreen state)

const panelStyle = computed<CSSProperties>(() => {
    // compute width depending on collapsed state and fullscreen
    const inTrueFullscreen = !!document.fullscreenElement;
    const widthPx = nodeSidebarCollapsed.value ? 56 : 360;

    // If document is in true Fullscreen API, keep the panel fixed
    if (inTrueFullscreen) {
        return {
            position: 'fixed',
            right: '0px',
            top: '0px',
            height: '100vh',
            width: `${widthPx}px`,
            zIndex: 100000,
            overflow: 'auto',
        } as CSSProperties;
    }

    // Normal mode: provide width so layout can shift; CSS handles positioning
    return {
        width: `${widthPx}px`,
    } as CSSProperties;
});

function startPanelDrag(e: PointerEvent) {
    panelDragging.value = true;
    panelDragOffset.value.x = e.clientX - (tooltipX.value || 0);
    panelDragOffset.value.y = e.clientY - (tooltipY.value || 0);
    window.addEventListener('pointermove', onPanelPointerMove);
    window.addEventListener('pointerup', onPanelPointerUp);
}

function onPanelPointerMove(e: PointerEvent) {
    if (!panelDragging.value) return;
    const proposedX = Math.round(e.clientX - panelDragOffset.value.x);
    const proposedY = Math.round(e.clientY - panelDragOffset.value.y);
    // attempt to clamp within mapRoot bounds (if available)
    const mapEl = mapRoot.value as HTMLElement | null;
    const panelEl = document.querySelector('.glass-panel-strong') as HTMLElement | null;
    if (panelEl) {
        const panelRect = panelEl.getBoundingClientRect();
        if (document.fullscreenElement) {
            // clamp against viewport when in fullscreen
            const minX = 8;
            const maxX = Math.round(window.innerWidth - panelRect.width - 8);
            const minY = 8;
            const maxY = Math.round(window.innerHeight - panelRect.height - 8);
            tooltipX.value = Math.min(Math.max(proposedX, minX), Math.max(minX, maxX));
            tooltipY.value = Math.min(Math.max(proposedY, minY), Math.max(minY, maxY));
            return;
        }
        if (mapEl) {
            const mapRect = mapEl.getBoundingClientRect();
            const minX = Math.round(mapRect.left + 8); // small padding
            const maxX = Math.round(mapRect.right - panelRect.width - 8);
            const minY = Math.round(mapRect.top + 8);
            const maxY = Math.round(mapRect.bottom - panelRect.height - 8);
            tooltipX.value = Math.min(Math.max(proposedX, minX), Math.max(minX, maxX));
            tooltipY.value = Math.min(Math.max(proposedY, minY), Math.max(minY, maxY));
            return;
        }
    }
    tooltipX.value = proposedX;
    tooltipY.value = proposedY;
}

function onPanelPointerUp() {
    panelDragging.value = false;
    window.removeEventListener('pointermove', onPanelPointerMove);
    window.removeEventListener('pointerup', onPanelPointerUp);
}

// Fullscreen toggle removed: UX disabled. We rely only on the browser Fullscreen API when used externally.

function expandCompetencies(node: NodeItem, initialParentPos?: { x: number; y: number }) {
    childNodes.value = [];
    childEdges.value = [];
    const comps = (node as any).competencies ?? [];
    if (!Array.isArray(comps) || comps.length === 0) return;
    // Layout child competencies in two vertical columns underneath the parent node
    const cx = node.x ?? Math.round(width.value / 2);
    const cy = node.y ?? Math.round(height.value / 2);
    const initX = initialParentPos?.x ?? cx;
    const initY = initialParentPos?.y ?? cy;
    // Layout policy: up to 5 columns.
    // Behavior: columns = min(5, total), rows = ceil(total / columns), centering incomplete rows.
    const total = comps.length;
    const columns = Math.min(5, total);
    // reduce spacing when more columns to keep layout compact
    const columnSpacing = 72;
    // If the node is itself a child node (we use negative ids for childNodes), use a tighter layout
    const isChildNode = node.id != null && node.id < 0;
    // Tighten layout for grandchildren: smaller spacing and much smaller vertical offset
    const baseRowSpacing = isChildNode ? 30 : 44; // tighter spacing for grandchildren
    const verticalOffset = isChildNode ? 80 : 150; // bring grandchildren closer to their parent
    // Add extra vertical gap for lower rows when there are many items
    const extraGapForLowerRows = comps.length > columns ? (isChildNode ? 8 : 28) : 0;

    const rowsPerColumn = Math.ceil(total / columns);
    // compute total block height accounting for the extra gap applied below the first row
    const blockHeight = (rowsPerColumn - 1) * baseRowSpacing + (rowsPerColumn > 1 ? extraGapForLowerRows : 0);
    // compute top start so the multi-column block is vertically centered under parent
    const startY = Math.round(cy + verticalOffset - blockHeight / 2);
    const fullRowsCount = Math.floor(total / columns);

    // Build child entries with initial position at parent, then animate to targets on nextTick
    const builtChildren: Array<any> = [];
        comps.forEach((c: any, i: number) => {
        const colIndex = i % columns; // 0..columns-1
        const rowIndex = Math.floor(i / columns);
        const itemsInRow = rowIndex < fullRowsCount ? columns : total % columns;
        const maxRowWidth = (columns - 1) * columnSpacing;
        const rowWidth = (itemsInRow - 1) * columnSpacing;
        const rowOffset = (maxRowWidth - rowWidth) / 2;
        const targetX = Math.round(cx - maxRowWidth / 2 + colIndex * columnSpacing + rowOffset);
        // apply extra gap only for rows below the first to avoid crowding lower rows
        const rawY = Math.round(startY + rowIndex * baseRowSpacing + (rowIndex > 0 ? extraGapForLowerRows : 0));
        const targetY = clampY(rawY);
        const id = -(node.id * 1000 + i + 1);
        // push with initial coordinates at parent's center so CSS transition can animate to target
        // small stagger so children appear organic; grandchildren slightly slower
        // base stagger per-row/col, plus a small random jitter for organic feel
        const baseDelay = rowIndex * 30 + colIndex * 12;
        const jitter = Math.round((Math.random() - 0.5) * 40); // -20..+20ms
        const delay = Math.max(0, baseDelay + jitter);
        const child = {
            id,
            compId: c.id ?? null,
            name: c.name ?? c,
            displayName: wrapLabel(c.name ?? c, 14),
            x: initX,
            y: initY,
            __scale: 0.84,
            __opacity: 0,
            __delay: delay,
            __filter: 'blur(6px) drop-shadow(0 10px 18px rgba(2,6,23,0.36))',
            __targetX: targetX,
            __targetY: targetY,
            is_critical: false,
            description: c.description ?? null,
            readiness: c.readiness ?? null,
            skills: Array.isArray(c.skills) ? c.skills : [],
            raw: c,
        } as any;
        builtChildren.push(child);
        childEdges.value.push({ source: node.id, target: id });
    });
    // assign initial children so they render at parent position
    childNodes.value = builtChildren.slice();
    // animate to target positions on next tick — apply slight overshoot + clear blur for organic effect
    nextTick(() => {
        // move to targets and make visible with slight overshoot
        childNodes.value = childNodes.value.map((ch: any) => ({
            ...ch,
            x: ch.__targetX ?? ch.x,
            y: ch.__targetY ?? ch.y,
            __scale: 1.06,
            __opacity: 1,
            __filter: 'none',
        }));

        // then settle scale to 1 for natural bounce
        setTimeout(() => {
            childNodes.value = childNodes.value.map((ch: any) => ({ ...ch, __scale: 1 }));
            // cleanup helper target props after settle
            nextTick(() => {
                childNodes.value.forEach((ch: any) => { delete ch.__targetX; delete ch.__targetY; delete ch.__delay; delete ch.__filter; });
            });
        }, 160);
    });
}

// clamp child node Y positions when setting them to avoid placing nodes outside viewport
function clampY(y: number) {
    const minY = 40;
    const maxY = Math.max(120, height.value - 40);
    return Math.min(Math.max(y, minY), maxY);
}

function startDrag(node: any, event: PointerEvent) {
    dragging.value = node;
    dragOffset.value.x = event.clientX - node.x;
    dragOffset.value.y = event.clientY - node.y;
    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);
}

function onPointerMove(e: PointerEvent) {
    if (!dragging.value) return;
    dragging.value.x = Math.round(e.clientX - dragOffset.value.x);
    dragging.value.y = Math.round(e.clientY - dragOffset.value.y);
    // mark positions as changed so they can be saved on pointer up
    positionsDirty.value = true;
}

async function onPointerUp() {
    if (dragging.value) {
        dragging.value = null;
    }
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerup', onPointerUp);

    // If positions changed during drag, save automatically
    if (positionsDirty.value) {
        try {
            await savePositions();
        } catch (err) {
            void err;
        }
        positionsDirty.value = false;
    }
}

const resetPositions = () => {
    // clear positions so layout recomputes
    nodes.value = nodes.value.map((n) => ({
        ...n,
        x: undefined as any,
        y: undefined as any,
    }));
    if (props.scenario && Array.isArray((props.scenario as any).capabilities)) {
        buildNodesFromItems((props.scenario as any).capabilities);
    }
};

const savePositions = async () => {
    if (!props.scenario || !props.scenario.id)
        return showError('Escenario no seleccionado');
    try {
        const payload = {
            positions: nodes.value.map((n) => ({ id: n.id, x: n.x, y: n.y })),
        };
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/capability-tree/save-positions`,
            payload,
        );
        showSuccess('Posiciones guardadas');
    } catch (e) {
        void e;
        showError('Error al guardar posiciones');
    }
};

const loadTreeFromApi = async (scenarioId?: number) => {
    if (!scenarioId) {
        nodes.value = [];
        loaded.value = true;
        return;
    }
    try {
        // fetch capability-tree for scenario
        const tree = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId}/capability-tree`,
        );
        const items = (tree as any) || [];
        // capability-tree response received
        buildNodesFromItems(items);
        // ensure edges are rebuilt from the fetched items
        buildEdgesFromItems(items);
    } catch (e) {
        void e;
        // error loading capability-tree
        nodes.value = [];
    } finally {
        loaded.value = true;
    }
};

onMounted(() => {
    // prefer passed-in scenario.capabilities to avoid extra network roundtrip
    // onMounted: handle incoming props.scenario
    if (
        props.scenario &&
        Array.isArray((props.scenario as any).capabilities) &&
        (props.scenario as any).capabilities.length > 0
    ) {
        const caps = (props.scenario as any).capabilities;
        buildNodesFromItems(caps);
        buildEdgesFromItems(caps);
        // restore persisted UI state after nodes built
        try {
            const collapsed = localStorage.getItem(LS_KEYS.collapsed);
            if (collapsed !== null) nodeSidebarCollapsed.value = collapsed === 'true';
            const lastView = localStorage.getItem(LS_KEYS.lastView);
            const lastId = localStorage.getItem(LS_KEYS.lastFocusedId);
            if (lastId) savedFocusedNodeId.value = parseInt(lastId, 10);
            if (lastView === 'scenario' && !focusedNode.value) showSidebar.value = true;
            if (savedFocusedNodeId.value) {
                const restored = nodeById(savedFocusedNodeId.value);
                if (restored) focusedNode.value = restored;
            }
        } catch (e) {
            void e;
            // ignore storage errors
        }
        loaded.value = true;
        // ensure scenario node initialized with correct name
        setScenarioInitial();
        // reset positions to default layout on reload (user requested automatic reset)
        try {
            resetPositions();
        } catch (err) {
            void err;
        }
        return;
    }
    // otherwise fetch capability tree from API
    void loadTreeFromApi(props.scenario?.id);

    // initialize responsive sizing and observe container
    const el = mapRoot.value as HTMLElement | null;
    const applySize = (w?: number, h?: number) => {
        const computedWidth = w ?? el?.clientWidth ?? 900;
        // compute available height inside the container: prefer container height if available,
        // otherwise use viewport remaining space below the container's top.
        let containerHeight = el?.clientHeight ?? 0;
        if (!containerHeight || containerHeight === 0) {
            const top = el?.getBoundingClientRect().top ?? 0;
            containerHeight = Math.max(320, Math.round(window.innerHeight - top - 24));
        }
        const controlsEl = el?.querySelector('.map-controls') as HTMLElement | null;
        const controlsH = controlsEl?.offsetHeight ?? 0;
        const computedHeight = h ?? Math.max(300, containerHeight - controlsH - 12);

        width.value = computedWidth;
        height.value = computedHeight;

        // if we have previously loaded items, rebuild positions to adapt
        if (Array.isArray(lastItems) && lastItems.length > 0) {
            buildNodesFromItems(lastItems);
            buildEdgesFromItems(lastItems);
        }
    };
    applySize();
    let ro: ResizeObserver | null = null;
    if (el && (window as any).ResizeObserver) {
        ro = new ResizeObserver((entries: any) => {
            for (const entry of entries) {
                const w = Math.round(entry.contentRect.width);
                const h = Math.round(entry.contentRect.height);
                applySize(w, h);
            }
        });
        ro.observe(el);
    }
    // fullscreen change listener removed (UI fullscreen button disabled)
    const onWindowResize = () => applySize();
    window.addEventListener('resize', onWindowResize);
    // attach scroll listener for edit form slider
    editFormScrollHandler = (ev: Event) => syncSliderFromScroll();
    // attempt to attach after a tick in case element not yet rendered
    nextTick(() => {
        if (editFormScrollEl.value) editFormScrollEl.value.addEventListener('scroll', editFormScrollHandler as EventListener);
    });
    onBeforeUnmount(() => {
        // cleanup edit form scroll listener
        if (editFormScrollEl.value && editFormScrollHandler) editFormScrollEl.value.removeEventListener('scroll', editFormScrollHandler as EventListener);
        editFormScrollHandler = null;
        if (ro) ro.disconnect();
        window.removeEventListener('resize', onWindowResize);
    });
});

// persist UI choices: collapsed, lastView, lastFocusedNodeId
watch(
    [nodeSidebarCollapsed, showSidebar, focusedNode],
    () => {
        try {
            localStorage.setItem(LS_KEYS.collapsed, nodeSidebarCollapsed.value ? 'true' : 'false');
            const lastView = focusedNode.value ? 'node' : showSidebar.value ? 'scenario' : 'none';
            localStorage.setItem(LS_KEYS.lastView, lastView);
            localStorage.setItem(LS_KEYS.lastFocusedId, focusedNode.value ? String((focusedNode.value as any).id) : '');
        } catch (e) {
            void e;
            // ignore storage errors
        }
    },
    { immediate: true },
);

// react to scenario prop updates (e.g., loaded after mount)
watch(
    () => props.scenario,
    (nv) => {
        if (!nv) return;
        if (
            Array.isArray((nv as any).capabilities) &&
            (nv as any).capabilities.length > 0
        ) {
            const caps = (nv as any).capabilities;
            buildNodesFromItems(caps);
            buildEdgesFromItems(caps);
            loaded.value = true;
        } else {
            void loadTreeFromApi((nv as any).id);
        }
    },
    { immediate: false, deep: true },
);

// keep scenario name in sync when scenario prop changes
watch(
    () => props.scenario && (props.scenario.name ?? null),
    (nv) => {
        if (scenarioNode.value) scenarioNode.value.name = nv ?? 'Escenario';
    },
);

// ensure scenarioEdges recompute whenever main nodes change (positions or list)
watch(
    () => nodes.value.map((n) => n.id),
    () => {
        if (scenarioNode.value && Array.isArray(nodes.value)) {
            scenarioEdges.value = nodes.value.map((n: any) => ({ source: scenarioNode.value!.id, target: n.id, isScenarioEdge: true } as Edge));
        }
    },
    { immediate: true },
);

// debug watch removed

// ensure edges exist even if no capabilities loaded yet (avoids template warnings)
if (!edges.value) edges.value = [];
</script>

<style scoped>
.prototype-map-root {
    padding: 16px;
    position: relative;
    /* use viewport-aware height so the component adapts to screen size */
    height: calc(100vh - 120px);
    max-height: calc(100vh - 72px);
    display: flex;
    flex-direction: column;
    color: #ffffff;
}

.prototype-map-root::before {
    content: '';
    position: absolute;
    inset: 8px;
    border-radius: 14px;
    pointer-events: none;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), inset 0 -24px 48px rgba(11,22,48,0.12);
    z-index: 10;
}

.map-canvas {
    border-radius: 12px;
    overflow: visible;
}

/* sidebar styles - anchored to the right */
.scenario-sidebar {
    position: absolute;
    right: 16px;
    top: 16px;
    bottom: 16px;
    width: 340px;
    max-width: calc(100% - 48px);
    background: rgba(18, 24, 32, 0.95);
    color: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(2,6,23,0.6);
    z-index: 60;
    overflow: auto;
}
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 240ms ease;
}
.slide-fade-enter-from {
    transform: translateX(12px);
    opacity: 0;
}
.slide-fade-enter-to {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-from {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-to {
    transform: translateX(12px);
    opacity: 0;
}

.edges line {
    transition:
        x1 0.08s linear,
        y1 0.08s linear,
        x2 0.08s linear,
        y2 0.08s linear;
}

/* visual improvements */
.edge-line {
    stroke: rgba(255, 255, 255, 0.12);
    stroke-width: 1.5;
    transition:
        stroke 0.12s ease,
        stroke-width 0.12s ease,
        opacity 0.12s ease;
}

/* make SVG canvas expand to available space */
.map-canvas {
    display: block;
    width: 100%;
    flex: 1 1 auto;
    height: auto;
    min-height: 260px;
}

/* smooth pan/zoom transitions for viewport group */
.viewport-group {
    transition: transform 420ms cubic-bezier(.22,.9,.3,1);
}

.node-group {
    cursor: grab;
    transition: transform 420ms cubic-bezier(.22,.9,.3,1), opacity 320ms ease, filter 360ms ease;
    transform-box: fill-box;
    transform-origin: center;
}
.node-group:active {
    cursor: grabbing;
}

/* hover scale removed to disable hover animation */

.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 0.12s ease;
}

/* SVG text labels should be white for better contrast */
.node-label {
    fill: #ffffff;
    fill-opacity: 1;
    font-weight: 600;
    font-size: 12px;
    dominant-baseline: hanging;
}

.node-group.small .node-label {
    font-size: 10px;
}

/* scale down non-selected nodes smoothly via CSS transform */
.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 420ms cubic-bezier(.22,.9,.3,1);
    transform-box: fill-box;
    transform-origin: center;
}

/* hover + focus */
.node-group:hover .node-circle {
    transform: scale(1.06);
}
.node-group:active .node-circle {
    transform: scale(1.02);
}

/* pulse for critical nodes */
.node-group.critical {
    animation: pulse 2400ms infinite;
}
@keyframes pulse {
    0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(0,0,0,0)); }
    50% { transform: scale(1.03); }
    100% { transform: scale(1); }
}

/* scale down visuals (circle + label) for non-selected nodes without moving their group */
.node-group.small .node-circle {
    transform: scale(0.5);
}
.node-group.small .node-label {
    transform: scale(0.85);
    transform-box: fill-box;
    transform-origin: center;
}

/* scenario node styling (smooth follow transition) */
.scenario-node {
    pointer-events: none;
    transition: transform 360ms cubic-bezier(.22,.9,.3,1);
}

/* animated scenario-edge: moving dash + soft pulse */
.scenario-edge {
    stroke-linecap: round;
}

/* scenario-edge: static styling (no animation) */
.edge-line.scenario-edge {
    stroke-linecap: round;
}

.child-node .node-circle {
    fill: #1f2937;
}

.child-iridescence {
    pointer-events: none;
    transition: opacity 200ms ease, transform 200ms ease;
    mix-blend-mode: screen;
}
.child-reflection {
    pointer-events: none;
}
.child-rim {
    pointer-events: none;
}
.child-edge {
    stroke-dasharray: none;
    opacity: 0.95;
}

/* make child edges more visible with subtle glow */
.edge-line.child-edge {
    mix-blend-mode: screen;
}

.edge-line {
    transition: stroke-width 180ms ease, stroke-opacity 180ms ease, filter 220ms ease;
}

.node-inner {
    pointer-events: none;
}

.node-reflection {
    pointer-events: none;
    transition: opacity 180ms ease, transform 180ms ease;
}
.node-rim {
    pointer-events: none;
    transition: stroke-opacity 180ms ease, transform 180ms ease;
}

.node-iridescence {
    pointer-events: none;
    transition: opacity 220ms ease, transform 220ms ease;
    mix-blend-mode: screen;
}

.node-gloss,
.child-gloss {
    pointer-events: none;
}

/* glass panel styles */
.glass-panel-strong {
    color: #ffffff;
    background: rgba(255,255,255,0.04);
    padding: 20px;
    box-sizing: border-box;
    min-width: 220px;
}
.glass-panel-strong .panel-header {
    cursor: move;
    user-select: none;
    padding-bottom: 8px;
    margin-bottom: 8px;
}
.glass-panel-strong .mb-2 {
    margin-bottom: 8px;
}

/* sidebar variant: when a node detail sidebar is open, push content to the left */
.with-node-sidebar {
    transition: margin 180ms ease;
    margin-right: 360px; /* width of sidebar */
}

.node-details-sidebar {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 360px;
    box-sizing: border-box;
    z-index: 60;
    /* allow the collapse toggle to overflow the panel edge so it remains visible */
    overflow: visible;
    padding: 20px;
    backdrop-filter: blur(6px);
}

.node-details-sidebar.theme-dark {
    box-shadow: 0 8px 30px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
    border-left: 1px solid rgba(255,255,255,0.04);
}
.node-details-sidebar.theme-light {
    box-shadow: 0 8px 24px rgba(2,6,23,0.28), inset 0 1px 0 rgba(255,255,255,0.02);
}

/* Light theme (default) */
.node-details-sidebar.theme-light {
    border-left: 1px solid rgba(0,0,0,0.06);
    background: rgba(255,255,255,0.96);
    color: #0b0b0b;
}
.node-details-sidebar.theme-light .text-white\/60,
.node-details-sidebar.theme-light .text-white\/50,
.node-details-sidebar.theme-light .text-small,
.node-details-sidebar.theme-light .text-xs {
    color: rgba(0,0,0,0.72) !important;
}
.node-details-sidebar.theme-light pre,
.node-details-sidebar.theme-light summary {
    color: #0b0b0b !important;
}

/* Dark theme (glass-style) */
.node-details-sidebar.theme-dark {
    border-left: 1px solid rgba(255,255,255,0.04);
    background: rgba(11,16,41,0.95);
    color: #ffffff;
}
.node-details-sidebar.theme-dark .text-white\/60,
.node-details-sidebar.theme-dark .text-white\/50,
.node-details-sidebar.theme-dark .text-small,
.node-details-sidebar.theme-dark .text-xs {
    color: rgba(255,255,255,0.72) !important;
}
.node-details-sidebar.theme-dark pre,
.node-details-sidebar.theme-dark summary {
    color: #ffffff !important;
}




/* collapsed sidebar: narrow tab and reduced margin */
.with-node-sidebar-collapsed {
    transition: margin 180ms ease;
    margin-right: 56px; /* narrow tab width */
}
.node-details-sidebar.collapsed {
    width: 56px !important;
    padding: 8px !important;
    overflow: visible;
}
.node-details-sidebar.collapsed .panel-header > strong,
.node-details-sidebar.collapsed .text-xs,
.node-details-sidebar.collapsed .text-small,
.node-details-sidebar.collapsed details,
.node-details-sidebar.collapsed .sidebar-body {
    display: none !important;
}
.sidebar-collapse-toggle {
    position: absolute;
    left: -3em;
    top: 14%;
    transform: translateY(-50%);
    z-index: 100001;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-collapse-toggle .v-btn {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 999px;
    background: rgba(0,0,0,0.06);
    box-shadow: 0 6px 18px rgba(2,6,23,0.18);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: inherit;
    border: 1px solid rgba(0,0,0,0.06);
}

</style>
