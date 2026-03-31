import Api from './Api';
import Auth from './Auth';
import CompetencyVersionController from './CompetencyVersionController';
import Deployment from './Deployment';
import Intelligence from './Intelligence';
import Neo4jSyncController from './Neo4jSyncController';
import Settings from './Settings';
import TransformCompetencyController from './TransformCompetencyController';

const Controllers = {
    Api: Object.assign(Api, Api),
    Intelligence: Object.assign(Intelligence, Intelligence),
    CompetencyVersionController: Object.assign(
        CompetencyVersionController,
        CompetencyVersionController,
    ),
    TransformCompetencyController: Object.assign(
        TransformCompetencyController,
        TransformCompetencyController,
    ),
    Neo4jSyncController: Object.assign(
        Neo4jSyncController,
        Neo4jSyncController,
    ),
    Deployment: Object.assign(Deployment, Deployment),
    Auth: Object.assign(Auth, Auth),
    Settings: Object.assign(Settings, Settings),
};

export default Controllers;
