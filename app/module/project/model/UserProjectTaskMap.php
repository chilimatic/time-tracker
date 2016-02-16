<?php
namespace timetracker\app\module\project\model;
use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class UserProjectTaskMap
 *
 * @ORM table=`time-tracker`.`user_project_task_map`;
 * @package timetracker\app\module\project\model
 */
class UserProjectTaskMap extends AbstractModel
{
    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $project_id;

    /**
     * @var int
     */
    private $task_id;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     *
     * @return $this
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * @param int $task_id
     *
     * @return $this
     */
    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'user_id'       => $this->user_id,
            'project_id'    => $this->project_id,
            'task_id'       => $this->task_id
        ];
    }
}