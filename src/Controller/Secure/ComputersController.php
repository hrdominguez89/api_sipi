<?php

namespace App\Controller\Secure;

use App\Constants\Constants;
use App\Entity\Computers;
use App\Entity\RequestsComputers;
use App\Form\ComputersType;
use App\Repository\ComputersRepository;
use App\Repository\RequestsComputersRepository;
use App\Repository\RequestsRepository;
use App\Repository\StatusComputerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\FormErrorsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpKernel\KernelInterface;

#[Route("/api/computers")]
class ComputersController extends AbstractController
{
    private $formErrorsUtil;
    private $kernel;


    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository, RequestStack $requestStack, FormErrorsUtil $formErrorsUtil, KernelInterface $kernel)
    {
        $this->formErrorsUtil = $formErrorsUtil;
        $this->kernel = $kernel;
        $request = $requestStack->getCurrentRequest();

        $token = explode(' ', $request->headers->get('Authorization'))[1];

        $username = @$jwtEncoder->decode($token)['username'] ?: '';

        $this->user = $userRepository->findOneBy(['email' => $username]);
    }

    #[Route("", name: "computers", methods: ["GET", "POST"])]
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


        // Obtén el path absoluto al archivo
        $projectDir = $this->kernel->getProjectDir();
        $logoPath = $projectDir . '/public/logo/logo.jpg';

        // Asegúrate de que el archivo exista
        if (!file_exists($logoPath)) {
            throw new \Exception("Logo file not found at path: " . $logoPath);
        }

        $qr = (new Builder(
            writer: new PngWriter(),
            data: $computer->getSerie(),
            size: 300,
            margin: 10,
            logoPath: $logoPath,          // Ruta absoluta al archivo
            logoResizeToWidth: 100,
            logoResizeToHeight: 100,
            errorCorrectionLevel: ErrorCorrectionLevel::High
        ))->build();

        $computer->setQrCode($qr->getDataUri());

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

    #[Route("/available", name: "computers_available", methods: ["GET"])]
    public function computersAvailable(ComputersRepository $computersRepository, StatusComputerRepository $statusComputerRepository): JsonResponse
    {

        $statu_available = $statusComputerRepository->find(Constants::STATUS_COMPUTER_AVAILABLE);

        $computers = $computersRepository->getComputersByStatus($statu_available);

        $computers_lists = [];
        foreach ($computers as $computer) {
            $computers_lists[] = $computer->getDataComputersAvailable();
        }

        return $this->json(
            $computers_lists,
            Response::HTTP_ACCEPTED,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/notavailable", name: "computers_not_available", methods: ["GET"])]
    public function computersNotAvailable(
        RequestsComputersRepository $requestsComputersRepository,
    ): JsonResponse {

        $computers_NotAvailable = $requestsComputersRepository->findNotAvailable();

        $computers = [];

        foreach ($computers_NotAvailable as $computer) {
            $computers[] = $computer->getComputer()->getDataComputers($computer->getId());
        }

        return $this->json(
            $computers,
            Response::HTTP_ACCEPTED,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/requests", name: "request_computer", methods: ["POST"])]
    public function requestComputer(Request $request, RequestsRepository $requestsRepository, ComputersRepository $computersRepository, StatusComputerRepository $statusComputerRepository, EntityManagerInterface $em): JsonResponse
    {
        if ($this->user->getRol()->getId() != Constants::ROLE_PROFESSOR) {

            $body = $request->getContent();
            $data = json_decode($body, true);

            if (!(isset($data['computer_id']) && isset($data['request_id']))) {
                return $this->json(
                    [
                        'message' => 'Error al enviar los datos, se espera un computer_id y un request_id',
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']
                );
            }


            $status_computer_not_available = $statusComputerRepository->find(Constants::STATUS_COMPUTER_NOT_AVAILABLE);
            $computer = $computersRepository->findOneBy(['id' => $data['computer_id'], 'statusComputer' => CONSTANTS::STATUS_COMPUTER_AVAILABLE]);
            if (!$computer) {
                return $this->json(
                    [
                        'message' => 'La computadora no se encuentra disponible',
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']
                );
            }

            $computer->setStatusComputer($status_computer_not_available);
            $requestDb = $requestsRepository->findOneBy(['id' => $data['request_id']]);

            if (!$requestDb) {
                return $this->json(
                    [
                        'message' => 'El evento no existe',
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']
                );
            }

            $em->persist($computer);

            $request_computer = new RequestsComputers;

            $request_computer->setComputer($computer);
            $request_computer->setRequest($requestDb);

            $em->persist($request_computer);
            $em->flush();

            return $this->json(
                ['message' => 'Se asignó la computadora al evento correctamente'],
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }

        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/return", name: "return_computer", methods: ["PATCH"])]
    public function returnComputer(Request $request, StatusComputerRepository $statusComputerRepository, RequestsComputersRepository $requestsComputersRepository, EntityManagerInterface $em): JsonResponse
    {
        if ($this->user->getRol()->getId() != Constants::ROLE_PROFESSOR) {

            $body = $request->getContent();
            $data = json_decode($body, true);

            if (!isset($data['id'])) {
                return $this->json(
                    [
                        'message' => 'Error al enviar los datos, se espera un id',
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']
                );
            }

            $request_computer_id = $requestsComputersRepository->findOneBy(['returnetAt' => null, 'computer' => $data['id']]);

            if (!$request_computer_id) {
                return $this->json(
                    [
                        'message' => 'No se encontro la computadora con el id indicado.',
                    ],
                    Response::HTTP_NOT_FOUND,
                    ['Content-Type' => 'application/json']
                );
            }

            $status_computer_available = $statusComputerRepository->find(Constants::STATUS_COMPUTER_AVAILABLE);

            $request_computer_id->getComputer()->setStatusComputer($status_computer_available);
            $request_computer_id->setReturnetAt(new \DateTime());



            $em->persist($request_computer_id);
            $em->flush();

            return $this->json(
                ['message' => 'La computadora se devolvio correctamente'],
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }

        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/{computer_id}", name: "computers_by_id", methods: ["GET", "PATCH"])]
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

        // Obtén el path absoluto al archivo
        $projectDir = $this->kernel->getProjectDir();
        $logoPath = $projectDir . '/public/logo/logo.jpg';

        // Asegúrate de que el archivo exista
        if (!file_exists($logoPath)) {
            throw new \Exception("Logo file not found at path: " . $logoPath);
        }

        $qr = (new Builder(
            writer: new PngWriter(),
            data: $computer->getSerie(),
            size: 300,
            margin: 10,
            logoPath: $logoPath,          // Ruta absoluta al archivo
            logoResizeToWidth: 100,
            logoResizeToHeight: 100,
            errorCorrectionLevel: ErrorCorrectionLevel::High
        ))->build();

        $computer->setQrCode($qr->getDataUri());
        $computer->setQrCode($qr->getDataUri());

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

    #[Route("/delete/{computer_id}", name: "delete_computer_by_id", methods: ["DELETE"])]
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
}
