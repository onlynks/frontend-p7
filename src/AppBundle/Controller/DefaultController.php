<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class DefaultController extends Controller
{
     /**
     * @Route("/login", name="login")
     */
    public function indexAction(Request $request)
    {
        return $this->render('login.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function loginAction(Request $request)
    {

        $code = $request->query->get('code');

        $client = new Client([
            'base_uri'=>'https://graph.facebook.com/v3.0/oauth/access_token?'
        ]);

        $query = $client->request('GET', null, [
            'query'=>[
                'client_id'=>$this->getParameter('id_api'),
                'redirect_uri'=>'http://localhost/frontend-p7/web/app_dev.php/admin',
                'client_secret'=>$this->getParameter('api_key'),
                'code'=>$code
            ]
        ]);

        $fbResponse = json_decode($query->getBody()->getContents());
        $token = $fbResponse->{'access_token'};

        $client = new Client([
            'base_uri'=>'http://localhost/Projet_7/web/'
        ]);

        $apiRequest = $client->request('GET', 'login', [
            'headers'=>[
                'X-AUTH-TOKEN'=>$token
            ]
        ]);

        $user = json_decode($apiRequest->getBody()->getContents());

        return $this->render('admin.html.twig', [
            'user'=>$user->{'name'}
        ]);
    }
}
