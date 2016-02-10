<?php
namespace timetracker\app\module\session\controller;

use chilimatic\lib\database\sql\orm\EntityManager;
use chilimatic\lib\di\ClosureFactory;
use chilimatic\lib\transformer\time\DateDiffToDecimalTime;
use timetracker\app\module\main\controller\Application;
use timetracker\app\module\session\model\Session;

class Index extends Application
{

    public function startAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        $request = ClosureFactory::getInstance()->get('request-handler', []);
        if (!$request->getRaw()) {
            $this->errorMessage('error', _('no request data'));
            return;
        }

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');

        $session = new Session();
        $session->setProjectId((int) $request->getRaw()->get('projectId'));
        $session->setUserId($this->user->getUser()->getId());
        $session->setStartTime(date('Y-m-d H:i:s'));
        $session->setCreated(date('Y-m-d H:i:s'));
        $session->setModified(date('Y-m-d H:i:s'));
        $session->setTimeDiff(0);

        if ($em->persist($session)) {
            $this->successMessage('session-started', _('Session successfully started!'), null, $session);
        } else {
            $this->errorMessage('session-start-failed', _('Session could not be started'));
        }
    }

    public function endAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        $request = ClosureFactory::getInstance()->get('request-handler', []);
        if (!$request->getRaw()) {
            $this->errorMessage('error', _('no request data'));
            return;
        }

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');

        /**
         * @var Session $session
         */
        $session = $em->findOneBy(new Session(), ['id' => (int) $request->getRaw()->get('sessionId')]);

        if (!$session->getId()) {
            $this->errorMessage('error', _('no session found'));
            return;
        }

        $endTime = new \DateTime('now');
        $startTime = new \DateTime($session->getStartTime());
        $diff = $startTime->diff($endTime);
        $session->setEndTime($endTime->format('Y-m-d H:i:s'));
        $transformer = new DateDiffToDecimalTime();


        $session->setTimeDiff($transformer->transform($diff));

        if ($em->persist($session)) {
            $this->successMessage('session-ended', _('Session successfully ended!'), null, $session);
        } else {
            $this->errorMessage('session-end-failed', _('Session could not be ended!'));
        }
    }

}