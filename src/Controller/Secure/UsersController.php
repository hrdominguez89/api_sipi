<?php

namespace App\Controller\Secure;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\FormErrorsUtil;

/**
 * @Route("/api/users")
 */
class UsersController extends AbstractController
{

    private $formErrorsUtil;

    public function __construct(FormErrorsUtil $formErrorsUtil)
    {
        $this->formErrorsUtil = $formErrorsUtil;
    }


    /**
     * @Route("", name="users", methods={"GET","POST"})
     */
    public function index(UserRepository $userRepository, RolesRepository $rolesRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->getMethod() == 'GET') {
            $users = $userRepository->findAll();
            $data = [];
            foreach ($users as $user) {
                $data[] = $user->getDataUser();
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

        $rol = null;
        if (@$data['rol']) {
            $rol = $rolesRepository->find(@$data['rol']);
        }
        $user = new User();
        $user->setEmail(@$data['email']);
        $user->setFullname(@$data['fullname']);
        $user->setRol(@$rol);

        $form = $this->createForm(UserType::class, $user);
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
        $em->persist($user);
        $em->flush();
        return $this->json(
            $user->getDataUser(),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/{user_id}", name="user_by_id", methods={"GET","PATCH"})
     */
    public function userById($user_id, userRepository $userRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$user_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $user = $userRepository->find($user_id);
        if (!$user) {
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
                $user->getDataUser(),
                Response::HTTP_ACCEPTED,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        // $user->setDni(@$data['dni'] ?: $user->getDni());
        $user->setFullname(@$data['fullname'] ?: $user->getFullname());

        $form = $this->createForm(userType::class, $user);
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
        $em->persist($user);
        $em->flush();
        return $this->json(
            $user->getDataUser(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/delete/{user_id}", name="delete_user_by_id", methods={"DELETE"})
     */
    public function deleteUserById($user_id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        if (!(int)$user_id) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Debe ingresar un id valido.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $user = $userRepository->find($user_id);
        if (!$user) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Usuario no encontrado.',
                ],
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/json']
            );
        }

        $user->setActive(false);
        $em->persist($user);
        $em->flush();
        return $this->json(
            ['message' => 'Usuario eliminado correctamente'],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
