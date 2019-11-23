<?php


namespace App\Controller;

use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class addAuthorController extends AbstractController
{

    /**
     * @Route("add_author", name="add_author")
     */
    public function new(Request $request)
    {
        // creates a author object and initializes some data for this example
        $author = new Author();

        $form = $this->createFormBuilder($author)
            ->add('name', TextType::class, ['label'=>'nom :'])
            ->add('firstName', TextType::class, ['label'=>'prénom :'])
            ->add('birthDate', DateType::class, ['label'=>'Date de naissance :'])
            ->add('deathDate', DateType::class, ['label'=>'Date de mort :', 'required'=>false, 'year'=>])
            ->add('save', SubmitType::class, ['label'=>'Ajouter'])
            ->getForm();

        return $this->render('add_author/new.html.twig', [
            'form' => $form->createView()
            ]);

    }
}