<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 14.10.18
 * Time: 20:54
 */

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    private $from = 'unt1tled@ua.fm';
    public function testSendConfirmationEmail()
    {
        $user = new User();
        $user->setEmail('db.unt1tled@gmail.com');

        $swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock->expects($this->once())->method('render')
            ->with('emails/registration.html.twig',[
                'user' => $user,
            ])
            ->willReturn('This is a message body');

        $swiftMailerMock->expects($this->once())->method('send')
            ->with($this->callback(function ($subject){
                $messageStr = (string) $subject;
                //dump($messageStr);
                return (
                    (mb_strpos($messageStr, "From: {$this->from}") !== false)
                    && (mb_strpos($messageStr, "Content-Type: text/html; charset=utf-8") !== false)
                );
            }));

        $mailer = new Mailer($swiftMailerMock,$twigMock,$this->from);
        $mailer->sendConfirmationEmail($user);
    }
}
