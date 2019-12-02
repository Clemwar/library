<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Your title must be at least {{ limit }} characters long",
     *      maxMessage = "Your title name cannot be longer than {{ limit }} characters"
     * )
     *
     * @Assert\NotNull(message="Merci d'ajouter un titre")
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="le genre doit faire 255 caractères maximum")
     */
    private $style;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inStock;

    /**
     * One Author has many books.
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="books")
     */
    private $author;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $image;



    public function getAuthor()
    {
        return $this->author;
    }


    public function setAuthor($author)
    {
        $this->author = $author;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function getNbpages(): ?int
    {
        return $this->nbPages;
    }

    public function setNbPages(?int $nbPages): self
    {
        $this->nbPages = $nbPages;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getInStock(): ?bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $inStock = null): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

}
