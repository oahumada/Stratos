import Api from './Api'
import Intelligence from './Intelligence'
import CompetencyVersionController from './CompetencyVersionController'
import TransformCompetencyController from './TransformCompetencyController'
import Neo4jSyncController from './Neo4jSyncController'
import Auth from './Auth'
import Settings from './Settings'

const Controllers = {
    Api: Object.assign(Api, Api),
    Intelligence: Object.assign(Intelligence, Intelligence),
    CompetencyVersionController: Object.assign(CompetencyVersionController, CompetencyVersionController),
    TransformCompetencyController: Object.assign(TransformCompetencyController, TransformCompetencyController),
    Neo4jSyncController: Object.assign(Neo4jSyncController, Neo4jSyncController),
    Auth: Object.assign(Auth, Auth),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers