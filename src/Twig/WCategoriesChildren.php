<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.10.18
 * Time: 12:47
 */

namespace App\Twig;


use App\Document\Category;
use App\Repositories\CategoryRepository;

class WCategoriesChildren
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {

        $this->categoryRepository = $categoryRepository;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('WCategoriesChildren',[$this,'WCategoriesChildren'], ['is_safe' => ['html'],'needs_environment' => true])
        ];
    }
    public function WCategoriesChildren(\Twig_Environment $engine, Category $category)
    {
        $categories = $this->categoryRepository->getChildren($category);
        return $engine->render('widgets/categories/w-categories-all/w-categories-children.html.twig',compact('categories'));
    }
}