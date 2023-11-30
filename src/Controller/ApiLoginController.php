<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
class ApiLoginController extends AbstractController
{
    /**
     * @Route("/login", name="login_check")
     */
    public function index(Request $request, UserRepository $userRepository, PasswordHasherFactoryInterface $passwordHasherFactoryInterface, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (!(@$data['username'] && @$data['password'])) {
            $validation = [];
            if (!@$data['username']) {
                $validation["username"] =  "Debe ingresar una dirección de correo.";
            }
            if (!@$data['password']) {
                $validation["password"] =  "El campo password es obligatorio.";
            }
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Error de validacion',
                    'validation' => $validation
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        try {
            $user = $userRepository->findOneBy(["email" => @$data['username']]);
            if (!$passwordHasherFactoryInterface->getPasswordHasher($user)->verify($user->getPassword(), $data['password'])) {
                throw new Exception();
            }
        } catch (\Exception $e) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Usuario y/o password incorrectos.',
                ],
                Response::HTTP_UNAUTHORIZED,
                ['Content-Type' => 'application/json']
            );
        }
        if (!$user->isActive()) {
            return $this->json(
                [
                    "status" => false,
                    'message' => 'Su cuenta se encuentra deshabilitada.',
                ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }
        $jwt = $jwtManager->create($user);

        return new JsonResponse([
            "status" => true,
            "token" => $jwt,
            "token_type" => "Bearer",
            "expires_in" => (int)$_ENV['JWT_TOKEN_TTL'],
            "user_data" => [
                "id" => $user->getId(),
                "fullname" => $user->getFullname(),
                "rol_id" => (int) $user->getRol()->getId(),
                "rol_name" => $user->getRol()->getName(),
                "email" => $user->getEmail()
            ]
        ]);
    }
}
