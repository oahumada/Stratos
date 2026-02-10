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

export function chooseMatrixVariant(total: number, matrixVariants: MatrixVariant[] = [{ min: 4, max: 8, rows: 2, cols: 4 }, { min: 9, max: 10, rows: 2, cols: 5 }], maxDisplay = 10) {
  const limit = Math.min(total, maxDisplay);
  for (const v of matrixVariants) {
    if ((v.min ?? 0) <= total && total <= (v.max ?? Infinity)) {
      return { rows: v.rows, cols: v.cols, limit };
    }
  }
  // default fallback
  return { rows: 2, cols: Math.min(5, Math.max(1, Math.ceil(total / 2))), limit };
}

// Compute matrix positions using existing helper
export function computeCompetencyMatrixPositions(total: number, cx: number, topY: number, rows: number, cols: number, hSpacing = 120, vSpacing = 100) {
  return computeMatrixPositions(total, cx, topY, { rows, cols, hSpacing, vSpacing });
}

// Compute simple left/right sides layout. Returns array of { x, y } positions.
export function computeSidesPositions(count: number, cx: number, parentY: number, opts: SidesOptions = {}, selectedIndex: number | null = null) {
  const hSpacing = opts.hSpacing ?? 140;
  const vSpacing = opts.vSpacing ?? 90;
  const parentOffset = opts.parentOffset ?? 80;
  const selectedOffsetMultiplier = opts.selectedOffsetMultiplier ?? 0.75;

  if (count === 0) return [];

  const topY = parentY + parentOffset;

  // split roughly half left/right
  const leftCount = Math.floor(count / 2);
  const rightCount = count - leftCount;

  // build y positions for each side centered around topY
  function buildYs(n: number) {
    const ys: number[] = [];
    if (n === 1) {
      ys.push(topY);
      return ys;
    }
    const totalHeight = (n - 1) * vSpacing;
    const startY = Math.round(topY - totalHeight / 2);
    for (let i = 0; i < n; i++) ys.push(startY + i * vSpacing);
    return ys;
  }

  const leftYs = buildYs(leftCount).reverse(); // reverse so nearest to parent appear first on left
  const rightYs = buildYs(rightCount);

  const positions: Array<{ x: number; y: number }> = [];

  // left side x is cx - hSpacing, right side cx + hSpacing
  const leftX = Math.round(cx - hSpacing);
  const rightX = Math.round(cx + hSpacing);

  // Build positions while preserving original index mapping.
  // If a selectedIndex is provided, reserve the center (cx) position for it
  if (selectedIndex != null && selectedIndex >= 0 && selectedIndex < count) {
    const others = Array.from({ length: count }, (_, i) => i).filter((i) => i !== selectedIndex);
    const leftCountSel = Math.floor(others.length / 2);
    const rightCountSel = others.length - leftCountSel;
    const leftYsSel = buildYs(leftCountSel).reverse();
    const rightYsSel = buildYs(rightCountSel);

    const lsi = 0;
    const rsi = 0;
    // Precompute others' positions so we can derive a sensible selected offset
    const othersPos: Array<{ x: number; y: number }> = [];
    let tmpL = 0;
    let tmpR = 0;
    for (let i = 0; i < count; i++) {
      if (i === selectedIndex) continue;
      if (tmpL < leftYsSel.length) {
        othersPos.push({ x: leftX, y: leftYsSel[tmpL++] });
      } else {
        othersPos.push({ x: rightX, y: rightYsSel[tmpR++] ?? topY });
      }
    }

    // distance to topY for others
    const otherDists = othersPos.map((p) => Math.abs(p.y - topY));
    const minOtherDist = otherDists.length ? Math.min(...otherDists) : Math.round(vSpacing);
    // Use a much smaller offset to bring selected node closer to parent
    const selY = parentY + Math.round(parentOffset * 0.8);

    // now build final positions preserving indices
    let lsi2 = 0;
    let rsi2 = 0;
    for (let i = 0; i < count; i++) {
      if (i === selectedIndex) {
        positions.push({ x: Math.round(cx), y: selY });
      } else if (lsi2 < leftYsSel.length) {
        positions.push({ x: leftX, y: leftYsSel[lsi2++] });
      } else {
        positions.push({ x: rightX, y: rightYsSel[rsi2++] ?? topY });
      }
    }
  } else {
    // No selected index: assign first half to left, remainder to right, preserving input order
    for (let i = 0; i < count; i++) {
      if (i < leftCount) {
        positions.push({ x: leftX, y: leftYs[i] ?? topY });
      } else {
        const nri = i - leftCount;
        positions.push({ x: rightX, y: rightYs[nri] ?? topY });
      }
    }
  }

  return positions;
}

export default {};

// Decide layout helper: centraliza la heurística que antes estaba en `Index.vue`.
export function decideCompetencyLayout(layoutOpt: 'auto' | 'radial' | 'matrix' | 'sides' | undefined, hasSelectedChild: boolean, childCount: number, configDefault = 'auto') {
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
