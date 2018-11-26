<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api;


class Category
{
    public $id;
    public $description;
    public $title;
    public $posts;
    public $parent;
    public $createdAt;
    public $slug;
    public $enabled;

    /**
     * Post constructor.
     * @param \App\Document\Category|array|null $category
     */
    public function __construct($category)
    {
        if (is_object($category)) {
            $this->setByObject($category);
        } elseif (is_array($category)) {
            $this->setByArray($category);
        }

    }

    /**
     * @param \App\Document\Category $category
     * @return $this
     */
    public function setByObject(\App\Document\Category $category)
    {
        $this->id = $category->getId();
        $this->description = $category->getDescription();
        $this->title = $category->getTitle();
        $this->posts = [];
        $posts = $category->getPosts();
        if(!empty($posts)) {
            foreach ($posts as $post) {
                array_push($this->posts,(new Post($post,true)));
            }
        }
        $parent = $category->getParent();
        if(!empty($parent)) {
            $parent = new Category($parent);
        }else {
            $parent = null;
        }
        $this->parent = $parent;
        $this->createdAt = $category->getCreatedAt();
        $this->slug = $category->getSlug();
        $this->enabled = $category->getEnabled();
        return $this;
    }
    /**
     * @param array $category
     * @return $this
     */
    public function setByArray(array $category)
    {
        if(isset($category['id'])) {
            $this->id = (string)$category['id'];
        }elseif(isset($post['_id'])) {
            $this->id = (string)$category['_id'];
        }
        $this->description = $category['description'] ?? null;
        $this->title = $post['title'] ?? null;
        if(is_array($category) || isset($category['parent']['id'])|| isset($category['parent']['_id'])) {
            $this->parent = new Category($category['parent']);
        }
        $this->createdAt = $post['createdAt'] ?? null;
        $this->posts = [];
        if(!empty($post['posts'])) {
            foreach ($post['posts'] as $post) {
                array_push($this->posts,new Post($post));
            }
        }
        $this->slug = $post['slug'] ?? null;
        $this->enabled = $post['enabled'] ?? null;
        return $this;
    }
}