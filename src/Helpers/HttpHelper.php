<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 20.11.18
 * Time: 11:21
 */

namespace App\Helpers;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HttpHelper
{
    /**
     * @param Request $request
     * @return ArrayCollection
     */
    public static function getContentAsArray(Request $request)
    {
        $content = $request->getContent();

        if (empty($content)) {
            throw new BadRequestHttpException("Content is empty");
        }

        /*if(!Validator::isValidJsonString($content)){
            throw new BadRequestHttpException("Content is not a valid json");
        }/**/

        return new ArrayCollection(json_decode($content, true));
    }
}