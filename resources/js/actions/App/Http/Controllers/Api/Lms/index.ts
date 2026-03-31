import CmsArticleController from './CmsArticleController'
import CertificateController from './CertificateController'

const Lms = {
    CmsArticleController: Object.assign(CmsArticleController, CmsArticleController),
    CertificateController: Object.assign(CertificateController, CertificateController),
}

export default Lms