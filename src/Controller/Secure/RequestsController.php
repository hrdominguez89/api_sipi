<?php

namespace App\Controller\Secure;

use App\Constants\Constants;
use App\Entity\Requests;
use App\Form\RequestType;
use App\Repository\RequestsRepository;
use App\Repository\RolesRepository;
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
        $this->user;

        if ($this->user->getRol()->getId() == Constants::ROLE_PROFESSOR) {
            if ($request->getMethod() == 'GET') {
                $requests = $requestsRepository->findRequestsByUserId($this->user->getId());
                $data = [];
                foreach ($requests as $request) {
                    $data[] = $request->getRequestData();
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
            $request = new Requests();

            $statusRequest = $statusRequestRepository->find(Constants::STATUS_REQUEST_PENDING);

            // $request->setRequestedDate(@$data['requestedDate']);
            // $request->setRequestedAmount(@$data['requestedAmount']);
            // $request->setRequestedPrograms(@$data['requestedPrograms']);
            // $request->setObservations(@$data['observations']);
            $request->setStatusRequest(@$statusRequest);
            $request->setProfessor(@$this->user->getId());
            // $request->setProfessor(@$data['professor']);

            $form = $this->createForm(RequestType::class, $request);
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
            $em->persist($request);
            $em->flush();

            return $this->json(
                ['Message' => 'Solicitud creada con éxito'],
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }



        return $this->json(
            $this->user->getRol()->getId(),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );

        // if ($request->getMethod() == 'GET') {
        //     $users = $userRepository->findAll();
        //     $data = [];
        //     foreach ($users as $user) {
        //         $data[] = $user->getDataUser();
        //     }
        //     return $this->json(
        //         $data,
        //         Response::HTTP_OK,
        //         ['Content-Type' => 'application/json']
        //     );
        // }

        // $body = $request->getContent();
        // $data = json_decode($body, true);

        // //creo el formulario para hacer las validaciones

        // $rol = null;
        // if (@$data['rol']) {
        //     $rol = $rolesRepository->find(@$data['rol']);
        // }
        // $user = new User();
        // $user->setEmail(@$data['email']);
        // $user->setFullname(@$data['fullname']);
        // $user->setRol(@$rol);

        // $form = $this->createForm(UserType::class, $user);
        // $form->submit($data, false);

        // if (!$form->isValid()) {
        //     $error_forms = $this->formErrorsUtil->getErrorsFromForm($form);
        //     return $this->json(
        //         [
        //             'message' => 'Error de validación.',
        //             'validation' => $error_forms
        //         ],
        //         Response::HTTP_BAD_REQUEST,
        //         ['Content-Type' => 'application/json']
        //     );
        // }
        // $em->persist($user);
        // $em->flush();
        // return $this->json(
        //     $user->getDataUser(),
        //     Response::HTTP_CREATED,
        //     ['Content-Type' => 'application/json']
        // );
    }
}
