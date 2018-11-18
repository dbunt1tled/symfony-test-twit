<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api;


class Status
{
    public $status;
    public $message;

    /**
     * @param string $message
     * @return Status
     */
    public function setSuccessStatus(string $message = 'OK'): self
    {
        $this->status = true;
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $message
     * @return Status
     */
    public function setFailureStatus(string $message = 'Fail'): self
    {
        $this->status = false;
        $this->message = $message;
        return $this;
    }

    /**
     * @return null|boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }
}