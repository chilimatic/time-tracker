<?php
namespace timetracker\app\module\user\controller;

/**
 * Created by PhpStorm.
 * User: j
 * Date: 02.12.14
 * Time: 23:04
 */

use timetracker\app\module\main\controller\Application;
use chilimatic\lib\di\ClosureFactory;

/**
 * Class User
 *
 * @package timetracker\app\module\user\controller
 */
class User extends Application
{

    /**
     * @view \chilimatic\lib\view\PHtml()
     */
    public function indexAction() {
        $this->getView()->pageTitle = 'chilimatic Admin';
    }


    /**
     * @view \chilimatic\lib\view\Json()
     *
     * @return bool
     */
    public function checkValidSessionAction()
    {
        $user = $this->authentification->getUserFromSession();
        if (!$user) {
            $this->errorMessage('session_invalid', _('Session is invalid'), null, ['logout' => true]);
            return false;
        }

        $this->successMessage('session_valid', _('Session is valid'));
        return true;
    }


    /**
     * logs out the user and triggers the
     * logout javascript
     *
     * @return null
     */
    public function logoutAction() {
        if (!$this->authentification->getUserFromSession()) {
            return null;
        } else {
            $this->session->delete('user');
        }

        $this->successMessage('logout', _('Logout was successful'));
    }


    /**
     * @return void
     */
    public function loginAction()
    {
        $request = ClosureFactory::getInstance()->get('request-handler', []);
        if (!$request->getRaw()) {
            $this->errorMessage('login_failed', _('Please enter username and Password!'));
            return;
        }

        /**
         * @var \timetracker\app\module\user\service\Authentification $authentificationService
         */
        $authentificationService = ClosureFactory::getInstance()->get('authentication-service', [], true);

        /**
         * @var \timetracker\app\module\user\model\User $user
         */
        $user = $authentificationService->getUserByUsername(
            $request->getRaw()->get('username')
        );

        if (!$user || $user->getId() === null) {
            $this->errorMessage('login_failed', _('Username or Password invalid!'));
            return;
        }

        // the same error so we can find out what happened but the user does not get a hint what part has been faulty
        if (!password_verify($request->getRaw()->get('password'), $user->getPassword())) {
            $this->errorMessage('login_failed', _('Username or Password invalid!'));
            return;
        }

        $this->user = new \timetracker\app\module\user\decorator\User();
        $this->user->setUser($user);
        $this->user->setConnected(true);


        $this->session->set('user', $user->getId());
        $this->getView()->response = [ 'user' => $this->user ];
    }
}