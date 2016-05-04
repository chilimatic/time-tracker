<?php
namespace timetracker\app\module\project\model;
use chilimatic\lib\Database\Model\AbstractModel;

/**
 * Class UserProjectTaskMap
 *
 * @ORM table=`time_tracker`.`user_project_task_map`;
 * @package timetracker\app\module\project\model
 */
class UserProjectTaskMap extends AbstractModel
{

    /**
     * @var int
     */
    private $user_project_map_id;

    /**
     * @var int
     */
    private $task_id;

    /**
     * @return int
     */
    public function getUserProjectMapId()
    {
        return $this->user_project_map_id;
    }

    /**
     * @param int $user_project_map_id
     */
    public function setUserProjectMapId($user_project_map_id)
    {
        $this->user_project_map_id = $user_project_map_id;
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
            'user_project_map_id' => $this->user_project_map_id,
            'task_id'       => $this->task_id
        ];
    }
}