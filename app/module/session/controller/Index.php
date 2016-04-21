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

    public function getUserStatisticAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        $request = ClosureFactory::getInstance()->get('request-handler', []);
        $result = [];



        $month = $request->getGet()->get('month', 'string', date('m'));
        $year = $request->getGet()->get('year', 'string', date('Y'));

        echo $month;
        echo $year;

        /**
         * @var EntityManager $em
         */
        $db = ClosureFactory::getInstance()->get('db');
        $con = $db->getConnectionByPosition(0);
        $dbh = $con->getDbAdapter();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("
SELECT 
  project_id, MONTH(start_time) AS sessionMonth, DAY(start_time) AS sessionDay, SUM(time_diff) AS hour_sum FROM `session` 
WHERE 
  user_id = :userId AND MONTH(start_time) = :month AND YEAR(start_time) = :year
GROUP BY 
  sessionDay, sessionMonth"
        );

        $stmt->bindValue('userId', $this->getUser()->getUserId(), \PDO::PARAM_INT);
        $stmt->bindValue('year', $year, \PDO::PARAM_INT);
        $stmt->bindValue('month', $month, \PDO::PARAM_INT);


        if ($stmt->execute()) {
            $set = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            /**
             * @var array $entry
             */
            foreach ($set as $entry) {
                $result[] = [
                    'key' => "{$entry['sessionMonth']}-{$entry['sessionDay']}",
                    'values' => [['x' => 1, 'y' => (float) $entry['hour_sum']]]
                ];
            }
        }


        $this->getView()->timeDiff = $result;
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