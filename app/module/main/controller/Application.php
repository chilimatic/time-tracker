<?php
/**
 *
 * @author j
 * Date: 12/29/14
 * Time: 5:49 PM
 *
 * File: chilimatic.class.php
 */

namespace timetracker\app\module\main\controller;

use chilimatic\lib\controller\HTTPController;
use chilimatic\lib\di\ClosureFactory;
use chilimatic\lib\view\AbstractView;

class Application extends HTTPController
{
    /**
     * @var mixed
     */
    protected $session;

    /**
     * @var \timetracker\app\module\user\decorator\User
     */
    protected $user;

    /**
     * @var \timetracker\app\module\user\service\authentification
     */
    protected $authentification;

    /**
     * @param AbstractView $view
     */
    public function __construct(AbstractView $view = null) {

        $config = ClosureFactory::getInstance()->get('config');

        $this->session = ClosureFactory::getInstance()->get('session',
            [
                'type' => 'cache',
                'param' =>
                [
                    'session_cache' => $config->get('session_cache')
                ]
            ],
            true
        );

        $this->authentification = ClosureFactory::getInstance()->get('authentication-service', [], true);
        parent::__construct($view);
    }

    /**
     * @return bool
     */
    public function loadUserFromSession()
    {
        if (!($user = $this->authentification->getUserFromSession())) {
            return false;
        }

        if (!$user instanceof \timetracker\app\module\user\decorator\User) {
            return false;
        }

        $this->user = $user;

        return true;
    }


    /**
     * @param $reason
     * @param $msg
     * @param $jsCallback
     * @param $data
     *
     * @view \chilimatic\lib\view\Json
     *
     * @return void
     */
    protected function errorMessage($reason, $msg, $jsCallback = null, $data = null)
    {
        $this->getView()->response = [
            'error' =>
                [
                    'reason' => $reason,
                    'msg' => $msg
                ],
            'call' => $jsCallback,
            'data' => $data
        ];
    }


    /**
     * @param string $reason
     * @param string $msg
     * @param string $jsCallback
     * @param mixed $data
     *
     * @return void
     */
    protected function successMessage($reason, $msg, $jsCallback = null, $data = null)
    {
        $this->getView()->response = [
            'success' => $reason,
            'msg' => $msg,
            'call' => $jsCallback,
            'data' => $data
        ];
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     *
     * @return $this
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return \timetracker\app\module\user\decorator\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \timetracker\app\module\user\decorator\User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \timetracker\app\module\user\service\authentification
     */
    public function getAuthentification()
    {
        return $this->authentification;
    }

    /**
     * @param \timetracker\app\module\user\service\authentification $authentification
     *
     * @return $this
     */
    public function setAuthentification($authentification)
    {
        $this->authentification = $authentification;

        return $this;
    }
}