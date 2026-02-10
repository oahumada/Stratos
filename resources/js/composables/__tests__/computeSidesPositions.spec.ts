import { describe, it, expect } from 'vitest';
import { computeSidesPositions } from '@/composables/useCompetencyLayout';

describe('computeSidesPositions', () => {
  it('returns correct number of positions and selected centered when selectedIndex provided', () => {
    const cx = 400;
    const parentY = 100;
    const positions = computeSidesPositions(6, cx, parentY, { hSpacing: 180, vSpacing: 60, parentOffset: 80 }, 2);
    expect(positions.length).toBe(6);

    // selected index (2) should be at approximately center x
    const sel = positions[2];
    expect(Math.abs(sel.x - cx)).toBeLessThanOrEqual(2);
    // positions should be finite numbers
    positions.forEach((p) => {
      expect(Number.isFinite(p.x)).toBeTruthy();
      expect(Number.isFinite(p.y)).toBeTruthy();
    });
  });

  it('preserves order and splits left/right when no selected', () => {
    const cx = 500;
    const parentY = 200;
    const positions = computeSidesPositions(5, cx, parentY, { hSpacing: 160, vSpacing: 50, parentOffset: 70 });
    expect(positions.length).toBe(5);
    // first half should have x < cx and latter half x >= cx
    const left = positions.slice(0, Math.floor(5 / 2));
    const right = positions.slice(Math.floor(5 / 2));
    left.forEach((p) => expect(p.x).toBeLessThan(cx));
    right.forEach((p) => expect(p.x).toBeGreaterThanOrEqual(cx));
  });

  it('avoids NaN for count=1', () => {
    const p = computeSidesPositions(1, 300, 120);
    expect(p.length).toBe(1);
    expect(Number.isFinite(p[0].x)).toBeTruthy();
    expect(Number.isFinite(p[0].y)).toBeTruthy();
  });
});
