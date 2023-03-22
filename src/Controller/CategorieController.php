<?php

namespace App\Controller;

use App\Dto\CategorieCountQuestionsDTO;
use App\Dto\CategorieWithQuestionsDTO;
use App\Repository\CategorieRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategorieController extends AbstractController
{
    private CategorieRepository $categorieRepository;
    private QuestionRepository $questionRepository;
    private SerializerInterface $serializer;

    /**
     * @param CategorieRepository $categorieRepository
     * @param QuestionRepository $questionRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(CategorieRepository $categorieRepository, QuestionRepository $questionRepository, SerializerInterface $serializer)
    {
        $this->categorieRepository = $categorieRepository;
        $this->questionRepository = $questionRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/categories', name: 'app_get_categories', methods: ['GET'])]
    public function getCategories(): Response
    {
        $categories = $this->categorieRepository->findAll();

        $categoriesJson = $this->serializer->serialize($categories, 'json', ['groups' => 'list_categories']);

        return new Response($categoriesJson, Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    #[Route('/api/categories/{slug}', name: 'api_get_categorie', methods: ['GET'])]
    public function getCategorie($slug): Response
    {
        $categorie = $this->categorieRepository->findOneBy(['slug' => $slug]);

        if (!$categorie) {
            return $this->generateError("La catégorie demandée n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $categorieDto = new CategorieCountQuestionsDTO();
        $categorieDto->setId($categorie->getId());
        $categorieDto->setLibelle($categorie->getLibelle());
        $categorieDto->setNbQuestions($categorie->getQuestions()->count());

        $categoryJson = $this->serializer->serialize($categorieDto, 'json');

        return new Response($categoryJson, Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    #[Route('/api/categories/{slug}/questions/{nombre}', name: 'api_get_categorie_nombre', methods: ['GET'])]
    public function getQuizCategorie($slug, $nombre): Response
    {
        $categorie = $this->categorieRepository->findOneBy(['slug' => $slug]);

        if (!$categorie) {
            return $this->generateError("La catégorie demandée n'existe pas.", Response::HTTP_NOT_FOUND);
        }

        $questions = $this->questionRepository->findBy(['categorie' => $categorie->getId()]);
        shuffle($questions);

        if (count($questions) < $nombre) {
            return $this->generateError("Il n'y a pas assez de questions", Response::HTTP_NOT_FOUND);
        }

        $questionsQuiz = [];
        for ($i=0; $i<$nombre; $i++){
            $categorieWithQuestionsDTO = new CategorieWithQuestionsDTO();
            $categorieWithQuestionsDTO->setId($questions[$i]->getId());
            $categorieWithQuestionsDTO->setLibelle(($questions[$i]->getLibelleQuestion()));
            $questionsQuiz[] = $categorieWithQuestionsDTO;
        }

        $categoryJson = $this->serializer->serialize($questionsQuiz, 'json');

        return new Response($categoryJson, Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    private function generateError(string $message,int $status) {
        // Créer un tableau associatif correspondant à l'erreur
        $erreur = [
            "status" => $status,
            "message" => $message
        ];
        // Générer la réponse HTTP
        return new Response(json_encode($erreur), $status, ["content-type" => "application/json"]);
    }
}
