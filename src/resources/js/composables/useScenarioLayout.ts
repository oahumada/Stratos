/**
 * useScenarioLayout.ts
 *
 * Centraliza toda la lógica de layout, animaciones y posicionamiento.
 * Incluye: expandCompetencies, expandSkills, centerOnNode, reorderNodes.
 */

import type { Edge, NodeItem } from '@/types/brain';
import * as d3 from 'd3';
import {
    chooseMatrixVariant,
    computeCompetencyMatrixPositions,
    computeSidesPositions,
    decideCompetencyLayout,
} from './useCompetencyLayout';
import { computeMatrixPositions } from './useNodeNavigation';

// Centralized layout configuration
export const LAYOUT_CONFIG = {
    capability: {
        spacing: { hSpacing: 10, vSpacing: 10 },
        forces: { linkDistance: 100, linkStrength: 0.5, chargeStrength: -220 },
        scenarioEdgeDepth: 120,
        text: { fontSize: 12, fontWeight: 600 },
    },
    competency: {
        radial: {
            radius: 150,
            selectedOffsetY: -20,
            startAngle: -Math.PI / 4,
            endAngle: (5 * Math.PI) / 4,
        },
        sides: { selectedOffsetMultiplier: 0.75 },
        defaultLayout: 'auto' as 'auto' | 'radial' | 'matrix' | 'sides',
        parentOffset: 20,
        maxDisplay: 10,
        matrixVariants: [
            { min: 2, max: 3, rows: 1, cols: 3 },
            { min: 4, max: 8, rows: 2, cols: 4 },
            { min: 9, max: 10, rows: 2, cols: 5 },
        ],
        spacing: { hSpacing: 100, vSpacing: 20, parentOffset: -10 },
        edge: { baseDepth: 40, curveFactor: 0.65, spreadOffset: 18 },
        text: { fontSize: 10, fontWeight: 600 },
    },
    skill: {
        maxDisplay: 10,
        radial: {
            radius: 20,
            startAngle: -Math.PI / 6,
            endAngle: (7 * Math.PI) / 6,
            offsetFactor: 0.8,
            offsetY: 40,
        },
        defaultLayout: 'sides' as 'auto' | 'radial' | 'matrix' | 'sides',
        matrixVariants: [
            { min: 2, max: 3, rows: 1, cols: 3 },
            { min: 4, max: 8, rows: 2, cols: 4 },
            { min: 9, max: 10, rows: 2, cols: 5 },
        ],
        sides: {
            hSpacing: 80,
            vSpacing: 40,
            parentOffset: 150,
            selectedOffsetMultiplier: 2,
        },
        linear: { hSpacing: 60, vSpacing: 40 },
        edge: { baseDepth: 35, curveFactor: 0.5, spreadOffset: 20 },
        text: { fontSize: 12, fontWeight: 600 },
    },
    animations: {
        competencyEntryFinalize: 80,
        skillEntryFinalize: 70,
        collapseDuration: 10,
        competencyStaggerRow: 30,
        competencyStaggerCol: 12,
        competencyStaggerRandom: 30,
        skillStaggerRow: 20,
        skillStaggerCol: 8,
        leadFactor: 0.6,
    },
    node: {
        radius: 34,
        focusRadius: 44,
        competencyOpacity: 0.2, // Opacidad de competency para efecto burbuja
        skillOpacity: 0.2, // Opacidad de skill para efecto burbuja
    },
    clamp: { minY: 40, bottomPadding: 40, minViewportHeight: 120 },
};

const TRANSITION_MS = 420;
const TRANSITION_BUFFER = 60;

export function useScenarioLayout() {
    // ==================== HELPERS ====================
    function wait(ms: number) {
        return new Promise((res) => setTimeout(res, ms));
    }

    function clampY(y: number) {
        const minY = LAYOUT_CONFIG.clamp.minY;
        const bottomPadding = LAYOUT_CONFIG.clamp.bottomPadding;
        const minViewportHeight = LAYOUT_CONFIG.clamp.minViewportHeight;
        const maxY =
            Math.max(
                minViewportHeight,
                (window as any).__scenarioHeight ?? 600,
            ) - bottomPadding;
        return Math.min(Math.max(y, minY), maxY);
    }

    function wrapLabel(s: any, max = 14) {
        if (s == null) return '';
        const str = String(s).trim();
        if (str.length <= max) return str;

        const cutLine = (text: string, limit: number) => {
            if (text.length <= limit) return { line: text, rest: '' };
            const slice = text.slice(0, limit + 1);
            const lastSpace = slice.lastIndexOf(' ');
            if (lastSpace > 0) {
                return {
                    line: text.slice(0, lastSpace),
                    rest: text.slice(lastSpace + 1).trim(),
                };
            }
            return {
                line: text.slice(0, limit),
                rest: text.slice(limit).trim(),
            };
        };

        const first = cutLine(str, max);
        if (!first.rest) return first.line;

        const secondRaw = first.rest;
        if (secondRaw.length <= max) return first.line + '\n' + secondRaw;

        const secondCut = cutLine(secondRaw, max);
        let second = secondCut.line;
        if (secondCut.rest && second.length >= max) {
            second =
                second.slice(0, Math.max(0, max - 1)).replace(/\s+$/, '') + '…';
        } else if (secondCut.rest) {
            second = second + '…';
        }
        return first.line + '\n' + second;
    }

    function computeInitialPosition(
        idx: number,
        total: number,
        width: number,
        height: number,
    ) {
        const centerX = width / 2;
        const centerY = height / 2 - 30;

        if (total <= 1)
            return { x: Math.round(centerX), y: Math.round(centerY) };

        const columns = Math.min(5, Math.max(1, Math.ceil(Math.sqrt(total))));
        const rows = Math.max(1, Math.ceil(total / columns));

        const margin = 24;
        const availableW = Math.max(120, width - margin * 2);
        const availableH = Math.max(120, height - margin * 2);
        const spacingX =
            columns > 1 ? Math.min(160, Math.floor(availableW / columns)) : 0;
        const spacingY =
            rows > 1 ? Math.min(140, Math.floor(availableH / rows)) : 0;

        const col = idx % columns;
        const row = Math.floor(idx / columns);

        const totalGridW = (columns - 1) * spacingX;
        const totalGridH = (rows - 1) * spacingY;
        const offsetX = col * spacingX - totalGridW / 2;
        const offsetY = row * spacingY - totalGridH / 2;

        return {
            x: Math.round(centerX + offsetX),
            y: Math.round(centerY + offsetY),
        };
    }

    // ==================== CENTER ON NODE ====================
    function centerOnNode(
        node: NodeItem,
        prev: NodeItem | undefined,
        width: number,
        height: number,
    ) {
        if (!node) return;

        // Store original positions
        const originalPositions = new Map<number, { x: number; y: number }>();

        const centerX = Math.round(width / 2);
        const VERTICAL_FOCUS_RATIO = 0.25;
        const centerY = Math.round(height * VERTICAL_FOCUS_RATIO);

        const leftX = Math.round(width * 0.18);
        const rightX = Math.round(width * 0.82);

        // If swapping with previous node, use positions
        if (prev && prev.id !== node.id) {
            const prevNode = prev;
            const newNode = node;

            const tx = prevNode.x ?? 0;
            const ty = prevNode.y ?? 0;
            prevNode.x = newNode.x ?? tx;
            prevNode.y = newNode.y ?? ty;
            newNode.x = tx;
            newNode.y = ty;

            return {
                updatedNode: newNode,
                updatedPrevNode: prevNode,
            };
        }

        // Position centered node
        node.x = centerX;
        node.y = centerY;

        return { updatedNode: node };
    }

    // ==================== EXPAND COMPETENCIES ====================
    function expandCompetencies(
        node: NodeItem,
        childNodes: any[],
        childEdges: Edge[],
        initialParentPos?: { x: number; y: number },
        opts: {
            limit?: number;
            rows?: number;
            cols?: number;
            layout?: 'auto' | 'radial' | 'matrix' | 'sides';
        } = {},
        width?: number,
        height?: number,
    ) {
        const comps = (node as any).competencies ?? [];
        if (!Array.isArray(comps) || comps.length === 0)
            return { childNodes: [], childEdges: [] };

        const maxDisplay = LAYOUT_CONFIG.competency.maxDisplay;
        const limit = Math.min(opts.limit ?? maxDisplay, maxDisplay);
        const toShow = comps.slice(0, limit);

        const cx = node.x ?? Math.round((width ?? 900) / 2);
        const parentY = node.y ?? Math.round((height ?? 600) / 2);
        const defaultParentOffset =
            LAYOUT_CONFIG.competency.spacing.parentOffset;
        const verticalOffset = defaultParentOffset;
        const CHILD_DROP = 18;
        const topY = Math.round(parentY + verticalOffset + CHILD_DROP);

        let rows = opts.rows ?? 1;
        let cols = opts.cols ?? 4;
        let hSpacing = LAYOUT_CONFIG.competency.spacing.hSpacing;
        let vSpacing = LAYOUT_CONFIG.competency.spacing.vSpacing;

        const matrixVariants = LAYOUT_CONFIG.competency.matrixVariants;
        try {
            const variantChoice = chooseMatrixVariant(
                toShow.length,
                matrixVariants,
                maxDisplay,
            );
            rows = variantChoice.rows;
            cols = variantChoice.cols;
        } catch (err) {
            void err;
        }

        const layout = decideCompetencyLayout(
            opts.layout,
            false,
            toShow.length,
            LAYOUT_CONFIG.competency.defaultLayout,
        );

        let positions: any[] = [];
        if (layout === 'radial') {
            const radius = LAYOUT_CONFIG.competency.radial.radius;
            const startAngle = LAYOUT_CONFIG.competency.radial.startAngle;
            const endAngle = LAYOUT_CONFIG.competency.radial.endAngle;
            const angleRange = endAngle - startAngle;
            const angleStep = angleRange / Math.max(1, toShow.length - 1);

            positions = toShow.map((c: any, i: number) => {
                const angle = startAngle + i * angleStep;
                const x = Math.round(cx + radius * Math.cos(angle));
                const y = Math.round(topY + radius * Math.sin(angle));
                return { x, y };
            });
        } else if (layout === 'sides') {
            try {
                positions = computeSidesPositions(
                    toShow.length,
                    cx,
                    parentY,
                    LAYOUT_CONFIG.competency.sides,
                    null,
                );
            } catch (err) {
                positions = [];
            }
        } else {
            positions = computeCompetencyMatrixPositions(
                toShow.length,
                cx,
                topY,
                rows,
                cols,
                hSpacing,
                vSpacing,
            );
        }

        const builtChildren: any[] = [];
        toShow.forEach((c: any, i: number) => {
            const pos = positions[i] || { x: cx, y: topY };
            const id = -(node.id * 1000 + i + 1);
            const delay = Math.max(
                0,
                Math.floor(i / cols) *
                    (LAYOUT_CONFIG.animations.competencyStaggerRow ?? 30) +
                    (i % cols) *
                        (LAYOUT_CONFIG.animations.competencyStaggerCol ?? 12) +
                    Math.round(
                        (Math.random() - 0.5) *
                            (LAYOUT_CONFIG.animations.competencyStaggerRandom ??
                                30),
                    ),
            );

            const child = {
                id,
                compId: c.id ?? null,
                name: c.name ?? c,
                displayName: wrapLabel(c.name ?? c, 14),
                x: initialParentPos?.x ?? node.x ?? cx,
                y: initialParentPos?.y ?? node.y ?? parentY,
                animScale: 0.84,
                animOpacity: 0,
                animDelay: delay,
                animFilter:
                    'blur(6px) drop-shadow(0 10px 18px rgba(2,6,23,0.36))',
                animTargetX: pos.x,
                animTargetY: clampY(pos.y),
                is_critical: false,
                description: c.description ?? null,
                readiness: c.readiness ?? null,
                skills: Array.isArray(c.skills) ? c.skills : [],
                raw: c,
            };
            builtChildren.push(child);
            childEdges.push({ source: node.id, target: id });
        });

        return { childNodes: builtChildren, childEdges };
    }

    // ==================== EXPAND SKILLS ====================
    function expandSkills(
        node: any,
        childNodes: any[],
        grandChildEdges: Edge[],
        initialPos?: { x: number; y: number },
        opts: { layout?: 'auto' | 'radial' | 'matrix' | 'sides' } = {},
        height?: number,
    ) {
        const skills = Array.isArray(node.skills)
            ? node.skills
            : (node.raw?.skills ?? []);
        if (!Array.isArray(skills) || skills.length === 0)
            return { grandChildNodes: [], grandChildEdges: [] };

        const limit = LAYOUT_CONFIG.skill.maxDisplay;
        const toShow = skills.slice(0, limit);

        const cx = initialPos?.x ?? node.x ?? 450;
        const parentY = initialPos?.y ?? node.y ?? 300;
        const SKILL_PARENT_OFFSET_BASE = LAYOUT_CONFIG.skill.radial.offsetY;
        const SKILL_PARENT_OFFSET = Math.round(
            SKILL_PARENT_OFFSET_BASE *
                (1 + (LAYOUT_CONFIG.skill.radial.offsetFactor ?? 0)),
        );
        const SKILL_DROP_EXTRA = 30;
        const topY = Math.round(
            parentY + SKILL_PARENT_OFFSET + SKILL_DROP_EXTRA,
        );

        const layoutOpt = opts?.layout ?? undefined;
        const layout = decideCompetencyLayout(
            layoutOpt as any,
            false,
            toShow.length,
            LAYOUT_CONFIG.skill.defaultLayout as any,
        );

        let positions: any[] = [];
        if (layout === 'radial') {
            const radius = LAYOUT_CONFIG.skill.radial.radius;
            const startAngle = LAYOUT_CONFIG.skill.radial.startAngle;
            const endAngle = LAYOUT_CONFIG.skill.radial.endAngle;
            const angleRange = endAngle - startAngle;
            const angleStep = angleRange / Math.max(1, toShow.length - 1);

            positions = toShow.map((sk: any, i: number) => {
                const angle = startAngle + i * angleStep;
                const x = Math.round(cx + radius * Math.cos(angle));
                const y = Math.round(topY + radius * Math.sin(angle));
                return { x, y };
            });
        } else if (layout === 'sides') {
            try {
                positions = computeSidesPositions(
                    toShow.length,
                    cx,
                    parentY,
                    LAYOUT_CONFIG.skill.sides,
                    null,
                );
            } catch (err) {
                positions = [];
            }
        } else {
            const matrixVariants = LAYOUT_CONFIG.skill.matrixVariants;
            try {
                const variant = chooseMatrixVariant(
                    toShow.length,
                    matrixVariants,
                    LAYOUT_CONFIG.skill.maxDisplay,
                );
                positions = computeMatrixPositions(toShow.length, cx, topY, {
                    rows: variant.rows,
                    cols: variant.cols,
                    hSpacing: LAYOUT_CONFIG.skill.linear.hSpacing,
                    vSpacing: LAYOUT_CONFIG.skill.linear.vSpacing,
                });
            } catch (err) {
                const rows = 1;
                const cols = Math.min(4, toShow.length);
                positions = computeMatrixPositions(toShow.length, cx, topY, {
                    rows,
                    cols,
                    hSpacing: LAYOUT_CONFIG.skill.linear.hSpacing,
                    vSpacing: LAYOUT_CONFIG.skill.linear.vSpacing,
                });
            }
        }

        const built: any[] = [];
        toShow.forEach((sk: any, i: number) => {
            const pos = positions[i] || { x: cx, y: topY };
            const id = -(Math.abs(node.id) * 100000 + i + 1);
            const delay = Math.max(
                0,
                Math.floor(i / 4) *
                    (LAYOUT_CONFIG.animations.skillStaggerRow ?? 20) +
                    (i % 4) * (LAYOUT_CONFIG.animations.skillStaggerCol ?? 8),
            );

            const item = {
                id,
                name: sk.name ?? sk,
                x: initialPos?.x ?? node.x ?? cx,
                y: initialPos?.y ?? node.y ?? parentY,
                animScale: 0.8,
                animOpacity: 0,
                animDelay: delay,
                animTargetX: pos.x,
                animTargetY: clampY(pos.y),
                raw: sk,
            };
            built.push(item);
            grandChildEdges.push({ source: node.id, target: id });
        });

        return { grandChildNodes: built, grandChildEdges };
    }

    // ==================== COLLAPSE GRANDCHILDREN ====================
    function collapseGrandChildren(animated = false, duration?: number) {
        duration =
            typeof duration === 'number'
                ? duration
                : (LAYOUT_CONFIG.animations.collapseDuration ?? 10);
        return { animated, duration };
    }

    // ==================== RUNFORCE LAYOUT ====================
    function runForceLayout(
        nodes: NodeItem[],
        edges: Edge[],
        width: number,
        height: number,
    ): NodeItem[] {
        try {
            const simNodes = nodes.map((n) => ({
                id: n.id,
                x: n.x || 0,
                y: n.y || 0,
            }));
            const simLinks = edges.map((l) => ({
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
                        .distance(LAYOUT_CONFIG.capability.forces.linkDistance)
                        .strength(LAYOUT_CONFIG.capability.forces.linkStrength),
                )
                .force(
                    'charge',
                    (d3 as any)
                        .forceManyBody()
                        .strength(
                            LAYOUT_CONFIG.capability.forces.chargeStrength,
                        ),
                )
                .force(
                    'center',
                    (d3 as any).forceCenter(width / 2, height / 2),
                );

            for (let i = 0; i < 300; i++) simulation.tick();
            simulation.stop();

            const pos = new Map(
                simNodes.map((n: any) => [
                    n.id,
                    { x: Math.round(n.x), y: Math.round(n.y) },
                ]),
            );

            return nodes.map((n: any) => {
                const p = pos.get(n.id);
                return { ...n, x: p?.x ?? n.x, y: p?.y ?? n.y };
            });
        } catch (err) {
            console.warn('[runForceLayout] failed', err);
            return nodes;
        }
    }

    return {
        LAYOUT_CONFIG,
        wait,
        clampY,
        wrapLabel,
        computeInitialPosition,
        centerOnNode,
        expandCompetencies,
        expandSkills,
        collapseGrandChildren,
        runForceLayout,
    };
}
