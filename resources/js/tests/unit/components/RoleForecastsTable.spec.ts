import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
// TODO: Uncomment when RoleForecastsTable component is created
// import RoleForecastsTable from '@/components/Paso2/RoleForecastsTable.vue';

// Mock fetch
global.fetch = vi.fn();

// SKIPPED: Component not yet implemented
describe.skip('RoleForecastsTable.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders component with correct structure', () => {
    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.exists()).toBe(true);
    expect(wrapper.find('.role-forecasts-table').exists()).toBeTruthy();
  });

  it('loads forecast data on mount', async () => {
    const mockForecasts = {
      role_forecasts: [
        {
          role_id: 1,
          role_name: 'Product Manager',
          current_fte: 2,
          future_fte: 3,
          fte_delta: 1,
          impact_level: 'high',
          change_reason: 'Business expansion',
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockForecasts,
    });

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(global.fetch).toHaveBeenCalledWith(
      '/api/scenarios/1/step2/role-forecasts',
      expect.any(Object)
    );
    expect(wrapper.vm.forecasts).toHaveLength(1);
  });

  it('displays forecast data in table', async () => {
    const mockForecasts = {
      role_forecasts: [
        {
          role_id: 1,
          role_name: 'Product Manager',
          current_fte: 2,
          future_fte: 3,
          fte_delta: 1,
          impact_level: 'high',
          change_reason: 'Growth',
        },
        {
          role_id: 2,
          role_name: 'Engineer',
          current_fte: 5,
          future_fte: 5,
          fte_delta: 0,
          impact_level: 'medium',
          change_reason: 'Stable',
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockForecasts,
    });

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    const rows = wrapper.findAll('tbody tr');
    expect(rows).toHaveLength(2);
  });

  it('calculates impact level based on FTE delta', async () => {
    const mockForecasts = {
      role_forecasts: [
        {
          role_id: 1,
          role_name: 'PM',
          current_fte: 1,
          future_fte: 5,
          fte_delta: 4,
          impact_level: 'critical',
          change_reason: 'Major expansion',
        },
      ],
    };

    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => mockForecasts,
    });

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.forecasts[0].impact_level).toBe('critical');
  });

  it('shows error message on fetch failure', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: false,
    });

    const wrapper = mount(RoleForecastsTable, {
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
                json: async () => ({ role_forecasts: [] }),
              }),
            100
          )
        )
    );

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    expect(wrapper.vm.loading).toBe(true);

    await flushPromises();

    expect(wrapper.vm.loading).toBe(false);
  });

  it('handles empty forecast list', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ role_forecasts: [] }),
    });

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.forecasts).toHaveLength(0);
    expect(wrapper.find('.empty-state').exists()).toBeTruthy();
  });

  it('updates data when scenarioId prop changes', async () => {
    (global.fetch as any)
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          role_forecasts: [
            {
              role_id: 1,
              role_name: 'PM',
              current_fte: 1,
              future_fte: 2,
              fte_delta: 1,
              impact_level: 'medium',
              change_reason: 'Growth',
            },
          ],
        }),
      })
      .mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          role_forecasts: [
            {
              role_id: 2,
              role_name: 'Engineer',
              current_fte: 3,
              future_fte: 5,
              fte_delta: 2,
              impact_level: 'high',
              change_reason: 'Expansion',
            },
          ],
        }),
      });

    const wrapper = mount(RoleForecastsTable, {
      props: {
        scenarioId: 1,
      },
    });

    await flushPromises();

    expect(wrapper.vm.forecasts).toHaveLength(1);
    expect(wrapper.vm.forecasts[0].role_id).toBe(1);

    await wrapper.setProps({ scenarioId: 2 });
    await flushPromises();

    expect(wrapper.vm.forecasts).toHaveLength(1);
    expect(wrapper.vm.forecasts[0].role_id).toBe(2);
  });

  it('passes scenarioId to API correctly', async () => {
    (global.fetch as any).mockResolvedValueOnce({
      ok: true,
      json: async () => ({ role_forecasts: [] }),
    });

    mount(RoleForecastsTable, {
      props: {
        scenarioId: 42,
      },
    });

    await flushPromises();

    expect(global.fetch).toHaveBeenCalledWith(
      '/api/scenarios/42/step2/role-forecasts',
      expect.any(Object)
    );
  });
});
