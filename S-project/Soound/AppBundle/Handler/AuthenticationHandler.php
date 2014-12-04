<?php
namespace Soound\AppBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    protected $securityContext;
    protected $router;
    protected $s3;

    public function __construct($securityContext, $router, $s3, $s3_bucket)
    {
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->s3 = $s3;
        $this->s3_bucket = $s3_bucket;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $this->securityContext->getToken()->getUser();
        $session = $request->getSession();
        $s3 = $this->s3;
        if ($request->isXmlHttpRequest()) {
            if ($this->securityContext->isGranted('ROLE_USER')) {
                $session->set('userPicture', $s3->get_object($this->s3_bucket,$user->getPicture(50),array("preauth"=>strtotime("+1 month"))));
                $response = new Response(json_encode(array(
                    'success'=> 1,
                    'route' => $this->router->generate(($user->getFullname() === 'New User' ?  'accountSettings' : 'browse'))
                )));
                /*
                $response = new Response(json_encode(array(
                    'success'=> 1,
                    'route' => $this->router->generate('userProfile', array(
                        'publicId' => $this->securityContext->getToken()->getUser()->getPublicId()
                    ))
                )));
                */
            }
            else {
                $response = new Response(json_encode(array(
                    'success'=> 0
                )));
            }
            $response->headers->set('Content-Type', 'application/json');
        }
        else {
            if ($this->securityContext->isGranted('ROLE_USER')) {
                $session->set('userPicture', $s3->get_object($this->s3_bucket,$user->getPicture(50),array("preauth"=>strtotime("+1 month"))));
                $response = new RedirectResponse(
                    $this->router->generate(($user->getFullname() === 'New User' ?  'accountSettings' : 'browse'))
                );
                /*
                $response = new RedirectResponse(
                    $this->router->generate('userProfile', array(
                        "publicId" => $this->securityContext->getToken()->getUser()->getPublicId()
                    ))
                );
                */
            } 
            /*
            if ($securityContext->isGranted('ROLE_ADMIN'))
            {

            }
            */
            $session->set('userID', $this->securityContext->getToken()->getUser()->getId());
        }
        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {

            $response = new Response(json_encode(array(
                'success' => 0,
                'error' => $exception->getMessage()
            )));

            $response->headers->set('Content-Type', 'application/json');
        }
        else {
            $referer = $request->headers->get('referer');
            $request->getSession()->setFlash('error', $exception->getMessage());
            $response = new RedirectResponse($referer);
        }
        return $response;
    }

    /**
     * Get the provider key.
     *
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * Set the provider key.
     *
     * @param string $providerKey
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;
    }
}