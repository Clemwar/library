<?php


namespace App\Controller;


use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ManageController extends AbstractController
{


    /**
     * @Route("/admin", name="admin")
     */
    public function manageLibrary(Request $request, BookRepository $bookRepository, AuthorRepository $authorRepository){
        $books = $bookRepository->findAll();
        $authors = $authorRepository->findAll();
        $message = null;
        $intitule = 'Administration du site';

        if (!empty($request->get('message'))){
            $intitule = $request->get('message');
        }

        if (!empty($request->get('intitule'))){
            $intitule = $request->get('intitule');
        }

        return $this->render('manage/admin.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'message' => $message,
            'intitule' => $intitule
        ]);
    }
}