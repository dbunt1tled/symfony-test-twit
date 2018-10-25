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
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="/'.$node['slug'].'">'.$node['title'].'</a>';
            }
        );
        $htmlTree = $this->categoryRepository->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* false: load all children, true: only direct */
            $options
        );
        return $engine->render('widgets/categories/w-categories-all/w-categories-all.html.twig',compact('htmlTree'));
    }
}