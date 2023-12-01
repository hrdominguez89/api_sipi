<?php

namespace App\Controller\Secure;

use App\Entity\Students;
use App\Form\StudentType;
use App\Repository\StudentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\FormErrorsUtil;

/**
 * @Route("/api/students")
 */
class StudentsController extends AbstractController
{

    private $formErrorsUtil;

    public function __construct(FormErrorsUtil $formErrorsUtil)
    {
        $this->formErrorsUtil = $formErrorsUtil;
    }


    /**
     * @Route("", name="students", methods={"GET","POST"})
     */
    public function index(StudentsRepository $studentsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() == 'GET') {
            $students = $studentsRepository->getAllStudents();
            $data = [];
            foreach ($students as $student) {
                $data[] = $student->getDataStudent();
            }
            return $this->json(
                $data,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        //creo el formulario para hacer las validaciones    
        $student = new Students();
        $student->setDni(@$data['dni']);
        $student->setFullname(@$data['fullname']);

        $form = $this->createForm(StudentType::class, $student);
        $form->submit($data, false);

        if (!$form->isValid()) {
            $error_forms = $this->formErrorsUtil->getErrorsFromForm($form);
            return $this->json(
                [
                    'message' => 'Error de validación.',
                    'validation' => $error_forms
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }
        $em->persist($student);
        $em->flush();
        return $this->json(
            $student->getDataStudent(),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/dni/{dni}", name="student_by_dni", methods={"GET"})
     */
    public function studentByDNI($dni, StudentsRepository $studentsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$dni) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $student = $studentsRepository->findOneBy(['dni' => $dni]);
        if (!$student) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Usuario no encontrado.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }
        return $this->json(
            $student->getDataStudent(),
            Response::HTTP_ACCEPTED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/delete/{student_id}", name="delete_student_by_id", methods={"DELETE"})
     */
    public function deleteComputerById($student_id, StudentsRepository $studensRepository, EntityManagerInterface $em): JsonResponse
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

        $studen = $studensRepository->find($student_id);
        if (!$studen) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Estudiante no encontrado.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        $studen->setVisible(false);
        $em->persist($studen);
        $em->flush();
        return $this->json(
            ['message'=>'Estudiante eliminado correctamente'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
    
    /**
     * @Route("/{student_id}", name="student_by_id", methods={"GET","PATCH"})
     */
    public function studentById($student_id, StudentsRepository $studentsRepository, Request $request, EntityManagerInterface $em): JsonResponse
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

        $student = $studentsRepository->find($student_id);
        if (!$student) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Usuario no encontrado.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }
        if ($request->getMethod() == 'GET') {
            return $this->json(
                $student->getDataStudent(),
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        $student->setDni(@$data['dni'] ?: $student->getDni());
        $student->setFullname(@$data['fullname'] ?: $student->getFullname());

        $form = $this->createForm(StudentType::class, $student);
        $form->submit($data, false);

        if (!$form->isValid()) {
            $error_forms = $this->formErrorsUtil->getErrorsFromForm($form);
            return $this->json(
                [
                    'message' => 'Error de validación.',
                    'validation' => $error_forms
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }
        $em->persist($student);
        $em->flush();
        return $this->json(
            $student->getDataStudent(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
