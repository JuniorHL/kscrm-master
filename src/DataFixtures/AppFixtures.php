<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface){}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
		// $manager->persist($product);
		$usuario = new Usuario();
		$usuario->setUsuCorreo('ops@ksperu.com');
		$usuario->setRoles(["ROLE_GESTOR"]);
		$usuario->setUsuEstado(1);
		$usuario->setVerified(1);
		$usuario->setPassword('$2y$13$Rz3ChZH5/sSx1H5pc85O5OES2N.wnev5DbNGwms8wx6CDjmKQ7TJ.');
		$manager->persist($usuario);
        $manager->flush();
    }
}

