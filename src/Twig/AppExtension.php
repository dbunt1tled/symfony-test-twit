<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.10.18
 * Time: 18:55
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {

        $this->locale = $locale;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function getGlobals()
    {
        return [
            'locale' => $this->locale,
        ];
    }

    public function priceFilter($number)
    {
        return '$'.number_format((float)$number, 2, '.',',');
    }
}