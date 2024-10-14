# Système d'Upload Multiple de Fichiers avec Symfony 7

Ce guide explique comment mettre en œuvre un système d'upload multiple de fichiers dans une application Symfony 7, en utilisant l'entité `Animal`, le formulaire `AnimalType` et le contrôleur pour gérer les uploads. Le système permet aux utilisateurs de sélectionner plusieurs fichiers, affiche dynamiquement les fichiers sélectionnés et leur permet de retirer des fichiers avant la soumission.

## Étape 1 : Configuration de l'Entité - Animal.php

Nous commençons par définir l'entité `Animal`. Le champ `images` est un tableau qui contient les chemins des images téléchargées. De plus, deux méthodes `addImage` et `removeImage` sont fournies pour gérer les images de manière dynamique.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnimalRepository;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
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

    public function addImage(string $image): self
    {
        if (!is_array($this->images)) {
            $this->images = [];
        }
        if (!in_array($image, $this->images, true)) {
            $this->images[] = $image;
        }
        return $this;
    }

    public function removeImage(string $image): self
    {
        if (in_array($image, $this->images, true)) {
            $this->images = array_diff($this->images, [$image]);
        }
        return $this;
    }
}
```

## Étape 2 : Création du Formulaire - AnimalType.php

Ensuite, nous créons le formulaire `AnimalType` qui permet à l'utilisateur d'entrer le nom de l'animal et de télécharger des fichiers. Le champ pour les fichiers est configuré pour accepter plusieurs fichiers.

```php
<?php
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('files', FileType::class, [
                'label' => 'Upload Files',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'file-upload-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
```

## Étape 3 : Gestion des Uploads - UploadMultipleController.php

Le contrôleur `UploadMultipleController` gère les requêtes d'upload. Il crée le formulaire, gère les fichiers téléchargés, les enregistre sur le disque et met à jour l'entité `Animal`.

```php
<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadMultipleController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/animal/upload', name: 'animal_upload')]
    public function upload(Request $request, SluggerInterface $slugger): Response
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $files = $form->get('files')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($files as $image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move($this->getParameter('dossier_uploads'), $newFilename);
                } catch (FileException $e) {
                    dd("Gérer l'exception");
                }
                $animal->addImage($newFilename);
            }

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($animal);
            $entityManager->flush();

            return new JsonResponse(['code' => 'success', 'message' => 'Fichier(s) dans le disque, entité dans la BD!'], 200);
        }

        return $this->render('upload_multiple/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/animal/show', name: 'animal_show')]
    public function animalShow(AnimalRepository $rep)
    {
        dd($rep->findAll());
    }
}
```

## Étape 4 : Création de la Vue - upload.html.twig

Enfin, nous créons la vue qui affiche le formulaire d'upload et gère l'affichage des fichiers sélectionnés.

```twig
{% extends 'base.html.twig' %}

{% block body %}
<form id="upload-form" method="post" enctype="multipart/form-data">
    {{ form_start(form) }}
        {{ form_widget(form) }}
        <ul id="file-list"></ul> <!-- Liste des fichiers sélectionnés -->
        <button type="submit">Upload</button>
    {{ form_end(form) }}
</form>

<script>
let allFiles = []; // Tableau pour stocker tous les noms des fichiers

document.querySelector('#animal_files').addEventListener('change', function(e) {
    const newFiles = Array.from(e.target.files); 
    allFiles = allFiles.concat(newFiles); 

    const fileList = document.querySelector('#file-list');
    fileList.innerHTML = ''; // Nettoyer la liste de fichiers

    allFiles.forEach((file, index) => {
        const listItem = document.createElement('li');
        listItem.classList.add('file-item');
        listItem.textContent = file.name;

        const removeLink = document.createElement('a');
        removeLink.href = '#';
        removeLink.textContent = ' X';
        removeLink.classList.add('remove-file');
        removeLink.onclick = function(e) {
            e.preventDefault();
            allFiles.splice(index, 1);
            listItem.remove();
        };

        listItem.appendChild(removeLink);
        fileList.appendChild(listItem);
    });

    e.target.value = '';
});

document.querySelector('#upload-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêcher la soumission par défaut

    const formData = new FormData(this); 

    allFiles.forEach((file) => {
        formData.append('animal[files][]', file);
    });

    fetch(this.action, {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        console.log('Soumission réussie:', data.message);
    })
    .catch(error => {
        console.error('Erreur dans la soumission:', error);
    });
});
</script>
{% endblock %}
