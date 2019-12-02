<?php


namespace App\Controller;


use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{


    /**
     * @Route("/", name="books")
     */

    //Récupération des données de la table book
    public function showBooks(BookRepository $bookRepository, Request $request){

        //Récupération des données de la table book
        $books = $bookRepository->findAll();

        $intitule = 'Tous nos livres';

        if (!empty($request->get('intitule'))){
            $intitule = $request->get('intitule');
        }

        //envoi des données au rendu
        return $this->render('/book/showBooks.html.twig', [
            'books' => $books,
            'intitule' => $intitule
        ]);
    }

    /**
     * @Route("book/show/{id}", name="book")
     */
    public function showBook($id, BookRepository $bookRepository, Request $request){

        //Sélection des données du livre voulu via l'id du livre
        $book = $bookRepository->find($id);

        $intitule = 'Livre';

        if (!empty($request->get('intitule'))){
            $intitule = $request->get('intitule');
        }

        //envoi des données au rendu
        return $this->render('/book/showBook.html.twig', [
            'book' => $book,
            'intitule' => $intitule
        ]);
    }

    /**
     * @Route("/admin/book/add", name="admin_add_book")
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager)
    {
        $book = new Book();
        $message = 'Livre ajouté';

        // On crée le FormBuilder en appelant le formtype
        $form = $this->createForm(BookType::class, $book);


        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $book contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {

                //On traite l'ajout d'image
                /** @var UploadedFile $image */
                $image = $form['image']->getData();

                // On vérifie la présence d'un fichier uploadé
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                    //On place le fichier dans le dossier prévu sur le serveur
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $message = 'La mise en ligne de l\'image a échoué';
                    }

                    // On ajoute le nom de l'image au livre concerné
                    $book->setImage($newFilename);
                }

                // On enregistre notre objet $author dans la base de données, par exemple
                $entityManager->persist($book);
                $entityManager->flush();


                // On redirige vers la page de visualisation du livre nouvellement créé
                    return $this->redirectToRoute('admin', [
                    'message' => $message,
                    'intitule' => null
                ]);
            }
        }

        //On crée la vue
        $formView = $form->createView();

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('book/form.html.twig', [
            'form' => $formView,
            'intitule' => 'Ajouter un livre'
        ]);
    }

    /**
     * @Route("/books_by_style/{style}", name="books_by_style")
     */
    public function getBooksByStyle($style, BookRepository $bookRepository){
        $books = $bookRepository->getByStyle($style);

        return $this->render('/book/showBooks.html.twig', [
            'books' => $books,
            'style' => $style,
            'intitule' => 'Recherche par genre : '.$style
        ]);
    }

    /**
     * @Route("book/search", name="search_by_title")
     */
    public function getBookByTitle(Request $request, BookRepository $bookRepository){
        $title = $request->get('title');
        $books = $bookRepository->getByTitle($title);

        if (count($books)>1)
        {
            $intitule = 'Les livres dont les titres contiennent : ' . $title;
        }
        else
            {
            $intitule = 'Le livre dont le titre contient : ' . $title;
            }

        if (count($books) >= 1) {
            return $this->render('/book/showBooks.html.twig', [
                'books' => $books,
                'title' => $title,
                'niet' => false,
                'intitule' => $intitule
            ]);
        }
        return $this->render('/book/showBooks.html.twig', [
            'books' => $books,
            'title' => $title,
            'niet' => 'Aucun livre trouvé.',
            'intitule' => 'Les livres dont les titres contiennent : '.$title
        ]);
    }

    /**
     * @Route("/admin/book/delete/{id}", name="admin_delete_book")
     */
    public function deleteBook(EntityManagerInterface $entityManager, $id, BookRepository $bookRepository){

        $book = $bookRepository->find($id);

        //J'appelle le gestionnaire de fichier
        $filesystem = new Filesystem();

        //Je m'assure qu'une image est liée au livre
        if ($book->getImage() !== null){
            //Je vérifie si le fichier existe, si oui, symfony le supprime
            if ($filesystem->exists( $imageURL = $this->getParameter('images_directory') ."/". $book->getImage())){
                $filesystem->remove([$imageURL]);
            }
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('admin', [
            'message' => 'Livre supprimé',
            'intitule' => null
        ]);
    }

    /**
     * @Route("/admin/book/update/{id}", name="admin_update_book")
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

                //On traite l'ajout d'image
                /** @var UploadedFile $image */
                $image = $form['image']->getData();

                // On vérifie la présence d'un fichier uploadé
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                    //On place le fichier dans le dossier prévu sur le serveur
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $message = 'La mise en ligne de l\'image a échoué';
                    }

                    // On ajoute le nom de l'image au livre concerné
                    $book->setImage($newFilename);
                }

                $entityManager->flush();

                // On redirige vers la page de visualisation de le livre mis à jour
                return $this->redirectToRoute('admin', [
                    'message' => 'Livre modifié',
                    'intitule' => null
                ]);
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('book/form.html.twig', [
            'form' => $formView,
            'intitule' => 'Mettre à jour un livre'
        ]);
    }
}
