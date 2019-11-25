<?php


namespace App\Controller;


use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("/add_book", name="add_book")
     */
    public function addAuthor(Request $request, EntityManagerInterface $entityManager)
    {
        $book = new Book();
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $book);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('title', TextType::class)
            ->add('style', TextType::class)
            ->add('NbPages', IntegerType::class)
            ->add('inStock', CheckboxType::class)
            ->add('Author', TextType::class)
            ->add('save', SubmitType::class)
        ;

        // À partir du formBuilder, on génère le formulaire
        $form = $formBuilder->getForm();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $author contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $author dans la base de données, par exemple
                $entityManager->persist($book);
                $entityManager->flush();

                // On redirige vers la page de visualisation de l'auteur nouvellement créé
                return $this->redirectToRoute('author', array('id' => $book->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage_book/new.html.twig', array(
            'form' => $form->createView(),
        ));

    }
}