import LeaderboardController from './LeaderboardController'
import StartupDirectoryController from './StartupDirectoryController'
import StartupProfileController from './StartupProfileController'
import MarketplaceController from './MarketplaceController'
import FounderProfileController from './FounderProfileController'
import BadgeController from './BadgeController'
import PageController from './PageController'

const Public = {
    LeaderboardController: Object.assign(LeaderboardController, LeaderboardController),
    StartupDirectoryController: Object.assign(StartupDirectoryController, StartupDirectoryController),
    StartupProfileController: Object.assign(StartupProfileController, StartupProfileController),
    MarketplaceController: Object.assign(MarketplaceController, MarketplaceController),
    FounderProfileController: Object.assign(FounderProfileController, FounderProfileController),
    BadgeController: Object.assign(BadgeController, BadgeController),
    PageController: Object.assign(PageController, PageController),
}

export default Public