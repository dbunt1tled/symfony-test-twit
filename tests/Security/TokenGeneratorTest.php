<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 14.10.18
 * Time: 20:02
 */

namespace App\Tests\Security;

use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{

    public function testGetRandomSecureToken()
    {
        $tokenGen = new TokenGenerator();
        $token = $tokenGen->getRandomSecureToken();
        $this->assertEquals(32,mb_strlen($token));
        $this->assertEquals(1,preg_match("/[a-z0-9]/",$token));
        $this->assertTrue(ctype_alnum($token),'Token contains incorrect symbols');
    }
}
