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
    private $user;
    private $category;
    private $likedBy;
    public $createdAt;
    public $slug;
    public $enabled;

    public function __construct(\App\Document\Post $post)
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

    }
}