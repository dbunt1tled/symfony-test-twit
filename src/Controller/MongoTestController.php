<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 15.10.18
 * Time: 22:24
 */

namespace App\Controller;


use App\Document\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MongoTestController extends Controller
{
    /**
     * @Route("/mongoTest")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function mongoTest()
    {
        $user = new User();
        $user->setEmail("hello@medium.com");
        $user->setFirstname("Matt");
        $user->setLastname("Matt");
        $user->setPassword(md5("123456"));

        $dm = $this->get('doctrine_mongodb')->getManager();

        $dm->persist($user);
        $dm->flush();
        return $this->json(['Status' => 'OK']);
    }
}