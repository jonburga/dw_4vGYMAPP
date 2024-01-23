<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ActivityType;
class ActivityTypeController extends AbstractController
{
    #[Route('/activity-type', methods: ['GET'])]
    public function getActivityTypes(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $activityTypes = $entityManager->getRepository(ActivityType::class)->findAll();
            $data = [];
            foreach ($activityTypes as $activityType) {
                $data[] = [
                    'id' => $activityType->getId(),
                    'name' => $activityType->getName(),
                    'numbermonitors'=>$activityType->getNumbermonitors()
                   
                ];
            }
            if (empty($data)) {
                // No hay ActivityTypes
                return new JsonResponse(['status' => 'No hay ActivityTypes disponibles'], JsonResponse::HTTP_NOT_FOUND);
            }
            return $this->json($data);
        } catch (\Exception $e) {
            // Manejar la excepción
            return new JsonResponse(['status' => 'Error: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
