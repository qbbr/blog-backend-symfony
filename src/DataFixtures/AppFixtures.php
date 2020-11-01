<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private array $users = [];
    private array $tags = [];

    private UserPasswordEncoderInterface $passwordEncoder;

    private SluggerInterface $slugger;

    private Generator $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUserData($manager);
        $this->loadTagData($manager);
        $this->loadPostData($manager);

        $manager->flush();
    }

    private function loadUserData(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setUsername($this->faker->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $this->faker->password(6)));
            $user->setAbout($this->faker->text());
            $manager->persist($user);

            $this->users[] = $user;
        }
    }

    private function loadTagData(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $tag = new Tag();
            $tag->setName($this->faker->text(20));
            $manager->persist($tag);

            $this->tags[] = $tag;
        }
    }

    private function loadPostData(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; ++$i) {
            $post = new Post();
            $post->setUser($this->faker->randomElement($this->users));
            foreach ($this->faker->randomElements($this->tags, mt_rand(1, 5)) as $tag) {
                $post->addTag($tag);
            }
            $post->setTitle($this->faker->text(100));
            $post->setSlug($this->slugger->slug($post->getTitle()));
            $post->setText($this->faker->text(1000));
            $manager->persist($post);
        }
    }
}
