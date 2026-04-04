import CmsArticleController from './CmsArticleController'
import CertificateController from './CertificateController'
import CourseController from './CourseController'
import AnalyticsController from './AnalyticsController'
import InterventionController from './InterventionController'
import CourseDesignerController from './CourseDesignerController'
import QuizController from './QuizController'
import LearningPathController from './LearningPathController'
import ScormPlayerController from './ScormPlayerController'

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
}

export default Lms