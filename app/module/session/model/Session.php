<?php
/**
 * @author  j
 * @created : 2/10/16 3:01 PM
 * @project : time-tracker
 */

namespace timetracker\app\module\session\model;
use chilimatic\lib\Database\Model\AbstractModel;

/**
 * Class Session
 *
 * @package timetracker\app\module\project\model
 * @ORM table=`time_tracker`.`session`;
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
     * @var bool
     */
    private $done;

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
     * this will just be injected atm the frameworks does not support this atm
     * @var SessionDescription
     */
    private $sessionDescription;

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
     * @return boolean
     */
    public function isDone()
    {
        return (bool) $this->done;
    }

    /**
     * @param boolean $done
     */
    public function setDone($done)
    {
        $this->done = $done;
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
     * @return SessionDescription
     */
    public function getSessionDescription()
    {
        return $this->sessionDescription;
    }

    /**
     * @param SessionDescription $sessionDescription
     */
    public function setSessionDescription($sessionDescription)
    {
        $this->sessionDescription = $sessionDescription;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'                    => $this->id,
            'user_id'               => $this->user_id,
            'project_id'            => $this->project_id,
            'done'                  => (bool) $this->done,
            'startTime'             => $this->start_time,
            'endTime'               => $this->end_time,
            'timeDiff'              => $this->time_diff,
            'sessionDescription'    => $this->sessionDescription
        ];
    }
}