import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
// TODO: Uncomment when SkillGapsMatrix component is created
// import SkillGapsMatrix from '@/components/Paso2/SkillGapsMatrix.vue';

global.fetch = vi.fn();

// SKIPPED: Component not yet implemented
describe.skip('SkillGapsMatrix.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders component with correct structure', () => {
    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('.skill-gaps-matrix').exists()).toBeTruthy();
  });

  it('loads skill gaps matrix data on mount', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              required_level: 4,
              current_level: 2,
              gap: 2,
              gap_percentage: 50,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(global.fetch).toHaveBeenCalledWith(
      '/api/scenarios/1/step2/skill-gaps-matrix',
      expect.any(Object)
    );
  });

  it('displays gap heat map with correct colors', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              required_level: 4,
              current_level: 2,
              gap: 2,
              gap_percentage: 50,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const heatMapCells = wrapper.findAll('.heatmap-cell');
    expect(heatMapCells.length).toBeGreaterThan(0);
  });

  it('calculates gap color based on percentage', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              required_level: 4,
              current_level: 0,
              gap: 4,
              gap_percentage: 100,
            },
            {
              role_id: 2,
              role_name: 'Engineer',
              required_level: 3,
              current_level: 3,
              gap: 0,
              gap_percentage: 0,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.getGapColor(100)).toContain('red');
    expect(wrapper.vm.getGapColor(0)).toContain('green');
    expect(wrapper.vm.getGapColor(50)).toContain('yellow');
  });

  it('shows gap detail on cell click', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              required_level: 4,
              current_level: 2,
              gap: 2,
              gap_percentage: 50,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const cell = wrapper.find('.heatmap-cell');
    await cell.trigger('click');

    expect(wrapper.vm.selectedGap).toBeDefined();
    expect(wrapper.vm.showGapDetail).toBe(true);
  });

  it('handles empty matrix', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ skills_gaps_matrix: [] }),
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.matrix).toHaveLength(0);
  });

  it('shows error on fetch failure', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: false,
    });

    const wrapper = mount(SkillGapsMatrix, {
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
                json: async () => ({ skills_gaps_matrix: [] }),
              }),
            100
          )
        )
    );

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.vm.loading).toBe(true);

    await flushPromises();

    expect(wrapper.vm.loading).toBe(false);
  });

  it('filters gaps by skill', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              gap_percentage: 50,
            },
          ],
        },
        {
          skill_id: 2,
          skill_name: 'JavaScript',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              gap_percentage: 30,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const filtered = wrapper.vm.filteredMatrix.filter(
      (skill: any) => skill.skill_name === 'Python'
    );
    expect(filtered).toHaveLength(1);
  });

  it('updates when scenarioId changes', async () => {
    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          skills_gaps_matrix: [
            {
              skill_id: 1,
              skill_name: 'Python',
              role_gaps: [],
            },
          ],
        }),
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          skills_gaps_matrix: [
            {
              skill_id: 2,
              skill_name: 'JavaScript',
              role_gaps: [],
            },
          ],
        }),
      });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();
    expect(wrapper.vm.matrix[0].skill_name).toBe('Python');

    await wrapper.setProps({ scenarioId: 2 });
    await flushPromises();

    expect(wrapper.vm.matrix[0].skill_name).toBe('JavaScript');
  });

  it('exports gap data to CSV', async () => {
    const mockMatrix = {
      skills_gaps_matrix: [
        {
          skill_id: 1,
          skill_name: 'Python',
          role_gaps: [
            {
              role_id: 1,
              role_name: 'Data Scientist',
              required_level: 4,
              current_level: 2,
              gap: 2,
              gap_percentage: 50,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockMatrix,
    });

    const wrapper = mount(SkillGapsMatrix, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const csv = wrapper.vm.exportToCSV();
    expect(csv).toContain('Python');
    expect(csv).toContain('Data Scientist');
    expect(csv).toContain('50');
  });
});
