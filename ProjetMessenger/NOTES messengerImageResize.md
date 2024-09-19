# Messenger 

https://symfony.com/doc/current/messenger.html

Messenger permet de **gérer des tâches asynchrones** dans une application Symfony. 
Cela permet d'améliorer les performances de l'application en évitant les temps d'attente inutiles. 


<br>

## Exemple pratique

<br>

Considérez un site qui permet d'uploader une photo de profil. L'utilisateur choisit la photo mais le serveur doit la traiter - changer la taille - pour éviter un gaspillage innécessaire de resources.
Considérez cette séquence :

**1**. L'utilisateur soumet le form avec l'image

**2**. Le controller reçoit le formulaire

**3**. Le controller fait appel à un service d'upload, tout ok. 

**4**. Le controller traite l'image

**5**. Le controller envoie une réponse au client (nouvelle page, message etc...)

Nous devons faire face à un possible problème: le traitement de l'image peut prendre du temps (ça serait encore pire si on devait traiter un fichier d'audio ou vidéo).
Pendant tout ce temps du traitement, l'utilisateur **devra attendre sans obtenir aucune réponse du serveur**! car 
l'éxécution de notre controller est **synchrone**.
**Messenger** est la solution car il va nous **permettre de lancer la tâche de traitement ()

Nous allons faire le code pour gérer cette situation!
On créera un controller qui affichera un formulaire d'upload pour uploader des images de pays. L'image será stocké dans le serveur après l'avoir reduite.
Le traitement se fera de façon **asynchrone**: **l'utilisateur recevra la réponse "upload ok" sans devoir attendre le traitement de l'image**.

On sait que le traitement se fera vraiment très vite. Pour pouvoir apprecier l'asynchronicité on mettra un sleep dans la fonction du traitement... on 
pourra voir que le client reçoit quand-même la réponse sans devoir attendre!

Allons-y!

<br>

## Procedure

<br>

Créez un projet ProjetMessenger (si vous ne l'avez pas encore).

    Nous allons utiliser du code déjà fait du ProjetFormulaires. Tout le code se trouve quand-même dans le repo (projetMessenger)
    Copiez l'entité Pays ainsi que son repository et son formulaire PaysType du ProjetFormulairesSymfony
    Copiez le template qui affiche le formulaire d'upload du ProjetFormulairesSymfony
    Copiez le service d'upload et configurer le paramètre pour le dossier de services.yaml tel qu'il se trouve dans ProjetFormulaires
    Faites la migration


## 1. Installez Messenger et Intervention (pour manipuler l'image)

<br>

```
composer require symfony/messenger
composer require intervention/image
```

Installez le support pour les fixtures:


```
symfony composer req --dev orm-fixtures
```


Dans **services.yaml**, assurez-vous d'avoir mis le paramètre pour configurer l'emplacement des uploads


```yaml
# This file is the entry point to configure your own services.
# Files in the packages/ subdirectory configure your dependencies.

# Put parameters here that don't need to change on each machine where the app is deployed
# https://symfony.com/doc/current/best_practices.html#use-parameters-for-application-configuration
parameters:

services:
    # default configuration for services in *this* file
    _defaults:
        autowire: true      # Automatically injects dependencies in your services.
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.

        bind:
            $dossierUpload: '%kernel.project_dir%/public/uploads'
.
.
.
```



## Créer le Message ResizeImage

**src/Message/ResizeImage.php**

```php
<?php

namespace App\Message;

class ResizeImage {
    private $path;
    private $width;
    private $height;

    public function __construct (string $path, int $width, int $height){
        $this->path = $path;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of path
     *
     * @return  self
     */ 
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of width
     */ 
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the value of width
     *
     * @return  self
     */ 
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get the value of height
     */ 
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the value of height
     *
     * @return  self
     */ 
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }
}
```

## Créer le handler

```php
<?php


namespace App\MessageHandler;

use App\Message\ResizeImage;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


// Annotation qui permet à Symfony de considérer cette class comme Handler
// Dans /config/packages/messenger.yaml on indique la correspondence entre 
// handlers y messages
#[AsMessageHandler]
class ResizeImageHandler {

    private $imageManager;
    private $logger;

    // on injecte l'ImageManager
    public function __construct (LoggerInterface $logger, ImageManager $imageManager){
        
        $this->logger = $logger;
        // librairie externe, on ne peux pas 
        // l'injecter sans le configurer (voir services.yaml)
        $this->imageManager = $imageManager;

    }

    public function __invoke (ResizeImage $message){
        $this->logger->info("Path du fichier: " . $message->getPath());
        // delai provoqué exprès
        sleep (10);
        // traitement de l'image
        $image = $this->imageManager->read ($message->getPath());
        $image->resize ($message->getWidth(), $message->getHeight());
        // enregistrer l'image sur le disque
        $image->save();
    }
}

```

## Configurer le message dans config/packages/messenger.yaml

```yaml
.
.
.
        routing:
            Symfony\Component\Mailer\Messenger\SendEmailMessage: async
            Symfony\Component\Notifier\Message\ChatMessage: async
            Symfony\Component\Notifier\Message\SmsMessage: async


            # Route your messages to the transports
            App\Message\ResizeImage: async

```

## Configurer la création du ImageManager dans services.yaml

Dans **config/services.yaml** on doit rajouter ImageManager comme service:

```yaml
.
.
.
    # add more service definitions when explicit configuration is needed
    # please note that last definitions always *replace* previous ones
    Intervention\Image\ImageManager:
        arguments:
            $driver: Intervention\Image\Drivers\Gd\Driver
```


## Configurer le transport dans .env

```yaml
###> symfony/messenger ###
# Choose one of the transports below
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```


## Créer le service pour gérer l'image et le service d'upload


**src/Service/ImageHelper**


```php
<?php

namespace App\Service;

use App\Message\ResizeImage;
use Symfony\Component\Messenger\MessageBusInterface;

// ce service permet de changer la taille d'une image.
// l'image originale sera modifiée dans le disque
class ImageHelper {
    

    // on injecte le bus de messages auquel on enverra de messages
    private $messageBus;
    private $dossierUpload;
    
    
    public function __construct (MessageBusInterface $messageBus, string $dossierUpload){
        $this->dossierUpload = $dossierUpload;
        $this->messageBus = $messageBus;        
    }

    public function resize (string $path, int $width, int $height){
        // on envoie le message au bus. Le handler le traitera à son tour
        $this->messageBus->dispatch(new ResizeImage(
            $this->dossierUpload . "/" . $path, 
            $width, 
            $height));

    } 

}
```

**src/Service/UploadHelper**

```php
<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{
    private string $dossierUpload;

    public function __construct (string $dossierUpload){
        $this->dossierUpload = $dossierUpload;
    }

    public function uploadImage(UploadedFile $fichier): string
    {

        // obtenir un nom de fichier unique pour éviter les doublons dans le dossierUpload
        $nomFichierServeur = md5(uniqid()) . "." . $fichier->guessExtension();
        // stocker le fichier dans le serveur (on peut préciser encore plus le dossier)
        $fichier->move($this->dossierUpload . "/", $nomFichierServeur);
        return $nomFichierServeur;
    }

    

}
```


## Créer le controller

```php
<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\PaysType;
use App\Service\ImageHelper;
use App\Service\UploadHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadAsyncController extends AbstractController
{

    // action pour uploader une image et la traiter en utilisant messenger
    // Le traitement de l'image se fait de façon asynchrone (ASYNC). 
    #[Route("/upload/async")]
    public function uploadAsync(Request $request, ManagerRegistry $doctrine, UploadHelper $uploader, ImageHelper $imageHelper)
    {
        $pays = new Pays();

        $formulairePays = $this->createForm(PaysType::class, $pays);
        $formulairePays->handleRequest($request);

        if ($formulairePays->isSubmitted() && $formulairePays->isValid()) {

            $fichier = $formulairePays['image']->getData();
            if ($fichier) {
                $nomFichierServeur = $uploader->uploadImage($fichier);
                $pays->setImage($nomFichierServeur);
            }

            // on a l'image et on va changer sa taille dans le disque: on appelle le service 
            // ImageHelper qui, à son tour, envoie un message à ResizeImageHandler
            $imageHelper->resize($nomFichierServeur, 30,30);


            $em = $doctrine->getManager();
            $em->persist($pays);  // pas besoin
            $em->flush();
            return new Response("Entité mise à jour dans la BD. Si le fichier a été selectionné, upload ok!");
        } else {
            return $this->render(
                "/upload_async/affichage_form_upload.html.twig",
                ['formulaire' => $formulairePays->createView()]
            );
        }
    }
}
```

## Lancer le worker

Pour consommer les messages, on lance le **worker**:

```
php bin/console messenger:consume async -vv
```




# Avantages d'utiliser Messenger

1. Séparation des responsabilités

Le composant Messenger permet de séparer la logique de traitement des messages de la logique métier. Cela signifie que vous pouvez écrire des handlers pour traiter les messages sans avoir à vous soucier de la logique métier de l'application.

2. Traitement asynchrone
   
Le composant Messenger prend en charge le traitement asynchrone des messages. Cela signifie que vous pouvez envoyer un message sur le bus de message et continuer à exécuter d'autres tâches sans attendre que le message soit traité. Cela peut améliorer les performances de l'application en évitant les blocages inutiles.


3. Scalabilité
   
Le composant Messenger prend en charge plusieurs transports, comme les files d'attente, les bases de données ou les sockets, ce qui vous permet de choisir le transport qui convient le mieux à vos besoins et de scaler facilement en fonction de la charge de travail.

4. Flexibilité 
   
Le composant Messenger permet de créer des workflows de traitement de message complexe en enchainant des handlers, en utilisant des middleware, etc. Il est également possible de définir des règles de routage pour rediriger les messages vers les handlers appropriés.

5. Intégration avec d'autres composants

Le composant Messenger est étroitement lié aux autres composants de Symfony, tels que les événements, les commandes, les tâches planifiées, etc. Il est donc facile de l'intégrer dans une application existante qui utilise déjà ces composants.

6. Sécurité 

Le composant Messenger permet de sécuriser les messages en utilisant des bus sécurisés, et de s'assurer que seul les handlers autorisés peuvent traiter les messages.

Ces avantages font de Messenger un outil puissant pour construire des applications robustes, évolutives et fiables.