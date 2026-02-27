<?php

namespace App\Controller\Traits;

use App\Entity\Subscription;
use App\Entity\User;

trait SaveSubscription
{

    private function saveSubscription(string $plan, User $user)
    {
        $date = new \DateTime();
        $date->modify('+1 month');

        /** @var Subscription $subscription */
        $subscription = $user->getSubscription();
        if (null === $subscription) {
            $subscription = new Subscription();
        }

        if ($subscription->isFreePlanUsed() &&
            $plan == Subscription::getPlanDataNameByIndex(0)
        ) {
            return;
        }

        $subscription->setValidTo($date);
        $subscription->setPlan($plan);

        if ($plan == Subscription::getPlanDataNameByIndex(0)) {
            $subscription->setFreePlanUsed(true);
            $subscription->setPaymentStatus('paid');
        }

        $subscription->setPaymentStatus('paid'); // to do
        $user->setSubscription($subscription);

        $this->em->persist($subscription);
        $this->em->persist($user);
        $this->em->flush();
    }

}