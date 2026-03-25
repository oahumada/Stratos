import VerificationDashboardController from './VerificationDashboardController'
import VerificationConfigurationController from './VerificationConfigurationController'
import VerificationMetricsDashboardController from './VerificationMetricsDashboardController'
import VerificationHubController from './VerificationHubController'

const Deployment = {
    VerificationDashboardController: Object.assign(VerificationDashboardController, VerificationDashboardController),
    VerificationConfigurationController: Object.assign(VerificationConfigurationController, VerificationConfigurationController),
    VerificationMetricsDashboardController: Object.assign(VerificationMetricsDashboardController, VerificationMetricsDashboardController),
    VerificationHubController: Object.assign(VerificationHubController, VerificationHubController),
}

export default Deployment