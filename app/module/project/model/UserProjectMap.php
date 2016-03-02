<?php
namespace timetracker\app\module\project\model;
use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class UserProjectMap
 *
 * @ORM table=`time-tracker`.`user_project_map`;
 * @package timetracker\app\module\project\model
 */
class UserProjectMap extends AbstractModel
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'user_id'       => $this->user_id,
            'project_id'    => $this->project_id
        ];
    }
}