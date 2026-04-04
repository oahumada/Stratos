import CmsArticleController from './CmsArticleController'
import CertificateController from './CertificateController'
import CourseController from './CourseController'
import AnalyticsController from './AnalyticsController'
import InterventionController from './InterventionController'
import CourseDesignerController from './CourseDesignerController'
import QuizController from './QuizController'
import LearningPathController from './LearningPathController'
import ScormPlayerController from './ScormPlayerController'
import ComplianceController from './ComplianceController'
import ReportController from './ReportController'
import CatalogController from './CatalogController'
import DiscussionController from './DiscussionController'
import XApiController from './XApiController'

const Lms = {
    CmsArticleController: Object.assign(CmsArticleController, CmsArticleController),
    CertificateController: Object.assign(CertificateController, CertificateController),
    CourseController: Object.assign(CourseController, CourseController),
    AnalyticsController: Object.assign(AnalyticsController, AnalyticsController),
    InterventionController: Object.assign(InterventionController, InterventionController),
    CourseDesignerController: Object.assign(CourseDesignerController, CourseDesignerController),
    QuizController: Object.assign(QuizController, QuizController),
    LearningPathController: Object.assign(LearningPathController, LearningPathController),
    ScormPlayerController: Object.assign(ScormPlayerController, ScormPlayerController),
    ComplianceController: Object.assign(ComplianceController, ComplianceController),
    ReportController: Object.assign(ReportController, ReportController),
    CatalogController: Object.assign(CatalogController, CatalogController),
    DiscussionController: Object.assign(DiscussionController, DiscussionController),
    XApiController: Object.assign(XApiController, XApiController),
}

export default Lms