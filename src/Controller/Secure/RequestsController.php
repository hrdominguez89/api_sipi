<?php

namespace App\Controller\Secure;

use App\Constants\Constants;
use App\Entity\Requests;
use App\Form\RequestType;
use App\Repository\RequestsRepository;
use App\Repository\StatusRequestRepository;
use App\Repository\UserRepository;
use App\Utils\FormErrorsUtil;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/api/requests")]
class RequestsController extends AbstractController
{

    private $user;
    private $formErrorsUtil;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository, RequestStack $requestStack, FormErrorsUtil $formErrorsUtil)
    {
        $this->formErrorsUtil = $formErrorsUtil;

        $request = $requestStack->getCurrentRequest();

        $token = explode(' ', $request->headers->get('Authorization'))[1];

        $username = @$jwtEncoder->decode($token)['username'] ?: '';

        $this->user = $userRepository->findOneBy(['email' => $username]);
    }

    #[Route("", name: "request", methods: ["GET", "POST"])]
    public function index(StatusRequestRepository $statusRequestRepository, RequestsRepository $requestsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {

        if ($this->user->getRol()->getId() == Constants::ROLE_PROFESSOR) {
            if ($request->getMethod() == 'GET') {
                $requests = $requestsRepository->findRequestsByUserId($this->user->getId());
                $data = [];
                foreach ($requests as $requestBd) {
                    $data[] = $requestBd->getRequestData();
                }
                return $this->json(
                    $data,
                    Response::HTTP_OK,
                    ['Content-Type' => 'application/json']
                );
            }

            //si es POST

            $body = $request->getContent();
            $data = json_decode($body, true);

            //creo el formulario para hacer las validaciones    
            $requestBd = new Requests();

            $statusRequest = $statusRequestRepository->find(Constants::STATUS_REQUEST_PENDING);

            $requestBd->setStatusRequest(@$statusRequest);
            $requestBd->setProfessor(@$this->user);
            $requestBd->setRequestedPrograms(implode(', ', $data['requestedPrograms']));

            $form = $this->createForm(RequestType::class, $requestBd);
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
            $em->persist($requestBd);
            $em->flush();

            return $this->json(
                ['message' => 'Solicitud creada con éxito'],
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }

        //si soy admin o bedele puedo ver todas las solicitudes pendientes.

        if ($request->getMethod() == 'GET') {
            $requests = $requestsRepository->findRequestsPending();
            $data = [];
            foreach ($requests as $requestBd) {
                $data[] = $requestBd->getRequestDataAdmin();
            }
            return $this->json(
                $data,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }
        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/edit/{request_id}", name: "request_edit", methods: ["GET", "PATCH"])]
    public function edit($request_id, RequestsRepository $requestsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$request_id) {
            return $this->json(
                [
                    'message' => 'El id ingresado no es valido',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $requestBd = $requestsRepository->findOneBy(['id' => $request_id, 'visible' => true]);
        if (!$requestBd) {
            return $this->json(
                [
                    'message' => 'El id ingresado no se encuentra',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        if ($this->user->getRol()->getId() == Constants::ROLE_PROFESSOR) {

            if ($request->getMethod() == 'GET') {

                return $this->json(
                    $requestBd->getRequestData(),
                    Response::HTTP_OK,
                    ['Content-Type' => 'application/json']
                );
            }

            $body = $request->getContent();
            $data = json_decode($body, true);

            $requestBd->setRequestedDate(@DateTime::createFromFormat('Y-m-d', @$data['requestedDate']));
            $requestBd->setRequestedAmount(@$data['requestedAmount']);
            $requestBd->setRequestedPrograms(implode(', ', @$data['requestedPrograms']));
            $requestBd->setSubject(@$data['requestedSubject']);
            $requestBd->setObservations(@$data['observations']);
            $form = $this->createForm(RequestType::class, $requestBd);
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
            $em->persist($requestBd);
            $em->flush();

            return $this->json(
                ['message' => 'Solicitud editada con éxito'],
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/delete/{request_id}", name: "delete_request_by_id", methods: ["DELETE"])]
    public function deleteRequest($request_id, RequestsRepository $requestsRepository, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$request_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $requestBd = $requestsRepository->findOneBy(['id' => $request_id, 'visible' => true]);
        if (!$requestBd) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Solicitud no encontrada.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        $requestBd->setVisible(false);
        $em->persist($requestBd);
        $em->flush();
        return $this->json(
            ['message' => 'Solicitud eliminada correctamente'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/review/{request_id}", name: "request_review", methods: ["PATCH"])]
    public function review($request_id, StatusRequestRepository $statusRequestRepository, RequestsRepository $requestsRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$request_id) {
            return $this->json(
                [
                    'message' => 'El id ingresado no es valido',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $requestBd = $requestsRepository->find($request_id);
        if (!$requestBd) {
            return $this->json(
                [
                    'message' => 'El id ingresado no se encuentra',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        if ($this->user->getRol()->getId() != Constants::ROLE_PROFESSOR) {

            $body = $request->getContent();
            $data = json_decode($body, true);

            if (!isset($data['status'])) {
                return $this->json(
                    [
                        'message' => 'Error al enviar los datos, se espera un valor de STATUS: true o false',
                    ],
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']
                );
            }
            $statusRequest = $statusRequestRepository->find($data['status'] ? Constants::STATUS_REQUEST_ACCEPTED : Constants::STATUS_REQUEST_REJECTED);

            $requestBd->setStatusRequest($statusRequest);

            $em->persist($requestBd);
            $em->flush();

            return $this->json(
                ['Message' => $data['status'] ? 'Solicitud aprobada correctamente' : 'Solicitud Rechazada correctamente'],
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }

    #[Route("/calendar", name: "calendar_request", methods: ["GET"])]
    public function calendar(RequestsRepository $requestsRepository): JsonResponse
    {

        $requests = $requestsRepository->findRequestsAcepted();

        $requestCalendar = [];

        foreach ($requests as $requestBd) {
            $requestCalendar[] = $requestBd->getCalendarData();
        }

        if ($this->user->getRol()->getId() != Constants::ROLE_PROFESSOR) {
            return $this->json(
                $requestCalendar,
                Response::HTTP_OK,
                ['Content-Type' => 'application/json']
            );
        }

        return $this->json(
            ['message' => 'Su cuenta no tiene permisos para realizar esta operación'],
            Response::HTTP_FORBIDDEN,
            ['Content-Type' => 'application/json']
        );
    }
}
