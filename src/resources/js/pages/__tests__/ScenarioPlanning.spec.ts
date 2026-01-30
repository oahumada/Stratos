import { describe, it, expect } from 'vitest';
import { computeMatrixPositions } from '@/composables/useNodeNavigation';

describe('computeMatrixPositions', () => {
  it('centers rows and respects spacing', () => {
    const total = 8;
    const cx = 400;
    const topY = 100;
    const hSpacing = 100;
    const vSpacing = 80;
    const rows = 2;
    const cols = 5;
    const positions = computeMatrixPositions(total, cx, topY, { rows, cols, hSpacing, vSpacing });

    // Expect number of positions equal to total (<= rows*cols)
    expect(positions.length).toBe(8);

    // Check Y values: should use topY and topY+vSpacing
    const ys = Array.from(new Set(positions.map((p) => p.y))).sort((a, b) => a - b);
    expect(ys.length).toBe(2);
    expect(ys[0]).toBe(topY);
    expect(ys[1]).toBe(topY + vSpacing);

    // In first row (5 items) x positions should be increasing left->right
    const firstRow = positions.slice(0, 5).map((p) => p.x);
    for (let i = 1; i < firstRow.length; i++) {
      expect(firstRow[i]).toBeGreaterThan(firstRow[i - 1]);
    }
  });

  it('truncates to rows*cols when total is larger', () => {
    const total = 14; // larger than 2x5
    const cx = 500;
    const topY = 200;
    const positions = computeMatrixPositions(total, cx, topY, { rows: 2, cols: 5, hSpacing: 120, vSpacing: 100 });
    expect(positions.length).toBe(10); // truncated to 2*5
  });
});

