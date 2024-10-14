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

    public function __construct (ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }

    #[Route('/animal/upload', name: 'animal_upload')]
    public function upload(Request $request, SluggerInterface $slugger): Response
    {
        // Create the form
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        
        // Handle the request
        $form->handleRequest($request);
        $files = $form->get('files')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            // dump ($files);
            // dd($request);
            // Handle uploaded files
            // $files = $form->get('files')->getData(); // Assuming the field is called 'files'
            // dd($form->getData());            

            foreach ($files as $image) {
                
                // Obtenir le nom original du fichier
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // Utiliser un slugger pour créer une version "URL-friendly" pour le nom du fichier (mettre en snake-case, enlever les espaces...)
                $safeFilename = $slugger->slug($originalFilename);
                // Créer un nom unique pour le fichier
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where uploads are stored
                try {
                    $image->move(
                        $this->getParameter('dossier_uploads'), // fixé dans services.yaml, pour pouvoir le ré-configurer facilement.
                                                            // On aurait pu aussi utiliser un service d'upload, mais on simplifie ici.
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer une possible exception
                    dd ("gérer l'exception");                  
                    // return new JsonResponse(['code' => 'error', 'message' => 'un message ici'], 500); 

                }
                // Ajouter le lien vers le fichier dans l'entité (array)
                $animal->addImage($newFilename);
            }

            // Save the animal entity (assuming you have a repository)
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($animal);
            $entityManager->flush();

            // Success message and redirect
            // $this->addFlash('success', 'files uploaded successfully!');
            return new JsonResponse(['code' => 'success', 'message' => 'fichier(s) dans le disque, entité dans la BD!'], 200); 
        }
        // quand on charge la page avec le lien ou en tapant l'url dans le navigateur, on affiche le formulaire d'upload
        return $this->render('upload_multiple/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/animal/show', name: 'animal_show')]
    public function animalShow(AnimalRepository $rep){
        dd($rep->findAll());
    }
        
}
