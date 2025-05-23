<?php

namespace AlAya\Student\UserBundle\Controller;

use AlAya\Common\Entity\PaymentType;
use AlAya\Common\Entity\SessionStudent;
use AlAya\Common\Service\PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayPalController extends AbstractController
{
    private $paypalService;

    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    #[Route('/paypal/create-order', name: 'paypal_create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'] ?? null;

        $order = $this->paypalService->createOrder($amount);
        return new JsonResponse($order);
    }

    #[Route('/paypal/capture-order/', name: 'paypal_capture_order', methods: ['POST'])]
    public function captureOrder(Request $request,SessionStudent $sessionStudent, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $orderId = $data['id'] ?? null;

        if (!$orderId) {
            return new JsonResponse(['error' => 'Missing order ID'], 400);
        }

     /*    $captureData = $this->paypalService->captureOrder($orderId);

        $json = json_encode($captureData);

        $jsonFilePath =  __DIR__ . '/capture_data.json'; // Specify the path where you want to save the JSON file
        file_put_contents($jsonFilePath, $json); */

      /*  $sessionStudent->setPayed(true);
        $sessionStudent->setDatePay(new \DateTime());
        $sessionStudent->setTypePay($em->getRepository(PaymentType::class)->find(2));
        $em->persist($sessionStudent);
        $em->flush(); */

        return new JsonResponse(['message' => 'Payment captured successfully!']);
    }
}
