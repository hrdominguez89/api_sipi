<?php

namespace App\Controller\Secure;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/students")
 */
class StudentsController extends AbstractController
{
    /**
     * @Route("", name="get_students")
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/StudentsController.php',
        ]);
    }
    /**
     * @Route("/{student_id}", name="get_student_by_id")
     */
    public function getStudentById($student_id): JsonResponse
    {
        if (!(int)$student_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }
        return $this->json(
            [
                'message' => (int)$student_id
            ],
            Response::HTTP_ACCEPTED,
            ['Content-Type' => 'application/json']
        );
    }
}
