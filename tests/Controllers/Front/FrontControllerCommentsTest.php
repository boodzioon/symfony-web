<?php

namespace App\Tests\Front;

use App\Tests\Rollback;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{

    use Rollback;

    public function testNotLoggedInUser(): void
    {
        $this->client->followRedirects();
        $this->client->setServerParameters([]);
        $crawler = $this->client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);
        $this->client->submit($form);

        $this->assertStringContainsString('Please sign in', $this->client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/video-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);
        $this->client->submit($form);
        $this->assertStringContainsString('Test comment', $this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/video-list/category/movies,4/4');
        $this->assertSame('Comments (1)', $crawler->filter('a.ml-1')->text());
    }
}
