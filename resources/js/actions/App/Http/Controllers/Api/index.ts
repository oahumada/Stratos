import CatalogsController from './CatalogsController'
import AssessmentController from './AssessmentController'
import RoleDesignerController from './RoleDesignerController'
import PublicJobController from './PublicJobController'
import ComplianceAuditController from './ComplianceAuditController'
import DevelopmentActionController from './DevelopmentActionController'
import MentorshipSessionController from './MentorshipSessionController'
import EvidenceController from './EvidenceController'
import PulseController from './PulseController'
import AgentController from './AgentController'
import JobOpeningController from './JobOpeningController'
import ApplicationController from './ApplicationController'
import StratosIqController from './StratosIqController'
import TalentPassController from './TalentPassController'
import MarketplaceController from './MarketplaceController'
import MentorController from './MentorController'
import AuthController from './AuthController'
import DashboardController from './DashboardController'
import BulkPeopleImportController from './BulkPeopleImportController'
import SuccessionController from './SuccessionController'
import MiStratosController from './MiStratosController'
import SmartAlertController from './SmartAlertController'
import GamificationController from './GamificationController'
import SupportTicketController from './SupportTicketController'
import SecurityAccessController from './SecurityAccessController'
import InvestorDashboardController from './InvestorDashboardController'
import AssessmentCycleController from './AssessmentCycleController'
import PeopleProfileController from './PeopleProfileController'
import DepartmentController from './DepartmentController'
import StratosMapController from './StratosMapController'
import RBACController from './RBACController'
import CulturalBlueprintController from './CulturalBlueprintController'
import PxCampaignController from './PxCampaignController'
import GapAnalysisController from './GapAnalysisController'
import DevelopmentPathController from './DevelopmentPathController'
import ScenarioController from './ScenarioController'
import PromptInstructionController from './PromptInstructionController'
import SocialLearningController from './SocialLearningController'
import ChangeSetController from './ChangeSetController'
import ScenarioGenerationController from './ScenarioGenerationController'
import ScenarioGenerationAbacusController from './ScenarioGenerationAbacusController'
import ScenarioGenerationIntelController from './ScenarioGenerationIntelController'
import GenerationChunkController from './GenerationChunkController'
import ScenarioSimulationController from './ScenarioSimulationController'
import ScenarioRoiController from './ScenarioRoiController'
import ScenarioStrategyController from './ScenarioStrategyController'
import IncubationController from './IncubationController'
import CompetencyMaterializerController from './CompetencyMaterializerController'
import Talento360Controller from './Talento360Controller'
import CompetencyCuratorController from './CompetencyCuratorController'
import MobilitySimulationController from './MobilitySimulationController'
import ExecutionTrackingController from './ExecutionTrackingController'
import LmsController from './LmsController'
import TelemetryController from './TelemetryController'
import CapabilityCompetencyController from './CapabilityCompetencyController'
import ScenarioTemplateController from './ScenarioTemplateController'
import Step2RoleCompetencyController from './Step2RoleCompetencyController'
import ImpactReportController from './ImpactReportController'
import ScenarioIQController from './ScenarioIQController'
import StratosIntelligenceController from './StratosIntelligenceController'
import PxController from './PxController'
import RAGASEvaluationController from './RAGASEvaluationController'
import RagController from './RagController'
import AgentInteractionMetricsController from './AgentInteractionMetricsController'
import IntelligenceAggregatesController from './IntelligenceAggregatesController'
import AnalyticsController from './AnalyticsController'
import AutomationController from './AutomationController'
import MobileController from './MobileController'
import Messaging from './Messaging'
import AdminOperationsController from './AdminOperationsController'
import Automation from './Automation'

const Api = {
    CatalogsController: Object.assign(CatalogsController, CatalogsController),
    AssessmentController: Object.assign(AssessmentController, AssessmentController),
    RoleDesignerController: Object.assign(RoleDesignerController, RoleDesignerController),
    PublicJobController: Object.assign(PublicJobController, PublicJobController),
    ComplianceAuditController: Object.assign(ComplianceAuditController, ComplianceAuditController),
    DevelopmentActionController: Object.assign(DevelopmentActionController, DevelopmentActionController),
    MentorshipSessionController: Object.assign(MentorshipSessionController, MentorshipSessionController),
    EvidenceController: Object.assign(EvidenceController, EvidenceController),
    PulseController: Object.assign(PulseController, PulseController),
    AgentController: Object.assign(AgentController, AgentController),
    JobOpeningController: Object.assign(JobOpeningController, JobOpeningController),
    ApplicationController: Object.assign(ApplicationController, ApplicationController),
    StratosIqController: Object.assign(StratosIqController, StratosIqController),
    TalentPassController: Object.assign(TalentPassController, TalentPassController),
    MarketplaceController: Object.assign(MarketplaceController, MarketplaceController),
    MentorController: Object.assign(MentorController, MentorController),
    AuthController: Object.assign(AuthController, AuthController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    BulkPeopleImportController: Object.assign(BulkPeopleImportController, BulkPeopleImportController),
    SuccessionController: Object.assign(SuccessionController, SuccessionController),
    MiStratosController: Object.assign(MiStratosController, MiStratosController),
    SmartAlertController: Object.assign(SmartAlertController, SmartAlertController),
    GamificationController: Object.assign(GamificationController, GamificationController),
    SupportTicketController: Object.assign(SupportTicketController, SupportTicketController),
    SecurityAccessController: Object.assign(SecurityAccessController, SecurityAccessController),
    InvestorDashboardController: Object.assign(InvestorDashboardController, InvestorDashboardController),
    AssessmentCycleController: Object.assign(AssessmentCycleController, AssessmentCycleController),
    PeopleProfileController: Object.assign(PeopleProfileController, PeopleProfileController),
    DepartmentController: Object.assign(DepartmentController, DepartmentController),
    StratosMapController: Object.assign(StratosMapController, StratosMapController),
    RBACController: Object.assign(RBACController, RBACController),
    CulturalBlueprintController: Object.assign(CulturalBlueprintController, CulturalBlueprintController),
    PxCampaignController: Object.assign(PxCampaignController, PxCampaignController),
    GapAnalysisController: Object.assign(GapAnalysisController, GapAnalysisController),
    DevelopmentPathController: Object.assign(DevelopmentPathController, DevelopmentPathController),
    ScenarioController: Object.assign(ScenarioController, ScenarioController),
    PromptInstructionController: Object.assign(PromptInstructionController, PromptInstructionController),
    SocialLearningController: Object.assign(SocialLearningController, SocialLearningController),
    ChangeSetController: Object.assign(ChangeSetController, ChangeSetController),
    ScenarioGenerationController: Object.assign(ScenarioGenerationController, ScenarioGenerationController),
    ScenarioGenerationAbacusController: Object.assign(ScenarioGenerationAbacusController, ScenarioGenerationAbacusController),
    ScenarioGenerationIntelController: Object.assign(ScenarioGenerationIntelController, ScenarioGenerationIntelController),
    GenerationChunkController: Object.assign(GenerationChunkController, GenerationChunkController),
    ScenarioSimulationController: Object.assign(ScenarioSimulationController, ScenarioSimulationController),
    ScenarioRoiController: Object.assign(ScenarioRoiController, ScenarioRoiController),
    ScenarioStrategyController: Object.assign(ScenarioStrategyController, ScenarioStrategyController),
    IncubationController: Object.assign(IncubationController, IncubationController),
    CompetencyMaterializerController: Object.assign(CompetencyMaterializerController, CompetencyMaterializerController),
    Talento360Controller: Object.assign(Talento360Controller, Talento360Controller),
    CompetencyCuratorController: Object.assign(CompetencyCuratorController, CompetencyCuratorController),
    MobilitySimulationController: Object.assign(MobilitySimulationController, MobilitySimulationController),
    ExecutionTrackingController: Object.assign(ExecutionTrackingController, ExecutionTrackingController),
    LmsController: Object.assign(LmsController, LmsController),
    TelemetryController: Object.assign(TelemetryController, TelemetryController),
    CapabilityCompetencyController: Object.assign(CapabilityCompetencyController, CapabilityCompetencyController),
    ScenarioTemplateController: Object.assign(ScenarioTemplateController, ScenarioTemplateController),
    Step2RoleCompetencyController: Object.assign(Step2RoleCompetencyController, Step2RoleCompetencyController),
    ImpactReportController: Object.assign(ImpactReportController, ImpactReportController),
    ScenarioIQController: Object.assign(ScenarioIQController, ScenarioIQController),
    StratosIntelligenceController: Object.assign(StratosIntelligenceController, StratosIntelligenceController),
    PxController: Object.assign(PxController, PxController),
    RAGASEvaluationController: Object.assign(RAGASEvaluationController, RAGASEvaluationController),
    RagController: Object.assign(RagController, RagController),
    AgentInteractionMetricsController: Object.assign(AgentInteractionMetricsController, AgentInteractionMetricsController),
    IntelligenceAggregatesController: Object.assign(IntelligenceAggregatesController, IntelligenceAggregatesController),
    AnalyticsController: Object.assign(AnalyticsController, AnalyticsController),
    AutomationController: Object.assign(AutomationController, AutomationController),
    MobileController: Object.assign(MobileController, MobileController),
    Messaging: Object.assign(Messaging, Messaging),
    AdminOperationsController: Object.assign(AdminOperationsController, AdminOperationsController),
    Automation: Object.assign(Automation, Automation),
}

export default Api