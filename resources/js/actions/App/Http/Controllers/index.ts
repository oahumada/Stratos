import Api from './Api'
import CompetencyVersionController from './CompetencyVersionController'
import TransformCompetencyController from './TransformCompetencyController'
import Neo4jSyncController from './Neo4jSyncController'
import Settings from './Settings'

const Controllers = {
    Api: Object.assign(Api, Api),
    CompetencyVersionController: Object.assign(CompetencyVersionController, CompetencyVersionController),
    TransformCompetencyController: Object.assign(TransformCompetencyController, TransformCompetencyController),
    Neo4jSyncController: Object.assign(Neo4jSyncController, Neo4jSyncController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers