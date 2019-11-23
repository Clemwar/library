<?php


namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{


    /**
     * @Route("/", name="books")
     */

    //Récupération des données de la table book
    public function showBooks(BookRepository $bookRepository){

        //Récupération des données de la table book
        $books = $bookRepository->findAll();

        //envoi des données au rendu
        return $this->render('showBooks.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/book/{id}", name="book")
     */
    public function showBook($id, BookRepository $bookRepository){

        //Sélection des données du livre voulu via l'id du livre
        $book = $bookRepository->find($id);

        //envoi des données au rendu
        return $this->render('showBook.html.twig', [
            'book' => $book
        ]);
    }
}