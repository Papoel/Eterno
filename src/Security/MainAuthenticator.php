<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class MainAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = (string) $request->request->get(key: 'email', default: '');

        // test pour passer PHPStan au level max
        $password = (string) $request->request->get(key: 'password', default: '');
        $csrfToken = $request->request->get(key: '_csrf_token');

        if (str_contains(haystack: $identifier, needle: '@')) {
            $user = $this->userRepository->findOneBy(['email' => $identifier]);
        } else {
            $user = $this->userRepository->findOneBy(['username' => $identifier]);
        }

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(message: 'Email or Username could not be found.');
        }

        $identifier = $user->getEmail();

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $identifier);
        $request->getSession()->set('user', $user->getFullname());

        return new Passport(
            /* @phpstan-ignore-next-line */
            new UserBadge($identifier),
            new PasswordCredentials(password: $password),
            badges: [
                new CsrfTokenBadge(csrfTokenId: 'authenticate', csrfToken: (string) $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate(name: 'home.user_connected'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(name: self::LOGIN_ROUTE);
    }
}
