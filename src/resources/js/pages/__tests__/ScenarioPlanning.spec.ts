import { describe, it, expect, beforeEach } from 'vitest';
import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning Index (smoke)', () => {
  let wrapper: any;

  beforeEach(() => {
    wrapper = shallowMount(ScenarioPlanning, {
      props: {
        scenario: { id: 1, name: 'S1', capabilities: [] },
        visualConfig: { nodeRadius: 20 },
      },
      // shallowMount to avoid heavy DOM from d3 rendering; focus on component lifecycle
    });
  });

  it('mounts and exposes expected props', () => {
    expect(wrapper.exists()).toBe(true);
    expect(wrapper.props().scenario).toBeTruthy();
    expect(wrapper.props().visualConfig.nodeRadius).toBe(20);
  });

  // TODO: Add focused unit tests that exercise `expandCompetencies` behaviour.
  // - Simulate clicks on capability nodes (update when selectors are finalized)
  // - Validate that `visualConfig` overrides (e.g., maxDisplay) affect layout selection
});
