<?php

namespace App\Controller;

use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('subscription/pricing.html.twig',
        [
            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrices()
        ]);
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('subscription/payment.html.twig');
    }
}
