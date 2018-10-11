<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/likes")
 * @Security("is_granted('ROLE_USER')", message="Access Denied")
 */
class LikesController extends AbstractController
{

    public function __construct(
        MicroPostRepository $microPostRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        FlashBagInterface $flashBag
    )
    {
    }

    /**
     * @Route("/like/{id}", name="likes_like")
     * @param MicroPost $post
     */
    public function like(MicroPost $post)
    {

    }
    public function unLike(MicroPost $post)
    {

    }
}
