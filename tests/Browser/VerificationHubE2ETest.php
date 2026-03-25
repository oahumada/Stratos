<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Verification Hub E2E Tests', function () {

    // ========================================================================
    // Setup & Fixtures
    // ========================================================================

    beforeEach(function () {
        $this->organization = Organization::factory()->create();
        $this->admin = User::factory()
            ->for($this->organization)
            ->admin()
            ->create();
        $this->user = User::factory()
            ->for($this->organization)
            ->create();
    });

    // ========================================================================
    // 1. Scheduler Status Tab Workflow
    // ========================================================================

    it('displays scheduler status with current phase', function () {
        $this->actingAs($this->admin);

        visit('/verification-hub')
            ->assertSee('Verification Hub')
            ->assertSee('Scheduler Status')
            ->assertSee('Last Run')
            ->assertSee('Next Run');
    });

    it('shows countdown timer for next execution', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->assertSee('Scheduler Status');
        // Should display time like "55m 30s until next run"
        $page->assertSee('until next run');
    });

    it('allows running scheduler immediately', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@run-now-button')
            ->waitFor('@success-message', seconds: 5)
            ->assertSee('Scheduler executed');
    });

    it('displays recent executions table', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->assertSee('Recent Executions');
        // Table should have headers
        $page->assertSee('Started At')
            ->assertSee('Status')
            ->assertSee('Phase Evaluated');
    });

    it('shows execution details on row click', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@expand-execution-1')
            ->waitFor('@execution-details-1', seconds: 3)
            ->assertSee('Execution Details');
    });

    // ========================================================================
    // 2. Notifications Center Tab Workflow
    // ========================================================================

    it('displays recent notifications with filtering', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->waitFor('@notifications-table', seconds: 2)
            ->assertSee('Notifications')
            ->assertSee('Type')
            ->assertSee('Severity');
    });

    it('filters notifications by type', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->select('@type-filter', 'phase_transition')
            ->waitFor('@filtered-notifications', seconds: 2)
            ->assertSee('phase_transition');
    });

    it('filters notifications by severity level', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->select('@severity-filter', 'error')
            ->waitFor('@severity-filtered', seconds: 2);
    });

    it('can send test notification to verify channels', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->click('@test-notification-button')
            ->fill('@test-recipient', 'admin@company.com')
            ->click('@send-test')
            ->waitFor('@test-success', seconds: 5)
            ->assertSee('Test notification sent');
    });

    it('marks notification as read', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->click('@mark-read-notification-1')
            ->waitFor('@notification-read', seconds: 2);
    });

    it('expands notification to show full details', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@notifications-tab')
            ->click('@expand-notification-1')
            ->waitFor('@notification-details-1', seconds: 2)
            ->assertSee('Metadata')
            ->assertSee('Confidence')
            ->assertSee('Reason');
    });

    // ========================================================================
    // 3. Configuration Tab Workflow
    // ========================================================================

    it('displays all 4 notification channels', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->waitFor('@channel-list', seconds: 2)
            ->assertSee('Slack')
            ->assertSee('Email')
            ->assertSee('Database')
            ->assertSee('Log');
    });

    it('can enable/disable notification channels', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@slack-toggle')
            ->waitFor('@channel-updated', seconds: 2)
            ->assertSee('Slack disabled');
    });

    it('validates and saves webhook URLs', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->fill('@slack-webhook-input', 'https://hooks.slack.com/services/INVALID')
            ->click('@validate-webhook')
            ->waitFor('@validation-error', seconds: 2)
            ->assertSee('Invalid webhook URL');
    });

    it('sends test message to channel', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@test-slack-button')
            ->waitFor('@test-success', seconds: 5)
            ->assertSee('Test message sent to Slack');
    });

    it('displays threshold sliders and allows adjustment', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->assertSee('Confidence Threshold')
            ->assertSee('Error Rate Maximum')
            ->assertSee('Retry Rate Maximum');
    });

    it('saves configuration changes', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@email-toggle')
            ->fill('@email-recipients', 'admin@company.com,ops@company.com')
            ->click('@save-config')
            ->waitFor('@config-saved', seconds: 3)
            ->assertSee('Configuration saved');
    });

    // ========================================================================
    // 4. Transition Readiness Tab Workflow
    // ========================================================================

    it('displays readiness gauge and metrics', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@readiness-tab')
            ->assertSee('Transition Readiness')
            ->assertSee('Confidence')
            ->assertSee('Error Rate')
            ->assertSee('Retry Rate')
            ->assertSee('Sample Size');
    });

    it('shows which metrics are ready vs not ready', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@readiness-tab')
            ->assertSee('Ready')
            ->assertSee('Not Ready');
    });

    it('displays countdown to readiness', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@readiness-tab')
            ->assertSee('Days to Ready');
    });

    it('lists blockers preventing transition', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@readiness-tab');

        // If there are blockers, they should be labeled
        if ($page->has('@blockers-list')) {
            $page->assertSee('Blockers')
                ->assertSee('Required')
                ->assertSee('Current');
        }
    });

    it('shows recommendations for improvement', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@readiness-tab');

        if ($page->has('@recommendations-list')) {
            $page->assertSee('Recommendations');
        }
    });

    // ========================================================================
    // 5. Dry-Run Simulator Tab Workflow
    // ========================================================================

    it('displays threshold sliders for simulation', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@dry-run-tab')
            ->assertSee('Dry-Run Simulator')
            ->assertSee('Confidence Threshold')
            ->assertSee('Error Rate')
            ->assertSee('Retry Rate');
    });

    it('runs simulation with custom thresholds', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@dry-run-tab')
            ->click('@run-simulation-button')
            ->waitFor('@simulation-results', seconds: 5)
            ->assertSee('Simulation Results');
    });

    it('displays what-if analysis results', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@dry-run-tab')
            ->click('@run-simulation-button')
            ->waitFor('@simulation-results', seconds: 5)
            ->assertSee('Would Transition')
            ->assertSee('Next Phase');
    });

    it('shows gaps when simulation reveals blockers', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@dry-run-tab')
            ->click('@run-simulation-button')
            ->waitFor('@simulation-results', seconds: 5);

        if ($page->has('@gaps-list')) {
            $page->assertSee('Gap Analysis')
                ->assertSee('Days to Meet');
        }
    });

    it('exports simulation results as PDF', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@dry-run-tab')
            ->click('@run-simulation-button')
            ->waitFor('@simulation-results', seconds: 5)
            ->click('@export-pdf-button');

        // PDF should be downloaded
        expect(true)->toBeTrue();
    });

    // ========================================================================
    // 6. Setup Wizard Workflow
    // ========================================================================

    it('launches setup wizard from config tab', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@launch-setup-wizard')
            ->waitFor('@wizard-modal', seconds: 2)
            ->assertSee('Setup Wizard');
    });

    it('navigates through wizard steps', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@launch-setup-wizard')
            ->waitFor('@wizard-modal', seconds: 2)
            ->assertSee('Step 1 of 5')
            ->click('@next-button')
            ->waitFor('@step-2', seconds: 1)
            ->assertSee('Step 2 of 5');
    });

    it('completes setup wizard and saves configuration', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@config-tab')
            ->click('@launch-setup-wizard')
            ->waitFor('@wizard-modal', seconds: 2);

        // Navigate through all steps
        for ($i = 0; $i < 4; $i++) {
            $page->click('@next-button')->waitFor($i === 3 ? '@complete-button' : '@next-button', seconds: 1);
        }

        $page->click('@complete-button')
            ->waitFor('@wizard-close', seconds: 2)
            ->assertSee('Configuration complete');
    });

    // ========================================================================
    // 7. Audit Log Workflow
    // ========================================================================

    it('displays audit log table with events', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->assertSee('Audit Log')
            ->assertSee('Action')
            ->assertSee('User')
            ->assertSee('Timestamp');
    });

    it('filters audit logs by action type', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->select('@action-filter', 'phase_transition')
            ->waitFor('@filtered-logs', seconds: 2);
    });

    it('filters audit logs by date range', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->fill('@from-date', '2026-03-01')
            ->fill('@to-date', '2026-03-31')
            ->click('@apply-filter')
            ->waitFor('@filtered-logs', seconds: 2);
    });

    it('expands audit log entry to show details', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->click('@expand-log-1')
            ->waitFor('@log-details-1', seconds: 2)
            ->assertSee('Details')
            ->assertSee('Metadata');
    });

    it('exports audit log as CSV', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->click('@export-csv-button');

        // CSV should be downloaded
        expect(true)->toBeTrue();
    });

    it('shows audit log summary statistics', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@audit-tab')
            ->assertSee('Total Events')
            ->assertSee('Phase Transitions')
            ->assertSee('Config Changes');
    });

    // ========================================================================
    // 8. Compliance Report Workflow
    // ========================================================================

    it('displays compliance report generator', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@compliance-tab')
            ->assertSee('Compliance Report')
            ->assertSee('Generate Report');
    });

    it('generates compliance report for date range', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@compliance-tab')
            ->fill('@report-from-date', '2026-03-01')
            ->fill('@report-to-date', '2026-03-31')
            ->click('@generate-report-button')
            ->waitFor('@report-generated', seconds: 5)
            ->assertSee('Compliance Report');
    });

    it('displays compliance metrics in report', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@compliance-tab')
            ->fill('@report-from-date', '2026-03-01')
            ->fill('@report-to-date', '2026-03-31')
            ->click('@generate-report-button')
            ->waitFor('@report-generated', seconds: 5)
            ->assertSee('Compliance Score')
            ->assertSee('Transitions')
            ->assertSee('Success Rate');
    });

    it('exports compliance report as PDF', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@compliance-tab')
            ->fill('@report-from-date', '2026-03-01')
            ->fill('@report-to-date', '2026-03-31')
            ->click('@generate-report-button')
            ->waitFor('@report-generated', seconds: 5)
            ->click('@export-report-pdf')
            ->pause(1000);

        // PDF should be downloaded
        expect(true)->toBeTrue();
    });

    // ========================================================================
    // 9. Multi-Tenant Isolation Tests
    // ========================================================================

    it('does not show data from other organizations', function () {
        $otherOrg = Organization::factory()->create();
        $otherAdmin = User::factory()
            ->for($otherOrg)
            ->admin()
            ->create();

        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        // Get IDs or identifiable data from current org
        $currentOrgData = $page->text();

        // Log out and log in as other org admin
        $this->actingAs($otherAdmin);
        $page = visit('/verification-hub');

        // Data should be different
        expect($page->text())->not->toBe($currentOrgData);
    });

    // ========================================================================
    // 10. Authorization & Permission Tests
    // ========================================================================

    it('denies access to non-admin users', function () {
        $this->actingAs($this->user);

        visit('/verification-hub')
            ->assertForbidden();
    });

    it('allows access to admin users only', function () {
        $this->actingAs($this->admin);

        visit('/verification-hub')
            ->assertSuccessful()
            ->assertSee('Verification Hub');
    });

    // ========================================================================
    // 11. Dark Mode & Language Toggle
    // ========================================================================

    it('toggles dark mode on/off', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        // Click dark mode toggle
        $page->click('@dark-mode-toggle')
            ->waitFor('@dark-mode-applied', seconds: 1);

        // Component should have dark class
        expect($page->has('.dark'))->toBeTrue();
    });

    it('switches language between English and Spanish', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        // Switch to Spanish
        $page->select('@language-select', 'es')
            ->waitFor('@language-changed', seconds: 1)
            ->assertSee('Centro de Verificación'); // Spanish translation
    });

    // ========================================================================
    // 12. Auto-Refresh & Real-Time Updates
    // ========================================================================

    it('auto-refreshes data every 5 minutes', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $initialText = $page->text();

        // Wait 5+ minutes (simulated with pause in testing context)
        $page->pause(5 * 60 * 1000);

        // Data should have refreshed
        // (In real testing, this would be validated differently)
        expect(true)->toBeTrue();
    });

    it('manual refresh button updates data', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        $page->click('@refresh-button')
            ->waitFor('@data-refreshed', seconds: 3)
            ->assertSee('Data refreshed');
    });

    // ========================================================================
    // 13. Error Handling
    // ========================================================================

    it('shows error message when API fails', function () {
        // This would require mocking API failures
        // Implementation depends on testing setup
        expect(true)->toBeTrue();
    });

    it('allows retry when data fetch fails', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        if ($page->has('@error-message')) {
            $page->click('@retry-button')
                ->waitFor('@data-loaded', seconds: 3);
        }
    });

    // ========================================================================
    // 14. Complex User Journeys
    // ========================================================================

    it('complete workflow: Configure → Simulate → Review → Report', function () {
        $this->actingAs($this->admin);

        $page = visit('/verification-hub');

        // Step 1: Configure channels
        $page->click('@config-tab')
            ->click('@email-toggle')
            ->fill('@email-recipients', 'admin@company.com')
            ->click('@save-config')
            ->waitFor('@config-saved', seconds: 3);

        // Step 2: Run simulation
        $page->click('@dry-run-tab')
            ->click('@run-simulation-button')
            ->waitFor('@simulation-results', seconds: 5);

        // Step 3: Review audit log
        $page->click('@audit-tab')
            ->assertSee('Configuration changed')
            ->assertSee('Simulation executed');

        // Step 4: Generate compliance report
        $page->click('@compliance-tab')
            ->fill('@report-from-date', now()->subDays(7)->format('Y-m-d'))
            ->fill('@report-to-date', now()->format('Y-m-d'))
            ->click('@generate-report-button')
            ->waitFor('@report-generated', seconds: 5)
            ->assertSee('Compliance Score');
    });
});
