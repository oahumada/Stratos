import MagicLinkController from './MagicLinkController'
import SsoController from './SsoController'

const Auth = {
    MagicLinkController: Object.assign(MagicLinkController, MagicLinkController),
    SsoController: Object.assign(SsoController, SsoController),
}

export default Auth