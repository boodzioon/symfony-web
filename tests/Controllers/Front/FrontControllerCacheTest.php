<?php

namespace App\Tests\Front;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCacheTest extends WebTestCase
{

    use RoleUser;

    public function testSomething(): void
    {
        $this->client->enableProfiler();
        $this->client->request('GET', '/video-list/category/movies,4/3');
        $this->assertGreaterThan(
            4,
            $this->client->getProfile()->getCollector('db')->getQueryCount()
        );

        $this->client->enableProfiler();
        $this->client->request('GET', '/video-list/category/movies,4/3');
        $this->assertSame(
            2,
            $this->client->getProfile()->getCollector('db')->getQueryCount()
        );
    }
}
