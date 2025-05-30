<?php

namespace AlAya\Student\UserBundle\Controller;

use AlAya\Common\Controller\BaseController;
use AlAya\Common\Entity\Payement;
use AlAya\Common\Entity\PayementType;
use AlAya\Common\Entity\Prestation;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends BaseController
{

    #[Route('/create-checkout-session/{prestation}', name: 'create_checkout_session')]
    public function createCheckoutSession(Prestation $prestation): JsonResponse
    {
        Stripe::setApiKey($this->getParameter('stripe.secret.key'));
        $paymentIntent = PaymentIntent::create([
            'amount' => intval(calculerTotalPrestation($prestation) * 100), // Montant en centimes (ex. 7€)
            'currency' => 'eur',
             'automatic_payment_methods' => ['enabled' => true]
        ]);

        return $this->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    #[Route('/save-payment-intent/{prestation}', name: 'save_payment_intent', methods: ['POST'])]
    public function savePaymentIntent(Request $request, Prestation $prestation)
    {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['paymentIntentId'];
        // 🔎 Récupère les vraies infos de Stripe
        Stripe::setApiKey($this->getParameter('stripe.secret.key'));
       $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

    // ✅ Vérifie que la transaction est vraiment réussie
    if ($paymentIntent->status === 'succeeded') {
        $payment = new Payement ;
        $payment->setAmount($paymentIntent->amount_received / 100); // Convertit en euros
        $payment->setDate(new \DateTime());
        $payment->setType($this->repo(PayementType::class)->find(1));
        $payment->setStripeId($paymentIntent->id);
        $payment->setPrestation($prestation);
        $this->doctrine->persist($payment);
        $this->doctrine->flush();
        // 🔄 Met à jour la prestation
        payerPrestation($prestation);
        $this->doctrine->persist($prestation);
        $this->doctrine->flush();
        return new Response('Transaction validée', 200);
    } else {
        return new Response('Transaction non validée', 400);
    }
    } 
}
