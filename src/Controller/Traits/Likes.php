<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Entity\Video;

trait Likes
{

    private function likeVideo(Video $video)
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->addLikedVideo($video);
        $this->em->persist($user);
        $this->em->flush();

        return 'liked';
    }

    private function unlikeVideo(Video $video)
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->removeLikedVideo($video);
        $this->em->persist($user);
        $this->em->flush();

        return 'undo liked';
    }

    private function dislikeVideo(Video $video)
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->addDislikedVideo($video);
        $this->em->persist($user);
        $this->em->flush();

        return 'disliked';
    }

    private function undoDislikeVideo(Video $video)
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->removeDislikedVideo($video);
        $this->em->persist($user);
        $this->em->flush();

        return 'undo disliked';
    }
}