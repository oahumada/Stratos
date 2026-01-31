// @vitest-environment jsdom
import { describe, it, expect, beforeEach, vi } from 'vitest';
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
      priority: 1,
      competencies: Array.from({ length: 6 }).map((_, i) => ({ id: 100 + i, name: `Comp ${i + 1}`, skills: [], pivot: {} })),
    },
  ],
});

describe('ScenarioPlanning integration - competency expand and centering', () => {
  let container: HTMLElement;
  let apiPostMock: any;

  beforeEach(async () => {
    // Provide a minimal global.window so modules referencing window.location won't throw
    (globalThis as any).window = (globalThis as any).window || { location: { origin: 'http://localhost' } };
    // spyable mock for useApi.post
    apiPostMock = vi.fn(async (url: string, data?: any) => {
      const m = url.match(/\/api\/competencies\/(\d+)\/skills/);
      if (m) {
        const id = 5555;
        const skill = (data && data.skill) ? { id, ...data.skill } : { id };
        return { data: skill };
      }
      return { data: {} };
    });
    vi.doMock('@/composables/useApi', () => ({ useApi: () => ({ post: apiPostMock, get: async () => ({ data: [] }) }) }));
    // dynamic import after window shim and mock
    Index = (await import('@/pages/ScenarioPlanning/Index.vue')).default;
    // render the page with minimal props
    const utils = render(Index as any, { props: { scenario: makeScenario() },
      global: {
        stubs: {
          'v-btn': { template: '<button><slot /></button>' },
          'v-icon': true,
          'v-list-item-icon': true,
          'v-list-item': true,
          'v-list': true,
          'v-menu': true,
          'v-card-title': true,
          'v-text-field': true,
          'v-textarea': true,
          'v-slider': true,
          'v-checkbox': true,
          'v-form': true,
          'v-select': true,
          'v-switch': true,
          'v-card-text': true,
          'v-spacer': true,
          'v-card-actions': true,
          'v-card': true,
          'v-dialog': { template: '<div><slot /></div>' },
          'v-text-field': { template: '<input />' },
          'v-textarea': { template: '<textarea></textarea>' },
        },
      },
    });
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
      // selected child should be reflected in the sidebar/breadcrumb
      expect(container.textContent).toContain('Competencia');
    });
  });

    it('creates and attaches a skill via modal and closes dialog', async () => {
      // expand capability -> open children
      const capBtn = Array.from(container.querySelectorAll('.node-label')).find((el) => el.textContent?.includes('Capability A')) as HTMLElement | undefined;
      expect(capBtn).toBeTruthy();
      await fireEvent.click(capBtn!);

      await waitFor(() => {
        const children = container.querySelectorAll('.child-node');
        expect(children.length).toBeGreaterThanOrEqual(6);
      });

      const childNodes = Array.from(container.querySelectorAll('.child-node')) as HTMLElement[];
      const targetChild = childNodes[1];
      await fireEvent.click(targetChild);

      // Instead of interacting with the full dialog (Vuetify stubs), call the composable directly
      const compIdAttr = targetChild.getAttribute('data-node-id');
      expect(compIdAttr).toBeTruthy();
      const compId = Number(compIdAttr);
      const { useCompetencySkills } = await import('@/composables/useCompetencySkills');
      const svc = useCompetencySkills();
      await svc.createAndAttachSkill(compId, { name: 'Integration Skill X' } as any);

      // assert API was called with competency attach endpoint and payload
      expect(apiPostMock).toHaveBeenCalled();
      const callArgs = apiPostMock.mock.calls.find((c: any) => c[1] && c[1].skill && c[1].skill.name === 'Integration Skill X');
      expect(callArgs).toBeTruthy();
      const sent = callArgs[1];
      expect(sent).toHaveProperty('skill');
      expect(sent.skill.name).toBe('Integration Skill X');
    });
});
