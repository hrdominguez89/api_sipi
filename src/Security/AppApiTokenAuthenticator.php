<?php

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AppApiTokenAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && (0 === strpos($request->headers->get('Authorization'), 'Bearer '));
    }

    public function authenticate(Request $request): Passport
    {
        $token = explode(' ', $request->headers->get('Authorization'))[1];
        
        $username = @$this->jwtEncoder->decode($token)['username'] ?: '';

        return new SelfValidatingPassport(
            new UserBadge($username, function ($userIdentifier) {
                $userApiClient = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$userIdentifier) {
                    throw new UserNotFoundException();
                }
                return $userApiClient;
            })
        );
        // TODO: Implement authenticate() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // TODO: Implement onAuthenticationSuccess() method.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $response->setContent(
            json_encode([
                'message' => 'Usuario y/o Password incorrecto.'
            ])
        );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        // TODO: Implement onAuthenticationFailure() method.
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
