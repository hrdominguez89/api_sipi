<?php

namespace App\Controller\Secure;

use App\Entity\Programs;
use App\Form\ProgramsType;
use App\Repository\ProgramsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\FormErrorsUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/programs")
 */
class ProgramsController extends AbstractController
{
    private $formErrorsUtil;

    public function __construct(FormErrorsUtil $formErrorsUtil)
    {
        $this->formErrorsUtil = $formErrorsUtil;
    }


    /**
     * @Route("", name="programs", methods={"GET","POST"})
     */
    public function programs(ProgramsRepository $programsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() == 'GET') {
            $programs = $programsRepository->findAll();
            $data = [];
            foreach ($programs as $program) {
                $data[] = $program->getDataPrograms();
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
        $program = new Programs();
        $program->setName(@$data['name']);
        $program->setVersion(@$data['version']);
        $program->setObservations(@$data['observations']);

        $form = $this->createForm(ProgramsType::class, $program);
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
        $em->persist($program);
        $em->flush();
        return $this->json(
            $program->getDataPrograms(),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/{program_id}", name="programs_by_id", methods={"GET","PATCH"})
     */
    public function programsById($program_id, ProgramsRepository $programsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$program_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $program = $programsRepository->find($program_id);
        if (!$program) {
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
                $program->getDataPrograms(),
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        $program->setName(@$data['name'] ?: $program->getName());
        $program->setVersion(@$data['version'] ?: $program->getVersion());
        $program->setObservations(@$data['observations'] ?: $program->getObservations());

        $form = $this->createForm(StudentType::class, $program);
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
        $em->persist($program);
        $em->flush();
        return $this->json(
            $program->getDataPrograms(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
