<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Cookie;

class UserController extends Controller
{
     /**
     * @Route("/homeUser", name="home_user")
     */
    public function homeUserAction(Request $request)
    {
        if(null != $request->cookies->get('token'))
        {
            $token = $request->cookies->get('token');
            $client = new Client([
                'base_uri'=>'http://localhost/Projet_7/web/app_dev.php/'
            ]);

            $apiRequest = $client->request('GET', 'getName', [
                'headers'=>[
                    'X-AUTH-TOKEN'=>$token
                ]
            ]);
            $user = $apiRequest->getBody()->getContents();
        }
        else
        {
            $user = "Vous n'êtes pas identifié";
        }

        $test = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        return $this->render('User/homeUser.html.twig',[
            'user'=>$user,
            'test'=>$test
        ]);
    }


    /**
     * @Route("/getToken", name="getToken")
     */
    public function getTokenAction(Request $request)
    {
        $code = $request->query->get('code');

        $client = new Client(['base_uri'=>'http://localhost/Projet_7/web/app_dev.php/getToken']);
        $request = $client->request('GET', null, [
            'headers'=>[
                'code'=> $code,
                'url'=> 'http://localhost/frontend-p7/web/app_dev.php/getToken'
            ]
        ]);

        $apiResponse = json_decode($request->getBody()->getContents(), true);
        $token = $apiResponse['access_token'];

        $response = new Response($this->renderView('User/getToken.html.twig', [
            'token'=>$token
        ]));
        $response->headers->setCookie(new Cookie('token', $token));
        return $response;
    }

    /**
     * @Route(name="menu")
     */
    public function menuAction(Request $request)
    {
        if(null != $request->cookies->get('token'))
        {
            $token = $request->cookies->get('token');
            $client = new Client([
                'base_uri'=>'http://localhost/Projet_7/web/app_dev.php/'
            ]);

            $apiRequest = $client->request('GET', 'getName', [
                'headers'=>[
                    'X-AUTH-TOKEN'=>$token
                ]
            ]);
            $user = $apiRequest->getBody()->getContents();
        }
        else
        {
            $user = "Vous n'êtes pas identifié";
        }

        return $this->render('menu.html.twig',[
            'user'=>$user
        ]);
    }
}
