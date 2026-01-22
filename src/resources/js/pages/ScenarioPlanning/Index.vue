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
            <div v-if="props.scenario && props.scenario.id">
                Escenario: {{ props.scenario?.name || '—' }}
            </div>
            <v-btn
                small
                color="primary"
                @click="savePositions"
                v-if="props.scenario && props.scenario.id"
                >Guardar posiciones</v-btn
            >
            <v-btn small @click="resetPositions" v-if="nodes.length > 0"
                >Reset posiciones</v-btn
            >
            <v-btn
                small
                icon
                color="primary"
                @click="openScenarioInfo"
                title="Volver a la vista inicial y mostrar información del escenario"
            >
                <v-icon icon="mdi-home" />
            </v-btn>
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
            <v-btn
                small
                icon
                color="primary"
                @click="togglePanelFullscreen"
                :title="panelFullscreen ? 'Salir de pantalla completa' : 'Ver detalles en pantalla completa'"
            >
                <v-icon :icon="panelFullscreen ? 'mdi-fullscreen-exit' : 'mdi-fullscreen'" />
            </v-btn>
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

            <!-- Nodo: panel lateral que desplaza contenido en vista normal -->
            <transition name="slide-fade">
                            <aside
                                class="node-details-sidebar glass-panel-strong"
                                :class="[{ 'glass-fullscreen': panelFullscreen && !isDocumentFullScreen, collapsed: nodeSidebarCollapsed }, sidebarTheme === 'dark' ? 'theme-dark' : 'theme-light']"
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
                        <div><strong>Crítico:</strong> {{ (focusedNode as any).is_critical ? 'Sí' : 'No' }}</div>
                        <div><strong>Importancia:</strong> {{ (focusedNode as any).importance ?? '—' }}</div>
                        <div><strong>Nivel:</strong> {{ (focusedNode as any).level ?? '—' }}</div>
                        <div><strong>Required:</strong> {{ (focusedNode as any).required ?? '—' }}</div>
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
                            <div v-if="(focusedNode as any).description">{{ (focusedNode as any).description }}</div>
                        </template>
                    </div>

                    <div v-if="(focusedNode as any).competencies && (focusedNode as any).competencies.length > 0">
                        <div class="text-xs text-white/60 mb-1">Competencias</div>
                        <ul class="pl-3 mb-0">
                            <li v-for="(c, i) in (focusedNode as any).competencies" :key="i">{{ (c && c.name) || c }}</li>
                        </ul>
                    </div>
                    <div v-else-if="!((focusedNode as any).skills) && !((focusedNode as any).competencies && (focusedNode as any).competencies.length)"> 
                        <div class="text-xs text-white/50">No hay competencias registradas.</div>
                    </div>

                    <!-- show created/updated if available in raw payload -->
                    <div class="mt-3 text-xs text-white/50">
                        <div v-if="(focusedNode as any).raw && (focusedNode as any).raw.created_at"><strong>Creado:</strong> {{ (focusedNode as any).raw.created_at }}</div>
                        <div v-if="(focusedNode as any).raw && (focusedNode as any).raw.updated_at"><strong>Actualizado:</strong> {{ (focusedNode as any).raw.updated_at }}</div>
                    </div>

                    <!-- quick debug: raw JSON -->
                    <details class="mt-3 text-xs text-white/50">
                        <summary>Mostrar JSON crudo</summary>
                        <pre style="white-space: pre-wrap; word-break: break-word;">{{ JSON.stringify((focusedNode as any).raw ?? focusedNode, null, 2) }}</pre>
                    </details>
                    </template>
                    <template v-else>
                        <div class="text-xs text-white/60 mb-2">Selecciona un nodo para ver detalles.</div>
                    </template>

                    <!-- Scenario info shown inside the same panel when toggled -->
                    <transition name="slide-fade">
                        <div v-if="showSidebar && !focusedNode" class="sidebar-body text-sm mt-4">
                            <div class="mb-2"><strong>Nombre:</strong> {{ props.scenario?.name || '—' }}</div>
                            <div v-if="props.scenario?.description" class="mb-2"><strong>Descripción:</strong> {{ props.scenario.description }}</div>
                            <div class="mb-2"><strong>ID:</strong> {{ props.scenario?.id ?? '—' }}</div>
                            <div class="mb-2"><strong>Estado:</strong> {{ props.scenario?.status ?? '—' }}</div>
                            <div class="mb-2"><strong>Año fiscal:</strong> {{ props.scenario?.fiscal_year ?? '—' }}</div>
                            <div class="mb-2"><strong>Organización:</strong> {{ props.scenario?.organization_id ?? '—' }}</div>
                            <div v-if="props.scenario?.created_at" class="mb-2"><strong>Creado:</strong> {{ props.scenario.created_at }}</div>
                            <div v-if="props.scenario?.updated_at" class="mb-2"><strong>Actualizado:</strong> {{ props.scenario.updated_at }}</div>
                            <div class="mb-2"><strong>Capacidades:</strong> {{ (props.scenario?.capabilities ?? []).length }}</div>
                        </div>
                    </transition>
                </aside>
            </transition>
            
            
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
import type { NodeItem, Edge, ConnectionPayload, ScenarioShape } from '@/types/brain';
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
const api = useApi();
const loaded = ref(false);
const nodes = ref<Array<NodeItem>>([]);
const edges = ref<Array<Edge>>([]);
const dragging = ref<any>(null);
const dragOffset = ref({ x: 0, y: 0 });
const { showSuccess, showError } = useNotification();
const width = ref(900);
const height = ref(600);
const mapRoot = ref<HTMLElement | null>(null);
let lastItems: any[] = [];
const focusedNode = ref<NodeItem | null>(null);
const tooltipX = ref(0);
const tooltipY = ref(0);
const childNodes = ref<Array<NodeItem>>([]);
const childEdges = ref<Array<Edge>>([]);
const scenarioEdges = ref<Array<Edge>>([]);
const showSidebar = ref(false);
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
        const minSpacing = 32; // minimum spacing between nodes (more compact)
        const maxSpacing = 100; // cap spacing to avoid overly spread columns
        const spacing = len > 1 ? Math.min(maxSpacing, Math.max(minSpacing, Math.floor(available / (len - 1)))) : 0;

        // if group is large, split into multiple parallel columns to avoid vertical crowding
        const maxPerColumn = 5;
        if (len <= maxPerColumn) {
            const startY = Math.round(centerY - ((len - 1) * spacing) / 2);
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
    distribute(leftGroup, leftX);
    distribute(rightGroup, rightX);

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

    // compute grid dimensions (cols x rows) close to square
    const cols = Math.max(1, Math.ceil(Math.sqrt(total)));
    const rows = Math.max(1, Math.ceil(total / cols));

    // margins and spacing (more compact vertical spacing)
    const margin = 24;
    const availableW = Math.max(120, width.value - margin * 2);
    const availableH = Math.max(120, height.value - margin * 2);
    const spacingX = cols > 1 ? Math.min(160, Math.floor(availableW / cols)) : 0;
    // increase vertical spacing cap to provide more room between rows
    const spacingY = rows > 1 ? Math.min(140, Math.floor(availableH / rows)) : 0;

    const col = idx % cols;
    const row = Math.floor(idx / cols);

    // center the whole grid around centerX/centerY
    const totalGridW = (cols - 1) * spacingX;
    const totalGridH = (rows - 1) * spacingY;
    const offsetX = col * spacingX - totalGridW / 2;
    const offsetY = row * spacingY - totalGridH / 2;

    return { x: Math.round(centerX + offsetX), y: Math.round(centerY + offsetY) };
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
    const mapped = items.map((it: any, idx: number) => {
        const rawX = it.position_x ?? it.x ?? it.cx ?? null;
        const rawY = it.position_y ?? it.y ?? it.cy ?? null;
        const parsedX = rawX != null ? parseFloat(String(rawX)) : NaN;
        const parsedY = rawY != null ? parseFloat(String(rawY)) : NaN;
        // Default behaviour: center grid by default. Ignore stored coordinates so layout is consistent.
    const hasPos = false;
        const x = hasPos ? Math.round(parsedX) : undefined;
        const y = hasPos ? Math.round(parsedY) : undefined;
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

    // If we are NOT in fullscreen mode, fix panel to top-left corner
    const inTrueFullscreen = !!document.fullscreenElement;
    if (!inTrueFullscreen && !panelFullscreen.value) {
        tooltipX.value = 24;
        tooltipY.value = 24;
    } else if (event) {
        // when in fullscreen or CSS-fullscreen, use event coords
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
const panelFullscreen = ref(false);

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
const isDocumentFullScreen = computed(() => {
    return !!(globalThis.document && globalThis.document.fullscreenElement);
});

const panelStyle = computed<CSSProperties>(() => {
    // compute width depending on collapsed state and fullscreen
    const inTrueFullscreen = !!document.fullscreenElement;
    const widthPx = inTrueFullscreen || panelFullscreen.value ? 360 : nodeSidebarCollapsed.value ? 56 : 360;

    // If document is in true Fullscreen API or panelFullscreen requested, keep the panel fixed
    if (inTrueFullscreen || panelFullscreen.value) {
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

async function togglePanelFullscreen() {
    // prefer using the Fullscreen API when available
    const fsAvailable = !!(document.fullscreenEnabled);
    // if Fullscreen API unavailable, fallback to CSS toggle on panel
    if (!fsAvailable) {
        panelFullscreen.value = !panelFullscreen.value;
        if (panelFullscreen.value) {
            tooltipX.value = 24;
            tooltipY.value = 24;
        }
        return;
    }

    // request fullscreen on the main map container so SVG remains visible
    const containerEl = (mapRoot.value as HTMLElement) || document.documentElement;
    const panelEl = document.querySelector('.glass-panel-strong') as HTMLElement | null;

    // compute relative ratios of the panel inside the container so we can restore position after resize
    let ratioX = 0.06;
    let ratioY = 0.06;
    if (panelEl && containerEl instanceof HTMLElement) {
        const mapRectBefore = containerEl.getBoundingClientRect();
        const panelRectBefore = panelEl.getBoundingClientRect();
        if (mapRectBefore.width > 0 && mapRectBefore.height > 0) {
            ratioX = (panelRectBefore.left - mapRectBefore.left) / mapRectBefore.width;
            ratioY = (panelRectBefore.top - mapRectBefore.top) / mapRectBefore.height;
        }
    }

    try {
        if (!document.fullscreenElement) {
            await containerEl.requestFullscreen();
            // ensure visual state
            panelFullscreen.value = true;
            // allow layout to stabilise and then compute new absolute pos using ratios
            await new Promise((r) => setTimeout(r, 48));
            const mapRectNow = (containerEl as HTMLElement).getBoundingClientRect();
            tooltipX.value = Math.round(Math.max(8, Math.min(mapRectNow.width - 48, ratioX * mapRectNow.width)));
            tooltipY.value = Math.round(Math.max(8, Math.min(mapRectNow.height - 48, ratioY * mapRectNow.height)));
        } else {
            await document.exitFullscreen();
            panelFullscreen.value = false;
            // when exiting fullscreen, clamp position to the normal container
            const panelElAfter = document.querySelector('.glass-panel-strong') as HTMLElement | null;
            if (panelElAfter && mapRoot.value) {
                const mapRectAfter = (mapRoot.value as HTMLElement).getBoundingClientRect();
                const panelRectAfter = panelElAfter.getBoundingClientRect();
                const clampedX = Math.min(Math.max(panelRectAfter.left - mapRectAfter.left, 8), Math.max(8, mapRectAfter.width - panelRectAfter.width - 8));
                const clampedY = Math.min(Math.max(panelRectAfter.top - mapRectAfter.top, 8), Math.max(8, mapRectAfter.height - panelRectAfter.height - 8));
                tooltipX.value = Math.round(clampedX);
                tooltipY.value = Math.round(clampedY);
            }
        }
    } catch (err) {
        // fallback: toggle CSS on panel only
        panelFullscreen.value = !panelFullscreen.value;
        if (panelEl) {
            if (panelFullscreen.value) panelEl.classList.add('glass-fullscreen');
            else panelEl.classList.remove('glass-fullscreen');
        }
    }
}

function onFullscreenChange() {
    const containerEl = (mapRoot.value as HTMLElement) || null;
    const isFs = document.fullscreenElement === containerEl;
    panelFullscreen.value = !!isFs;
}

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
    const columns = Math.max(1, Math.min(8, parseInt(String(props.childColumns ?? 4), 10))); // configurable via props, clamped 1..8
    const columnSpacing = 72; // horizontal distance between columns (reduced for 4 columns)
    // If the node is itself a child node (we use negative ids for childNodes), use a tighter layout
    const isChildNode = node.id != null && node.id < 0;
    // Tighten layout for grandchildren: smaller spacing and much smaller vertical offset
    const baseRowSpacing = isChildNode ? 30 : 44; // tighter spacing for grandchildren
    const verticalOffset = isChildNode ? 80 : 100; // bring grandchildren closer to their parent
    // Add extra vertical gap for lower rows when there are many items
    const extraGapForLowerRows = comps.length > columns ? (isChildNode ? 8 : 28) : 0;

    const total = comps.length;
    const rowsPerColumn = Math.ceil(total / columns);
    // compute total block height accounting for the extra gap applied below the first row
    const blockHeight = (rowsPerColumn - 1) * baseRowSpacing + (rowsPerColumn > 1 ? extraGapForLowerRows : 0);
    // compute top start so the multi-column block is vertically centered under parent
    const startY = Math.round(cy + verticalOffset - blockHeight / 2);

    // Build child entries with initial position at parent, then animate to targets on nextTick
    const builtChildren: Array<any> = [];
        comps.forEach((c: any, i: number) => {
        const colIndex = i % columns; // 0..columns-1
        const rowIndex = Math.floor(i / columns);
        const targetX = Math.round(cx + (colIndex - (columns - 1) / 2) * columnSpacing);
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
}

function onPointerUp() {
    if (dragging.value) {
        dragging.value = null;
    }
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerup', onPointerUp);
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
            // ignore storage errors
        }
        loaded.value = true;
        // ensure scenario node initialized with correct name
        setScenarioInitial();
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
    // listen fullscreen change to sync state and classes
    document.addEventListener('fullscreenchange', onFullscreenChange);
    const onWindowResize = () => applySize();
    window.addEventListener('resize', onWindowResize);
    onBeforeUnmount(() => {
        if (ro) ro.disconnect();
        window.removeEventListener('resize', onWindowResize);
        document.removeEventListener('fullscreenchange', onFullscreenChange);
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


/* fullscreen variant for the details panel */
.glass-fullscreen {
    position: fixed !important;
    inset: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    left: 0 !important;
    top: 0 !important;
    border-radius: 0 !important;
    padding: 28px !important;
    z-index: 100000 !important;
    overflow: auto !important;
    backdrop-filter: blur(6px);
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
