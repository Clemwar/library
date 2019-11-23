<?php


namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class AuthorController extends AbstractController
{


    /**
     * @Route("/authors", name="authors")
     */

    //Récupération des données de la table author
    public function showAuthors(AuthorRepository $authorRepository){

        //Récupération des données de la table author
        $authors = $authorRepository->findAll();

        //envoi des données au rendu
        return $this->render('showAuthors.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * @Route ("/author/{id}", name="author")
     */

    //Récupération des données d'un auteur de la table author et de ses livres associés
    public  function showAuthor($id, AuthorRepository $authorRepository, BookRepository $bookRepository){

        //Sélection des données de l'auteur voulu via l'id de l'author
        $author = $authorRepository->find($id);

        //Récupération des infos de la table book en fonction de l'id author
        $booksByAuth = $bookRepository->findBy([
            'author' => $id
        ]);

        //envoi des données au rendu
        return $this->render('showAuthor.html.twig', [
            'author' => $author,
            'books' => $booksByAuth
        ]);
    }

}