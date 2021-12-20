<?php

namespace App\Controller;

use App\Form\CustomerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/front")
 */
class FrontController extends AbstractController
{
    /**
     * @Route("/", name="front")
     */
    public function index(Request $request, HttpClientInterface $httpClient, SessionInterface $session): Response
    {
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
                    ]
                );
            } catch (\Symfony\Component\HttpClient\Exception\TransportException | \Exception | \Throwable $exception) {
                die($exception->getMessage());
            }

            $session->set('token', $response->toArray()['token']);

            return $this->redirectToRoute('requestApi', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/requestApi", name="requestApi")
     */
    public function requestApi(): Response
    {
        return $this->render('front/requestApi.html.twig', []);
    }

    /**
     * @Route("/listProducts", name="listProducts")
     */
    public function listProducts(Request $request, HttpClientInterface $httpClient, SessionInterface $session): Response
    {

        $token = $session->get('token');

        $response = $httpClient->request(
            'GET',
            'https://127.0.0.1:8000/api/products',
            [
                'auth_bearer' => $token,
            ]
        );

        // dd($response);
        // dd($response->getContent());
        // dd($response->toArray());
        // dd($response->toArray()['token']);
        // $response->getInfo();

        return $this->render('front/listProducts.html.twig', [
            'products' => $response->toArray(),
        ]);
    }

    /**
     * @Route("/listCustomers", name="listCustomers")
     */
    public function listCustomers(Request $request, HttpClientInterface $httpClient, SessionInterface $session): Response
    {

        $token = $session->get('token');

        $response = $httpClient->request(
            'GET',
            'https://127.0.0.1:8000/api/customers',
            [
                'auth_bearer' => $token,
            ]
        );

        return $this->render('front/listCustomers.html.twig', [
            'customers' => $response->toArray(),
        ]);
    }
}
