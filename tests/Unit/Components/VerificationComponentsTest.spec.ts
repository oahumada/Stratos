import AuditLogExplorer from '@/components/Verification/AuditLogExplorer.vue';
import ChannelConfig from '@/components/Verification/ChannelConfig.vue';
import DryRunSimulator from '@/components/Verification/DryRunSimulator.vue';
import NotificationCenter from '@/components/Verification/NotificationCenter.vue';
import SchedulerStatus from '@/components/Verification/SchedulerStatus.vue';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// ============================================================================
// SchedulerStatus Component Tests
// ============================================================================

describe('SchedulerStatus Component', () => {
    let mockFetch: any;

    beforeEach(() => {
        mockFetch = vi.fn();
        global.fetch = mockFetch;
    });

    it('renders scheduler status card', () => {
        const wrapper = mount(SchedulerStatus, {
            props: {
                isLoading: false,
                schedulerData: {
                    enabled: true,
                    mode: 'auto_transitions',
                    last_run: '2026-03-24T12:00:00Z',
                    next_run: '2026-03-24T13:00:00Z',
                    seconds_until_next: 3600,
                    recent_executions: [],
                },
            },
        });

        expect(wrapper.find('h2').text()).toContain('Scheduler');
        expect(
            wrapper.findAll('[data-testid="status-badge"]').length,
        ).toBeGreaterThan(0);
    });

    it('displays enabled/disabled status correctly', () => {
        const wrapper = mount(SchedulerStatus, {
            props: {
                isLoading: false,
                schedulerData: {
                    enabled: true,
                    mode: 'auto_transitions',
                    last_run: '2026-03-24T12:00:00Z',
                    next_run: '2026-03-24T13:00:00Z',
                    seconds_until_next: 3600,
                    recent_executions: [],
                },
            },
        });

        const badge = wrapper.find('[data-testid="enabled-badge"]');
        expect(badge.text()).toBe('Enabled');
    });

    it('shows countdown timer', () => {
        const wrapper = mount(SchedulerStatus, {
            props: {
                isLoading: false,
                schedulerData: {
                    enabled: true,
                    mode: 'auto_transitions',
                    last_run: '2026-03-24T12:00:00Z',
                    next_run: '2026-03-24T13:00:00Z',
                    seconds_until_next: 1800, // 30 minutes
                    recent_executions: [],
                },
            },
        });

        expect(wrapper.text()).toContain('30m');
    });

    it('displays recent executions table', () => {
        const executions = [
            {
                id: 1,
                started_at: '2026-03-24T12:00:00Z',
                ended_at: '2026-03-24T12:01:00Z',
                status: 'completed',
                phase_evaluated: 'flagging',
            },
        ];

        const wrapper = mount(SchedulerStatus, {
            props: {
                isLoading: false,
                schedulerData: {
                    enabled: true,
                    mode: 'auto_transitions',
                    last_run: '2026-03-24T12:00:00Z',
                    next_run: '2026-03-24T13:00:00Z',
                    seconds_until_next: 3600,
                    recent_executions: executions,
                },
            },
        });

        expect(wrapper.find('table').exists()).toBe(true);
        expect(wrapper.text()).toContain('completed');
    });

    it('has functioning run now button', async () => {
        const onRunNow = vi.fn();
        const wrapper = mount(SchedulerStatus, {
            props: {
                isLoading: false,
                schedulerData: {
                    enabled: true,
                    mode: 'auto_transitions',
                    last_run: '2026-03-24T12:00:00Z',
                    next_run: '2026-03-24T13:00:00Z',
                    seconds_until_next: 3600,
                    recent_executions: [],
                },
                onRunNow,
            },
        });

        const button = wrapper.find('[data-testid="run-now-button"]');
        await button.trigger('click');

        expect(onRunNow).toHaveBeenCalled();
    });
});

// ============================================================================
// NotificationCenter Component Tests
// ============================================================================

describe('NotificationCenter Component', () => {
    it('renders notifications table', () => {
        const notifications = [
            {
                id: 1,
                type: 'phase_transition',
                severity: 'info',
                message: 'Phase changed to flagging',
                read: false,
                created_at: '2026-03-24T10:00:00Z',
            },
        ];

        const wrapper = mount(NotificationCenter, {
            props: {
                notifications,
                isLoading: false,
            },
        });

        expect(wrapper.find('table').exists()).toBe(true);
        expect(wrapper.text()).toContain('phase_transition');
    });

    it('filters notifications by type', async () => {
        const notifications = [
            {
                id: 1,
                type: 'phase_transition',
                severity: 'info',
                message: 'msg1',
                read: false,
                created_at: '2026-03-24T10:00:00Z',
            },
            {
                id: 2,
                type: 'config_change',
                severity: 'warning',
                message: 'msg2',
                read: false,
                created_at: '2026-03-24T10:01:00Z',
            },
        ];

        const wrapper = mount(NotificationCenter, {
            props: {
                notifications,
                isLoading: false,
            },
        });

        const typeFilter = wrapper.find('[data-testid="type-filter"]');
        await typeFilter.setValue('phase_transition');

        // Should only show phase_transition
        expect(wrapper.findAll('tbody tr').length).toBe(1);
    });

    it('filters notifications by severity', async () => {
        const notifications = [
            {
                id: 1,
                type: 'phase_transition',
                severity: 'info',
                message: 'msg',
                read: false,
                created_at: '2026-03-24T10:00:00Z',
            },
            {
                id: 2,
                type: 'config_change',
                severity: 'error',
                message: 'msg',
                read: false,
                created_at: '2026-03-24T10:01:00Z',
            },
        ];

        const wrapper = mount(NotificationCenter, {
            props: {
                notifications,
                isLoading: false,
            },
        });

        const severityFilter = wrapper.find('[data-testid="severity-filter"]');
        await severityFilter.setValue('error');

        // Should only show error severity
        expect(wrapper.findAll('tbody tr').length).toBeLessThanOrEqual(1);
    });

    it('expands notification details', async () => {
        const notifications = [
            {
                id: 1,
                type: 'phase_transition',
                severity: 'info',
                message: 'msg',
                read: false,
                created_at: '2026-03-24T10:00:00Z',
                metadata: { confidence: 92 },
            },
        ];

        const wrapper = mount(NotificationCenter, {
            props: {
                notifications,
                isLoading: false,
            },
        });

        const expandButton = wrapper.find('[data-testid="expand-row-1"]');
        await expandButton.trigger('click');

        expect(wrapper.find('[data-testid="detail-panel-1"]').exists()).toBe(
            true,
        );
    });

    it('paginates notifications', () => {
        const notifications = Array.from({ length: 50 }, (_, i) => ({
            id: i + 1,
            type: 'phase_transition',
            severity: 'info',
            message: `msg${i}`,
            read: false,
            created_at: '2026-03-24T10:00:00Z',
        }));

        const wrapper = mount(NotificationCenter, {
            props: {
                notifications: notifications.slice(0, 20),
                isLoading: false,
            },
        });

        expect(wrapper.find('[data-testid="pagination"]').exists()).toBe(true);
    });
});

// ============================================================================
// DryRunSimulator Component Tests
// ============================================================================

describe('DryRunSimulator Component', () => {
    it('renders slider controls', () => {
        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
            },
        });

        const sliders = wrapper.findAll('[data-testid*="slider"]');
        expect(sliders.length).toBeGreaterThanOrEqual(3); // confidence, error_rate, retry_rate
    });

    it('updates threshold values on slider change', async () => {
        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
            },
        });

        const confidenceSlider = wrapper.find(
            '[data-testid="confidence-slider"]',
        );
        await confidenceSlider.setValue(85);

        expect(wrapper.vm.thresholds.confidence_threshold).toBe(85);
    });

    it('runs simulation with custom thresholds', async () => {
        const onSimulate = vi.fn();
        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
                onSimulate,
            },
        });

        const runButton = wrapper.find('[data-testid="run-simulation-button"]');
        await runButton.trigger('click');

        expect(onSimulate).toHaveBeenCalled();
    });

    it('displays simulation results', async () => {
        const results = {
            current_phase: 'flagging',
            would_transition: true,
            next_phase: 'reject',
            reason: 'error_rate exceeds threshold',
        };

        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
                simulationResults: results,
            },
        });

        expect(wrapper.text()).toContain('would_transition: true');
        expect(wrapper.text()).toContain('reject');
    });

    it('shows gaps when simulation reveals blockers', () => {
        const results = {
            current_phase: 'flagging',
            would_transition: false,
            gaps: [
                {
                    metric: 'error_rate',
                    current_value: 45,
                    required_value: 40,
                    days_to_meet: 3,
                },
            ],
        };

        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
                simulationResults: results,
            },
        });

        expect(wrapper.text()).toContain('error_rate');
        expect(wrapper.text()).toContain('3');
    });

    it('exports simulation results as PDF', async () => {
        const onExport = vi.fn();
        const wrapper = mount(DryRunSimulator, {
            props: {
                isLoading: false,
                currentPhase: 'flagging',
                onExport,
            },
        });

        const exportButton = wrapper.find('[data-testid="export-pdf-button"]');
        await exportButton.trigger('click');

        expect(onExport).toHaveBeenCalledWith('pdf');
    });
});

// ============================================================================
// ChannelConfig Component Tests
// ============================================================================

describe('ChannelConfig Component', () => {
    it('renders all 4 channel toggles', () => {
        const config = {
            channels: {
                slack: { enabled: true },
                email: { enabled: false },
                database: { enabled: true },
                log: { enabled: true },
            },
        };

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
            },
        });

        expect(wrapper.findAll('[data-testid*="toggle"]').length).toBe(4);
    });

    it('toggles channel on/off', async () => {
        const config = {
            channels: {
                slack: { enabled: false },
            },
        };

        const onToggle = vi.fn();

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
                onToggle,
            },
        });

        const slackToggle = wrapper.find('[data-testid="slack-toggle"]');
        await slackToggle.trigger('click');

        expect(onToggle).toHaveBeenCalledWith('slack', true);
    });

    it('renders threshold sliders', () => {
        const config = {
            thresholds: {
                confidence_min: 90,
                error_rate_max: 40,
                retry_rate_max: 20,
            },
        };

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
            },
        });

        const sliders = wrapper.findAll('input[type="range"]');
        expect(sliders.length).toBeGreaterThanOrEqual(3);
    });

    it('sends test message to selected channel', async () => {
        const onTest = vi.fn();
        const config = {
            channels: {
                slack: { enabled: true },
            },
        };

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
                onTest,
            },
        });

        const testButton = wrapper.find('[data-testid="test-slack-button"]');
        await testButton.trigger('click');

        expect(onTest).toHaveBeenCalledWith('slack');
    });

    it('shows channel configuration inputs', () => {
        const config = {
            channels: {
                slack: {
                    enabled: true,
                    webhook_url: 'https://hooks.slack.com/...',
                },
                email: { enabled: true, recipients: ['admin@company.com'] },
            },
        };

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
            },
        });

        expect(
            wrapper.find('[data-testid="slack-webhook-input"]').exists(),
        ).toBe(true);
        expect(
            wrapper.find('[data-testid="email-recipients-input"]').exists(),
        ).toBe(true);
    });

    it('validates webhook URL format', async () => {
        const config = {
            channels: {
                slack: { enabled: true, webhook_url: '' },
            },
        };

        const wrapper = mount(ChannelConfig, {
            props: {
                config,
                isLoading: false,
            },
        });

        const input = wrapper.find('[data-testid="slack-webhook-input"]');
        await input.setValue('invalid-url');

        expect(wrapper.find('[data-testid="webhook-error"]').exists()).toBe(
            true,
        );
    });
});

// ============================================================================
// AuditLogExplorer Component Tests
// ============================================================================

describe('AuditLogExplorer Component', () => {
    it('renders audit logs table', () => {
        const logs = [
            {
                id: 1,
                action: 'phase_transition',
                user_name: 'System',
                phase_from: 'silent',
                phase_to: 'flagging',
                created_at: '2026-03-24T10:00:00Z',
            },
        ];

        const wrapper = mount(AuditLogExplorer, {
            props: {
                logs,
                isLoading: false,
            },
        });

        expect(wrapper.find('table').exists()).toBe(true);
        expect(wrapper.text()).toContain('phase_transition');
    });

    it('filters logs by action type', async () => {
        const logs = [
            {
                id: 1,
                action: 'phase_transition',
                user_name: 'System',
                created_at: '2026-03-24T10:00:00Z',
            },
            {
                id: 2,
                action: 'config_change',
                user_name: 'Admin',
                created_at: '2026-03-24T10:01:00Z',
            },
        ];

        const wrapper = mount(AuditLogExplorer, {
            props: {
                logs,
                isLoading: false,
            },
        });

        const actionFilter = wrapper.find('[data-testid="action-filter"]');
        await actionFilter.setValue('phase_transition');

        expect(wrapper.findAll('tbody tr').length).toBe(1);
    });

    it('filters logs by date range', async () => {
        const logs = [
            {
                id: 1,
                action: 'phase_transition',
                user_name: 'System',
                created_at: '2026-03-01T10:00:00Z',
            },
            {
                id: 2,
                action: 'config_change',
                user_name: 'Admin',
                created_at: '2026-03-24T10:01:00Z',
            },
        ];

        const wrapper = mount(AuditLogExplorer, {
            props: {
                logs,
                isLoading: false,
            },
        });

        const fromDateInput = wrapper.find('[data-testid="date-from"]');
        const toDateInput = wrapper.find('[data-testid="date-to"]');

        await fromDateInput.setValue('2026-03-20');
        await toDateInput.setValue('2026-03-31');

        // Should filter to only logs within this range
        expect(wrapper.findAll('tbody tr').length).toBeLessThanOrEqual(1);
    });

    it('exports audit logs as CSV', async () => {
        const onExport = vi.fn();
        const logs = [
            {
                id: 1,
                action: 'phase_transition',
                user_name: 'System',
                created_at: '2026-03-24T10:00:00Z',
            },
        ];

        const wrapper = mount(AuditLogExplorer, {
            props: {
                logs,
                isLoading: false,
                onExport,
            },
        });

        const exportButton = wrapper.find('[data-testid="export-csv-button"]');
        await exportButton.trigger('click');

        expect(onExport).toHaveBeenCalledWith('csv');
    });

    it('shows audit log summary statistics', () => {
        const logs = [];
        const summary = {
            total_events: 150,
            phase_transitions: 12,
            config_changes: 89,
            manual_overrides: 5,
        };

        const wrapper = mount(AuditLogExplorer, {
            props: {
                logs,
                isLoading: false,
                summary,
            },
        });

        expect(wrapper.text()).toContain('150');
        expect(wrapper.text()).toContain('12');
        expect(wrapper.text()).toContain('89');
    });
});
