<?php

namespace App\Utils;

use App\Entity\User;
use App\Entity\Video;
use Symfony\Bundle\SecurityBundle\Security;

class VideoForNoValidSubscribtions
{

    public $isSubscriptionValid = false;

    public function __construct(Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();

        if ($user && $user->getSubscription() != null) {
            $paymentStatus = $user->getSubscription()->getPaymentStatus();
            $valid = new \DateTime() < $user->getSubscription()->getValidTo();

            if ($paymentStatus != null && $valid) {
                $this->isSubscriptionValid = true;
            }
        }
    }

    public function check()
    {
        if ($this->isSubscriptionValid) {
            return null;
        } else {
            static $video = Video::videoForNotLoggedInOrNoMembers;
            return $video;
        }
    }
}