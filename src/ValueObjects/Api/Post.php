<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api;


class Post
{
    public $id;
    public $text;
    public $title;
    public $user;
    public $category;
    public $likedBy;
    public $createdAt;
    public $slug;
    public $enabled;

    /**
     * Post constructor.
     * @param \App\Document\Post|array|null $post
     */
    public function __construct($post)
    {
        if (is_object($post)) {
            $this->setByObject($post);
        } elseif (is_array($post)) {
            $this->setByArray($post);
        }

    }

    /**
     * @param \App\Document\Post $post
     * @return $this
     */
    public function setByObject(\App\Document\Post $post)
    {
        $this->id = $post->getId();
        $this->text = $post->getText();
        $this->title = $post->getTitle();
        $this->user = $post->getUser();
        $this->category = $post->getCategory();
        $this->createdAt = $post->getCreatedAt();
        $this->likedBy = $post->getLikedBy();
        $this->slug = $post->getSlug();
        $this->enabled = $post->getEnabled();
        return $this;
    }
    /**
     * @param array $post
     * @return $this
     */
    public function setByArray(array $post)
    {
        $post['user']['fullName'] = $post['user']['firstName'] . ' ' . $post['user']['lastName'];
        $this->id = $post['id'];
        $this->text = $post['text'];
        $this->title = $post['title'];
        $this->user = $post['user'];
        //$this->category = $post->getCategory();
        $this->createdAt = $post['createdAt'];
        //$this->likedBy = $post->getLikedBy();
        $this->slug = $post['slug'];
        $this->enabled = $post['enabled'];
        return $this;
    }
}