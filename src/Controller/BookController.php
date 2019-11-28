<?php


namespace App\Controller;


use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("book/show/{id}", name="book")
     */
    public function showBook($id, BookRepository $bookRepository){

        //Sélection des données du livre voulu via l'id du livre
        $book = $bookRepository->find($id);

        //envoi des données au rendu
        return $this->render('showBook.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/admin/book/add", name="add_book")
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager)
    {
        $book = new Book();

        // On crée le FormBuilder en appelant le formtype
        $form = $this->createForm(BookType::class, $book);

        //On crée la vue
        $formView = $form->createView();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $book contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $author dans la base de données, par exemple
                $entityManager->persist($book);
                $entityManager->flush();

                // On redirige vers la page de visualisation du livre nouvellement créé
                return $this->redirectToRoute('manage_library');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/manage_book/new.html.twig', [
            'form' => $formView
        ]);
    }

    /**
     * @Route("/books_by_style/{style}", name="books_by_style")
     */
    public function getBooksByStyle($style, BookRepository $bookRepository){
        $books = $bookRepository->getByStyle($style);

        return $this->render('showBooksByStyle.html.twig', [
            'books' => $books,
            'style' => $style
        ]);
    }

    /**
     * @Route("book/search", name="search_by_title")
     */
    public function getBookByTitle(Request $request, BookRepository $bookRepository){
        $title = $request->get('title');
        $books = $bookRepository->getByTitle($title);

        if (count($books) >= 1) {
            return $this->render('showBooksByTitle.html.twig', [
                'books' => $books,
                'title' => $title,
                'niet' => false
            ]);
        }
        return $this->render('showBooksByTitle.html.twig', [
            'books' => $books,
            'title' => $title,
            'niet' => 'Aucun livre trouvé.'
        ]);
    }

    /**
     * @Route("/admin/book/delete/{id}", name="delete_book")
     */
    public function deleteBook(EntityManagerInterface $entityManager, $id, BookRepository $bookRepository){

        $book = $bookRepository->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('manage_library');
    }

    /**
     * @Route("/admin/book/update/{id}", name="update_book")
     */
    public function updateBook($id, BookRepository $bookRepository ,Request $request, EntityManagerInterface $entityManager)
    {
        $book = $bookRepository->find($id);

        // On crée le FormBuilder en appelant le formtype
        $form = $this->createForm(BookType::class, $book);

        //On crée la vue
        $formView = $form->createView();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $book contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {

                $entityManager->flush();

                // On redirige vers la page de visualisation de le livre mis à jour
                return $this->redirectToRoute('manage_library');
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/manage_book/update.html.twig', [
            'form' => $formView
        ]);
    }
}
