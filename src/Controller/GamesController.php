<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\GamesType;
use App\Repository\GamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/games', name: 'app_games')]


class GamesController extends AbstractController
{

    #[Route('/new', name: 'app_new')]
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        // $newGames = new Games;
        // $newGames->setTitle("Super Titre");
        // $newGames->setbool(true);

        // $em->persist($newGames); //prépare la requête d'ajouter
        // $em->flush(); //execute

        // Nouveau version du code

        $entity = new Games;

        // Création d'un formulaire, et envoyer dans $entity
        $form = $this->createForm(GamesType::class, $entity);

        // Cherche des données _POST pour les envoyer au formulaire
        $form->handleRequest($request);

        // Test form
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return$this->redirectToRoute('app_gamesapp_admin');
        }

        return $this->render('games/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(GamesRepository $gamesRepository, Request $request): Response
    {
        $p = $request->get('p', 1); // Page 1 par defaut
        $itemCount = 5;
        $search = $request->get('s', '');

        $entities = $gamesRepository->findData($itemCount, $p, $search);

        $pageCount = ceil($entities->count() / $itemCount);

        return $this->render('games/admin.html.twig', [
            'entities' => $entities,
            'pageCount' => max($pageCount, 1),
        ]);
    }

    /**
     * Route avec un parmettre "id"
     * 
     * regex "\d+" d signifi uniquement nombre et le + veut dire 1 ou plus
     * 
     * @Route("/{id}/edit", requirements={"id": "\d+"})
     */
    public function edit(EntityManagerInterface $em, Request $request, Games $entity): Response{
        $form = $this->createForm(GamesType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_gamesapp_admin');
        }

        return $this->render('games/edit.html.twig',[
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id": "\d+"})
     */
    public function delete(EntityManagerInterface $em, Request $request, Games $entity): Response{

        if ($this->isCsrfTokenValid('delete_games_'.$entity->getId(), $request->get('token'))) {
            $em->remove($entity);
            $em->flush();

            return $this->redirectToRoute('app_gamesapp_admin');
        }

        return $this->render('games/delete.html.twig', [
            'entity' => $entity,
        ]);
    }
}
