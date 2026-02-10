import Api from './Api'
import CompetencyVersionController from './CompetencyVersionController'
import TransformCompetencyController from './TransformCompetencyController'
import Settings from './Settings'

const Controllers = {
    Api: Object.assign(Api, Api),
    CompetencyVersionController: Object.assign(CompetencyVersionController, CompetencyVersionController),
    TransformCompetencyController: Object.assign(TransformCompetencyController, TransformCompetencyController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers