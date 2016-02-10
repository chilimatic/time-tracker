<?php
/**
 * @author  j
 * @created : 2/10/16 3:01 PM
 * @project : time-tracker
 */

namespace timetracker\app\module\session\model;
use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class Session
 *
 * @package timetracker\app\module\project\model
 * @ORM table=`time-tracker`.`session`;
 */
class Session extends AbstractModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $project_id;

    /**
     * @var string
     */
    private $start_time;

    /**
     * @var string
     */
    private $end_time;

    /**
     * @var float
     */
    private $time_diff;

    /**
     * @var string
     */
    private $created;

    /**
     * @var string
     */
    private $modified;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param string $startTime
     *
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param string $endTime
     *
     * @return $this
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return string
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param string $modified
     *
     * @return $this
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return float
     */
    public function getTimeDiff()
    {
        return $this->time_diff;
    }

    /**
     * @param float $time_diff
     *
     * @return $this
     */
    public function setTimeDiff($time_diff)
    {
        $this->time_diff = $time_diff;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'project_id'    => $this->project_id,
            'startTime'     => $this->start_time,
            'endTime'       => $this->end_time,
            'timeDiff'      => $this->time_diff
        ];
    }
}