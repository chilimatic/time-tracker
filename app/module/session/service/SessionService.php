<?php
namespace timetracker\app\module\session\service;


use timetracker\app\module\session\model\Session;
use timetracker\app\module\session\model\SessionDescription;

class SessionService
{
    private $entityManager;

    private $di;

    private $errorLog;

    /**
     * SessionService constructor.
     * @param \chilimatic\lib\Di\ClosureFactory $di
     */
    public function __construct(\chilimatic\lib\Di\ClosureFactory $di)
    {
        $this->di = $di;
        $this->errorLog = new \SplQueue();
    }

    /**
     * @param array $sessionData
     */
    public function save(array $sessionData)
    {
        /**
         * @var Session $session
         */
        $session = $this->getEntityManager()->findOneBy(
            new Session(),
            [
                'id' => (int) $sessionData['id']
            ]
        );

        if (!$session->getId()) {
            $this->errorLog->enqueue(_('No session with this id has been found'));
            return false;
        }


        if (array_key_exists('sessionDescription', $sessionData))
        {
            $sessionDescriptionModel = new SessionDescription();


            if (!isset($sessionData['sessionDescription']['session_id'])) {
                $sessionDescriptionModel->setSessionId($session->getId());
                $sessionDescriptionModel->setText($sessionData['sessionDescription']['text']);
                $sessionDescriptionModel->setCreated(date('Y-m-d H:i:s'));
                $sessionDescriptionModel->setModified(date('Y-m-d H:i:s'));
                if (!$this->getEntityManager()->persist($sessionDescriptionModel)) {
                    $this->errorLog->enqueue(_('Session Description could not be saved'));
                }
            } else {
                /**
                 * @var $sessionDescriptionModel SessionDescription
                 */
                $sessionDescriptionModel = $this->getEntityManager()->findOneBy($sessionDescriptionModel, [
                    'session_id' => (int) $sessionData['sessionDescription']['session_id']
                ]);
                if ($sessionDescriptionModel->getText() != $sessionData['sessionDescription']['text']) {
                    $sessionDescriptionModel->setText($sessionData['sessionDescription']['text']);
                    $sessionDescriptionModel->setModified(date('Y-m-d H:i:s'));

                    if (!$this->getEntityManager()->persist($sessionDescriptionModel)) {
                        $this->errorLog->enqueue(_('Session Description could not be saved'));
                    }
                }
            }
            $session->setSessionDescription($sessionDescriptionModel);
        }

        $session->setStartTime($sessionData['startTime']);
        $session->setEndTime($sessionData['endTime']);
        $session->setTimeDiff($sessionData['timeDiff']);
        $session->setModified(date('Y-m-d H:i:s'));
        $session->setDone((bool) $sessionData['done']);

        if ($this->getEntityManager()->persist($session)) {
            return $session;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->di->get('entity-manager');
        }
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }
}