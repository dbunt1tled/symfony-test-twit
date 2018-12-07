<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 02.11.18
 * Time: 12:42
 */

namespace App\ValueObjects\Api\Short;


class Category
{
    public $id;
    public $description;
    public $title;
    public $createdAt;
    public $slug;
    public $level;
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
        $this->createdAt = $category->getCreatedAt();
        $this->slug = $category->getSlug();
        $this->level = $category->getLevel();
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
        }elseif(isset($category['_id'])) {
            $this->id = (string)$category['_id'];
        }
        $this->description = $category['description'] ?? null;
        $this->title = $category['title'] ?? null;
        $this->createdAt = $category['createdAt'] ?? null;
        $this->slug = $category['slug'] ?? null;
        $this->level = $category['level'] ?? null;
        $this->enabled = $category['enabled'] ?? null;
        return $this;
    }
}