import VerificationConfigurationController from './VerificationConfigurationController'
import VerificationMetricsDashboardController from './VerificationMetricsDashboardController'
import VerificationHubController from './VerificationHubController'

const Deployment = {
    VerificationConfigurationController: Object.assign(VerificationConfigurationController, VerificationConfigurationController),
    VerificationMetricsDashboardController: Object.assign(VerificationMetricsDashboardController, VerificationMetricsDashboardController),
    VerificationHubController: Object.assign(VerificationHubController, VerificationHubController),
}

export default Deployment