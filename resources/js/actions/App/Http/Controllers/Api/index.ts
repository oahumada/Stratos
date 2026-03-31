import AdminOperationsController from './AdminOperationsController';
import AgentController from './AgentController';
import AgentInteractionMetricsController from './AgentInteractionMetricsController';
import AlertController from './AlertController';
import AnalyticsController from './AnalyticsController';
import ApplicationController from './ApplicationController';
import AssessmentController from './AssessmentController';
import AssessmentCycleController from './AssessmentCycleController';
import AuditController from './AuditController';
import AuthController from './AuthController';
import Automation from './Automation';
import AutomationController from './AutomationController';
import BulkPeopleImportController from './BulkPeopleImportController';
import CapabilityCompetencyController from './CapabilityCompetencyController';
import CatalogsController from './CatalogsController';
import ChangeSetController from './ChangeSetController';
import CompetencyCuratorController from './CompetencyCuratorController';
import CompetencyMaterializerController from './CompetencyMaterializerController';
import ComplianceAuditController from './ComplianceAuditController';
import CulturalBlueprintController from './CulturalBlueprintController';
import DashboardController from './DashboardController';
import DepartmentController from './DepartmentController';
import DevelopmentActionController from './DevelopmentActionController';
import DevelopmentPathController from './DevelopmentPathController';
import EvidenceController from './EvidenceController';
import ExecutionTrackingController from './ExecutionTrackingController';
import ExecutiveSummaryController from './ExecutiveSummaryController';
import ExportController from './ExportController';
import GamificationController from './GamificationController';
import GapAnalysisController from './GapAnalysisController';
import GenerationChunkController from './GenerationChunkController';
import ImpactReportController from './ImpactReportController';
import IncubationController from './IncubationController';
import IntelligenceAggregatesController from './IntelligenceAggregatesController';
import InvestorDashboardController from './InvestorDashboardController';
import JobOpeningController from './JobOpeningController';
import LmsController from './LmsController';
import MarketplaceController from './MarketplaceController';
import MentorController from './MentorController';
import MentorshipSessionController from './MentorshipSessionController';
import Messaging from './Messaging';
import MiStratosController from './MiStratosController';
import MobileController from './MobileController';
import MobilitySimulationController from './MobilitySimulationController';
import OrgChartController from './OrgChartController';
import PeopleProfileController from './PeopleProfileController';
import PromptInstructionController from './PromptInstructionController';
import PublicJobController from './PublicJobController';
import PulseController from './PulseController';
import PxCampaignController from './PxCampaignController';
import PxController from './PxController';
import RAGASEvaluationController from './RAGASEvaluationController';
import RagController from './RagController';
import RBACController from './RBACController';
import RoleDesignerController from './RoleDesignerController';
import ScenarioAnalyticsController from './ScenarioAnalyticsController';
import ScenarioApprovalController from './ScenarioApprovalController';
import ScenarioController from './ScenarioController';
import ScenarioGenerationAbacusController from './ScenarioGenerationAbacusController';
import ScenarioGenerationController from './ScenarioGenerationController';
import ScenarioGenerationIntelController from './ScenarioGenerationIntelController';
import ScenarioIQController from './ScenarioIQController';
import ScenarioRoiController from './ScenarioRoiController';
import ScenarioSimulationController from './ScenarioSimulationController';
import ScenarioStrategyController from './ScenarioStrategyController';
import ScenarioTemplateController from './ScenarioTemplateController';
import SecurityAccessController from './SecurityAccessController';
import SmartAlertController from './SmartAlertController';
import SocialLearningController from './SocialLearningController';
import Step2RoleCompetencyController from './Step2RoleCompetencyController';
import StratosIntelligenceController from './StratosIntelligenceController';
import StratosIqController from './StratosIqController';
import StratosMapController from './StratosMapController';
import SuccessionController from './SuccessionController';
import SupportTicketController from './SupportTicketController';
import Talento360Controller from './Talento360Controller';
import TalentPassController from './TalentPassController';
import TalentSearchController from './TalentSearchController';
import TelemetryController from './TelemetryController';
import WhatIfAnalysisController from './WhatIfAnalysisController';

const Api = {
    CatalogsController: Object.assign(CatalogsController, CatalogsController),
    AssessmentController: Object.assign(
        AssessmentController,
        AssessmentController,
    ),
    RoleDesignerController: Object.assign(
        RoleDesignerController,
        RoleDesignerController,
    ),
    PublicJobController: Object.assign(
        PublicJobController,
        PublicJobController,
    ),
    ComplianceAuditController: Object.assign(
        ComplianceAuditController,
        ComplianceAuditController,
    ),
    TalentPassController: Object.assign(
        TalentPassController,
        TalentPassController,
    ),
    TalentSearchController: Object.assign(
        TalentSearchController,
        TalentSearchController,
    ),
    DevelopmentActionController: Object.assign(
        DevelopmentActionController,
        DevelopmentActionController,
    ),
    MentorshipSessionController: Object.assign(
        MentorshipSessionController,
        MentorshipSessionController,
    ),
    EvidenceController: Object.assign(EvidenceController, EvidenceController),
    PulseController: Object.assign(PulseController, PulseController),
    AgentController: Object.assign(AgentController, AgentController),
    JobOpeningController: Object.assign(
        JobOpeningController,
        JobOpeningController,
    ),
    ApplicationController: Object.assign(
        ApplicationController,
        ApplicationController,
    ),
    StratosIqController: Object.assign(
        StratosIqController,
        StratosIqController,
    ),
    MarketplaceController: Object.assign(
        MarketplaceController,
        MarketplaceController,
    ),
    MentorController: Object.assign(MentorController, MentorController),
    AuthController: Object.assign(AuthController, AuthController),
    DashboardController: Object.assign(
        DashboardController,
        DashboardController,
    ),
    BulkPeopleImportController: Object.assign(
        BulkPeopleImportController,
        BulkPeopleImportController,
    ),
    SuccessionController: Object.assign(
        SuccessionController,
        SuccessionController,
    ),
    MiStratosController: Object.assign(
        MiStratosController,
        MiStratosController,
    ),
    SmartAlertController: Object.assign(
        SmartAlertController,
        SmartAlertController,
    ),
    AlertController: Object.assign(AlertController, AlertController),
    GamificationController: Object.assign(
        GamificationController,
        GamificationController,
    ),
    SupportTicketController: Object.assign(
        SupportTicketController,
        SupportTicketController,
    ),
    SecurityAccessController: Object.assign(
        SecurityAccessController,
        SecurityAccessController,
    ),
    InvestorDashboardController: Object.assign(
        InvestorDashboardController,
        InvestorDashboardController,
    ),
    AssessmentCycleController: Object.assign(
        AssessmentCycleController,
        AssessmentCycleController,
    ),
    PeopleProfileController: Object.assign(
        PeopleProfileController,
        PeopleProfileController,
    ),
    DepartmentController: Object.assign(
        DepartmentController,
        DepartmentController,
    ),
    StratosMapController: Object.assign(
        StratosMapController,
        StratosMapController,
    ),
    RBACController: Object.assign(RBACController, RBACController),
    CulturalBlueprintController: Object.assign(
        CulturalBlueprintController,
        CulturalBlueprintController,
    ),
    PxCampaignController: Object.assign(
        PxCampaignController,
        PxCampaignController,
    ),
    GapAnalysisController: Object.assign(
        GapAnalysisController,
        GapAnalysisController,
    ),
    DevelopmentPathController: Object.assign(
        DevelopmentPathController,
        DevelopmentPathController,
    ),
    ScenarioController: Object.assign(ScenarioController, ScenarioController),
    PromptInstructionController: Object.assign(
        PromptInstructionController,
        PromptInstructionController,
    ),
    SocialLearningController: Object.assign(
        SocialLearningController,
        SocialLearningController,
    ),
    ChangeSetController: Object.assign(
        ChangeSetController,
        ChangeSetController,
    ),
    ScenarioGenerationController: Object.assign(
        ScenarioGenerationController,
        ScenarioGenerationController,
    ),
    ScenarioGenerationAbacusController: Object.assign(
        ScenarioGenerationAbacusController,
        ScenarioGenerationAbacusController,
    ),
    ScenarioGenerationIntelController: Object.assign(
        ScenarioGenerationIntelController,
        ScenarioGenerationIntelController,
    ),
    GenerationChunkController: Object.assign(
        GenerationChunkController,
        GenerationChunkController,
    ),
    ScenarioAnalyticsController: Object.assign(
        ScenarioAnalyticsController,
        ScenarioAnalyticsController,
    ),
    ScenarioApprovalController: Object.assign(
        ScenarioApprovalController,
        ScenarioApprovalController,
    ),
    ScenarioSimulationController: Object.assign(
        ScenarioSimulationController,
        ScenarioSimulationController,
    ),
    ScenarioRoiController: Object.assign(
        ScenarioRoiController,
        ScenarioRoiController,
    ),
    ScenarioStrategyController: Object.assign(
        ScenarioStrategyController,
        ScenarioStrategyController,
    ),
    IncubationController: Object.assign(
        IncubationController,
        IncubationController,
    ),
    CompetencyMaterializerController: Object.assign(
        CompetencyMaterializerController,
        CompetencyMaterializerController,
    ),
    Talento360Controller: Object.assign(
        Talento360Controller,
        Talento360Controller,
    ),
    CompetencyCuratorController: Object.assign(
        CompetencyCuratorController,
        CompetencyCuratorController,
    ),
    MobilitySimulationController: Object.assign(
        MobilitySimulationController,
        MobilitySimulationController,
    ),
    ExecutionTrackingController: Object.assign(
        ExecutionTrackingController,
        ExecutionTrackingController,
    ),
    LmsController: Object.assign(LmsController, LmsController),
    TelemetryController: Object.assign(
        TelemetryController,
        TelemetryController,
    ),
    CapabilityCompetencyController: Object.assign(
        CapabilityCompetencyController,
        CapabilityCompetencyController,
    ),
    ScenarioTemplateController: Object.assign(
        ScenarioTemplateController,
        ScenarioTemplateController,
    ),
    WhatIfAnalysisController: Object.assign(
        WhatIfAnalysisController,
        WhatIfAnalysisController,
    ),
    ExecutiveSummaryController: Object.assign(
        ExecutiveSummaryController,
        ExecutiveSummaryController,
    ),
    ExportController: Object.assign(ExportController, ExportController),
    OrgChartController: Object.assign(OrgChartController, OrgChartController),
    Step2RoleCompetencyController: Object.assign(
        Step2RoleCompetencyController,
        Step2RoleCompetencyController,
    ),
    ImpactReportController: Object.assign(
        ImpactReportController,
        ImpactReportController,
    ),
    ScenarioIQController: Object.assign(
        ScenarioIQController,
        ScenarioIQController,
    ),
    StratosIntelligenceController: Object.assign(
        StratosIntelligenceController,
        StratosIntelligenceController,
    ),
    PxController: Object.assign(PxController, PxController),
    RAGASEvaluationController: Object.assign(
        RAGASEvaluationController,
        RAGASEvaluationController,
    ),
    RagController: Object.assign(RagController, RagController),
    AgentInteractionMetricsController: Object.assign(
        AgentInteractionMetricsController,
        AgentInteractionMetricsController,
    ),
    IntelligenceAggregatesController: Object.assign(
        IntelligenceAggregatesController,
        IntelligenceAggregatesController,
    ),
    AnalyticsController: Object.assign(
        AnalyticsController,
        AnalyticsController,
    ),
    AutomationController: Object.assign(
        AutomationController,
        AutomationController,
    ),
    MobileController: Object.assign(MobileController, MobileController),
    Messaging: Object.assign(Messaging, Messaging),
    AdminOperationsController: Object.assign(
        AdminOperationsController,
        AdminOperationsController,
    ),
    AuditController: Object.assign(AuditController, AuditController),
    Automation: Object.assign(Automation, Automation),
};

export default Api;
