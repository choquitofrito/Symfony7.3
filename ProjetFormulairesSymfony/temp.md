## 1. Créer l'entité **Animal** (nom: string, images: json)

On utilisera le type json pour la propriété **images** car on a plusieurs valeurs simples pour la même colonne (chaque lien de l'image)

Nous devons rajouter deux méthodes: **addImage** pour rajouter une image dans l'array et **removeImage** pour l'effaccer de l'array.

Voici le code final de l'entité:

```php
<?php

namespace App\Entity;

use ORM\Entity;
use Doctrine\ORM\Mapping as ORM;
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
```

## 2. Créez le form qui permettra de rajouter plusieurs images (ou aucune)



## 3. Créez le controller


**Attention**: Rajoutez le paramètre 




```yaml
parameters:
    dossier_upload: '%kernel.project_dir%/public/uploads/images'
```


## 4. Créez la vue


