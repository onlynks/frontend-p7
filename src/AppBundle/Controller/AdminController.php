<?php

namespace AppBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

Class AdminController extends Controller
{
    /**
     * @Route("/homeAdmin", name="home_admin")
     */
    public function homeAdmin(Request $request)
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

        return $this->render('Admin/homeAdmin.html.twig',[
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/addPhone", name="add_phone")
     */
    public function createPhoneAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('brand', TextType::class)
            ->add('price', TextType::class)
            ->add('token', TextType::class)
            ->add('Envoyer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();
                $data['brand'] = ['name' => $data['brand']];
                $token = array_pop($data);

                $array = json_encode($data);

                $client = new Client([
                    'base_uri'=>'http://localhost/Projet_7/web/app_dev.php/'
                ]);
                $apiRequest = $client->request('POST', 'phone', [
                    'headers'=>[
                        'X-AUTH-TOKEN'=>$token
                    ],
                    'body'=> $array
                ]);

                $response = $apiRequest->getBody()->getContents();

                return $this->render('phoneCreated.html.twig', array(
                    'response' => $response)
                );
            }

        return $this->render('phoneForm.html.twig', array(
            'form'=>$form->createView()
        ));

    }

}