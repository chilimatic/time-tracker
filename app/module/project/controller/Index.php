<?php
/**
 * @author  j
 * @created : 2/9/16 10:51 AM
 * @project : time-tracker
 */

namespace timetracker\app\module\project\controller;

use chilimatic\lib\database\sql\orm\EntityManager;
use chilimatic\lib\Di\ClosureFactory;
use timetracker\app\module\main\controller\Application;
use timetracker\app\module\project\model\Project;
use timetracker\app\module\project\model\UserProjectMap;
use timetracker\app\module\session\model\Session;
use timetracker\app\module\session\model\SessionDescription;

/**
 * Class Index
 *
 * @package timetracker\app\module\project\controller
 */
class Index extends Application
{
    public function getListAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');

        $projectMapList = $em->findBy(new UserProjectMap(), ['user_id' => $this->user->getUser()->getId()]);
        $set = [];
        foreach ($projectMapList as $project) {
            $set[] = $project->getProjectId();
        }

        $projectList = $em->findBy(new Project(), ['id' => $set]);

        $this->getView()->response = [ 'projectList' => $projectList ];
    }


    public function getDetailAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        $request = ClosureFactory::getInstance()->get('request-handler', []);
        if (!$request->getGet()) {
            $this->errorMessage('getting-detail-failed', _('Please enter project-name!'));
            return;
        }

        $projectName = $request->getGet()->get('name');

        if (!$projectName) {
            $this->errorMessage('getting-detail-failed', _('Please enter project-name!'));
            return;
        }


        $projectName = urldecode($projectName);

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');
        $project = $em->findOneBy(new Project(), ['name' => $projectName]);

        if (!$project->getId()) {
            $this->errorMessage('getting-detail-failed', _('Please enter project-name!'));
            return;
        }

        $sessionDataList = $em->findBy(
            new Session(),
            [
                'project_id' => $project->getId(),
                'user_id' => $this->user->getUser()->getId()
            ]
        );

        // template structure
        $idSet = [];
        $sessionDataMap = [];

        /**
         * @var Session $session
         */
        foreach ($sessionDataList as $session)
        {
            $id = $session->getId();
            $idSet[] = $id;
            $sessionDataMap[$id] = $session;
        }

        $idSetString = implode(',', $idSet);
        $query = "SELECT * FROM `session_description` WHERE `session_id` IN ($idSetString)";
        unset($idSet);

        /**
         * @var \PDOStatement $stmt
         */
        $stmt = $em->db->query($query);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $sessionDescription = new SessionDescription();

        foreach ($result as $row) {
            $sd = clone $sessionDescription;
            $sd->setText($row['text']);
            $sd->setSessionId($row['session_id']);
            $sd->setCreated($row['created']);
            $sd->setModified($row['modified']);

            $sessionDataMap[$row['session_id']]->setSessionDescription($sd);
        }

        $this->successMessage(
            'project-loaded',
            _('Project successfully loaded'),
            null,
            [
                'project'       => $project,
                'sessionList'   => $sessionDataMap
            ]
        );
    }


    public function newAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        $request = ClosureFactory::getInstance()->get('request-handler', []);
        if (!$request->getRaw()) {
            $this->errorMessage('creation-failed', _('Please enter project-name!'));
            return;
        }

        $projectName = $request->getRaw()->get('name');

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');
        $project = $em->findOneBy(new Project(), ['name' => $projectName]);

        if ($project->getId()) {
            $this->errorMessage('creation-failed', _('Project-name already exists!'));
            return;
        }
        $dateTime = new \DateTime();

        $projectNew = clone $project;
        /**
         * @var Project $projectNew
         */
        $projectNew->setName($projectName);
        $projectNew->setCreated($dateTime->format('Y-m-d H:i:s'));
        $projectNew->setModified($dateTime->format('Y-m-d H:i:s'));

        if (!$em->persist($projectNew)) {
            $this->errorMessage('project-creation-failed', _('Project could not be created'));
        }

        $userProjectMap = new UserProjectMap();
        $userProjectMap->setProjectId($projectNew->getId());
        $userProjectMap->setUserId($this->getUser()->getUser()->getId());

        if ($em->persist($userProjectMap)) {
            $this->successMessage('project-created', _('Project successfully created'), null, $projectNew);
        } else {
            $this->errorMessage('project-creation-failed', _('Project could not be created'));
        }

    }


    public function deleteAction()
    {
        try {
            if (!$this->loadUserFromSession()) {
                $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
                return;
            }

            $request = ClosureFactory::getInstance()->get('request-handler', []);
            if (!$request->getRaw()) {
                $this->errorMessage('creation-failed', _('Please enter project-name!'));
                return;
            }

            $projectId = (int) $request->getRaw()->get('projectId');

            /**
             * @var EntityManager $em
             */
            $em = ClosureFactory::getInstance()->get('entity-manager');
            $project = $em->findOneBy(new Project(), ['id' => $projectId]);

            if (!$project->getId()) {
                $this->errorMessage('project-deletion-failed', _('Project-name does not exist!'));
                return;
            }

            if ($em->delete($project)) {
                $this->successMessage('project-deleted', _('Project successfully deleted'));
            } else {
                $this->errorMessage('project-deletion-failed', _('Project could not be deleted'));
            }
        } catch (\Exception $e) {

            $log = ClosureFactory::getInstance()->get('error-log', []);
            $log->log($e->getMessage());

            $this->errorMessage('project-deletion-failed', _('Project could not be deleted'));
        }
    }
}