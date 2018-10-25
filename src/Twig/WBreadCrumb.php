<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.10.18
 * Time: 12:56
 */

namespace App\Twig;

use Symfony\Component\Routing\Router;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class WBreadCrumb
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var string
     */
    private $homeRoute;
    /**
     * @var string
     */
    private $homeLabel;

    public function __construct(Router $router, $homeRoute = 'homepage', $homeLabel= 'Home')
    {
        $this->router = $router;
        $this->homeRoute = $homeRoute;
        $this->homeLabel = $homeLabel;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('WBreadCrumb',[$this,'addBreadcrumb'])
        ];
    }
    public function addBreadcrumb($label, $url = '', array $translationParameters = [])
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem($this->homeLabel, $this->router->generate($this->homeRoute));
        }
        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }
}