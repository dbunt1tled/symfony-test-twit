<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.12.18
 * Time: 15:12
 */

namespace App\Form\Api;


use Symfony\Component\HttpFoundation\Request;

interface RequestDTOInterface
{
    //public function __construct(Request $request);
    public function getErrors(): array;
    public function setErrors($errors);
}