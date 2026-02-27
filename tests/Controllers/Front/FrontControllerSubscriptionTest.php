<?php

namespace App\Tests\Controllers\Front;

use App\Entity\Subscription;
use App\Tests\RoleUser;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSubscriptionTest extends WebTestCase
{

    use RoleUser;

    #[DataProvider('urlsWithVideoLists')]
    public function testLoggedInUserDoesNotSeeTextForNoMembers($url): void
    {
        $this->client->request('GET', $url);

        $this->assertStringNotContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    #[DataProvider('urlsWithVideoLists')]
    public function testNotLoggedInUserSeesTextForNoMembers($url): void
    {
        $this->client->setServerParameters([]);
        $this->client->request('GET', $url);

        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    #[DataProvider('urlsWithVideoLists')]
    public function testExpiredSubscription($url)
    {
        /** @var Subscription $subscription */
        $subscription = $this->em->getRepository(Subscription::class)->find(2);

        $invalidDate = new \DateTime();
        $invalidDate->modify('-1 day');
        $subscription->setValidTo($invalidDate);

        $this->em->persist($subscription);
        $this->em->flush();

        $this->client->request('GET', $url);
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    public static function urlsWithVideoLists(): \Generator
    {
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
    }

    #[DataProvider('urlsWithVideos')]
    public function testNotLoggedInUserSeesVideosForNoMembers($url): void
    {
        $this->client->setServerParameters([]);
        $this->client->request('GET', $url);

        $this->assertStringContainsString('https://player.vimeo.com/video/113716040', $this->client->getResponse()->getContent());
    }

    public static function urlsWithVideos(): \Generator
    {
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
        yield ['/video-details/2'];
        yield ['/video-details/13#video_comments'];
    }
}
