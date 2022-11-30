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
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/games', name: 'app_games')]


class GamesController extends AbstractController
{

    #[Route('/new', name: 'app_new')]
    #[IsGranted("ROLE_USER")]
    public function new(EntityManagerInterface $em, Request $request, TranslatorInterface $translator): Response
    {
        // $newGames = new Games;
        // $newGames->setTitle("Super Titre");
        // $newGames->setbool(true);

        // $em->persist($newGames); //prépare la requête d'ajouter
        // $em->flush(); //execute

        // Nouveau version du code

        $entity = new Games;
        $entity->setAuthor($this->getUser());
        // Création d'un formulaire, et envoyer dans $entity
        $form = $this->createForm(GamesType::class, $entity);

        // Cherche des données _POST pour les envoyer au formulaire
        $form->handleRequest($request);

        // Test form
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();

            //Ajout de message temporaire, addFlash est une fonction de symfony, qui indique le nom du message, puis le contenu
            $this->addFlash('success', $translator->trans('games.new.success', [
                '%title%' => $entity->getTitle(),
            ]));

            return $this->redirectToRoute('app_gamesapp_admin');
        }

        return $this->render('games/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    #[IsGranted("ROLE_USER")]
    public function admin(GamesRepository $gamesRepository, Request $request): Response
    {
        $author = $this->getUser();

        if ($this->isGranted('ROLE_USER')) {
            $author = null;
        }

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
    public function edit(EntityManagerInterface $em, Request $request, Games $entity, TranslatorInterface $translator): Response{
        if ($entity->getAuthor() === null) {
            $entity->setAuthor($this->getUser());
        }
        
        $form = $this->createForm(GamesType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('warning', $translator->trans('games.edit.success', [
                '%title%' => $entity->getTitle(),
            ]));

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
    public function delete(EntityManagerInterface $em, Request $request, Games $entity, TranslatorInterface $translator): Response{

        if ($this->isCsrfTokenValid('delete_games_'.$entity->getId(), $request->get('token'))) {
            $em->remove($entity);
            $em->flush();
            $this->addFlash('danger', $translator->trans('games.delete.success', [
                '%title%' => $entity->getTitle(),
            ]));

            return $this->redirectToRoute('app_gamesapp_admin');
        }

        return $this->render('games/delete.html.twig', [
            'entity' => $entity,
        ]);
    }
}
