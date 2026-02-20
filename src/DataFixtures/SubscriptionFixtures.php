<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getSubscriptionData() as [$userId, $plan, $validTo, $paymentStatus, $freePlanUser]) {
            $subscription = new Subscription;
            $subscription->setPlan($plan);
            $subscription->setValidTo($validTo);
            $subscription->setPaymentStatus($paymentStatus);
            $subscription->setFreePlanUsed($freePlanUser);
            $manager->persist($subscription);

            /** @var User $user */
            $user = $manager->getRepository(User::class)->find($userId);
            $user->setSubscription($subscription);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getSubscriptionData(): array
    {
        return [
            [1, Subscription::getPlanDataNameByIndex(2), (new DateTime())->modify('+100 year'), 'paid', false], // super admin
            [3, Subscription::getPlanDataNameByIndex(0), (new DateTime())->modify('+1 month'), 'paid', true], // super admin
            [4, Subscription::getPlanDataNameByIndex(1), (new DateTime())->modify('+1 minute'), 'paid', false], // super admin
        ];
    }
}
