import { computeMatrixPositions } from './useNodeNavigation';

export interface MatrixVariant {
    rows: number;
    cols: number;
    min?: number;
    max?: number;
}

export interface SidesOptions {
    hSpacing?: number;
    vSpacing?: number;
    parentOffset?: number;
    selectedOffsetMultiplier?: number; // <1 moves selected closer to parent
}

export function chooseMatrixVariant(
    total: number,
    matrixVariants: MatrixVariant[] = [
        { min: 4, max: 8, rows: 2, cols: 4 },
        { min: 9, max: 10, rows: 2, cols: 5 },
    ],
    maxDisplay = 10,
) {
    const limit = Math.min(total, maxDisplay);
    for (const v of matrixVariants) {
        if ((v.min ?? 0) <= total && total <= (v.max ?? Infinity)) {
            return { rows: v.rows, cols: v.cols, limit };
        }
    }
    // default fallback
    return {
        rows: 2,
        cols: Math.min(5, Math.max(1, Math.ceil(total / 2))),
        limit,
    };
}

// Compute matrix positions using existing helper
export function computeCompetencyMatrixPositions(
    total: number,
    cx: number,
    topY: number,
    rows: number,
    cols: number,
    hSpacing = 120,
    vSpacing = 100,
) {
    return computeMatrixPositions(total, cx, topY, {
        rows,
        cols,
        hSpacing,
        vSpacing,
    });
}

// Compute simple left/right sides layout. Returns array of { x, y } positions.
// Helper to build Y positions centered around topY
function buildYs(n: number, topY: number, vSpacing: number): number[] {
    const ys: number[] = [];
    if (n <= 0) return ys;
    if (n === 1) {
        ys.push(topY);
        return ys;
    }
    const totalHeight = (n - 1) * vSpacing;
    const startY = Math.round(topY - totalHeight / 2);
    for (let i = 0; i < n; i++) ys.push(startY + i * vSpacing);
    return ys;
}

function computePositionsWithSelection(
    count: number,
    cx: number,
    parentY: number,
    selectedIndex: number,
    opts: {
        hSpacing: number;
        vSpacing: number;
        parentOffset: number;
        selectedOffsetMultiplier: number;
        topY: number;
    },
) {
    const { hSpacing, vSpacing, parentOffset, selectedOffsetMultiplier, topY } =
        opts;
    const leftX = Math.round(cx - hSpacing);
    const rightX = Math.round(cx + hSpacing);
    const positions: Array<{ x: number; y: number }> = [];

    const otherCount = count - 1;
    const leftCountSel = Math.floor(otherCount / 2);
    const rightCountSel = otherCount - leftCountSel;

    const leftYsSel = buildYs(leftCountSel, topY, vSpacing).reverse();
    const rightYsSel = buildYs(rightCountSel, topY, vSpacing);

    // Use the configured offset multiplier to bring selected node closer to parent
    const selY = parentY + Math.round(parentOffset * selectedOffsetMultiplier);

    let lsi = 0;
    let rsi = 0;

    for (let i = 0; i < count; i++) {
        if (i === selectedIndex) {
            positions.push({ x: Math.round(cx), y: selY });
        } else if (lsi < leftYsSel.length) {
            positions.push({ x: leftX, y: leftYsSel[lsi++] });
        } else {
            positions.push({ x: rightX, y: rightYsSel[rsi++] ?? topY });
        }
    }
    return positions;
}

function computePositionsDefault(
    count: number,
    cx: number,
    topY: number,
    opts: { hSpacing: number; vSpacing: number },
) {
    const { hSpacing, vSpacing } = opts;
    const leftX = Math.round(cx - hSpacing);
    const rightX = Math.round(cx + hSpacing);
    const positions: Array<{ x: number; y: number }> = [];

    const leftCount = Math.floor(count / 2);
    const rightCount = count - leftCount;

    const leftYs = buildYs(leftCount, topY, vSpacing).reverse();
    const rightYs = buildYs(rightCount, topY, vSpacing);

    for (let i = 0; i < count; i++) {
        if (i < leftCount) {
            positions.push({ x: leftX, y: leftYs[i] ?? topY });
        } else {
            const nri = i - leftCount;
            positions.push({ x: rightX, y: rightYs[nri] ?? topY });
        }
    }
    return positions;
}

// Compute simple left/right sides layout. Returns array of { x, y } positions.
export function computeSidesPositions(
    count: number,
    cx: number,
    parentY: number,
    opts: SidesOptions = {},
    selectedIndex: number | null = null,
) {
    const hSpacing = opts.hSpacing ?? 140;
    const vSpacing = opts.vSpacing ?? 90;
    const parentOffset = opts.parentOffset ?? 80;
    const selectedOffsetMultiplier = opts.selectedOffsetMultiplier ?? 0.75;

    if (count === 0) return [];

    const topY = parentY + parentOffset;

    if (selectedIndex != null && selectedIndex >= 0 && selectedIndex < count) {
        return computePositionsWithSelection(
            count,
            cx,
            parentY,
            selectedIndex,
            {
                hSpacing,
                vSpacing,
                parentOffset,
                selectedOffsetMultiplier,
                topY,
            },
        );
    }

    return computePositionsDefault(count, cx, topY, { hSpacing, vSpacing });
}

export default {};

export type LayoutType = 'auto' | 'radial' | 'matrix' | 'sides';

// Decide layout helper: centraliza la heurística que antes estaba en `Index.vue`.
export function decideCompetencyLayout(
    layoutOpt: LayoutType | undefined,
    hasSelectedChild: boolean,
    childCount: number,
    configDefault: LayoutType = 'auto',
) {
    // explicit override
    if (layoutOpt && layoutOpt !== 'auto') return layoutOpt;
    const def = configDefault ?? 'auto';
    if (def !== 'auto') return def as any;

    // Heurística actualizada: cuando hay una child seleccionada y varios hijos, preferir 'sides'
    // (reemplaza la elección previa 'radial' por 'sides' para mejores transiciones)
    if (hasSelectedChild && childCount > 3) return 'sides';
    // por defecto usar matriz
    return 'matrix';
}
