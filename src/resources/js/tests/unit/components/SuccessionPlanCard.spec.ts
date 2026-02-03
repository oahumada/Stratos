import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
// TODO: Uncomment when SuccessionPlanCard component is created
// import SuccessionPlanCard from '@/components/Paso2/SuccessionPlanCard.vue';

global.fetch = vi.fn();

// SKIPPED: Component not yet implemented
describe.skip('SuccessionPlanCard.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders component with correct structure', () => {
    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('.succession-plan-card').exists()).toBeTruthy();
  });

  it('loads succession plans on mount', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [
            {
              person_id: 2,
              person_name: 'Bob Smith',
              readiness_level: 'ready',
              readiness_percentage: 90,
              skills_gaps: 1,
              experience_aligned: true,
            },
            {
              person_id: 3,
              person_name: 'Carol Davis',
              readiness_level: 'developing',
              readiness_percentage: 65,
              skills_gaps: 3,
              experience_aligned: false,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(global.fetch).toHaveBeenCalledWith(
      '/api/v1/scenarios/1/step2/succession-plans',
      expect.any(Object)
    );
  });

  it('displays current role holder information', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.plans[0].current_holder_name).toBe('Alice Johnson');
    expect(wrapper.vm.plans[0].tenure_years).toBe(5);
  });

  it('displays successors with readiness levels', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [
            {
              person_id: 2,
              person_name: 'Bob Smith',
              readiness_level: 'ready',
              readiness_percentage: 90,
              skills_gaps: 1,
              experience_aligned: true,
            },
            {
              person_id: 3,
              person_name: 'Carol Davis',
              readiness_level: 'developing',
              readiness_percentage: 65,
              skills_gaps: 3,
              experience_aligned: false,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.plans[0].successors).toHaveLength(2);
    expect(wrapper.vm.plans[0].successors[0].readiness_level).toBe('ready');
  });

  it('shows color coding based on readiness level', async () => {
    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.vm.getReadinessColor('ready')).toContain('green');
    expect(wrapper.vm.getReadinessColor('developing')).toContain('yellow');
    expect(wrapper.vm.getReadinessColor('not_ready')).toContain('red');
  });

  it('handles empty succession plans', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ succession_plans: [] }),
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.plans).toHaveLength(0);
  });

  it('shows error on fetch failure', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: false,
    });

    const wrapper = mount(SuccessionPlanCard, {
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
                json: async () => ({ succession_plans: [] }),
              }),
            100
          )
        )
    );

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.vm.loading).toBe(true);

    await flushPromises();

    expect(wrapper.vm.loading).toBe(false);
  });

  it('opens edit dialog when edit button clicked', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [
            {
              person_id: 2,
              person_name: 'Bob Smith',
              readiness_level: 'ready',
              readiness_percentage: 90,
              skills_gaps: 1,
              experience_aligned: true,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    wrapper.vm.openEditDialog(0);

    expect(wrapper.vm.editingPlanIndex).toBe(0);
    expect(wrapper.vm.showEditDialog).toBe(true);
  });

  it('saves edited plan', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [],
        },
      ],
    };

    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => mockPlans,
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({ success: true }),
      });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    await wrapper.vm.savePlan(0);

    expect(wrapper.vm.showEditDialog).toBe(false);
  });

  it('filters successors with gaps', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [
            {
              person_id: 2,
              person_name: 'Bob Smith',
              readiness_level: 'ready',
              readiness_percentage: 90,
              skills_gaps: 0,
              experience_aligned: true,
            },
            {
              person_id: 3,
              person_name: 'Carol Davis',
              readiness_level: 'developing',
              readiness_percentage: 65,
              skills_gaps: 3,
              experience_aligned: false,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const withGaps = wrapper.vm.plans[0].successors.filter(
      (s: any) => s.skills_gaps > 0
    );
    expect(withGaps).toHaveLength(1);
  });

  it('updates when scenarioId changes', async () => {
    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          succession_plans: [
            {
              role_id: 1,
              role_name: 'VP Engineering',
              current_holder_id: 1,
              current_holder_name: 'Alice Johnson',
              tenure_years: 5,
              successors: [],
            },
          ],
        }),
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          succession_plans: [
            {
              role_id: 2,
              role_name: 'CTO',
              current_holder_id: 2,
              current_holder_name: 'Bob Chen',
              tenure_years: 3,
              successors: [],
            },
          ],
        }),
      });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();
    expect(wrapper.vm.plans[0].role_name).toBe('VP Engineering');

    await wrapper.setProps({ scenarioId: 2 });
    await flushPromises();

    expect(wrapper.vm.plans[0].role_name).toBe('CTO');
  });

  it('shows successor gaps summary', async () => {
    const mockPlans = {
      succession_plans: [
        {
          role_id: 1,
          role_name: 'VP Engineering',
          current_holder_id: 1,
          current_holder_name: 'Alice Johnson',
          tenure_years: 5,
          successors: [
            {
              person_id: 2,
              person_name: 'Bob Smith',
              readiness_level: 'ready',
              readiness_percentage: 90,
              skills_gaps: 1,
              experience_aligned: true,
            },
          ],
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockPlans,
    });

    const wrapper = mount(SuccessionPlanCard, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const successor = wrapper.vm.plans[0].successors[0];
    expect(successor.skills_gaps).toBe(1);
  });
});
