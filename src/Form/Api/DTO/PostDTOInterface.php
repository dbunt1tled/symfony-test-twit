<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.12.18
 * Time: 18:03
 */

namespace App\Form\Api\DTO;



interface PostDTOInterface
{
    public function getErrors(): array;
    public function setErrors($errors);
    public function getCategory(): string;
    public function setCategory(string $category);
    public function getSlug(): string;
    public function setSlug(string $slug);
    public function getText(): string;
    public function setText(string $text);
    public function getTitle(): string;
    public function setTitle(string $title);
}