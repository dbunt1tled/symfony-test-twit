<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $users = [];
    private $languages = ['en','ru'];
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder )
    {
        $this->faker = Factory::create();
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts (ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText($this->faker->text(400))
                ->setCreatedAt(new \DateTime($this->faker->date()))
                ->setUser($this->getReference($this->users[array_rand($this->users)]));

            $manager->persist($microPost);
        }
        $manager->flush();
    }
    private function loadUsers (ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $name = $this->faker->unique()->firstName;
            $user->setEmail($this->faker->email)
                ->setFullName($name.' '.$this->faker->lastName)
                ->setRoles([User::ROLE_USER])
                ->setUsername($this->slug($name))
                ->setPlainPassword('12345678')
                ->setEnabled(true)
                ->setPassword($this->userPasswordEncoder->encodePassword($user,$user->getPlainPassword()));
            $this->addReference($user->getUsername(),$user);
            $preference = new UserPreferences();
            $preference->setLocale($this->languages[array_rand($this->languages)]);
            $user->setPreferences($preference);
            //$manager->persist($preference); // set  cascade={"persist"}  in User
            $manager->persist($user);
            $this->users[] = $user->getUsername();
        }
        $user = new User();
        $user->setEmail('unt1tled@ua.fm')
            ->setFullName('Ddd Bbb')
            ->setRoles([User::ROLE_ADMIN])
            ->setUsername('sidni')
            ->setPlainPassword('12345678')
            ->setEnabled(true)
            ->setPassword($this->userPasswordEncoder->encodePassword($user,$user->getPlainPassword()));
        $this->addReference($user->getUsername(),$user);
        $preference = new UserPreferences();
        $preference->setLocale('ru');
        $user->setPreferences($preference);
        //$manager->persist($preference); // set  cascade={"persist"} in User
        $manager->persist($user);
        $this->users[] = $user->getUsername();
        $manager->flush();
    }

    private function slug($slug) {
        $name = $slug;
        $slug = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; [:Punctuation:] Remove; Lower();',
            $slug
        );
        if (false == $slug) {
            throw new \RuntimeException('Unable to sluggize: ' . $name);
        }
        $slug = preg_replace('/\W/', ' ', $slug); //remove remaining nonword characters
        $slug = preg_replace('/[-\s]+/', '-', $slug);
        return $slug;
    }
}
