<?php

namespace App\Controller\Secure;

use App\Constants\Constants;
use App\Entity\Requests;
use App\Entity\RequestsComputers;
use App\Entity\StatusComputer;
use App\Form\RequestType;
use App\Repository\ComputersRepository;
use App\Repository\RequestsRepository;
use App\Repository\RolesRepository;
use App\Repository\StatusComputerRepository;
use App\Repository\StatusRequestRepository;
use App\Repository\UserRepository;
use App\Utils\FormErrorsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Namshi\JOSE\Signer\SecLib\RSA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/requests")
 */
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

    /**
     * @Route("", name="request", methods={"GET","POST"})
     */
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

    /**
     * @Route("/review/{request_id}", name="request_review", methods={"PATCH"})
     */
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

    /**
     * @Route("/calendar", name="calendar_request", methods={"GET"})
     */
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

    /**
     * @Route("/computer", name="calendar_request", methods={"POST"})
     */
    public function requestComputer(Request $request, RequestsRepository $requestsRepository, ComputersRepository $computersRepository, StatusComputerRepository $statusComputerRepository,EntityManagerInterface $em): JsonResponse
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
            $computer = $computersRepository->find($data['computer_id']);
            $computer->setStatusComputer($status_computer_not_available);
            $requestDb = $requestsRepository->find($data['request_id']);

            $em->persist($computer);

            $request_computer = new RequestsComputers;

            $computer->$request_computer->setComputer($computer);
            $request_computer->setRequest($requestDb);

            $em->persist($request_computer);
            $em->flush();

            return $this->json(
                ['message' => 'Se asigno la computadora al evento correctamente'],
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
}
