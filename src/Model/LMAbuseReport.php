<?php
/**
 * Created by PhpStorm.
 * User: maronl
 * Date: 29/11/17
 * Time: 10:30
 */

namespace Axenso\AXEGamification\Model;


class LMAbuseReport
{
    protected $id;
    protected $user_id;
    protected $reference_type;
    protected $reference_id;
    protected $issue;
    protected $operator_id;
    protected $closed_status;
    protected $closed_at;
    protected $created_at;
    protected $updated_at;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getReferenceType()
    {
        return $this->reference_type;
    }

    /**
     * @param mixed $reference_type
     */
    public function setReferenceType($reference_type)
    {
        $this->reference_type = $reference_type;
    }

    /**
     * @return mixed
     */
    public function getReferenceId()
    {
        return $this->reference_id;
    }

    /**
     * @param mixed $reference_id
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;
    }

    /**
     * @return mixed
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @param mixed $issue
     */
    public function setIssue($issue)
    {
        $this->issue = $issue;
    }

    /**
     * @return mixed
     */
    public function getOperatorId()
    {
        return $this->operator_id;
    }

    /**
     * @param mixed $operator_id
     */
    public function setOperatorId($operator_id)
    {
        $this->operator_id = $operator_id;
    }

    /**
     * @return mixed
     */
    public function getClosedStatus()
    {
        return $this->closed_status;
    }

    /**
     * @param mixed $closed_status
     */
    public function setClosedStatus($closed_status)
    {
        $this->closed_status = $closed_status;
    }

    /**
     * @return mixed
     */
    public function getClosedAt()
    {
        return $this->closed_at;
    }

    /**
     * @param mixed $closed_at
     */
    public function setClosedAt($closed_at)
    {
        $this->closed_at = $closed_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'issue' => $this->issue,
            'operator_id' => $this->operator_id,
            'closed_status' => $this->closed_status,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}