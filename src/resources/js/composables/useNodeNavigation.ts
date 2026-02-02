export interface MatrixOptions {
  rows?: number;
  cols?: number;
  hSpacing?: number;
  vSpacing?: number;
}

/**
 * Compute positions for items arranged in a fixed matrix (rows x cols) centered on cx
 * and starting at topY (the top of the matrix block). Returns array of { x, y }
 */
export function computeMatrixPositions(total: number, cx: number, topY: number, opts: MatrixOptions = {}) {
  const rows = opts.rows ?? 2;
  const cols = opts.cols ?? 5;
  const hSpacing = opts.hSpacing ?? 120;
  const vSpacing = opts.vSpacing ?? 100;

  const limit = Math.min(total, rows * cols);
  const positions: Array<{ x: number; y: number }> = [];
  // Fill by rows: row 0 left->right, row 1 left->right, etc.
  for (let i = 0; i < limit; i++) {
    const row = Math.floor(i / cols);
    const indexInRow = i % cols;
    // compute how many items are in this row (may be less than cols for the last row)
    const remaining = limit - row * cols;
    const itemsInRow = Math.min(remaining, cols);
    // center the occupied columns for this row
    const rowBlockWidth = itemsInRow > 1 ? (itemsInRow - 1) * hSpacing : 0;
    const x = Math.round(cx - rowBlockWidth / 2 + indexInRow * hSpacing);
    const y = Math.round(topY + row * vSpacing);
    positions.push({ x, y });
  }
  return positions;
}

export default {};
