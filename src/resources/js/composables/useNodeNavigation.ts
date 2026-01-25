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
  const totalCols = cols;
  const blockWidth = (totalCols - 1) * hSpacing;
  for (let i = 0; i < limit; i++) {
    const row = Math.floor(i / totalCols);
    const col = i % totalCols;
    // center block on cx
    const x = Math.round(cx - blockWidth / 2 + col * hSpacing);
    const y = Math.round(topY + row * vSpacing);
    positions.push({ x, y });
  }
  return positions;
}

export default {};
