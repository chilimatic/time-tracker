<?php
/**
 * @author  j
 * @created : 2/9/16 10:51 AM
 * @project : time-tracker
 */

namespace timetracker\app\module\project\controller;

use chilimatic\lib\database\sql\orm\EntityManager;
use chilimatic\lib\di\ClosureFactory;
use timetracker\app\module\main\controller\Application;
use timetracker\app\module\project\model\Project;
use timetracker\app\module\project\model\UserProjectMap;
use timetracker\app\module\session\model\Session;

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
        if (!$request->getRaw()) {
            $this->errorMessage('getting-detail-failed', _('Please enter project-name!'));
            return;
        }

        $projectName = $request->getGet()->get('name');

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');
        $project = $em->findOneBy(new Project(), ['name' => $projectName]);

        $sessionData = $em->findBy(
            new Session(),
            [
                'project_id' => $project->getId(),
                'user_id' => $this->user->getUser()->getId()
            ]
        );


        $this->successMessage(
            'project-loaded',
            _('Project successfully loaded'),
            null,
            [
                'project'       => $project,
                'sessionList'   => $sessionData
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
         * @var Project $project
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
            $this->errorMessage('project-deletion-failed', _('Project-name already exists!'));
            return;
        }

        if (!$em->delete($project)) {
            $this->successMessage('project-deleted', _('Project successfully deleted'));
        } else {
            $this->errorMessage('project-deletion-failed', _('Project could not be deleted'));
        }

    }
}