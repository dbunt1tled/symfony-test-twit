<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.10.18
 * Time: 12:39
 */

namespace App\Twig;


use App\Repositories\CategoryRepository;

class WCategoriesAll extends \Twig_Extension
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
            new \Twig_SimpleFunction('WCategoriesAll',[$this,'WCategoriesAll'], ['is_safe' => ['html'],'needs_environment' => true])
        ];
    }
    public function WCategoriesAll(\Twig_Environment $engine)
    {
        $tree = $this->categoryRepository->getFullTreeArray();
        return $engine->render('widgets/categories/w-categories-all/w-categories-all.html.twig',compact('tree'));
    }
}