<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/front")
 */
class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front")
     */
    public function index(Request $request, SerializerInterface $serializer): Response
    {
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $customer = $form->getData();
            // dump($form->get('username')->getData());
            // dump($customer);

            $data = $serializer->serialize($customer, 'json');
            dump($data);

            // return $this->redirectToRoute('apiconnect', ['customer' => $customer], Response::HTTP_SEE_OTHER);

            // $request = new HTTP_Request2();
            // $request->setUrl('https://127.0.0.1:8000/api/login_check');
            // $request->setMethod(HTTP_Request2::METHOD_POST);
            // $request->setConfig(array(
            //   'follow_redirects' => TRUE
            // ));
            // $request->setHeader(array(
            //   'Content-Type' => 'application/json'
            // ));
            // $request->setBody('{
            // \n    "username": "compagny4@test.com",
            // \n    "password": "compagny4"
            // \n}');
            // try {
            //   $response = $request->send();
            //   if ($response->getStatus() == 200) {
            //     echo $response->getBody();
            //   }
            //   else {
            //     echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
            //     $response->getReasonPhrase();
            //   }
            // }


        }

        return $this->render('front/index.html.twig', [
            // 'controller_name' => 'FrontController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/apiconnect", name="apiconnect")
     */
    public function apiconnect(Request $request): Response
    {
        return $this->render('front/index.html.twig', [
            // 'controller_name' => 'FrontController',
            'form' => $form->createView(),
        ]);
    }
}
