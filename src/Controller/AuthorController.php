<?php


namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/add_author", name="add_author")
     */
    public function addAuthor(Request $request, EntityManagerInterface $entityManager)
    {
        $author = new Author();
        // On crée le FormBuilder grâce au service form factory
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $author);

        // On ajoute les champs de l'entité que l'on veut à notre formulaire
        $formBuilder
            ->add('name', TextType::class)
            ->add('firstName', TextType::class)
            ->add('birthDate', BirthdayType::class)
            ->add('deathDate', BirthdayType::class, array('required' => false))
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
                $entityManager->persist($author);
                $entityManager->flush();

                // On redirige vers la page de visualisation de l'auteur nouvellement créé
                return $this->redirectToRoute('author', ['id' => $author->getId()]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage_author/new.html.twig', array(
            'form' => $form->createView(),
        ));

    }

}