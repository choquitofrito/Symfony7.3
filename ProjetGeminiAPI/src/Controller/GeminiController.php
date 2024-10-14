<?php

namespace App\Controller;

use App\Form\GenreFilmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiController extends AbstractController
{
    // injecter le service HttpClientInterface pour faire l'appel à l'API
    // l'API key est dans le fichier .env.local ou .env

    #[Route('/gemini/form', name: 'gemini_form_show')]
    public function showForm(Request $request, HttpClientInterface $client): Response
    {
        // obtenir l'API key
        $apiKey = $this->getParameter('api_key_gemini'); // cette variable est définie dans le fichier .env et 
        // elle est configurée comme paramètre dans /config/services.yaml,
        // ce qui permet de l'obtenir dans le controller 

        // form normal...
        $form = $this->createForm(GenreFilmType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $genre = $data['genre'];

            $texteRequete = "Invente le titre et la description dun film de ce genre: " . $genre . ". Les protagonistes sont des developpeuses d'applications webs backend qui n'aiment pas le css.";

            // on doit construir le contenu à envoyer à cette API, qui suit ce format :
            $donneesRequete = [
                'contents' => [
                    'parts' => [
                        ['text' => $texteRequete]
                    ]
                ]
            ];

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;


            // on appelle l'API et on envoie la requête
            // on obtient une reponse
            $reponse = $client->request('POST', $url, [
                'json' => $donneesRequete,
                'headers' => ['Content-Type: application/json']
            ]);

            $contenu = $reponse->toArray();
            // dd ($contenu); // pour voir la structure de la reponse

            // dd($contenu['candidates'][0]['content']['parts'][0]['text']);
            $texteResponse = $contenu['candidates'][0]['content']['parts'][0]['text'];

            // pour debug:
            // dump ($contenu);
            // dd($texteResponse);

            // on a la reponse, on la renvoie vers une autre action pour qu'elle puisse l'afficher
            return $this->redirectToRoute('gemini_description_show', ['texteResponse' => $texteResponse]);
        }

        $vars = ['form' => $form];
        return $this->render('gemini/show_form.html.twig', $vars);
    }

    #[Route('/gemini/description/{texteResponse}', name: 'gemini_description_show')]
    public function showDescription(string $texteResponse): Response
    {
        $vars = ['texteResponse' => $texteResponse];
        return $this->render('gemini/show_description.html.twig', $vars);
    }
}
