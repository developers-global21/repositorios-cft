<?php
// src/Security/AuthenticationEntryPoint.php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\Response;


class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {

        // add a custom flash message and redirect to the login page
        //$request->getSession()->getFlashBag()->add('note', 'Usted no tiene permitido entrar a esta página');
        //$this->addFlash('notice', 'Tus cambios se han guardado!');
        //return new RedirectResponse($this->urlGenerator->generate('/'));
        //return new RedirectResponse($this->urlGenerator->generate('security_login'));
        return new RedirectResponse('/app_login');
        //return new RedirectResponse('/');
        //return $this->redirectToRoute('/');

    }
}
