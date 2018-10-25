<?php

namespace App\DataFixturesMongo;

use App\Document\Category;
use App\Document\Post;
use App\Document\UserPreferences;
use App\Document\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppMongoFixtures extends Fixture implements ContainerAwareInterface
{
    private $faker;
    private $users = [];
    private $categories = [];
    private $languages = ['en','ru'];
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->userPasswordEncoder = $this->container->get('security.password_encoder');
        $this->loadUsers($manager);
        $this->loadCategories($manager);
        $this->loadPosts($manager);
    }

    private function loadCategories (ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setTitle($this->faker->sentence(2))
                ->setDescription($this->faker->text(400))
                ->setEnabled($this->faker->boolean);
            $this->addReference($category->getTitle(),$category);
            $this->categories[] = $category->getTitle();
            $manager->persist($category);
        }
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setTitle($this->faker->sentence(2))
                ->setDescription($this->faker->text(400))
                ->setParent($this->getReference($this->categories[array_rand($this->categories)]))
                ->setEnabled($this->faker->boolean);
            $this->addReference($category->getTitle(),$category);
            $this->categories[] = $category->getTitle();
            $manager->persist($category);

        }
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setTitle($this->faker->sentence(2))
                ->setDescription($this->faker->text(400))
                ->setParent($this->getReference($this->categories[array_rand($this->categories)]))
                ->setEnabled($this->faker->boolean);
            $this->addReference($category->getTitle(),$category);
            $this->categories[] = $category->getTitle();
            $manager->persist($category);
        }
        for ($i = 0; $i < 7; $i++) {
            $category = new Category();
            $category->setTitle($this->faker->sentence(2))
                ->setDescription($this->faker->text(400))
                ->setParent($this->getReference($this->categories[array_rand($this->categories)]))
                ->setEnabled($this->faker->boolean);
            $this->addReference($category->getTitle(),$category);
            $this->categories[] = $category->getTitle();
            $manager->persist($category);
        }
        $manager->flush();
    }

    private function loadPosts (ObjectManager $manager)
    {
        for ($i = 0; $i < 70; $i++) {
            $post = new Post();
            $post->setText($this->faker->text(400))
                ->setTitle($this->faker->sentence)
                ->setEnabled($this->faker->boolean)
                ->setUser($this->getReference($this->users[array_rand($this->users)]))
                ->setCategory($this->getReference($this->categories[array_rand($this->categories)]));
            $manager->persist($post);
        }
        $manager->flush();
    }
    private function loadUsers (ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $name = $this->faker->unique()->firstName;
            $user->setEmail($this->faker->email)
                ->setFirstName($name)
                ->setLastName($this->faker->lastName)
                ->setRoles([User::ROLE_USER])
                ->setPlainPassword('12345678')
                ->setEnabled(true)
                ->setPassword($this->userPasswordEncoder->encodePassword($user,$user->getPlainPassword()));
            $this->addReference($user->getUsername(),$user);
            $preference = new UserPreferences();
            $preference->setLocale($this->languages[array_rand($this->languages)]);
            $user->setPreferences($preference);

            $manager->persist($user);
            $this->users[] = $user->getUsername();
        }
        $user = new User();
        $user->setEmail('unt1tled@ua.fm')
            ->setFirstName('Dd')
            ->setLastName('Bb')
            ->setRoles([User::ROLE_ADMIN])
            ->setPlainPassword('12345678')
            ->setEnabled(false)
            ->setConfirmationToken('12345678')
            ->setPassword($this->userPasswordEncoder->encodePassword($user,$user->getPlainPassword()));
        $this->addReference($user->getUsername(),$user);
        $preference = new UserPreferences();
        $preference->setLocale('ru');
        $user->setPreferences($preference);
        $manager->persist($user);
        $this->users[] = $user->getUsername();
        $manager->flush();
    }
}
