<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Cookie;

class DefaultController extends Controller
{
     /**
     * @Route("/home", name="home")
     */
    public function homeAction(Request $request)
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/checkToken", name="checkToken")
     */
    public function checkTokenAction(Request $request)
    {

        $code = $request->query->get('code');

        $client = new Client([
            'base_uri'=>'https://graph.facebook.com/v3.0/oauth/access_token?'
        ]);

        $query = $client->request('GET', null, [
            'query'=>[
                'client_id'=>$this->getParameter('id_api'),
                'redirect_uri'=>'http://localhost/frontend-p7/web/app_dev.php/checkToken',
                'client_secret'=>$this->getParameter('api_key'),
                'code'=>$code
            ]
        ]);

        $fbResponse = json_decode($query->getBody()->getContents(), true);
        $token = $fbResponse['access_token'];

        $client = new Client([
            'base_uri'=>'http://localhost/Projet_7/web/app_dev.php/'
        ]);

        $apiRequest = $client->request('GET', 'testToken', [
            'headers'=>[
                'X-AUTH-TOKEN'=>$token
            ]
        ]);

        $user = $apiRequest->getBody()->getContents();


        return $this->render('checkToken.html.twig', [
            'user'=>$user
        ]);
/*
        dump($token);
        return new Response('');*/
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $code = $request->query->get('code');

        $client = new Client([
            'base_uri'=>'https://graph.facebook.com/v3.0/oauth/access_token?'
        ]);

        $query = $client->request('GET', null, [
            'query'=>[
                'client_id'=>$this->getParameter('id_api'),
                'redirect_uri'=>'http://localhost/frontend-p7/web/app_dev.php/register',
                'client_secret'=>$this->getParameter('api_key'),
                'code'=>$code
            ]
        ]);

        $fbResponse = json_decode($query->getBody()->getContents(), true);
        $token = $fbResponse['access_token'];
    }

    /**
     * @Route("/getToken", name="getToken")
     */
    public function getTokenAction(Request $request)
    {
        $code = $request->query->get('code');

        $client = new Client(['base_uri'=>'http://localhost/Projet_7/web/app_dev.php/testCode']);
        $request = $client->request('GET', null, [
            'headers'=>[
                'code'=> $code,
                'url'=> 'http://localhost/frontend-p7/web/app_dev.php/getToken'
            ]
        ]);

        $apiResponse = json_decode($request->getBody()->getContents(), true);
        $token = $apiResponse['access_token'];

        $response = new Response($this->render('getToken.html.twig', [
            'token'=>$token
        ]));
        $response->headers->setCookie(new Cookie('token', $token));
        return $response;
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {
        dump($request->cookies->get('token'));
        return new Response('');
    }
}
