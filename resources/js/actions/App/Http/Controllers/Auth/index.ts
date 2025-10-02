import RegisterController from './RegisterController'
import LoginController from './LoginController'
import ForgotPasswordController from './ForgotPasswordController'
import ResetPasswordController from './ResetPasswordController'
import ConfirmPasswordController from './ConfirmPasswordController'
import LogoutController from './LogoutController'

const Auth = {
    RegisterController: Object.assign(RegisterController, RegisterController),
    LoginController: Object.assign(LoginController, LoginController),
    ForgotPasswordController: Object.assign(ForgotPasswordController, ForgotPasswordController),
    ResetPasswordController: Object.assign(ResetPasswordController, ResetPasswordController),
    ConfirmPasswordController: Object.assign(ConfirmPasswordController, ConfirmPasswordController),
    LogoutController: Object.assign(LogoutController, LogoutController),
}

export default Auth