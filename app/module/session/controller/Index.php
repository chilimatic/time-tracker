<?php
namespace timetracker\app\module\session\controller;

use chilimatic\lib\database\sql\orm\EntityManager;
use chilimatic\lib\Di\ClosureFactory;
use chilimatic\lib\Transformer\Time\DateDiffToDecimalTime;
use timetracker\app\module\main\controller\Application;
use timetracker\app\module\session\model\Session;
use timetracker\app\module\session\model\SessionDescription;
use timetracker\app\module\session\service\SessionService;

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
        $session->setUserId($this->getUser()->getUserId());
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

    
    public function saveSessionAction()
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

        $sessionRequest =  $request->getRaw()->get('session', null, null);

        if (!$sessionRequest) {
            $this->errorMessage('session-end-failed', _('Session data is empty'));
        }
        $sessionService = new SessionService(ClosureFactory::getInstance());

        $session = $sessionService->save($sessionRequest);


        if ($session) {
            $this->successMessage('session-ended', _('Session successfully ended!'), null, $session);
        } else {
            $this->errorMessage('session-end-failed', _('Session could not be ended!'));
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

        $sessionRequest =  $request->getRaw()->get('session', null, null);

        if (!$sessionRequest) {
            $this->errorMessage('session-end-failed', _('Session data is empty'));
        }
        $sessionService = new SessionService(ClosureFactory::getInstance());
        $transformer = new DateDiffToDecimalTime();

        $endTime = new \DateTime('now');
        $startTime = new \DateTime($sessionRequest['startTime']);

        $sessionRequest['endTime'] = $endTime->format('Y-m-d H:i:s');
        $sessionRequest['startTime'] = $startTime->format('Y-m-d H:i:s');
        $sessionRequest['timeDiff'] = $transformer->transform($startTime->diff($endTime));
        $session = $sessionService->save($sessionRequest);
        if ($session) {
            $this->successMessage('session-ended', _('Session successfully ended!'), null, $session);
        } else {
            $this->errorMessage('session-end-failed', _('Session could not be ended!'));
        }
    }

}