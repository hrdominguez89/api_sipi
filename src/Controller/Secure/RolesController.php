<?php

namespace App\Controller\Secure;

use App\Repository\RolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/roles")
 */
class RolesController extends AbstractController
{
    /**
     * @Route("", name="roles", methods={"GET"})
     */
    public function index(RolesRepository $rolesRepository): JsonResponse
    {
        $roles = $rolesRepository->findAll();
        $data = [];
        foreach ($roles as $role) {
            $data[] = $role->getDataRoles();
        }
        return $this->json(
            $data,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
