<?php
namespace timetracker\app\module\user\decorator;
use \timetracker\app\module\user\model\User as UserModel;

/**
 * Class User
 *
 * @package timetracker\app\module\user\decorator
 */
class User implements \JsonSerializable{

    /**
     * @var bool
     */
    protected $connected;

    /**
     * @var UserModel
     */
    protected $user;

    /**
     * @param UserModel $user
     */
    public function __constructor(UserModel $user = null) {
        $this->user = $user;
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param boolean $connected
     *
     * @return $this
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * @return UserModel
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return $this
     */
    public function setUser(UserModel $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        if (!$this->user) {
            return null;
        }

        return $this->user->getId();
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'user' => $this->user,
            'connected' => $this->connected
        ];
    }
}