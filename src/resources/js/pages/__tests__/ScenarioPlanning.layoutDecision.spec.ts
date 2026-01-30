import { describe, it, expect } from 'vitest';
import { decideCompetencyLayout } from '@/composables/useCompetencyLayout';

describe('decideCompetencyLayout', () => {
  it('returns sides when layout auto, hasSelectedChild and childCount > 5', () => {
    expect(decideCompetencyLayout('auto', true, 6)).toBe('sides');
  });

  it('returns sides when layout auto, hasSelectedChild and 4 <= childCount <= 5', () => {
    expect(decideCompetencyLayout('auto', true, 4)).toBe('sides');
    expect(decideCompetencyLayout('auto', true, 5)).toBe('sides');
  });

  it('returns matrix when no selected child', () => {
    expect(decideCompetencyLayout('auto', false, 6)).toBe('matrix');
  });

  it('respects explicit layout option', () => {
    expect(decideCompetencyLayout('matrix', true, 10)).toBe('matrix');
    expect(decideCompetencyLayout('radial', false, 2)).toBe('radial');
  });

  it('respects non-auto default config', () => {
    expect(decideCompetencyLayout(undefined, false, 6, 'radial')).toBe('radial');
  });
});
