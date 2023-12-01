<?php

namespace App\Controller\Secure;

use App\Constants\Constants;
use App\Entity\Computers;
use App\Entity\StatusComputer;
use App\Form\ComputersType;
use App\Repository\ComputersRepository;
use App\Repository\StatusComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\FormErrorsUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/api/computers")
 */
class ComputersController extends AbstractController
{
    private $formErrorsUtil;

    public function __construct(FormErrorsUtil $formErrorsUtil)
    {
        $this->formErrorsUtil = $formErrorsUtil;
    }

    /**
     * @Route("", name="computers", methods={"GET","POST"})
     */
    public function computers(StatusComputerRepository $statusComputerRepository, ComputersRepository $computersRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() == 'GET') {
            $computers = $computersRepository->getAllComputers();
            $data = [];
            foreach ($computers as $computer) {
                $data[] = $computer->getDataComputers();
            }
            return $this->json(
                $data,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        $status_computer_available = $statusComputerRepository->findOneBy(['id' => Constants::STATUS_COMPUTER_AVAILABLE]);

        //creo el formulario para hacer las validaciones    
        $computer = new Computers();
        $computer->setName(@$data['name']);
        $computer->setBrand(@$data['brand']);
        $computer->setModel(@$data['model']);
        $computer->setSerie(@$data['serie']);
        $computer->setDetails(@$data['details']);
        $computer->setStatusComputer(@$status_computer_available);


        $form = $this->createForm(ComputersType::class, $computer);
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
        $em->persist($computer);
        $em->flush();
        return $this->json(
            $computer->getDataComputers(),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/{computer_id}", name="computers_by_id", methods={"GET","PATCH"})
     */
    public function computersById($computer_id, ComputersRepository $computersRepository, StatusComputerRepository $statusComputerRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$computer_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $computer = $computersRepository->find($computer_id);
        if (!$computer) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Computadora no encontrada.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }
        if ($request->getMethod() == 'GET') {
            return $this->json(
                $computer->getDataComputers(),
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        $status_computer_available = $statusComputerRepository->findOneBy(['id' => @$data['status_computer']]);

        $computer->setName(@$data['name'] ?: $computer->getName());
        $computer->setBrand(@$data['brand'] ?: $computer->getBrand());
        $computer->setModel(@$data['model'] ?: $computer->getModel());
        $computer->setSerie(@$data['serie'] ?: $computer->getSerie());
        $computer->setDetails(@$data['details'] ?: $computer->getDetails());
        $computer->setStatusComputer(@$status_computer_available ?: $computer->getStatusComputer());

        $form = $this->createForm(ComputersType::class, $computer);
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
        $em->persist($computer);
        $em->flush();
        return $this->json(
            $computer->getDataComputers(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/delete/{computer_id}", name="delete_computer_by_id", methods={"DELETE"})
     */
    public function deleteComputerById($computer_id, ComputersRepository $computersRepository, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$computer_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $computer = $computersRepository->find($computer_id);
        if (!$computer) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Computadora no encontrada.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        $computer->setVisible(false);
        $em->persist($computer);
        $em->flush();
        return $this->json(
            ['message' => 'Computadora eliminada correctamente'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/available", name="computers_by_id", methods={"GET"})
     */
    public function computersAvailable(ComputersRepository $computersRepository, StatusComputerRepository $statusComputerRepository): JsonResponse
    {

        $statu_available = $statusComputerRepository->find(Constants::STATUS_COMPUTER_AVAILABLE);

        $computers = $computersRepository->getComputersByStatus($statu_available);

        $computers_lists=[];
        foreach($computers as $computer){
            $computer_list[]=$computer->getDataComputers();
        }

        return $this->json(
            $computer_list,
            Response::HTTP_ACCEPTED,
            ['Content-Type' => 'application/json']
        );
    }
}
