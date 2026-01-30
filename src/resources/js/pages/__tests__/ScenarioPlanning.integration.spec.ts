// @vitest-environment jsdom
import { describe, it, expect, beforeEach } from 'vitest';
import { render, fireEvent, waitFor } from '@testing-library/vue';
// Delay importing Index.vue until we set up a fake `window` to satisfy modules
let Index: any;

// Provide a lightweight fixture: one capability with several competencies
const makeScenario = () => ({
  id: 1,
  name: 'Fixture Scenario',
  capabilities: [
    {
      id: 10,
      name: 'Capability A',
      x: 400,
      y: 120,
      competencies: Array.from({ length: 6 }).map((_, i) => ({ id: 100 + i, name: `Comp ${i + 1}`, skills: [] })),
    },
  ],
});

describe('ScenarioPlanning integration - competency expand and centering', () => {
  let container: HTMLElement;

  beforeEach(async () => {
    // Provide a minimal global.window so modules referencing window.location won't throw
    (globalThis as any).window = (globalThis as any).window || { location: { origin: 'http://localhost' } };
    // dynamic import after window shim
    Index = (await import('@/pages/ScenarioPlanning/Index.vue')).default;
    // render the page with minimal props
    const utils = render(Index as any, { props: { scenario: makeScenario() } });
    container = utils.container;
  });

  it('opens competency children and centers selected child when clicked', async () => {
    // find the capability node by label text
    const capBtn = Array.from(container.querySelectorAll('.node-label')).find((el) => el.textContent?.includes('Capability A')) as HTMLElement | undefined;
    expect(capBtn).toBeTruthy();

    // click the capability to expand competencies
    await fireEvent.click(capBtn!);

    // wait for child nodes to be rendered
    await waitFor(() => {
      const children = container.querySelectorAll('.child-node');
      expect(children.length).toBeGreaterThanOrEqual(6);
    });

    // pick a child node and click it to select
    const childNodes = Array.from(container.querySelectorAll('.child-node')) as HTMLElement[];
    expect(childNodes.length).toBeGreaterThanOrEqual(6);
    const secondChild = childNodes[2];
    await fireEvent.click(secondChild);

    // after selecting, wait for reflow/animation to apply
    await waitFor(() => {
      // selected child should have 'selected' class or be roughly centered
      const sel = container.querySelector('.child-node.selected') || secondChild;
      // check approximate horizontal centering against svg/root width (tolerance 24px)
      const root = container.querySelector('.map-root') as HTMLElement | null;
      const rootRect = root ? root.getBoundingClientRect() : { width: 800, left: 0 };
      const selRect = sel!.getBoundingClientRect();
      const selCenter = selRect.left + selRect.width / 2 - (rootRect.left || 0);
      const centerX = (rootRect.width || 800) / 2;
      expect(Math.abs(selCenter - centerX)).toBeLessThanOrEqual(36);
    });
  });
});
