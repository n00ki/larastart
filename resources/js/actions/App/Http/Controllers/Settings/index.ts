import ProfileController from './ProfileController'
import PasswordController from './PasswordController'
import AccountController from './AccountController'

const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    PasswordController: Object.assign(PasswordController, PasswordController),
    AccountController: Object.assign(AccountController, AccountController),
}

export default Settings