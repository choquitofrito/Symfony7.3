<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\AnimalRepository;

#[Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?array $images = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }

    // méthodes crées pour gérer les images dans l'entité
    public function addImage(string $image): self
    {
        // dump ($image);
        // dd($this->images);

        if (!is_array($this->images)) {
            $this->images = [];
        }

        // chercher l'image dans les images existants (c'est une recherche de string, il s'agit de liens)
        if (!in_array($image, $this->images, true)) {
            $this->images[] = $image;
        }

        return $this;
    }


    public function removeImage(string $image): self
    {
        // effacer l'image de l'array d'images
        if (in_array($image, $this->images, true)) {
            $this->images = array_diff($this->images, [$image]);
        }

        return $this;
    }
}
