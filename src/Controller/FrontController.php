<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/front")
 */
class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front")
     */
    public function index(Request $request, SerializerInterface $serializer, HttpClientInterface $httpClient, LoggerInterface $logger): Response
    {
        $customer = new Customer();

        $form = $this->createForm(CustomerType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();

            try {
                $response = $httpClient->request(
                    'POST',
                    'https://127.0.0.1:8000/api/login_check',
                    [
                        'json' => ['username' => $username, 'password' =>  $password]
                        // 'json' => ['username' => 'compagny4@test.com', 'password' => 'compagny4']
                    ]
                );
            } catch (\Symfony\Component\HttpClient\Exception\TransportException | \Exception | \Throwable $exception) {
                die($exception->getMessage());
            }

            $token = $response->toArray()['token'];

            // $customer->setUsername($username);
            // $customer->setPassword($password);
            // $customer->setToken($token);

            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($customer);
            // $entityManager->flush();


            $response2 = $httpClient->request(
                'GET',
                'https://127.0.0.1:8000/api/products',
                [
                    'auth_bearer' => $token,
                ]
            );

            return $this->render('front/listProducts.html.twig', [
                'products' => $response2->toArray(),
            ]);
        }

        return $this->render('front/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
