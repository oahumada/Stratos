import SuccessionPlanCard from '@/components/ScenarioPlanning/Step2/SuccessionPlanCard.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

global.fetch = vi.fn();

describe('SuccessionPlanCard.vue', () => {
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
        expect(
            wrapper.find('.succession-plan-container').exists(),
        ).toBeTruthy();
    });

    it('loads succession plans on mount', async () => {
        const mockPlans = {
            data: [
                {
                    id: 1,
                    position_name: 'VP Engineering',
                    department: 'Engineering',
                    criticality: 'critical',
                    current_holder_name: 'Alice Johnson',
                    current_holder_age: 45,
                    years_in_position: 5,
                    estimated_retirement: '2030',
                    successors: [
                        {
                            id: 2,
                            name: 'Bob Smith',
                            current_role: 'Director',
                            readiness_level: 'ready_now',
                            readiness_percentage: 90,
                            skill_gaps: [],
                            timeline_months: 6,
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
            '/api/scenarios/1/step2/succession-plans',
        );
        expect(wrapper.vm.plans).toHaveLength(1);
    });

    it('displays current role holder information', async () => {
        const mockPlans = {
            data: [
                {
                    id: 1,
                    position_name: 'VP Engineering',
                    department: 'Engineering',
                    criticality: 'critical',
                    current_holder_name: 'Alice Johnson',
                    current_holder_age: 45,
                    years_in_position: 5,
                    estimated_retirement: '2030',
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
        expect(wrapper.vm.plans[0].years_in_position).toBe(5);
    });

    it('displays successors with readiness levels', async () => {
        const mockPlans = {
            data: [
                {
                    id: 1,
                    position_name: 'VP Engineering',
                    department: 'Engineering',
                    criticality: 'critical',
                    current_holder_name: 'Alice Johnson',
                    current_holder_age: 45,
                    years_in_position: 5,
                    estimated_retirement: '2030',
                    successors: [
                        {
                            id: 2,
                            name: 'Bob Smith',
                            current_role: 'Director',
                            readiness_level: 'ready_now',
                            readiness_percentage: 90,
                            skill_gaps: [],
                            timeline_months: 6,
                        },
                        {
                            id: 3,
                            name: 'Carol Davis',
                            current_role: 'Manager',
                            readiness_level: 'ready_12_months',
                            readiness_percentage: 65,
                            skill_gaps: [],
                            timeline_months: 12,
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
        expect(wrapper.vm.plans[0].successors[0].readiness_level).toBe(
            'ready_now',
        );
    });

    it('shows color coding based on readiness level', async () => {
        const wrapper = mount(SuccessionPlanCard, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.vm.getReadinessColor('ready_now')).toContain('success');
        expect(wrapper.vm.getReadinessColor('ready_12_months')).toContain(
            'warning',
        );
        expect(wrapper.vm.getReadinessColor('not_ready')).toContain('error');
    });

    it('handles empty succession plans', async () => {
        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => ({ data: [] }),
        });

        const wrapper = mount(SuccessionPlanCard, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.plans).toHaveLength(0);
    });

    it('opens edit dialog when edit button clicked', async () => {
        const mockPlans = {
            data: [
                {
                    id: 1,
                    position_name: 'VP Engineering',
                    department: 'Engineering',
                    criticality: 'critical',
                    current_holder_name: 'Alice',
                    years_in_position: 5,
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

        wrapper.vm.editPlan(wrapper.vm.plans[0]);

        expect(wrapper.vm.editingPlan).not.toBeNull();
        expect(wrapper.vm.showEditDialog).toBe(true);
    });

    it('updates when scenarioId changes', async () => {
        (global.fetch as any)
            .mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    data: [
                        {
                            id: 1,
                            position_name: 'VP Engineering',
                            successors: [],
                        },
                    ],
                }),
            })
            .mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    data: [
                        {
                            id: 2,
                            position_name: 'CTO',
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
        expect(wrapper.vm.plans[0].position_name).toBe('VP Engineering');

        await wrapper.setProps({ scenarioId: 2 });
        await flushPromises();

        expect(wrapper.vm.plans[0].position_name).toBe('CTO');
    });
});
