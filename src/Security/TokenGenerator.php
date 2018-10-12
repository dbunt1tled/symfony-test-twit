<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.10.18
 * Time: 18:59
 */

namespace App\Security;


class TokenGenerator
{
    public function getRandomSecureToken()
    {
        return md5(random_bytes(255));
    }
}