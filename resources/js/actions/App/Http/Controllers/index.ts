import Api from './Api'
import HomeController from './HomeController'
import Public from './Public'
import DashboardController from './DashboardController'
import StartupController from './StartupController'
import ConnectionController from './ConnectionController'
import Settings from './Settings'

const Controllers = {
    Api: Object.assign(Api, Api),
    HomeController: Object.assign(HomeController, HomeController),
    Public: Object.assign(Public, Public),
    DashboardController: Object.assign(DashboardController, DashboardController),
    StartupController: Object.assign(StartupController, StartupController),
    ConnectionController: Object.assign(ConnectionController, ConnectionController),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers