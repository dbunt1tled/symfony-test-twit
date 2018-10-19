<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 18.10.18
 * Time: 21:15
 */

namespace App\Repositories\Traits;


/**
 * Trait PagerTrait
 *
 * @package App\Utils
 */
trait PagerTrait
{
    public function getPage($page = 1)
    {
        if ($page < 1) {
            $page = 1;
        }elseif ($page > 100) {
            $page = 100;
        }
        return floor($page);
    }
    public function getLimit($limit = 100)
    {
        if ($limit < 1 || $limit > 100) {
            $limit = 100;
        }
        return floor($limit);
    }
    public function getOffset($page, $limit)
    {
        $offset = 0;
        if ($page != 0 && $page != 1) {
            $offset = ($page - 1) * $limit;
        }
        return $offset;
    }
}