import User from './User'
import Auth from './Auth'

const Controllers = {
    User: Object.assign(User, User),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers