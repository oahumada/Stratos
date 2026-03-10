import AgentController from './AgentController';
import ApplicationController from './ApplicationController';
import AssessmentController from './AssessmentController';
import AssessmentCycleController from './AssessmentCycleController';
import AuthController from './AuthController';
import Automation from './Automation';
import CapabilityCompetencyController from './CapabilityCompetencyController';
import CatalogsController from './CatalogsController';
import ChangeSetController from './ChangeSetController';
import CompetencyCuratorController from './CompetencyCuratorController';
import DashboardController from './DashboardController';
import DepartmentController from './DepartmentController';
import DevelopmentActionController from './DevelopmentActionController';
import DevelopmentPathController from './DevelopmentPathController';
import EvidenceController from './EvidenceController';
import GamificationController from './GamificationController';
import GapAnalysisController from './GapAnalysisController';
import GenerationChunkController from './GenerationChunkController';
import ImpactReportController from './ImpactReportController';
import IncubationController from './IncubationController';
import InvestorDashboardController from './InvestorDashboardController';
import JobOpeningController from './JobOpeningController';
import LmsController from './LmsController';
import MarketplaceController from './MarketplaceController';
import MentorController from './MentorController';
import MentorshipSessionController from './MentorshipSessionController';
import MiStratosController from './MiStratosController';
import MobilitySimulationController from './MobilitySimulationController';
import PeopleProfileController from './PeopleProfileController';
import PromptInstructionController from './PromptInstructionController';
import PublicJobController from './PublicJobController';
import PulseController from './PulseController';
import PxCampaignController from './PxCampaignController';
import PxController from './PxController';
import RAGASEvaluationController from './RAGASEvaluationController';
import RBACController from './RBACController';
import RoleDesignerController from './RoleDesignerController';
import ScenarioController from './ScenarioController';
import ScenarioGenerationAbacusController from './ScenarioGenerationAbacusController';
import ScenarioGenerationController from './ScenarioGenerationController';
import ScenarioGenerationIntelController from './ScenarioGenerationIntelController';
import ScenarioIQController from './ScenarioIQController';
import ScenarioRoiController from './ScenarioRoiController';
import ScenarioSimulationController from './ScenarioSimulationController';
import ScenarioStrategyController from './ScenarioStrategyController';
import ScenarioTemplateController from './ScenarioTemplateController';
import SmartAlertController from './SmartAlertController';
import SocialLearningController from './SocialLearningController';
import Step2RoleCompetencyController from './Step2RoleCompetencyController';
import StratosIntelligenceController from './StratosIntelligenceController';
import SupportTicketController from './SupportTicketController';
import Talento360Controller from './Talento360Controller';
import TelemetryController from './TelemetryController';

const Api = {
    CatalogsController: Object.assign(CatalogsController, CatalogsController),
    AssessmentController: Object.assign(
        AssessmentController,
        AssessmentController,
    ),
    PublicJobController: Object.assign(
        PublicJobController,
        PublicJobController,
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
    DashboardController: Object.assign(
        DashboardController,
        DashboardController,
    ),
    MarketplaceController: Object.assign(
        MarketplaceController,
        MarketplaceController,
    ),
    MentorController: Object.assign(MentorController, MentorController),
    AuthController: Object.assign(AuthController, AuthController),
    MiStratosController: Object.assign(
        MiStratosController,
        MiStratosController,
    ),
    SmartAlertController: Object.assign(
        SmartAlertController,
        SmartAlertController,
    ),
    GamificationController: Object.assign(
        GamificationController,
        GamificationController,
    ),
    SupportTicketController: Object.assign(
        SupportTicketController,
        SupportTicketController,
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
    RBACController: Object.assign(RBACController, RBACController),
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
    RoleDesignerController: Object.assign(
        RoleDesignerController,
        RoleDesignerController,
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
    Automation: Object.assign(Automation, Automation),
};

export default Api;
