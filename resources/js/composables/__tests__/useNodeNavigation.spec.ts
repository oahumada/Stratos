import { describe, it, expect } from 'vitest';
import { computeMatrixPositions } from '../useNodeNavigation';

describe('computeMatrixPositions', () => {
  it('returns at most 10 positions for total > 10 and arranges in 2x5 grid', () => {
    const total = 14;
    const cx = 500;
    const topY = 200;
    const positions = computeMatrixPositions(total, cx, topY, { rows: 2, cols: 5, hSpacing: 120, vSpacing: 100 });
    // should be limited to 10
    expect(positions.length).toBe(10);

    // Check that positions are arranged in two rows
    const rowYs = Array.from(new Set(positions.map((p) => p.y))).sort((a, b) => a - b);
    expect(rowYs.length).toBe(2);
    expect(rowYs[0]).toBe(topY);
    expect(rowYs[1]).toBe(topY + 100);

    // Check x centering: first and last x should be symmetric around cx
    const xs = positions.map((p) => p.x).sort((a, b) => a - b);
    const leftMost = xs[0];
    const rightMost = xs[xs.length - 1];
    const distLeft = Math.abs(cx - leftMost);
    const distRight = Math.abs(rightMost - cx);
    // distances should be approximately equal (allow some tolerance)
    expect(Math.abs(distLeft - distRight)).toBeLessThanOrEqual(10);
  });

  it('returns correct layout for total <= cols*rows', () => {
    const total = 7; // will occupy 2 rows x up to 5 cols -> 7 positions
    const cx = 400;
    const topY = 100;
    const positions = computeMatrixPositions(total, cx, topY, { rows: 2, cols: 5, hSpacing: 100, vSpacing: 80 });
    expect(positions.length).toBe(7);
    // first row count should be 5, second row 2
    const firstRow = positions.filter((p, i) => i < 5);
    const secondRow = positions.filter((p, i) => i >= 5);
    expect(firstRow.length).toBe(5);
    expect(secondRow.length).toBe(2);
    // y positions check
    expect(positions[0].y).toBe(topY);
    expect(positions[5].y).toBe(topY + 80);
  });

  it('for total=10 returns exactly 2 rows x 5 cols and fills left-to-right by row', () => {
    const total = 10;
    const cx = 600;
    const topY = 50;
    const positions = computeMatrixPositions(total, cx, topY, { rows: 2, cols: 5, hSpacing: 90, vSpacing: 70 });
    expect(positions.length).toBe(10);

    // Expect two distinct Y values (two rows)
    const rowYs = Array.from(new Set(positions.map((p) => p.y))).sort((a, b) => a - b);
    expect(rowYs.length).toBe(2);
    expect(rowYs[0]).toBe(topY);
    expect(rowYs[1]).toBe(topY + 70);

    // Check that within each row, x values are strictly increasing (left->right)
    const firstRowXs = positions.slice(0, 5).map((p) => p.x);
    const secondRowXs = positions.slice(5, 10).map((p) => p.x);
    for (let i = 1; i < firstRowXs.length; i++) expect(firstRowXs[i]).toBeGreaterThan(firstRowXs[i - 1]);
    for (let i = 1; i < secondRowXs.length; i++) expect(secondRowXs[i]).toBeGreaterThan(secondRowXs[i - 1]);
  });
});
