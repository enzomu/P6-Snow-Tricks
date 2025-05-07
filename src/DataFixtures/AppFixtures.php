<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@snowtricks.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password123'));
        $admin->setIsVerified(true);
        $admin->setProfilePicture('https://picsum.photos/200');
        $manager->persist($admin);

        $categories = [
            'Les grabs',
            'Les rotations',
            'Les flips',
            'Les slides',
            'Old school'
        ];

        $figure = new Figure();
        $figure->setName('Mute Grab');
        $figure->setCategory($categories[0]);
        $figure->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=1');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/jm19nEvmZgM',
            'https://picsum.photos/800/600?random=2'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Stalefish');
        $figure->setCategory($categories[0]);
        $figure->setDescription('Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=3');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/f9FjhCt_w2U'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('360');
        $figure->setCategory($categories[1]);
        $figure->setDescription('Rotation horizontale complète d\'un tour (360 degrés).');
        $figure->setMainMedia('https://picsum.photos/800/600?random=4');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/hUddT6FGCws',
            'https://picsum.photos/800/600?random=5'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('540');
        $figure->setCategory($categories[1]);
        $figure->setDescription('Rotation horizontale d\'un tour et demi (540 degrés).');
        $figure->setMainMedia('https://picsum.photos/800/600?random=6');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/eGJ8keB1-JM'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Backflip');
        $figure->setCategory($categories[2]);
        $figure->setDescription('Rotation en arrière (salto arrière).');
        $figure->setMainMedia('https://picsum.photos/800/600?random=7');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/jH76540wSqU'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Frontflip');
        $figure->setCategory($categories[2]);
        $figure->setDescription('Rotation en avant (salto avant).');
        $figure->setMainMedia('https://picsum.photos/800/600?random=8');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/gMfmjr-kuOg'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Tail Slide');
        $figure->setCategory($categories[3]);
        $figure->setDescription('Glisse sur un obstacle avec l\'arrière de la planche.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=9');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/oAK9mK7wWvw'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Nose Slide');
        $figure->setCategory($categories[3]);
        $figure->setDescription('Glisse sur un obstacle avec l\'avant de la planche.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=10');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/MviULRz8c9k'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Method Air');
        $figure->setCategory($categories[4]); // Old school
        $figure->setDescription('Figure classique où le rider saisit la carre arrière avec sa main arrière, tout en cambrant le dos et en remontant les genoux.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=11'); // Placeholder
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/CzDjM7h_Fwo'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $figure = new Figure();
        $figure->setName('Japan Air');
        $figure->setCategory($categories[4]);
        $figure->setDescription('Figure où le rider saisit l\'avant de sa planche, entre les fixations, avec sa main avant, tout en ramenant ses genoux vers sa poitrine.');
        $figure->setMainMedia('https://picsum.photos/800/600?random=12');
        $figure->setMediaGallery([
            'https://www.youtube.com/embed/CJdZTe4pZUU'
        ]);
        $figure->setCreatedAt(new \DateTimeImmutable());
        $figure->setAuthor($admin);
        $manager->persist($figure);

        $manager->flush();
    }
}