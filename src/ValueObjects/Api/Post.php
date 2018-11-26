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

    public $asArray;

    /**
     * Post constructor.
     * @param $post
     * @param bool $asArray
     */
    public function __construct($post, $asArray = false)
    {
        $this->asArray = $asArray;
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
        $user = $post->getUser();
        $this->user = null;
        if(is_object($user)) {
            if($this->asArray){
                $this->user = new User($user->toArray());
            }else{
                $this->user = new User($user);
            }

        }
        $this->category = null;
        $category = $post->getCategory();

        if(is_object($category)) {
            if($this->asArray){
                $this->category = new Category($category->toArray());
            }else{
                $this->category = new Category($category);
            }
        }
        $this->createdAt = $post->getCreatedAt();
        $this->likedBy = [];
        $likedBy = $post->getLikedBy();
        if(!empty($likedBy)) {
            foreach ($likedBy as $user) {
                if(is_object($user) && $this->asArray) {
                    $user = $user->toArray();
                }
                array_push($this->likedBy,new User($user));
            }
        }
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
        if(isset($post['id'])) {
            $this->id = (string)$post['id'];
        }elseif(isset($post['_id'])) {
            $this->id = (string)$post['_id'];
        }
        $this->text = $post['text'] ?? null;
        $this->title = $post['title'] ?? null;
        $this->user = null;
        if(isset($post['user']['id'])|| isset($post['user']['_id'])) {
            $this->user = new User($post['user']);
        }
        $this->category = null;
        if(isset($post['category']['id'])|| isset($post['category']['_id'])) {
            $this->category = new Category($post['category']);
        }
        $this->createdAt = $post['createdAt'] ?? null;
        $this->likedBy = [];
        if(!empty($post['likedBy'])) {
            foreach ($post['likedBy'] as $user) {
                if(isset($user['id'])|| isset($user['_id'])) {
                    array_push($this->likedBy,new User($user));
                }
            }
        }
        $this->slug = $post['slug'] ?? null;
        $this->enabled = $post['enabled'] ?? null;
        return $this;
    }
}