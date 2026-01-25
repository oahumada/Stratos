import { describe, it, expect } from 'vitest';
import { reorderNodesHelper } from '../reorderNodesHelper';

describe('reorderNodesHelper', () => {
  it('for 10 nodes arranges them in 2 rows x 5 cols (desired behavior)', () => {
    const nodes = Array.from({ length: 10 }, (_, i) => ({ id: i + 1 }));
    const width = 1200;
    const height = 800;
    const arranged = reorderNodesHelper(nodes, width, height);

    expect(arranged.length).toBe(10);

    // group by rounded Y position
    const rowsMap = new Map<number, any[]>();
    arranged.forEach((n) => {
      const y = Math.round(n.y ?? 0);
      const key = y;
      const arr = rowsMap.get(key) || [];
      arr.push(n);
      rowsMap.set(key, arr);
    });

    const rowYs = Array.from(rowsMap.keys()).sort((a, b) => a - b);
    // Expect exactly 2 rows
    expect(rowYs.length).toBe(2);

    // Expect 5 nodes per row
    const counts = rowYs.map((y) => rowsMap.get(y).length);
    expect(counts).toEqual([5, 5]);
  });

  it('ignores scenario node if accidentally included and still produces 2x5 for 10 real nodes', () => {
    const real = Array.from({ length: 10 }, (_, i) => ({ id: i + 1 }));
    const scenario = { id: 999, x: 0, y: 0 };
    // include scenario node inside the list (simulate bug) at position 0
    const nodesWithScenario = [{ id: 999 }, ...real];
    const arranged = reorderNodesHelper(nodesWithScenario as any, 1200, 800, scenario as any);
    // last element should be the scenario reinserted
    expect(arranged.length).toBe(11);
    const last = arranged[arranged.length - 1];
    expect(last.id).toBe(999);
    // first 10 should be layouted into 2 rows x 5 cols
    const firstTen = arranged.slice(0, 10);
    const rowsMap = new Map<number, any[]>();
    firstTen.forEach((n) => {
      const y = Math.round(n.y ?? 0);
      const arr = rowsMap.get(y) || [];
      arr.push(n);
      rowsMap.set(y, arr);
    });
    const rowYs = Array.from(rowsMap.keys()).sort((a, b) => a - b);
    expect(rowYs.length).toBe(2);
    const counts = rowYs.map((y) => rowsMap.get(y).length);
    expect(counts).toEqual([5, 5]);
  });
});
