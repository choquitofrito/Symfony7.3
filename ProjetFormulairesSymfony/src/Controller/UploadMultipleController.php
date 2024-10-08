<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadMultipleController extends AbstractController
{
    #[Route('/upload/multiple/rajouter', name: 'animal_rajouter')]
    public function uploadMultiplelRajouter(Request $request, ManagerRegistry $doctrine)
    {
        $animal = new Animal ();

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $files = $form->get('files')->getData();

            dd($files);
            $uploadDir = $this->getParameter('dossier_upload'); // Make sure this directory is configured
            $filePaths = [];

            foreach ($files as $file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($uploadDir, $filename);
                $filePaths[] = $filename; // Store the uploaded file path
                
            }
            
            // if (count($filePaths)>2){
                dd($filePaths);
            // }
            // stocker les chemins pour les images
            $animal->setImages($filePaths); // on stocke just les noms des images

            $doctrine->getManager()->persist($animal);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('animal_show');
        } else {
            return $this->render('upload_multiple/rajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }


    #[Route('/animal/show', name: 'animal_show')]
    public function animalShow(AnimalRepository $rep){
        dd($rep->findAll());
    }
        
}
