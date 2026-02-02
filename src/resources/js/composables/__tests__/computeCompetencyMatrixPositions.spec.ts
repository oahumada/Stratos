import { describe, it, expect } from 'vitest';
import { computeCompetencyMatrixPositions } from '@/composables/useCompetencyLayout';

describe('computeCompetencyMatrixPositions', () => {
  it('returns grid positions for given count and respects cols parameter', () => {
    const count = 10;
    const cx = 600;
    const parentY = 80;
    const rows = 3;
    const cols = 4;
    const hSpacing = 140;
    const vSpacing = 84;
    const positions = computeCompetencyMatrixPositions(count, cx, parentY, rows, cols, hSpacing, vSpacing);
    expect(positions.length).toBe(count);

    // distinct x positions should not exceed cols
    const distinctX = Array.from(new Set(positions.map((p) => p.x)));
    expect(distinctX.length).toBeLessThanOrEqual(cols);

    // spacing between rows for same column should be approximately vSpacing
    for (let i = 0; i < positions.length; i++) {
      for (let j = i + 1; j < positions.length; j++) {
        const dx = Math.abs(positions[i].x - positions[j].x);
        const dy = Math.abs(positions[i].y - positions[j].y);
        if (dx === 0) {
          expect(dy).toBeGreaterThanOrEqual(vSpacing - 1);
        }
      }
    }
  });

  it('handles small counts (1) without errors', () => {
    const p = computeCompetencyMatrixPositions(1, 320, 140);
    expect(p.length).toBe(1);
    expect(Number.isFinite(p[0].x)).toBeTruthy();
    expect(Number.isFinite(p[0].y)).toBeTruthy();
  });
});
