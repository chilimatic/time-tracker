<?php
namespace timetracker\app\module\session\model;
use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class SessionDescription
 * @package timetracker\app\module\session\model
 * @ORM table=`time_tracker`.`session_description`;
 */
class SessionDescription extends AbstractModel
{
    /**
     * @var int
     */
    private $session_id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'session_id' => $this->session_id,
            'text'       => $this->text,
            'created'    => $this->created,
            'modified'   => $this->modified
        ];
    }
}