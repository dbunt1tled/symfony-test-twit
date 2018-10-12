<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.10.18
 * Time: 15:45
 */

namespace App\Mailer;


use App\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $mailFrom;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }
    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('emails/registration.html.twig',[
            'user' => $user,
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Welcome to our Portal')
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body,'text/html');
        return  $this->mailer->send($message);
    }
}