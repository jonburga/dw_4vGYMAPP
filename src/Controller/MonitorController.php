<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Monitor;
class MonitorController extends AbstractController
{
    #[Route('/monitors', name: 'get_monitor',methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $monitors = $entityManager->getRepository(Monitor::class)->findAll();
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

            if (empty($data)) {
                // No hay Monitores
                return new JsonResponse(['error' => 'No hay Monitores disponibles'], JsonResponse::HTTP_NOT_FOUND);
            }

            return $this->json($data);
        } catch (\Exception $e) {
            // Manejar la excepción
            return new JsonResponse(['error' =>  $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/monitors',name:'create_monitor', methods: ['POST'])]
    public function createMonitor(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Verificar si los datos necesarios están presentes en el array
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['photo'])) {
                return new JsonResponse(['error' => ' Faltan datos '], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Crear una nueva instancia de Monitor
            $monitor = new Monitor();
            $monitor->setName($data['name']);
            $monitor->setEmail($data['email']);
            $monitor->setPhone($data['phone']);
            $monitor->setPhoto($data['photo']);
            $entityManager->persist($monitor);
            $entityManager->flush();

            return $this->json([
                'error' => 'Se ha creado existosamente este monitor',
                'monitor' => [
                    'name' => $monitor->getName(),
                    'email' => $monitor->getEmail(),
                    'phone' => $monitor->getPhone(),
                    'photo' => $monitor->getPhoto(),
                ],
            ]);
        } catch (\Exception $e) {
            // Manejar la excepción
            return new JsonResponse(['error' =>  $e->getMessage() . ' Code: ' . $e->getCode()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/monitors/{id}',name:'changeActivity', methods: ['PUT'])]
    public function updateMonitor($id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $monitor = $entityManager->getRepository(Monitor::class)->find($id);

            if (!$monitor) {
                return new JsonResponse(['error' => 'Monitor no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            // Verificar si los datos necesarios están presentes en el array
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone']) || !isset($data['photo'])) {
                return new JsonResponse(['error' => 'Missing required data'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Actualizar los datos del Monitor
            $monitor->setName($data['name']);
            $monitor->setEmail($data['email']);
            $monitor->setPhone($data['phone']);
            $monitor->setPhoto($data['photo']);

            $entityManager->flush();

            return $this->json(['error' => 'Monitor cambiado con exito'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Manejar la excepción
            return new JsonResponse(['error' =>  $e->getMessage() . ' Code: ' . $e->getCode()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/monitors/{id}',name:'deleteActivity', methods: ['DELETE'])]
    public function deleteMonitor($id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $monitor = $entityManager->getRepository(Monitor::class)->find($id);

            if (!$monitor) {
                return new JsonResponse(['error' => 'Monitor no se aha encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $entityManager->remove($monitor);
            $entityManager->flush();
            return new JsonResponse(['error' => ' Monitor eliminidao'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Manejar la excepción
            return new JsonResponse(['error' => 'Error: ' . $e->getMessage() . ' Code: ' . $e->getCode()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
