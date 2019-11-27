<?php


namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AuthorController extends AbstractController
{


    /**
     * @Route("/author/list", name="authors")
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
     * @Route ("/author/show/{id}", name="author")
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
     * @Route("author/add", name="add_author")
     */
    public function addAuthor(Request $request, EntityManagerInterface $entityManager)
    {
        $author = new Author();

        // On crée le FormBuilder en appelant le formtype
        $form = $this->createForm(AuthorType::class, $author);

        //On crée la vue
        $formView = $form->createView();

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
                return $this->redirectToRoute('manage_library', ['id' => $author->getId()]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/manage_author/new.html.twig', [
            'form' => $formView
        ]);
    }

    /**
     * @Route("author/search/{name}", name="search_by_name")
     */
    public function getAuthorByName($name = null, AuthorRepository $authorRepository){
        //On appelle notre contructeur de requete pour récupérer les auteurs en fonction de la recherche sur le nom
        $authors = $authorRepository->getByName($name);

        if (count($authors) >= 1){
            //On envoit les données à la vue
            return $this->render('showAuthorsByName.html.twig', [
                'authors' => $authors,
                'name' => $name,
                'niet' => false
            ]);
        }
        return $this->render('showAuthorsByName.html.twig', [
            'authors' => $authors,
            'name' => $name,
            'niet' => 'Aucun auteur trouvé'
        ]);
    }

    /**
     * @Route("/author/delete/{id}", name="delete_author")
     */
    public function deleteAuthor(EntityManagerInterface $entityManager, $id, AuthorRepository $authorRepository){

        $author = $authorRepository->find($id);

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('manage_library');
    }

    /**
     * @Route("/author/ajoute"), name="ajoute_author"
     */

    public function ajouteAuthor(EntityManagerInterface $entityManager){
        $author = new Author();

        $author->setName('Alex');
        $author->setFirstName('Jobs');
        $author->setBirthDate(new \DateTime('1965/06/09'));
        $author->setDeathDate(new \DateTime('2016/10/03'));
        $author->setBiography('RIP Alex');

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->render('showAuthor.html.twig',[
            'id'=> $author->getId(),
            'author' => $author,
            'books' => null
        ]);
    }

    /**
     * @Route("author/update/{id}", name="update_author")
     */
    public function updateAuthor($id, Request $request, AuthorRepository $authorRepository, EntityManagerInterface $entityManager)
    {

        $author = $authorRepository->find($id);

        // On crée le FormBuilder en appelant le formtype
        $form = $this->createForm(AuthorType::class, $author);

        //On crée la vue
        $formView = $form->createView();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $author contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {

                $entityManager->flush();

                // On redirige vers la page de visualisation de l'auteur modifié
                return $this->redirectToRoute('manage_library', ['author' => $author]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/manage_author/update.html.twig', [
            'form' => $formView
        ]);
    }

}