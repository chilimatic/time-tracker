<?php
namespace timetracker\app\module\project\controller;

use chilimatic\lib\database\sql\orm\EntityManager;
use chilimatic\lib\di\ClosureFactory;
use timetracker\app\module\main\controller\Application;
use timetracker\app\module\project\model\Task as TaskModel;
use timetracker\app\module\project\model\UserProjectTaskMap;

/**
 * Class Task
 *
 * @package timetracker\app\module\project\controller
 */
class Task extends Application
{
    public function getTaskListAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');
        $model = $em->findBy(new TaskModel(), []);

        $this->successMessage('success', '', null, $model);
    }


    public function getTaskForUserAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

    }

    public function getTaskForProjectAction()
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
        $taskList = $em->findBy(
            new UserProjectTaskMap(),
            [
                'user_id'       => $this->getUser()->getUser()->getId(),
                'project_id'    => (int) $request->getRaw()->get('project_id')
            ]
        );

        $modelList = $em->findBy(
            new TaskModel(),
            [
                'id' => $taskList->getAsArray('task_id')
            ]
        );

        $this->successMessage('success', '', null, $modelList);
    }

    public function createTaskAction()
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

        $taskName = $request->getRaw()->get('task_name');
        if (!$taskName) {
            $this->errorMessage('error', _('no project id has been given'));
            return;
        }

        /**
         * @var EntityManager $em
         */
        $em = ClosureFactory::getInstance()->get('entity-manager');
        $task = new TaskModel();
        $task->setName($taskName);
        $task->setPublic((bool) $request->getRaw()->get('public'));
        $task->setCreated(date('Y-m-d H:i:s'));
        $task->setModified(date('Y-m-d H:i:s'));

        if (!$em->persist($task)) {
            $this->errorMessage('error', _('task could not be created'));
            return;
        }

        // if it's mapped to a user it will be created accordingly
        if ($request->getRaw()->get('project_id')) {
            $map = new UserProjectTaskMap();
            $map->setUserId($this->getUser()->getUser()->getId());
            $map->setProjectId((int) $request->getRaw()->get('project_id'));
            $map->setTaskId($task->getId());
            $em->persist($map);
        }


        $this->successMessage('success', '', null, $task);
    }

    public function deleteTaskAction()
    {
        if (!$this->loadUserFromSession()) {
            $this->errorMessage('login-needed', _('please login!'), null, ['logout' => true]);
            return;
        }

    }

}