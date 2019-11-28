<?php


namespace App\Controller;


use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ManageController extends AbstractController
{


    /**
     * @Route("/admin", name="admin")
     */
    public function manageLibrary(BookRepository $bookRepository, AuthorRepository $authorRepository){
        $books = $bookRepository->findAll();
        $authors = $authorRepository->findAll();

        return $this->render('manage/admin.html.twig', [
            'books' => $books,
            'authors' => $authors
        ]);
    }
}