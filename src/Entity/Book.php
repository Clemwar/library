<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;


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
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $style;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inStock;

    /**
     * One Author has many books.
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="book")
     */
    private $author;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    public function __construct()
    {

    }

    public function getAuthor()
    {
        return $this->author;
    }


    public function setAuthor($author): void
    {
        $this->author = $author;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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
    public function setImage($image): void
    {
        $this->image = $image;
    }

}
