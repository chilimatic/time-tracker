<?php

namespace timetracker\app\module\project\model;
use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class UserProjectMap
 *
 * @ORM table=`time_tracker`.`task_session_map`;
 * @package timetracker\app\module\project\model
 */
class TaskSessionMap extends AbstractModel
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $session_id;

    /**
     * @var int
     */
    private $task_id;

    /**
     * @var float
     */
    private $percentage;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * @param int $session_id
     */
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;
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
     */
    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }


    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

}