/**
 * useScenarioEdges.ts
 * 
 * Composable que encapsula toda la lógica de renderizado de aristas (edges) para el grafo
 * de escenarios de planificación de capacidades. Maneja:
 * 
 * - Cálculo de puntos finales de aristas (ajuste para nodos hijo/nieto)
 * - Índice de agrupamiento para aristas paralelas
 * - Modos de renderizado: offset, gap-large, curve, spread
 * - Opacidad animada de aristas
 * - Curvas scenario -> capability (Bezier)
 * 
 * Configuración centralizada en LAYOUT_CONFIG para:
 * - Profundidad y factor de curvatura (competencia vs skill)
 * - Offset de propagación (spread)
 * - Radios de nodos y espacios
 */

import { ref, computed, Ref } from 'vue';
import { LAYOUT_CONFIG } from './useScenarioLayout';

// Tipos locales (importar de tipos globales si existen)
interface Edge {
    source: number;
    target: number;
    animOpacity?: number;
}

interface Node {
    id: number;
    x?: number;
    y?: number;
}

export function useScenarioEdges() {
    // Estado: modo de renderizado de aristas (0=offset pequeño, 1=gap grande, 2=curve, 3=spread)
    const childEdgeMode = ref<0 | 1 | 2 | 3>(2); // Default: curva (2)
    
    // Referencias a datos necesarios (inyectadas desde Index.vue)
    const childEdges: Ref<Edge[]> = ref([]);
    const grandChildEdges: Ref<Edge[]> = ref([]);
    const nodes: Ref<Node[]> = ref([]);
    const childNodes: Ref<Node[]> = ref([]);
    const grandChildNodes: Ref<Node[]> = ref([]);

    // Inyectar referencias de estado externo (desde Index.vue o composables de estado)
    // Llamar en el setup() de Index.vue antes de usar funciones de renderizado
    function injectState(
        injectedChildEdges: Ref<Edge[]>,
        injectedGrandChildEdges: Ref<Edge[]>,
        injectedNodes: Ref<Node[]>,
        injectedChildNodes: Ref<Node[]>,
        injectedGrandChildNodes: Ref<Node[]>,
    ) {
        childEdges.value = injectedChildEdges;
        grandChildEdges.value = injectedGrandChildEdges;
        nodes.value = injectedNodes;
        childNodes.value = injectedChildNodes;
        grandChildNodes.value = injectedGrandChildNodes;
    }

    /**
     * Encuentra un nodo por ID entre todos los nodos renderizados
     * (capabilities, competencies, skills)
     */
    function renderedNodeById(id: number): Node | undefined {
        if (!id) return undefined;
        // Primero intenta en nodos principales (capabilities, nivel 0)
        if (nodes.value && Array.isArray(nodes.value)) {
            const n = nodes.value.find((n: Node) => n.id === id);
            if (n) return n;
        }
        // Luego en nodos hijo (competencies, nivel 1)
        if (childNodes.value && Array.isArray(childNodes.value)) {
            const cn = childNodes.value.find((cn: Node) => cn.id === id);
            if (cn) return cn;
        }
        // Finalmente en nodos nieto (skills, nivel 2)
        if (grandChildNodes.value && Array.isArray(grandChildNodes.value)) {
            const gn = grandChildNodes.value.find((gn: Node) => gn.id === id);
            if (gn) return gn;
        }
        return undefined;
    }

    /**
     * Verifica si un ID pertenece a un nodo nieto (skill, id negativo)
     */
    function isGrandChildNode(id: number): boolean {
        if (!id || id >= 0) return false;
        return !!grandChildNodes.value?.find((gn: Node) => gn.id === id);
    }

    /**
     * Devuelve coordenadas ajustadas para el extremo de una arista.
     * Si forTarget es true y el target es un child node, devolvemos y ligeramente por encima
     * del centro del nodo para que la línea no quede oculta por el círculo del nodo.
     */
    function edgeEndpoint(e: Edge, forTarget = true): { x?: number; y?: number } {
        try {
            const id = forTarget ? e.target : e.source;
            const n = renderedNodeById(id);
            if (!n) return { x: undefined, y: undefined };
            const x = n.x;
            let y = n.y;

            // Si es el target y corresponde a un child node (id negativo), ajustar para evitar solapamiento
            if (forTarget && id < 0) {
                // Distinguimos entre nodo child (competency) y grandChild (skill)
                const isGrand = isGrandChildNode(id);
                const childRadius = isGrand ? 14 : 24; // skills son más pequeños
                const extraGap = isGrand ? 4 : 6; // separación visual adicional menor para skills
                y = (y ?? 0) - (childRadius + extraGap); // dejar un gap suficiente para que la línea no quede oculta
            }
            return { x, y };
        } catch (err: unknown) {
            void err;
        }
        return { x: undefined, y: undefined };
    }

    /**
     * Devuelve el índice dentro de un grupo de aristas que comparten mismo source y target aproximado.
     * Sirve para desplazar aristas paralelas en modo 'spread'.
     */
    function groupedIndexForEdge(e: Edge): number {
        try {
            const tgt = renderedNodeById(e.target);
            if (!tgt) return 0;
            const candidates = childEdges.value?.filter((ed: Edge) => {
                const rt = renderedNodeById(ed.target);
                return ed.source === e.source && rt && Math.abs((rt.x ?? 0) - (tgt.x ?? 0)) <= 8;
            }) || [];
            candidates.sort((a: Edge, b: Edge) => (a.target - b.target));
            return Math.max(0, candidates.findIndex((c: Edge) => c === e));
        } catch (err: unknown) {
            void err;
        }
        return 0;
    }

    /**
     * Helper para leer opacidad animada opcional desde objetos arista.
     * Algunas aristas cargan propiedades solo en runtime.
     */
    function edgeAnimOpacity(e: Edge): number {
        return ((e as any).animOpacity ?? 0.98) as number;
    }

    /**
     * Construye los puntos o path para una arista según el modo seleccionado.
     * Modos:
     * - 0: offset pequeño (por defecto)
     * - 1: gap grande (aumentar separación vertical)
     * - 2: curve (Bezier cúbica con profundidad configurable)
     * - 3: spread (desplazar en X según índice de grupo)
     */
    function edgeRenderFor(e: Edge): { isPath?: boolean; d?: string; x1?: number; y1?: number; x2?: number; y2?: number } {
        try {
            const start = edgeEndpoint(e, false);
            const end = edgeEndpoint(e, true);
            const x1 = start.x;
            const y1 = start.y;
            const x2 = end.x;
            const y2 = end.y;
            const mode = childEdgeMode.value;

            // Detectar si la arista apunta a un grandChild (skill) para parámetros específicos
            const isGrand =
                isGrandChildNode(e.target) ||
                isGrandChildNode(e.source) ||
                (grandChildEdges.value && grandChildEdges.value.includes(e as any));

            // Modo curva
            if (mode === 2 && typeof x1 === 'number' && typeof x2 === 'number') {
                // Control point adaptativo para curvas más pronunciadas, configurable via LAYOUT_CONFIG
                const baseDepth = isGrand ? LAYOUT_CONFIG.skill.edge.baseDepth : LAYOUT_CONFIG.competency.edge.baseDepth;
                const curveFactor = isGrand ? LAYOUT_CONFIG.skill.edge.curveFactor : LAYOUT_CONFIG.competency.edge.curveFactor;
                const distance = Math.abs((y2 ?? 0) - (y1 ?? 0));
                const depth = Math.max(baseDepth, Math.round(distance * curveFactor) + baseDepth);
                const cpY = Math.min((y1 ?? 0), (y2 ?? 0)) + depth;
                const d = `M ${x1} ${y1} C ${x1} ${cpY} ${x2} ${cpY} ${x2} ${y2}`;
                return { isPath: true, d };
            }

            // Modo spread: desplazar X del target según índice en grupo
            if (mode === 3 && typeof x1 === 'number' && typeof x2 === 'number') {
                const idx = groupedIndexForEdge(e);
                // Usar candidatos de childEdges y grandChildEdges para mantener espaciado consistente
                const candidates = (childEdges.value || [])
                    .concat(grandChildEdges.value || [])
                    .filter((ed: Edge) => {
                        const rt = renderedNodeById(ed.target);
                        const r = renderedNodeById(e.target);
                        return (
                            ed.source === e.source &&
                            rt &&
                            r &&
                            Math.abs((rt.x ?? 0) - (r.x ?? 0)) <= 8
                        );
                    });
                const centerOffset =
                    ((idx - (candidates.length - 1) / 2) *
                        (isGrand ? LAYOUT_CONFIG.skill.edge.spreadOffset : LAYOUT_CONFIG.competency.edge.spreadOffset));
                return { isPath: false, x1, y1, x2: (x2 ?? 0) + centerOffset, y2 };
            }

            // Modo gap grande: aumentar el desplazamiento vertical del target
            if (mode === 1 && typeof x1 === 'number' && typeof x2 === 'number') {
                // Reducir ajuste de gap para nodos skill (radio más pequeño)
                const childRadius = isGrand ? 14 : 20;
                const y2adj = (y2 ?? 0) - (childRadius - 2);
                return { isPath: false, x1, y1, x2, y2: y2adj };
            }

            // Modo por defecto: offset pequeño
            return { isPath: false, x1, y1, x2, y2 };
        } catch (err: unknown) {
            void err;
            // En caso de error devolver forma segura que indique geometría no válida
            return { isPath: false, x1: undefined, y1: undefined, x2: undefined, y2: undefined };
        }
    }

    /**
     * Devuelve un path curvo para aristas scenario->capability.
     * Configurable por LAYOUT_CONFIG.capability.scenarioEdgeDepth.
     */
    function scenarioEdgePath(e: Edge): string {
        try {
            const s = renderedNodeById(e.source);
            const t = renderedNodeById(e.target);
            if (!s || !t || typeof s.x !== 'number' || typeof t.x !== 'number') return '';
            const depth = LAYOUT_CONFIG.capability.scenarioEdgeDepth;
            const cpY = Math.min((s.y ?? 0), (t.y ?? 0)) + depth;
            return `M ${s.x} ${s.y} C ${s.x} ${cpY} ${t.x} ${cpY} ${t.x} ${t.y}`;
        } catch (err: unknown) {
            void err;
        }
        return '';
    }

    return {
        childEdgeMode,
        // Métodos
        injectState,
        renderedNodeById,
        isGrandChildNode,
        edgeEndpoint,
        groupedIndexForEdge,
        edgeAnimOpacity,
        edgeRenderFor,
        scenarioEdgePath,
    };
}
