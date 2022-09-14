<?php

namespace App\Controller;

use App\Entity\Games;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/games', name: 'app_games')]


class GamesController extends AbstractController
{

    #[Route('/new', name: 'app_new')]
    public function new(EntityManagerInterface $em): Response
    {
        $newGames = new Games;
        $newGames->setTitle("Super Titre");
        $newGames->setbool(true);

        $em->persist($newGames); //prépare la requête d'ajouter
        $em->flush(); //execute



        return $this->render('games/new.html.twig', [
            'controller_name' => 'GamesController',
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('admin.html.twig');
    }
}
