import VerificationConfigurationController from './VerificationConfigurationController';
import VerificationDashboardController from './VerificationDashboardController';
import VerificationHubController from './VerificationHubController';
import VerificationMetricsDashboardController from './VerificationMetricsDashboardController';

const Deployment = {
    VerificationDashboardController: Object.assign(
        VerificationDashboardController,
        VerificationDashboardController,
    ),
    VerificationConfigurationController: Object.assign(
        VerificationConfigurationController,
        VerificationConfigurationController,
    ),
    VerificationMetricsDashboardController: Object.assign(
        VerificationMetricsDashboardController,
        VerificationMetricsDashboardController,
    ),
    VerificationHubController: Object.assign(
        VerificationHubController,
        VerificationHubController,
    ),
};

export default Deployment;
