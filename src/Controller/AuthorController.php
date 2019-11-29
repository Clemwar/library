<?php


namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AuthorController extends AbstractController
{


    /**
     * @Route("/author/list", name="authors")
     */

    //Récupération des données de la table author
    public function showAuthors(AuthorRepository $authorRepository, Request $request){

        //Récupération des données de la table author
        $authors = $authorRepository->findAll();

        $intitule = 'Liste des auteurs';

        if(!empty($request->get('intitule'))){
            $intitule = $request->get('intitule');
        }

        //envoi des données au rendu
        return $this->render('/author/showAuthors.html.twig', [
            'authors' => $authors,
            'intitule' => $intitule
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
        return $this->render('/author/showAuthor.html.twig', [
            'author' => $author,
            'books' => $booksByAuth,
            'intitule' => 'Auteur'
        ]);
    }

    /**
     * @Route("/admin/author/add", name="admin_add_author")
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
                return $this->redirectToRoute('admin', [
                    'message' => 'Auteur ajouté',
                    'intitule' => null
                ]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/form.html.twig', [
            'form' => $formView,
            'intitule' => 'Ajouter un auteur'
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
            return $this->render('/author/showAuthors.html.twig', [
                'authors' => $authors,
                'name' => $name,
                'niet' => false,
                'intitule' => 'Recherche d\'auteurs dont le nom contient : '.$name
            ]);
        }
        return $this->render('/author/showAuthors.html.twig', [
            'authors' => $authors,
            'name' => $name,
            'niet' => 'Aucun auteur trouvé',
            'intitule' => 'Recherche d\'auteurs dont le nom contient : '.$name
        ]);
    }

    /**
     * @Route("/admin/author/delete/{id}", name="admin_delete_author")
     */
    public function deleteAuthor(EntityManagerInterface $entityManager, $id, AuthorRepository $authorRepository){

        $author = $authorRepository->find($id);

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('admin', [
            'message' => 'Auteur supprimé',
            'intitule' => null
        ]);
    }

    /**
     * @Route("/admin/author/update/{id}", name="admin_update_author")
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
                return $this->redirectToRoute('admin', [
                    'message' => 'Auteur modifié',
                    'intitule' => null
                ]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('/manage/form.html.twig', [
            'form' => $formView,
            'intitule' => 'Mettre à jour un auteur'
        ]);
    }

}