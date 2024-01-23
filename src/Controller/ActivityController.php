<?php

namespace App\Controller;

use App\Entity\Activity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ActivityType;
use App\Entity\Monitor;



use DateTime;
class ActivityController extends AbstractController
{
    #[Route('/activities', name: 'get_activities', methods: ['GET'])]
    public function getAll(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $dateParam = $request->query->get('date_param');
        try {
            $date = DateTime::createFromFormat('d-m-Y', $dateParam);
            if (!$date) {
                throw new \Exception('Invalid date format. Please use dd-MM-yyyy.');
            }
            $formattedDate = $date->format('Y-m-d');
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $data = [];
        try {
            $startDate = new DateTime($formattedDate . ' 00:00:00');
            $endDate = new DateTime($formattedDate . ' 23:59:59');

            // Utiliza getRepository y createQueryBuilder desde el EntityManager
            $activityRepository = $entityManager->getRepository(Activity::class);
            $activities = $activityRepository->createQueryBuilder('a')
                ->where('a.date_start BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        if (!$activities) {
            return $this->json(['error' => 'No activities for the specified date'], 404);
        }

        foreach ($activities as $activity) {
            $monitorsData = [];
            foreach ($activity->getMonitors() as $monitor) {
                $monitorsData[] = [
                    'monitor_id' => $monitor->getId(),
                    'name' => $monitor->getName(),
                    'email' => $monitor->getEmail(),
                    'phone' => $monitor->getPhone(),
                    'photo' => $monitor->getPhoto(),
                ];
            }

            $data[] = [
                'id' => $activity->getId(),
                'activity_type_id' => $activity->getActivityType()->getId(),
                'monitors' => $monitorsData,
                'date_start' => $activity->getDateStart()->format('Y-m-d H:i:s'),
                'date_end' => $activity->getDateEnd()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }
    #[Route('/activities', methods: ['POST'])]
    public function createActivity(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $activityTypeRepository = $entityManager->getRepository(ActivityType::class);
        $monitorRepository = $entityManager->getRepository(Monitor::class);
    
        $activityType = $activityTypeRepository->find($data['id']);
        if (!$activityType) {
            return $this->json([ 'eror' => 'No se ha encontradoe activitadType'], 400);
        }
        if (count($data['monitors_id']) < $activityType->getNumbermonitors()) {
            return $this->json(['eror'=> 'No hay suficientes monitores para este tipo de actividad'], 400);
        }
    
        $monitors = $monitorRepository->findBy(['id' => $data['id']]);
        if (count($monitors) !== count($data['id'])) {
            return $this->json(['errord' => 'Monitor sno encontrado'], 400);
        }
    
        $dateStart = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['date_start']);
        $dateEnd = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['date_end']);
    
        $allowedStartTimes = ['09:00', '13:30', '17:30'];
        $duration = $dateStart->diff($dateEnd);
        $totalMinutes = $duration->days * 24 * 60;
        $totalMinutes += $duration->h * 60;
        $totalMinutes += $duration->i;
        
        if (!in_array($dateStart->format('H:i'), $allowedStartTimes) || $totalMinutes != 90) {
            return $this->json(['error' => 'No vale '], 400);
        }
        
        $activity = new Activity();
        $activity->setActivityType($activityType);
        foreach ($monitors as $monitor) {
            $activity->addMonitor($monitor);
        }
        $activity->setDateStart($dateStart);
        $activity->setDateEnd($dateEnd);
    
        $entityManager->persist($activity);
        $entityManager->flush();
    
        $activityData = [
            'id' => $activity->getId(),
            'activityType' => [
                'id' => $activity->getActivityType()->getId(),
                'name' => $activity->getActivityType()->getName(),
                'numbersOfMonitors' => $activity->getActivityType()->getNumbermonitors(),
            ],
            'monitors' => $this->formatMonitorsData($activity->getMonitors()),
            'date_start' => $activity->getDateStart()->format('Y-m-d H:i'),
            'date_end' => $activity->getDateEnd()->format('Y-m-d H:i'),
        ];
        
    
        return $this->json($activityData);
    }

    private function formatMonitorsData(Collection $monitors): array
    {
        $data = [];
        foreach ($monitors as $monitor) {
            $data[] = [
                'id' => $monitor->getId(),
                'name' => $monitor->getName(),
                'email' => $monitor->getEmail(),
                'phone' => $monitor->getPhone(),
                'photo' => $monitor->getPhoto(),
            ];
        }
        return $data;
    }

    #[Route('/activities/{id}', name: 'changeActivity', methods: ['PUT'])]
public function updateActivity($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    try {
        $activity = $entityManager->getRepository(Activity::class)->find($id);

        if (!$activity) {
            return new JsonResponse(['code' => 'Actividad no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Verificar si los datos necesarios están presentes en el array
        if (!isset($data['type']) || !isset($data['date_start']) || !isset($data['date_end'])) {
            return new JsonResponse(['code' => 'Faltan datos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $activityType = $entityManager->getRepository(ActivityType::class)->findOneBy(['name' => $data['type']]);
        if (!$activityType) {
            return new JsonResponse(['code' => 'No se ha encontrado el activty Type'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $activity->setActivityType($activityType);
        $activity->setDateStart(DateTime::createFromFormat('d-MM-yyyy H:i', $data['date_start'] . ' 09:00'));
        $activity->setDateEnd(DateTime::createFromFormat('d-MM-yyyy H:i', $data['date_end'] . ' 10:30'));

        $entityManager->flush();

        // Formato de la respuesta directamente en el código
        $monitorsData = [];
        foreach ($activity->getMonitors() as $monitor) {
            $monitorsData[] = [
                'id' => $monitor->getId(),
                'name' => $monitor->getName(),
                'email' => $monitor->getEmail(),
                'phone' => $monitor->getPhone(),
                'photo' => $monitor->getPhoto(),
            ];
        }

        $activityData = [
            'id' => $activity->getId(),
            'activityType' => [
                'id' => $activity->getActivityType()->getId(),
                'name' => $activity->getActivityType()->getName(),
                'numbersOfMonitors' => $activity->getActivityType()->getNumbermonitors(),
            ],
            'monitors' => $monitorsData,
            'date_start' => $activity->getDateStart()->format('d-MM-yyyy H:i'),
            'date_end' => $activity->getDateEnd()->format('d-MM-yyyy H:i'),
        ];

        return $this->json($activityData);
    } catch (\Exception $e) {
        return new JsonResponse(['status' => 'Error: ' . $e->getMessage() . ' Code: ' . $e->getCode()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}






    #[Route('/activities/{id}',name:'deleteActivity', methods: ['DELETE'])]
    public function deleteActivity($id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $activity = $entityManager->getRepository(Activity::class)->find($id);

            if (!$activity) {
                return new JsonResponse(['status' => 'No se ha encontrado esta actividad'], JsonResponse::HTTP_NOT_FOUND);
            }

            $entityManager->remove($activity);
            $entityManager->flush();

            return new JsonResponse(['status' => 'Actividad eliminada'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Error: ' . $e->getMessage() . ' Code: ' . $e->getCode()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
