import Settings from './Settings'
import Auth from './Auth'

const Controllers = {
    Settings: Object.assign(Settings, Settings),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers