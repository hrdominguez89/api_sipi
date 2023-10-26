<?php

namespace App\Controller\Secure;

use App\Repository\StatusComputerRepository;
use App\Repository\StatusRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/status")
 */
class StatusTypesController extends AbstractController
{
    /**
     * @Route("/computers", name="status_types_computers")
     */
    public function computers(StatusComputerRepository $statusComputerRepository): JsonResponse
    {
        $statusComputers = $statusComputerRepository->findAll();
        $data = [];
        foreach ($statusComputers as $statusComputer) {
            $data[] = $statusComputer->getDataStatus();
        }
        return $this->json(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/requests", name="status_types_requests")
     */
    public function requests(StatusRequestRepository $statusRequestRepository): JsonResponse
    {
        $statusRequests = $statusRequestRepository->findAll();
        $data = [];
        foreach ($statusRequests as $statusRequest) {
            $data[] = $statusRequest->getDataStatus();
        }
        return $this->json(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
