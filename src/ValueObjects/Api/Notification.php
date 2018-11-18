<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api;


class Notification
{
    public $id;
    public $seen;
    public $likedBy;
    public $user;
    public $post;
    public $type;

    /**
     * Post constructor.
     * @param \App\Document\Notification|array|null $notification
     */
    public function __construct($notification)
    {
        if (is_object($notification)) {
            $this->setByObject($notification);
        } elseif (is_array($notification)) {
            $this->setByArray($notification);
        }

    }

    /**
     * @param \App\Document\Notification $notification
     * @return $this
     */
    public function setByObject(\App\Document\Notification $notification)
    {
        $this->id = $notification->getId();
        $this->seen = $notification->getSeen();
        $this->user = $notification->getUser();

        if(method_exists( $notification , 'getType' )) {
            $this->type = $notification->geType();
        }
        if(method_exists( $notification , 'getLikedBy' )) {
            $this->likedBy = $notification->getLikedBy();
        }
        if(method_exists( $notification , 'getPost' )) {
            $this->post = $notification->getPost();
        }
        return $this;
    }
    /**
     * @param array $notification
     * @return $this
     */
    public function setByArray(array $notification)
    {
        if(isset($notification['id'])){
            $this->id = (string)$notification['id'];
        }elseif (isset($notification['_id'])) {
            $this->id = (string)$notification['_id'];
        }
        $this->type = $notification['type']??$notification['type'];
        $this->seen = $notification['seen']??$notification['seen'];
        if(isset($notification['user']['id'])|| isset($notification['user']['_id'])) {
            $this->user = new User($notification['user']);
        }
        if(isset($notification['post']['id'])|| isset($notification['post']['_id'])) {
            $this->post = new Post($notification['post']);
        }
        if(isset($notification['likedBy']['id'])|| isset($notification['likedBy']['_id'])) {
            $this->likedBy = new User($notification['likedBy']);
        }
        return $this;
    }
}