import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const mockGet = vi.fn();
const mockPost = vi.fn();

vi.mock('axios', () => ({
    default: {
        get: (...args: unknown[]) => mockGet(...args),
        post: (...args: unknown[]) => mockPost(...args),
    },
}));

import ComplianceAuditDashboard from '@/pages/Quality/ComplianceAuditDashboard.vue';

describe('ComplianceAuditDashboard layout', () => {
    beforeEach(() => {
        vi.clearAllMocks();

        mockGet.mockImplementation((url: string) => {
            if (url === '/api/compliance/audit-events') {
                return Promise.resolve({
                    data: { data: { data: [] } },
                });
            }

            if (url === '/api/compliance/audit-events/summary') {
                return Promise.resolve({
                    data: {
                        data: {
                            total_events: 10,
                            events_last_24h: 2,
                            unique_event_names: 4,
                            unique_aggregates: 3,
                            top_event_names: {},
                        },
                    },
                });
            }

            if (url === '/api/compliance/iso30414/summary') {
                return Promise.resolve({
                    data: {
                        data: {
                            replacement_cost: {
                                total_headcount: 1,
                                total_estimated_replacement_cost: 1000,
                                average_estimated_replacement_cost: 1000,
                                highest_risk_roles: [],
                            },
                            talent_maturity_by_department: [],
                            transversal_capability_gaps: [],
                        },
                    },
                });
            }

            if (url === '/api/compliance/internal-audit-wizard') {
                return Promise.resolve({
                    data: {
                        data: {
                            signature_valid_days: 365,
                            summary: {
                                total_critical_roles: 1,
                                compliant_roles: 1,
                                non_compliant_roles: 0,
                                compliance_rate: 100,
                            },
                            roles: [],
                        },
                    },
                });
            }

            return Promise.resolve({ data: { data: null } });
        });
    });

    it('applies larger spacing to the dashboard root and KPI summary grid', async () => {
        const wrapper = mount(ComplianceAuditDashboard, {
            global: {
                stubs: {
                    StBadgeGlass: true,
                    StButtonGlass: true,
                    StCardGlass: {
                        props: ['class'],
                        template:
                            '<section :class="$props.class"><slot /></section>',
                    },
                    'v-text-field': true,
                },
            },
        });

        await flushPromises();

        const root = wrapper.get('[data-testid="compliance-dashboard-root"]');
        const summaryGrid = wrapper.get(
            '[data-testid="compliance-summary-grid"]',
        );
        const summaryCardContent = wrapper.get(
            '[data-testid="summary-card-content"]',
        );

        expect(root.classes()).toContain('space-y-10');
        expect(summaryGrid.classes()).toContain('gap-x-10');
        expect(summaryGrid.classes()).toContain('gap-y-8');
        expect(summaryGrid.classes()).toContain('px-12');
        expect(summaryCardContent.classes()).toContain('min-h-[148px]');
        expect(summaryCardContent.classes()).toContain('px-8');
        expect(summaryCardContent.classes()).toContain('py-7');
    });
});
