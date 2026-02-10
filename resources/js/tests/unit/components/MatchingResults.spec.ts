import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
// TODO: Uncomment when MatchingResults component is created
// import MatchingResults from '@/components/Paso2/MatchingResults.vue';

global.fetch = vi.fn();

// SKIPPED: Component not yet implemented
describe.skip('MatchingResults.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders component with correct structure', () => {
    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('.matching-results').exists()).toBeTruthy();
  });

  it('loads matching results on mount', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John Doe',
          target_role_id: 1,
          target_role_name: 'Product Manager',
          match_percentage: 85,
          skills_fit: 80,
          experience_fit: 90,
          cultural_fit: 85,
          readiness_level: 'ready',
          risk_factors: ['New to PM role'],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockResults,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(global.fetch).toHaveBeenCalledWith(
      '/api/scenarios/1/step2/matching-results',
      expect.any(Object)
    );
  });

  it('displays match cards with match percentage', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John Doe',
          target_role_id: 1,
          target_role_name: 'Product Manager',
          match_percentage: 85,
          skills_fit: 80,
          experience_fit: 90,
          cultural_fit: 85,
          readiness_level: 'ready',
          risk_factors: [],
        },
        {
          candidate_id: 2,
          candidate_name: 'Jane Smith',
          target_role_id: 2,
          target_role_name: 'Engineer',
          match_percentage: 70,
          skills_fit: 75,
          experience_fit: 70,
          cultural_fit: 65,
          readiness_level: 'developing',
          risk_factors: ['Skills gap in Rust'],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockResults,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const cards = wrapper.findAll('.match-card');
    expect(cards).toHaveLength(2);
  });

  it('shows different colors based on match percentage', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John',
          target_role_id: 1,
          target_role_name: 'PM',
          match_percentage: 90,
          skills_fit: 90,
          experience_fit: 90,
          cultural_fit: 90,
          readiness_level: 'ready',
          risk_factors: [],
        },
        {
          candidate_id: 2,
          candidate_name: 'Jane',
          target_role_id: 2,
          target_role_name: 'Engineer',
          match_percentage: 50,
          skills_fit: 50,
          experience_fit: 50,
          cultural_fit: 50,
          readiness_level: 'not_ready',
          risk_factors: ['Major gaps'],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockResults,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.getMatchColor(90)).toContain('green');
    expect(wrapper.vm.getMatchColor(50)).toContain('red');
  });

  it('displays risk factors when present', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John',
          target_role_id: 1,
          target_role_name: 'PM',
          match_percentage: 70,
          skills_fit: 65,
          experience_fit: 75,
          cultural_fit: 70,
          readiness_level: 'developing',
          risk_factors: ['First management role', 'Needs training in budgeting'],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockResults,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.results[0].risk_factors).toHaveLength(2);
    expect(wrapper.vm.results[0].risk_factors).toContain('First management role');
  });

  it('handles empty results', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ matching_results: [] }),
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.results).toHaveLength(0);
  });

  it('shows error on fetch failure', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: false,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.error).toBeTruthy();
  });

  it('shows loading state during fetch', async () => {
    (global.fetch as any).mockImplementationOnce(
      () =>
        new Promise((resolve) =>
          setTimeout(
            () =>
              resolve({
                ok: true,
                json: async () => ({ matching_results: [] }),
              }),
            100
          )
        )
    );

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.vm.loading).toBe(true);

    await flushPromises();

    expect(wrapper.vm.loading).toBe(false);
  });

  it('accepts match when button clicked', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John',
          target_role_id: 1,
          target_role_name: 'PM',
          match_percentage: 85,
          skills_fit: 80,
          experience_fit: 90,
          cultural_fit: 85,
          readiness_level: 'ready',
          risk_factors: [],
        },
      ],
    };

    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => mockResults,
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({ success: true }),
      });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    await wrapper.vm.acceptMatch(1, 1);

    expect(global.fetch).toHaveBeenCalledWith(
      expect.stringContaining('/step2/matching-results'),
      expect.any(Object)
    );
  });

  it('updates when scenarioId changes', async () => {
    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          matching_results: [
            {
              candidate_id: 1,
              candidate_name: 'John',
              target_role_id: 1,
              target_role_name: 'PM',
              match_percentage: 85,
              skills_fit: 80,
              experience_fit: 90,
              cultural_fit: 85,
              readiness_level: 'ready',
              risk_factors: [],
            },
          ],
        }),
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          matching_results: [
            {
              candidate_id: 2,
              candidate_name: 'Jane',
              target_role_id: 2,
              target_role_name: 'Engineer',
              match_percentage: 75,
              skills_fit: 70,
              experience_fit: 80,
              cultural_fit: 75,
              readiness_level: 'developing',
              risk_factors: [],
            },
          ],
        }),
      });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();
    expect(wrapper.vm.results[0].candidate_name).toBe('John');

    await wrapper.setProps({ scenarioId: 2 });
    await flushPromises();

    expect(wrapper.vm.results[0].candidate_name).toBe('Jane');
  });

  it('filters by readiness level', async () => {
    const mockResults = {
      matching_results: [
        {
          candidate_id: 1,
          candidate_name: 'John',
          target_role_id: 1,
          target_role_name: 'PM',
          match_percentage: 85,
          skills_fit: 80,
          experience_fit: 90,
          cultural_fit: 85,
          readiness_level: 'ready',
          risk_factors: [],
        },
        {
          candidate_id: 2,
          candidate_name: 'Jane',
          target_role_id: 2,
          target_role_name: 'Engineer',
          match_percentage: 50,
          skills_fit: 45,
          experience_fit: 55,
          cultural_fit: 50,
          readiness_level: 'not_ready',
          risk_factors: ['Major gaps'],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockResults,
    });

    const wrapper = mount(MatchingResults, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const ready = wrapper.vm.filterByReadiness('ready');
    expect(ready).toHaveLength(1);
    expect(ready[0].candidate_name).toBe('John');
  });
});
