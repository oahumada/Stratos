import { describe, it, expect } from 'vitest';
import { chooseMatrixVariant, computeSidesPositions } from '../useCompetencyLayout';

describe('useCompetencyLayout - chooseMatrixVariant', () => {
  it('selects 4x2 variant for totals between 4 and 8', () => {
    for (const t of [4, 5, 6, 7, 8]) {
      const v = chooseMatrixVariant(t, [{ min: 4, max: 8, rows: 2, cols: 4 }, { min: 9, max: 10, rows: 2, cols: 5 }], 10);
      expect(v.rows).toBe(2);
      expect(v.cols).toBe(4);
      expect(v.limit).toBe(Math.min(t, 10));
    }
  });

  it('selects 5x2 variant for totals 9..10', () => {
    for (const t of [9, 10]) {
      const v = chooseMatrixVariant(t, [{ min: 4, max: 8, rows: 2, cols: 4 }, { min: 9, max: 10, rows: 2, cols: 5 }], 10);
      expect(v.rows).toBe(2);
      expect(v.cols).toBe(5);
      expect(v.limit).toBe(Math.min(t, 10));
    }
  });
});

describe('useCompetencyLayout - computeSidesPositions', () => {
  it('returns positions and moves selected closer to parent', () => {
    const count = 5;
    const cx = 500;
    const parentY = 100;
    const opts = { hSpacing: 120, vSpacing: 50, parentOffset: 60, selectedOffsetMultiplier: 0.75 };
    const positions = computeSidesPositions(count, cx, parentY, opts, 2);

    expect(positions.length).toBe(5);

    const topY = parentY + opts.parentOffset;
    // compute distances of each y to parent topY
    const dists = positions.map((p) => Math.abs(p.y - topY));

    // selected index (2) must have smaller distance than at least one other (moved closer)
    expect(dists[2]).toBeLessThanOrEqual(Math.max(...dists));
    // selected must be less than or equal to a non-selected neighbor
    expect(dists[2]).toBeLessThanOrEqual(dists[0] + 1);
  });
});
