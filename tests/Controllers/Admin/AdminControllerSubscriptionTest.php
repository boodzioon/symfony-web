<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerSubscriptionTest extends WebTestCase
{

    use RoleUser;

    #[DataProvider('urlsWithVideoLists')]
    public function testDeleteSubscription($url)
    {
        $crawler = $this->client->request('GET', '/admin/');
        $link = $crawler->filter('a:contains("Cancel plan")')->link();

        $this->client->click($link);

        $this->client->request('GET', $url);
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    public static function urlsWithVideoLists(): \Generator
    {
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
    }
}
