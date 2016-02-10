<?php
namespace timetracker\app\module\user\service;

/**
 *
 * @author j
 * Date: 2/18/15
 * Time: 7:19 PM
 *
 * File: authentification.class.php
 */


use chilimatic\lib\di\ClosureFactory;
use timetracker\app\module\user\decorator\User;

/**
 * Class Authentification
 *
 * @package \app\service
 */
class Authentification
{
    /**
     * @var \chilimatic\lib\session\handler\Session
     */
    private $sessionHandler;


    public function __construct(){
        $this->sessionHandler = ClosureFactory::getInstance()->get('session', [], true);
    }

    /**
     * @param int $userId
     *
     * @return User|null
     */
    public function getUserById($userId)
    {
        if (!$userId) {
            return null;
        }


        $em = ClosureFactory::getInstance()->get('entity-manager');
        $userModel = $em->findOneBy(new \timetracker\app\module\user\model\User(), [
            'id' => $userId
        ]);

        $user = new User();
        $user->setUser($userModel)->setConnected(true);

        return $user;
    }


    /**
     * @param string $username
     *
     * @return null
     */
    public function getUserByUsername($username)
    {
        if (!$username) {
            return null;
        }

        $em = ClosureFactory::getInstance()->get('entity-manager');

        return $em->findOneBy(new \timetracker\app\module\user\model\User(), [
            'name' => $username
        ]);

    }

    /**
     * loads the user from session
     * we always get the user via DB abstraction
     *
     * @return bool|mixed
     */
    public function getUserFromSession() {
        if (!$this->sessionHandler->get('user')) {
            return null;
        }

        return $this->getUserById($this->sessionHandler->get('user'));
    }
}