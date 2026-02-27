<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{

    use SaveSubscription;

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('subscription/pricing.html.twig',
        [
            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrices()
        ]);
    }

    #[Route('/payment/{paypal?false}', name: 'payment')]
    public function payment($paypal, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if ($paypal) {
            $this->saveSubscription($session->get('planName'), $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('subscription/payment.html.twig');
    }
}
